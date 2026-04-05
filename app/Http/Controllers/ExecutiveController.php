<?php

namespace App\Http\Controllers;

use App\Http\Requests\AssignContactExecutiveRequest;
use App\Http\Requests\BulkAssignContactsExecutiveRequest;
use App\Http\Requests\UpdateExecutiveAccountStatusRequest;
use App\Http\Requests\StoreExecutiveRequest;
use App\Http\Requests\TransferExecutiveContactRequest;
use App\Http\Requests\TransferExecutivePortfolioRequest;
use App\Http\Requests\UpdateExecutiveAssignmentsRequest;
use App\Models\Company;
use App\Models\Contact;
use App\Models\User;
use App\Support\MexicanStates;
use Database\Seeders\RolePermissionSeeder;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\View\View;
use Spatie\Permission\Models\Role;

class ExecutiveController extends Controller
{
    private const SESSION_EXECUTIVES_INDEX_FILTERS = 'executives_index_filters';

    /**
     * Listado de ejecutivos: mismas cuentas que los usuarios del CRM (modelo User),
     * excluyendo solo perfiles administrador (no son ejecutivos de cartera).
     *
     * Filtros: empresa, contacto, entidad (estado de México), estado de cuenta (activo/inactivo), ejecutivo.
     * Se guardan en sesión hasta «Limpiar».
     */
    public function index(Request $request): View|RedirectResponse
    {
        if ($request->boolean('clear_filters')) {
            $request->session()->forget(self::SESSION_EXECUTIVES_INDEX_FILTERS);

            return redirect()->route('executives.index');
        }

        if ($request->boolean('cerrar_asistencia_contrasenas')) {
            $request->session()->forget('managed_user_id');
            $query = collect($request->query())
                ->except(['cerrar_asistencia_contrasenas', 'user_search'])
                ->filter(fn ($v) => $v !== null && $v !== '')
                ->all();

            return redirect()->route('executives.index', $query);
        }

        $this->ensureWebRoleExists('usuario');
        $this->ensureWebRoleExists('administrador');

        $filterKeys = ['empresa_id', 'contacto_id', 'entidad', 'cuenta_activa', 'ejecutivo_id', 'estado'];
        $hasFilterKeysInQuery = $request->hasAny($filterKeys);

        if (! $hasFilterKeysInQuery) {
            $saved = $request->session()->get(self::SESSION_EXECUTIVES_INDEX_FILTERS, []);
            $saved = is_array($saved) ? $this->normalizeSavedExecutiveFilters($saved) : [];
            $nonEmpty = array_filter($saved, fn ($v) => $v !== null && $v !== '');
            if ($nonEmpty !== []) {
                $query = $nonEmpty;
                // No perder la búsqueda de asistencia de contraseñas al rehidratar filtros desde sesión.
                if ($request->filled('user_search')) {
                    $query['user_search'] = (string) $request->query('user_search');
                }

                return redirect()->route('executives.index', $query);
            }
        }

        $effective = array_filter(
            $request->only(['empresa_id', 'contacto_id', 'entidad', 'cuenta_activa', 'ejecutivo_id']),
            fn ($v) => $v !== null && $v !== ''
        );

        // Compatibilidad: ?estado=activo|inactivo (antes era cuenta activa/inactiva)
        if (! isset($effective['cuenta_activa']) && $request->filled('estado')) {
            $legacy = (string) $request->input('estado');
            if (in_array($legacy, ['activo', 'inactivo'], true)) {
                $effective['cuenta_activa'] = $legacy;
            }
        }

        $request->session()->put(self::SESSION_EXECUTIVES_INDEX_FILTERS, $effective);

        $empresaId = isset($effective['empresa_id']) ? (int) $effective['empresa_id'] : null;
        if ($empresaId !== null && $empresaId < 1) {
            $empresaId = null;
        }
        $contactoId = isset($effective['contacto_id']) ? (int) $effective['contacto_id'] : null;
        if ($contactoId !== null && $contactoId < 1) {
            $contactoId = null;
        }

        $ejecutivoRaw = $effective['ejecutivo_id'] ?? null;
        $ejecutivoModo = null;
        $ejecutivoUserId = null;
        $ejecutivoTextoFiltro = null;
        if ($ejecutivoRaw === 'sin') {
            $ejecutivoModo = 'sin';
        } elseif ($ejecutivoRaw === 'con') {
            $ejecutivoModo = 'con';
        } elseif (is_string($ejecutivoRaw) && str_starts_with($ejecutivoRaw, 'E:')) {
            $decoded = base64_decode(substr($ejecutivoRaw, 2), true);
            if ($decoded !== false && trim($decoded) !== '') {
                $ejecutivoTextoFiltro = trim($decoded);
            }
        } elseif ($ejecutivoRaw !== null && $ejecutivoRaw !== '' && is_numeric($ejecutivoRaw)) {
            $uid = (int) $ejecutivoRaw;
            if ($uid > 0) {
                $ejecutivoUserId = $uid;
            }
        }

        $entidadFiltro = isset($effective['entidad']) ? (string) $effective['entidad'] : null;
        if (! MexicanStates::isValid($entidadFiltro)) {
            $entidadFiltro = null;
        }

        $cuentaActivaFiltro = $effective['cuenta_activa'] ?? null;
        if (! in_array($cuentaActivaFiltro, ['activo', 'inactivo'], true)) {
            $cuentaActivaFiltro = null;
        }

        $companiesForFilter = Company::query()->orderBy('nombre_comercial')->get(['id', 'nombre_comercial']);
        $contactsForFilter = Contact::query()->orderBy('nombre_completo')->limit(500)->get(['id', 'nombre_completo', 'company_id']);

        $executivesForTransfer = User::query()
            ->whereDoesntHave('roles', function ($q): void {
                $q->whereIn('name', ['admin', 'administrador']);
            })
            ->orderBy('name')
            ->get(['id', 'name', 'email', 'is_active']);

        $executiveFilterOptions = $this->buildExecutiveFilterOptions();

        $executivesForPortfolioDestination = User::query()
            ->whereDoesntHave('roles', function ($q): void {
                $q->whereIn('name', ['admin', 'administrador']);
            })
            ->where('is_active', true)
            ->where('approval_status', 'aprobado')
            ->orderBy('name')
            ->get(['id', 'name', 'email']);

        $canPortfolioTransfer = $this->portfolioTransferHasValidPair($executiveFilterOptions, $executivesForPortfolioDestination);

        /**
         * Listado por asignaciones (contactos): con empresa y/o contacto, o vista masiva (sin/con ejecutivo).
         */
        $assignmentContacts = null;
        $assignmentFilterStats = null;
        $executives = null;

        $showAssignments = $empresaId !== null
            || $contactoId !== null
            || $ejecutivoModo !== null
            || $ejecutivoUserId !== null
            || $ejecutivoTextoFiltro !== null;

        if ($showAssignments) {
            $cq = Contact::query()->with(['company', 'assignedExecutive']);

            if ($empresaId !== null) {
                $cq->where('company_id', $empresaId);
            }

            if ($contactoId !== null) {
                $cq->where('id', $contactoId);
            }

            if ($entidadFiltro !== null) {
                $cq->where(function ($q) use ($entidadFiltro): void {
                    $q->where('contacts.estado', $entidadFiltro)
                        ->orWhereHas('company', fn ($c) => $c->where('estado', $entidadFiltro));
                });
            }

            // Sin ejecutivo asignado: no aplica "estado de cuenta" del responsable (no hay usuario).
            // Tampoco si el filtro es por texto de ficha (ejecutivo_asignado): puede no haber usuario vinculado.
            if ($cuentaActivaFiltro !== null && $ejecutivoModo !== 'sin' && $ejecutivoTextoFiltro === null) {
                if ($cuentaActivaFiltro === 'activo') {
                    $cq->whereHas('assignedExecutive', fn ($q) => $q->where('is_active', true));
                } else {
                    $cq->whereHas('assignedExecutive', fn ($q) => $q->where('is_active', false));
                }
            }

            if ($ejecutivoModo === 'sin') {
                $cq->whereNull('assigned_user_id');
            } elseif ($ejecutivoModo === 'con') {
                $cq->whereNotNull('assigned_user_id');
            } elseif ($ejecutivoUserId !== null) {
                $cq->where('assigned_user_id', $ejecutivoUserId);
            } else            if ($ejecutivoTextoFiltro !== null) {
                $cq->whereHas('company', function ($q) use ($ejecutivoTextoFiltro): void {
                    $q->where('ejecutivo_asignado', $ejecutivoTextoFiltro);
                });
            }

            $assignmentFilterStats = [
                'contacts' => (clone $cq)->count(),
                'companies' => (int) (clone $cq)
                    ->whereNotNull('contacts.company_id')
                    ->selectRaw('count(distinct contacts.company_id) as aggregate')
                    ->value('aggregate'),
            ];
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
                ->withCount('assignedCompanies')
                ->orderBy('name');

            if ($cuentaActivaFiltro !== null) {
                if ($cuentaActivaFiltro === 'activo') {
                    $query->where('is_active', true);
                } else {
                    $query->where('is_active', false);
                }
            }

            if ($ejecutivoUserId !== null) {
                $query->whereKey($ejecutivoUserId);
            }

            if ($entidadFiltro !== null) {
                $query->where(function ($q) use ($entidadFiltro): void {
                    $q->whereHas('assignedCompanies', fn ($c) => $c->where('estado', $entidadFiltro))
                        ->orWhereHas('assignedContacts', function ($ct) use ($entidadFiltro): void {
                            $ct->where(function ($x) use ($entidadFiltro): void {
                                $x->where('contacts.estado', $entidadFiltro)
                                    ->orWhereHas('company', fn ($co) => $co->where('estado', $entidadFiltro));
                            });
                        });
                });
            }

            $executives = $query->paginate(12)->withQueryString();
        }

        return view('executives.index', array_merge([
            'executives' => $executives,
            'assignmentContacts' => $assignmentContacts,
            'assignmentFilterStats' => $assignmentFilterStats,
            'autoAssignContactId' => $autoAssignContactId,
            'companiesForFilter' => $companiesForFilter,
            'contactsForFilter' => $contactsForFilter,
            'executivesForTransfer' => $executivesForTransfer,
            'executiveFilterOptions' => $executiveFilterOptions,
            'executivesForPortfolioDestination' => $executivesForPortfolioDestination,
            'canPortfolioTransfer' => $canPortfolioTransfer,
            'mexicanStates' => MexicanStates::all(),
        ], ProfileController::adminPasswordAssistanceState($request)));
    }

