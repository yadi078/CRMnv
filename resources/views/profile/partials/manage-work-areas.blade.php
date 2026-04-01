<section class="space-y-6">
    <header>
        <h2 class="text-lg font-medium text-white">
            Areas de trabajo
        </h2>
        <p class="mt-1 text-sm text-white/70">
            Catalogo usado en el campo "Area de trabajo" de contactos. Solo se permiten valores de esta lista.
        </p>
    </header>

    <form method="POST" action="{{ route('settings.work-areas.store') }}" class="space-y-3">
        @csrf
        <label for="work_area_name" class="block text-sm font-medium text-white/90">Agregar area</label>
        <div class="flex flex-col sm:flex-row gap-3">
            <input
                id="work_area_name"
                name="name"
                type="text"
                value="{{ old('name') }}"
                class="block w-full rounded-xl border-0 bg-white/15 text-white placeholder-white/60 py-2.5 px-3"
                placeholder="Ej. RECURSOS HUMANOS"
                required
            />
            <button type="submit" class="btn-amber-app whitespace-nowrap">Agregar</button>
        </div>
        <x-input-error :messages="$errors->get('name')" class="mt-2" />
    </form>

    <div class="space-y-3">
        @forelse($workAreas as $workArea)
            <div class="rounded-xl border border-white/20 bg-white/5 p-3">
                <div class="flex flex-col lg:flex-row gap-3 lg:items-center">
                    <form method="POST" action="{{ route('settings.work-areas.update', $workArea) }}" class="flex-1 flex flex-col sm:flex-row gap-3">
                        @csrf
                        @method('PUT')
                        <input
                            type="text"
                            name="name"
                            value="{{ old('name', $workArea->name) }}"
                            class="block w-full rounded-xl border-0 bg-white/15 text-white py-2.5 px-3"
                            required
                        />
                        <button type="submit" class="btn-panel-dark bg-white/10 text-white border border-white/30 hover:bg-white/20 whitespace-nowrap">
                            Guardar
                        </button>
                    </form>

                    <form method="POST" action="{{ route('settings.work-areas.destroy', $workArea) }}" onsubmit="return confirm('¿Eliminar esta area de trabajo?');">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn-danger-app whitespace-nowrap">Eliminar</button>
                    </form>
                </div>
            </div>
        @empty
            <p class="text-sm text-white/70">No hay areas registradas.</p>
        @endforelse
    </div>
</section>
