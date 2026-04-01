<?php

namespace App\Http\Controllers;

use App\Http\Requests\AssignContactExecutiveRequest;
use App\Http\Requests\StoreExecutiveRequest;
use App\Http\Requests\TransferExecutiveContactRequest;
use App\Http\Requests\TransferExecutivePortfolioRequest;
use App\Http\Requests\UpdateExecutiveAssignmentsRequest;
use App\Models\Company;
use App\Models\Contact;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\View\View;

class ExecutiveController extends Controller
{
    private const SESSION_EXECUTIVES_INDEX_FILTERS = 'executives_index_filters';

    /**
     * Listado de ejecutivos: mismas cuentas que los usuarios del CRM (modelo User),
     * excluyendo solo perfiles administrador (no son ejecutivos de cartera).
     *
     * Los filtros (empresa, contacto, estado) se guardan en sesión hasta que el administrador pulse «Limpiar».
     */
    public function index(Request $request): View|RedirectResponse
    {
        if ($request->boolean('clear_filters')) {
            $request->session()->forget(self::SESSION_EXECUTIVES_INDEX_FILTERS);

            return redirect()->route('executives.index');
        }

        $hasFilterKeysInQuery = $request->hasAny(['empresa_id', 'contacto_id', 'estado']);

        if (! $hasFilterKeysInQuery) {
            $saved = $request->session()->get(self::SESSION_EXECUTIVES_INDEX_FILTERS, []);
            $saved = is_array($saved) ? $saved : [];
            $nonEmpty = array_filter($saved, fn ($v) => $v !== null && $v !== '');
            if ($nonEmpty !== []) {
                return redirect()->route('executives.index', $nonEmpty);
            }
        }

        $effective = array_filter(
            $request->only(['empresa_id', 'contacto_id', 'estado']),
            fn ($v) => $v !== null && $v !== ''
        );

        $request->session()->put(self::SESSION_EXECUTIVES_INDEX_FILTERS, $effective);

        $empresaId = isset($effective['empresa_id']) ? (int) $effective['empresa_id'] : null;
        if ($empresaId !== null && $empresaId < 1) {
            $empresaId = null;
        }
        $contactoId = isset($effective['contacto_id']) ? (int) $effective['contacto_id'] : null;
        if ($contactoId !== null && $contactoId < 1) {
            $contactoId = null;
        }
        $estadoFiltro = $effective['estado'] ?? null;
        if (! in_array($estadoFiltro, ['activo', 'inactivo'], true)) {
            $estadoFiltro = null;
        }

        $companiesForFilter = Company::query()->orderBy('nombre_comercial')->get(['id', 'nombre_comercial']);
        $contactsForFilter = Contact::query()->orderBy('nombre_completo')->limit(500)->get(['id', 'nombre_completo', 'company_id']);

        $executivesForTransfer = User::query()
            ->whereDoesntHave('roles', function ($q): void {
                $q->whereIn('name', ['admin', 'administrador']);
            })
            ->orderBy('name')
            ->get(['id', 'name', 'email', 'is_active']);

        /**
         * Con filtro de empresa y/o contacto: listado por asignaciones (empresa → contacto → ejecutivo),
         * no por tarjetas de ejecutivo.
         */
        $assignmentContacts = null;
        $executives = null;

        if ($empresaId !== null || $contactoId !== null) {
            $cq = Contact::query()->with(['company', 'assignedExecutive']);

            if ($empresaId !== null) {
                $cq->where('company_id', $empresaId);
            }

            if ($contactoId !== null) {
                $cq->where('id', $contactoId);
            }

            if ($estadoFiltro !== null) {
                if ($estadoFiltro === 'activo') {
                    $cq->whereHas('assignedExecutive', fn ($q) => $q->where('is_active', true));
                } else {
                    $cq->whereHas('assignedExecutive', fn ($q) => $q->where('is_active', false));
                }
            }

            $assignmentContacts = $cq->orderBy('nombre_completo')->paginate(20)->withQueryString();
        }

        $autoAssignContactId = null;
        if ($assignmentContacts !== null) {
            $unassignedOnPage = collect($assignmentContacts->items())->filter(
                static fn (Contact $c) => $c->assigned_user_id === null
            );
            if ($unassignedOnPage->count() === 1) {
                $autoAssignContactId = (int) $unassignedOnPage->first()->id;
            }
        }

        if ($assignmentContacts === null) {
            $query = User::query()
                ->whereDoesntHave('roles', function ($q): void {
                    $q->whereIn('name', ['admin', 'administrador']);
                })
                ->with(['roles'])
                ->orderBy('name');

            if ($estadoFiltro !== null) {
                if ($estadoFiltro === 'activo') {
                    $query->where('is_active', true);
                } else {
                    $query->where('is_active', false);
                }
            }

            $executives = $query->paginate(12)->withQueryString();
        }

        return view('executives.index', array_merge([
            'executives' => $executives,
            'assignmentContacts' => $assignmentContacts,
            'autoAssignContactId' => $autoAssignContactId,
            'companiesForFilter' => $companiesForFilter,
            'contactsForFilter' => $contactsForFilter,
            'executivesForTransfer' => $executivesForTransfer,
        ], ProfileController::adminPasswordAssistanceState($request)));
    }