    /**
     * Hay al menos un par válido origen → destino (origen distinto del destino cuando ambos son ID de usuario).
     */
    private function portfolioTransferHasValidPair(Collection $originOptions, Collection $destUsers): bool
    {
        if ($destUsers->isEmpty() || $originOptions->isEmpty()) {
            return false;
        }

        foreach ($originOptions as $row) {
            $val = (string) ($row['value'] ?? '');
            if ($val === '') {
                continue;
            }
            if (str_starts_with($val, 'E:')) {
                return true;
            }
            if (ctype_digit($val)) {
                $fromId = (int) $val;
                foreach ($destUsers as $d) {
                    if ((int) $d->id !== $fromId) {
                        return true;
                    }
                }
            }
        }

        return false;
    }

    /**
     * Opciones del filtro «Ejecutivo (asignación)»: usuarios de cartera (no admin) + nombres ya guardados
     * en empresa.ejecutivo_asignado sin cuenta vinculada (mismo criterio que el filtro Comercial en Filtros).
     *
     * @return \Illuminate\Support\Collection<int, array{value: string, label: string}>
     */
    private function buildExecutiveFilterOptions(): \Illuminate\Support\Collection
    {
        $rows = collect();

        $users = User::query()
            ->whereDoesntHave('roles', function ($q): void {
                $q->whereIn('name', ['admin', 'administrador']);
            })
            ->orderBy('name')
            ->get(['id', 'name', 'email']);

        foreach ($users as $u) {
            $label = $u->name;
            if ($u->email !== null && trim((string) $u->email) !== '') {
                $label .= ' — '.$u->email;
            }
            $rows->push(['value' => (string) $u->id, 'label' => $label]);
        }

        $texts = Company::query()
            ->whereNotNull('ejecutivo_asignado')
            ->where('ejecutivo_asignado', '!=', '')
            ->distinct()
            ->orderBy('ejecutivo_asignado')
            ->pluck('ejecutivo_asignado');

        foreach ($texts as $raw) {
            $t = trim((string) $raw);
            if ($t === '') {
                continue;
            }
            $key = 'E:'.base64_encode($t);
            $rows->push(['value' => $key, 'label' => $t]);
        }

        return $rows
            ->sortBy(fn (array $row): string => mb_strtolower($row['label']))
            ->values();
    }

