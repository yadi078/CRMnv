<?php

namespace App\Http\Controllers;

use App\Models\WorkArea;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class WorkAreaController extends Controller
{
    public function store(Request $request): RedirectResponse
    {
        abort_unless($request->user()?->esAdmin(), 403);

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255', Rule::unique('work_areas', 'name')],
        ]);

        WorkArea::create([
            'name' => $this->normalizeName($validated['name']),
        ]);

        return back()->with('success', 'Area de trabajo agregada correctamente.');
    }

    public function update(Request $request, WorkArea $workArea): RedirectResponse
    {
        abort_unless($request->user()?->esAdmin(), 403);

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255', Rule::unique('work_areas', 'name')->ignore($workArea->id)],
        ]);

        $workArea->update([
            'name' => $this->normalizeName($validated['name']),
        ]);

        return back()->with('success', 'Area de trabajo actualizada correctamente.');
    }

    public function destroy(Request $request, WorkArea $workArea): RedirectResponse
    {
        abort_unless($request->user()?->esAdmin(), 403);

        $workArea->delete();

        return back()->with('success', 'Area de trabajo eliminada correctamente.');
    }

    private function normalizeName(string $value): string
    {
        return mb_strtoupper(trim(preg_replace('/\s+/u', ' ', $value) ?? $value), 'UTF-8');
    }
}
