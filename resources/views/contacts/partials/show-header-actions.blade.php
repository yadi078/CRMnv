@php
    /** @var \App\Models\Contact $contact */
    $hideGenerarFichaEnCabecera = $hideGenerarFichaEnCabecera ?? false;
    $nuevaVentaDesdeContactoUrl = $contact->company_id
        ? route('user.sales.create', ['company_id' => $contact->company_id, 'contact_id' => $contact->id])
        : route('user.sales.create');
@endphp

<div class="flex flex-wrap gap-2 ml-auto justify-end items-center">
    @unless($hideGenerarFichaEnCabecera)
        @can('sales.create')
            <a href="{{ $nuevaVentaDesdeContactoUrl }}" class="btn-amber-app inline-flex items-center gap-2">
                <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                Generar ficha de inscripción
            </a>
        @endcan
    @endunless
    @can('contacts.edit')
        <a href="{{ \App\Support\CrmNavigation::withReturn(route('contacts.edit', $contact)) }}" class="btn-amber-app">
            <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
            </svg>
            Editar
        </a>
    @endcan
    @can('delete', $contact)
        <form
            method="POST"
            action="{{ route('contacts.destroy', $contact) }}"
            class="inline-flex items-center gap-2"
            onsubmit="return confirm('¿Eliminar el contacto «{{ addslashes($contact->nombre_completo) }}»? Esta acción no se puede deshacer.');"
        >
            @csrf
            @method('DELETE')
            <button
                type="submit"
                class="inline-flex items-center gap-2 px-4 py-2 rounded-xl border-2 border-red-400/80 bg-red-600/90 text-white font-medium hover:bg-red-600"
            >
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                Eliminar
            </button>
        </form>
    @endcan
    @can('requestDeletion', $contact)
        @cannot('delete', $contact)
        <button
            type="button"
            class="inline-flex items-center gap-2 px-4 py-2 rounded-xl border-2 border-red-400/80 bg-red-600/90 text-white font-medium hover:bg-red-600"
            onclick="document.getElementById('modal-solicitud-eliminacion-contacto').classList.remove('hidden'); document.body.style.overflow='hidden';"
        >
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
            Solicitar eliminación
        </button>
        @endcannot
    @endcan
</div>