    /**
     * Perfil del ejecutivo y asignaciones (solo admin).
     */
    public function show(User $user): View
    {
        if ($user->esAdmin()) {
            abort(404);
        }

        $user->load([
            'roles',
            'assignedCompanies' => fn ($q) => $q->with([
                'contacts' => fn ($cq) => $cq->orderBy('nombre_completo'),
            ])->orderBy('nombre_comercial'),
            'assignedContacts' => fn ($q) => $q->with('company')->orderBy('nombre_completo'),
        ]);

        $assignedCompanyIds = $user->assignedCompanies->pluck('id');
        $orphanAssignedContacts = $user->assignedContacts->filter(
            fn (Contact $c) => $c->company_id === null || ! $assignedCompanyIds->contains($c->company_id)
        );

        $seenContactIds = [];
        $unifiedContactsForList = collect();
        foreach ($user->assignedCompanies as $company) {
            foreach ($company->contacts as $contact) {
                if (! isset($seenContactIds[$contact->id])) {
                    $seenContactIds[$contact->id] = true;
                    $unifiedContactsForList->push($contact);
                }
            }
        }
        foreach ($user->assignedContacts as $contact) {
            if (! isset($seenContactIds[$contact->id])) {
                $seenContactIds[$contact->id] = true;
                $unifiedContactsForList->push($contact);
            }
        }
        $unifiedContactsForList = $unifiedContactsForList->sortBy('nombre_completo')->values();

        $allCompanies = Company::query()->orderBy('nombre_comercial')->get(['id', 'nombre_comercial', 'assigned_user_id']);
        $allContacts = Contact::query()->with('company')->orderBy('nombre_completo')->limit(1500)->get();

        $unassignedCompaniesForDatalist = Company::query()
            ->whereNull('assigned_user_id')
            ->orderBy('nombre_comercial')
            ->get(['id', 'nombre_comercial']);

        $unassignedContactsForDatalist = Contact::query()
            ->whereNull('assigned_user_id')
            ->with('company')
            ->orderBy('nombre_completo')
            ->limit(1500)
            ->get();

        $otherExecutives = User::query()
            ->whereDoesntHave('roles', function ($q): void {
                $q->whereIn('name', ['admin', 'administrador']);
            })
            ->whereKeyNot($user->id)
            ->orderBy('name')
            ->get(['id', 'name', 'email']);

        return view('executives.show', [
            'executive' => $user,
            'allCompanies' => $allCompanies,
            'allContacts' => $allContacts,
            'orphanAssignedContacts' => $orphanAssignedContacts,
            'unifiedContactsForList' => $unifiedContactsForList,
            'otherExecutives' => $otherExecutives,
            'unassignedCompaniesForDatalist' => $unassignedCompaniesForDatalist,
            'unassignedContactsForDatalist' => $unassignedContactsForDatalist,
        ]);
    }

    /**
     * Reasignar un contacto de la cartera de este ejecutivo a otro (solo admin).
     */
    public function transferContact(TransferExecutiveContactRequest $request, User $user): RedirectResponse
    {
        if ($user->esAdmin()) {
            abort(404);
        }

        $validated = $request->validated();
        $to = User::findOrFail((int) $validated['to_user_id']);

        if ($to->esAdmin()) {
            return back()
                ->with('error', 'No se puede asignar la cartera a un administrador.');
        }

        $contact = Contact::findOrFail((int) $validated['contact_id']);

        if ((int) $contact->assigned_user_id !== (int) $user->id) {
            abort(403, 'Este contacto no está asignado a este ejecutivo.');
        }

        $contact->update(['assigned_user_id' => $to->id]);

        return back()
            ->with('status', 'Contacto transferido a «'.$to->name.'».');
    }

