<x-app-layout>
    <x-slot name="header">
        @include('user.sales.partials.create-header-inner')
    </x-slot>

    <div class="space-y-8">
        @include('user.sales.partials.create-form')
    </div>
</x-app-layout>