    /**
     * Migra filtros guardados con la clave antigua `estado` (activo/inactivo).
     *
     * @param  array<string, mixed>  $saved
     * @return array<string, mixed>
     */
    private function normalizeSavedExecutiveFilters(array $saved): array
    {
        if (isset($saved['estado']) && in_array($saved['estado'], ['activo', 'inactivo'], true)
            && (! isset($saved['cuenta_activa']) || $saved['cuenta_activa'] === '' || $saved['cuenta_activa'] === null)) {
            $saved['cuenta_activa'] = $saved['estado'];
        }
        unset($saved['estado']);

        return $saved;
    }

    /**
     * Activar o desactivar la cuenta del ejecutivo (solo admin).
     */
    public function updateAccountStatus(UpdateExecutiveAccountStatusRequest $request, User $user): RedirectResponse
    {
        if ($user->esAdmin()) {
            abort(404);
        }

        $active = $request->boolean('is_active');
        if ($user->is_active === $active) {
            return back();
        }

        $user->update(['is_active' => $active]);

        return back()->with(
            'status',
            $active ? 'La cuenta quedó activa: el usuario puede iniciar sesión.' : 'La cuenta quedó inactiva: no podrá iniciar sesión hasta que la reactive.'
        );
    }

    /**
     * Perfil del ejecutivo y asignaciones (solo admin).
     */
    public function show(User $user): View|RedirectResponse
    {
        if ($user->esAdmin()) {
            return redirect()
                ->route('executives.index')
                ->with('warning', 'Los usuarios administradores no tienen ficha de ejecutivo en este módulo.');
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

        $execSearchPayload = [
            'companies' => $user->assignedCompanies->map(static fn ($c) => [
                'nombre' => (string) $c->nombre_comercial,
                'contactos' => $c->contacts->map(static fn ($ct) => (string) $ct->nombre_completo)->values()->all(),
            ])->values()->all(),
            'orphans' => $orphanAssignedContacts->map(static fn ($ct) => [
                'nombre' => (string) $ct->nombre_completo,
                'empresa' => (string) ($ct->company?->nombre_comercial ?? ''),
            ])->values()->all(),
            'unifiedContacts' => $unifiedContactsForList->map(static fn ($c) => [
                'nombre' => (string) $c->nombre_completo,
                'empresa' => (string) ($c->company?->nombre_comercial ?? ''),
            ])->values()->all(),
        ];

        return view('executives.show', [
            'executive' => $user,
            'orphanAssignedContacts' => $orphanAssignedContacts,
            'unifiedContactsForList' => $unifiedContactsForList,
            'execSearchPayload' => $execSearchPayload,
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
     * Asignar varios contactos a un ejecutivo (vista Asignaciones).
     */
    public function bulkAssignContactsToExecutive(BulkAssignContactsExecutiveRequest $request): RedirectResponse
    {
        $validated = $request->validated();
        $executive = User::findOrFail((int) $validated['user_id']);

        if ($executive->esAdmin()) {
            return back()
                ->with('error', 'No se puede asignar un administrador como ejecutivo de los contactos.');
        }

        $ids = array_map('intval', $validated['contact_ids']);
        $ids = array_values(array_unique($ids));

        $updated = Contact::query()
            ->whereIn('id', $ids)
            ->update(['assigned_user_id' => $executive->id]);

        return back()
            ->with(
                'success',
                $updated === 1
                    ? '1 contacto asignado a «'.$executive->name.'».'
                    : $updated.' contactos asignados a «'.$executive->name.'».'
            );
    }

    /**
     * Alta de ejecutivo desde modal.
     */
    public function store(StoreExecutiveRequest $request): RedirectResponse
    {
        $data = $request->validated();
        $roleName = $data['role'];
        unset($data['role']);

        $this->ensureWebRoleExists($roleName);

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
     * Vista previa JSON: empresas cuya cartera se movería con el origen elegido (solo admin).
     */
    public function previewPortfolioTransfer(Request $request): JsonResponse
    {
        if (! $request->user()?->esAdmin()) {
            abort(403);
        }

        $fromRaw = $request->query('from_user_id', '');
        $fromStr = is_string($fromRaw) ? $fromRaw : (string) $fromRaw;
        $fromStr = trim($fromStr);

        if ($fromStr === '') {
            return response()->json([
                'companies' => [],
                'company_count' => 0,
                'contact_count' => 0,
                'companies_preview_truncated' => false,
            ]);
        }

        if (str_starts_with($fromStr, 'E:')) {
            $decoded = base64_decode(substr($fromStr, 2), true);
            $text = $decoded !== false ? trim((string) $decoded) : '';
            if ($text === '') {
                return response()->json([
                    'companies' => [],
                    'company_count' => 0,
                    'contact_count' => 0,
                    'companies_preview_truncated' => false,
                ]);
            }

            $companyQuery = Company::query()
                ->where('ejecutivo_asignado', $text)
                ->whereNull('assigned_user_id');

            $companyCount = (clone $companyQuery)->count();
            $contactCount = Contact::query()
                ->whereNull('assigned_user_id')
                ->whereIn('company_id', (clone $companyQuery)->select('id'))
                ->count();

            $companies = (clone $companyQuery)
                ->orderBy('nombre_comercial')
                ->limit(500)
                ->get(['id', 'nombre_comercial']);

            return response()->json([
                'companies' => $companies,
                'company_count' => $companyCount,
                'contact_count' => $contactCount,
                'companies_preview_truncated' => $companyCount > 500,
            ]);
        }

        if (! ctype_digit($fromStr)) {
            return response()->json([
                'companies' => [],
                'company_count' => 0,
                'contact_count' => 0,
                'companies_preview_truncated' => false,
            ]);
        }

        $fromId = (int) $fromStr;
        $from = User::find($fromId);
        if ($from === null || $from->esAdmin()) {
            return response()->json([
                'companies' => [],
                'company_count' => 0,
                'contact_count' => 0,
                'companies_preview_truncated' => false,
            ]);
        }

        $companyCount = Company::query()->where('assigned_user_id', $fromId)->count();
        $contactCount = Contact::query()->where('assigned_user_id', $fromId)->count();

        $companies = Company::query()
            ->where('assigned_user_id', $fromId)
            ->orderBy('nombre_comercial')
            ->limit(500)
            ->get(['id', 'nombre_comercial']);

        return response()->json([
            'companies' => $companies,
            'company_count' => $companyCount,
            'contact_count' => $contactCount,
            'companies_preview_truncated' => $companyCount > 500,
        ]);
    }

    /**
     * Pasa empresas y contactos asignados de un ejecutivo a otro (solo admin).
     */
    public function transferPortfolio(TransferExecutivePortfolioRequest $request): RedirectResponse
    {
        $validated = $request->validated();
        $to = User::findOrFail((int) $validated['to_user_id']);

        if ($to->esAdmin() || ! $to->is_active || $to->approval_status !== 'aprobado') {
            return redirect()
                ->route('executives.index')
                ->with('error', 'El destino debe ser un ejecutivo activo y aprobado.');
        }

        $fromRaw = $validated['from_user_id'];

        if (is_string($fromRaw) && str_starts_with($fromRaw, 'E:')) {
            $decoded = base64_decode(substr($fromRaw, 2), true);
            $text = $decoded !== false ? trim((string) $decoded) : '';
            if ($text === '') {
                return redirect()
                    ->route('executives.index')
                    ->with('error', 'El origen por ficha no es válido.');
            }

            $counts = DB::transaction(function () use ($text, $to): array {
                $companyIds = Company::query()
                    ->where('ejecutivo_asignado', $text)
                    ->whereNull('assigned_user_id')
                    ->pluck('id')
                    ->all();

                $companies = 0;
                if ($companyIds !== []) {
                    $companies = Company::query()
                        ->whereIn('id', $companyIds)
                        ->update([
                            'assigned_user_id' => $to->id,
                            'ejecutivo_asignado' => $to->name,
                        ]);
                }

                $contacts = 0;
                if ($companyIds !== []) {
                    $contacts = Contact::query()
                        ->whereIn('company_id', $companyIds)
                        ->whereNull('assigned_user_id')
                        ->update(['assigned_user_id' => $to->id]);
                }

                return ['companies' => $companies, 'contacts' => $contacts];
            });

            $detail = 'Se actualizaron '.$counts['companies'].' empresa(s) y '.$counts['contacts'].' contacto(s); destino: «'.$to->name.'».';

            return redirect()
                ->route('executives.index')
                ->with(
                    'executives_transfer_toast',
                    'Transferencia completada exitosamente. '.$detail
                );
        }

        $fromId = (int) $fromRaw;
        $from = User::findOrFail($fromId);

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

        $detail = $counts['companies'].' empresa(s) y '.$counts['contacts'].' contacto(s) pasaron de «'.$from->name.'» a «'.$to->name.'».';

        return redirect()
            ->route('executives.index')
            ->with(
                'executives_transfer_toast',
                'Transferencia completada exitosamente. '.$detail
            );
    }

    /**
     * Eliminar cuenta de ejecutivo: libera asignaciones y borra el usuario (solo admin).
     */
    public function destroy(Request $request, User $user): RedirectResponse
    {
        if ($user->esAdmin()) {
            abort(404);
        }

        if ((int) $user->id === (int) $request->user()->id) {
            return redirect()
                ->route('executives.index')
                ->with('error', 'No puede eliminar su propia cuenta.');
        }

        $name = $user->name;

        DB::transaction(function () use ($user): void {
            Company::query()->where('assigned_user_id', $user->id)->update([
                'assigned_user_id' => null,
                'ejecutivo_asignado' => null,
            ]);

            Contact::query()->where('assigned_user_id', $user->id)->update([
                'assigned_user_id' => null,
            ]);

            $user->permissions()->detach();
            $user->roles()->detach();
            $user->delete();
        });

        return redirect()
            ->route('executives.index')
            ->with('success', 'Ejecutivo «'.$name.'» eliminado. Sus asignaciones quedaron liberadas.');
    }

    /**
     * En hosting suele faltar el seeder de Spatie; syncRoles() lanza excepción si el rol no existe.
     * El seeder es idempotente (firstOrCreate / givePermissionTo).
     */
    private function ensureWebRoleExists(string $roleName): void
    {
        if (Role::query()->where('name', $roleName)->where('guard_name', 'web')->exists()) {
            return;
        }

        app(RolePermissionSeeder::class)->run();
    }
}
