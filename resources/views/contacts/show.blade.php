<x-app-layout>
    <x-slot name="header">
        <x-page-header-avatar><svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
            </svg></x-page-header-avatar>
        <div>
            <h2 class="page-header-card__title">{{ $contact->nombre_completo }}</h2>
            <p class="page-header-card__subtitle">Detalle de contacto</p>
        </div>
        @include('contacts.partials.show-header-actions', ['contact' => $contact, 'sale' => $sale ?? null])
    </x-slot>

    <div class="space-y-8">
        @include('contacts.partials.show-body', ['contact' => $contact, 'contactSales' => $contactSales ?? null])
    </div>

</x-app-layout>
