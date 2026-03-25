<x-app-layout>
    <x-slot name="header">
        <div class="page-header-card__icon" aria-hidden="true">
            <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9" />
            </svg>
        </div>
        <div>
            <h2 class="page-header-card__title">Detalle de notificación</h2>
            <p class="page-header-card__subtitle">{{ $notification->created_at->format('d/m/Y H:i') }}</p>
        </div>
        <a href="{{ route('notifications.index') }}" class="btn-panel-dark ml-auto">← Volver al listado</a>
    </x-slot>

    @php
        $d = is_array($notification->data) ? $notification->data : [];
        $titulo = $d['titulo'] ?? $d['message'] ?? $d['contact_name'] ?? 'Notificación';
        $mensaje = $d['mensaje'] ?? $d['message'] ?? '';
        $tipo = $d['tipo'] ?? 'general';
    @endphp

    <div class="space-y-8">
        <div class="panel-card-dark p-6">
        <h3 class="panel-card-dark__title panel-card-dark__title--accent mb-4">{{ $titulo }}</h3>
        <div class="mt-4 text-white">
            @if($tipo === 'recordatorio')
                @include('notifications.partials.reminder-notification-detail', ['d' => $d, 'mensaje' => $mensaje])
            @elseif($tipo === 'eliminacion_solicitud')
                <div class="whitespace-pre-wrap !text-white">{{ $mensaje ?: 'Sin contenido.' }}</div>
                @if(!empty($d['entity_name']))
                    <p class="text-white/80 text-sm mt-4"><span class="text-[#FFE600] font-semibold">{{ ($d['entity'] ?? '') === 'company' ? 'Empresa' : 'Contacto' }}:</span> {{ $d['entity_name'] }}</p>
                @endif
                @if(($d['outcome'] ?? '') === 'denied' && !empty($d['nota_admin']))
                    <p class="text-[#FFE600] font-semibold text-sm mt-4">Motivo del administrador</p>
                    <div class="mt-2 whitespace-pre-wrap text-white/90 border border-white/15 rounded-xl p-3 bg-black/20">{{ $d['nota_admin'] }}</div>
                @endif
            @else
                <div class="whitespace-pre-wrap !text-white">{{ $mensaje ?: 'Sin contenido.' }}</div>
            @endif
        </div>
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
    </div>
</x-app-layout>