    /**
     * Asignar un contacto (sin ejecutivo o reasignación) a un usuario ejecutivo (solo admin).
     */
    public function assignContactToExecutive(AssignContactExecutiveRequest $request): RedirectResponse
    {
        $validated = $request->validated();
        $executive = User::findOrFail((int) $validated['user_id']);

        if ($executive->esAdmin()) {
            return back()
                ->with('error', 'No se puede asignar un administrador como ejecutivo del contacto.');
        }

        $contact = Contact::findOrFail((int) $validated['contact_id']);
        $contact->update(['assigned_user_id' => $executive->id]);

        return back()
            ->with('success', 'El contacto «'.$contact->nombre_completo.'» quedó asignado a «'.$executive->name.'».');
    }

    /**
     * Alta de ejecutivo desde modal.
     */
    public function store(StoreExecutiveRequest $request): RedirectResponse
    {
        $data = $request->validated();
        $roleName = $data['role'];
        unset($data['role']);

        $user = DB::transaction(function () use ($data, $request, $roleName) {
            $admin = $request->user();
            $user = User::create([
                'name' => $data['name'],
                'email' => $data['email'],
                'password' => Hash::make($data['password']),
                'is_active' => $data['is_active'],
                'approval_status' => 'aprobado',
                'approved_by' => $admin->id,
                'approved_at' => now(),
            ]);
            $user->syncRoles([$roleName]);

            return $user;
        });

        return redirect()
            ->route('executives.show', $user)
            ->with('status', 'Ejecutivo creado correctamente.');
    }

    /**
     * Sincronizar empresas y contactos asignados al ejecutivo.
     */
    public function updateAssignments(UpdateExecutiveAssignmentsRequest $request, User $user): RedirectResponse
    {
        if ($user->esAdmin()) {
            abort(404);
        }

        $validated = $request->validated();
        $companyIds = array_map('intval', $validated['company_ids'] ?? []);
        $contactIds = array_map('intval', $validated['contact_ids'] ?? []);

        DB::transaction(function () use ($user, $companyIds, $contactIds): void {
            $previousCompanyIds = Company::query()->where('assigned_user_id', $user->id)->pluck('id')->all();

            foreach (array_diff($previousCompanyIds, $companyIds) as $cid) {
                Company::whereKey($cid)->update([
                    'assigned_user_id' => null,
                    'ejecutivo_asignado' => null,
                ]);
            }

            foreach ($companyIds as $cid) {
                Company::whereKey($cid)->update([
                    'assigned_user_id' => $user->id,
                    'ejecutivo_asignado' => $user->name,
                ]);
            }

            $previousContactIds = Contact::query()->where('assigned_user_id', $user->id)->pluck('id')->all();

            foreach (array_diff($previousContactIds, $contactIds) as $ctid) {
                Contact::whereKey($ctid)->update(['assigned_user_id' => null]);
            }

            foreach ($contactIds as $ctid) {
                Contact::whereKey($ctid)->update(['assigned_user_id' => $user->id]);
            }
        });

        return redirect()
            ->route('executives.show', $user)
            ->with('status', 'Asignaciones actualizadas.');
    }

    /**
     * Pasa empresas y contactos asignados de un ejecutivo a otro (solo admin).
     */
    public function transferPortfolio(TransferExecutivePortfolioRequest $request): RedirectResponse
    {
        $from = User::findOrFail($request->validated('from_user_id'));
        $to = User::findOrFail($request->validated('to_user_id'));

        if ($from->esAdmin() || $to->esAdmin()) {
            return redirect()
                ->route('executives.index')
                ->with('error', 'Solo se puede transferir cartera entre ejecutivos.');
        }

        $counts = DB::transaction(function () use ($from, $to): array {
            $companies = Company::query()
                ->where('assigned_user_id', $from->id)
                ->update([
                    'assigned_user_id' => $to->id,
                    'ejecutivo_asignado' => $to->name,
                ]);

            $contacts = Contact::query()
                ->where('assigned_user_id', $from->id)
                ->update(['assigned_user_id' => $to->id]);

            return ['companies' => $companies, 'contacts' => $contacts];
        });

        return redirect()
            ->route('executives.index')
            ->with(
                'success',
                'Cartera transferida: '.$counts['companies'].' empresa(s) y '.$counts['contacts'].' contacto(s) pasaron de «'.$from->name.'» a «'.$to->name.'».'
            );
    }
}
