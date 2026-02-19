<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center flex-wrap gap-4">
            <div class="view-header">
                <h2 class="view-header__title">Detalle de notificación</h2>
                <p class="view-header__subtitle">{{ $notification->created_at->format('d/m/Y H:i') }}</p>
            </div>
            <a href="{{ route('notifications.index') }}" class="btn-primary-app">← Volver al listado</a>
        </div>
    </x-slot>

    @php
        $d = is_array($notification->data) ? $notification->data : [];
        $titulo = $d['titulo'] ?? $d['message'] ?? $d['contact_name'] ?? 'Notificación';
        $mensaje = $d['mensaje'] ?? $d['message'] ?? '';
    @endphp

    <div class="view-card">
        <h3 class="text-lg font-semibold text-[#1F2937]">{{ $titulo }}</h3>
        <div class="mt-4 text-[#374151] whitespace-pre-wrap">{{ $mensaje ?: 'Sin contenido.' }}</div>
        <div class="mt-6 flex flex-wrap gap-2">
            @if(!$notification->read_at)
                <form method="POST" action="{{ route('notifications.mark-read', $notification) }}" class="inline">
                    @csrf
                    <button type="submit" class="btn-primary-app">Marcar como leída</button>
                </form>
            @endif
            @if(!empty($notification->starred))
                <form method="POST" action="{{ route('notifications.unstar', $notification) }}" class="inline">
                    @csrf
                    <button type="submit" class="btn-amber-app">Quitar destacada</button>
                </form>
            @else
                <form method="POST" action="{{ route('notifications.star', $notification) }}" class="inline">
                    @csrf
                    <button type="submit" class="btn-amber-app">★ Destacar</button>
                </form>
            @endif
            <form method="POST" action="{{ route('notifications.destroy', $notification) }}" class="inline" onsubmit="return confirm('¿Eliminar esta notificación?');">
                @csrf
                @method('DELETE')
                <button type="submit" class="px-4 py-2 rounded-xl border border-red-200 text-red-600 hover:bg-red-50">Eliminar</button>
            </form>
        </div>
    </div>
</x-app-layout>
