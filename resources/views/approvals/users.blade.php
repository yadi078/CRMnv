<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-azul-fuerte leading-tight">
            Aprobaciones Pendientes - Usuarios
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white rounded-lg shadow-md p-6">
                @if($users->count() > 0)
                <div class="space-y-4">
                    @foreach($users as $user)
                    <div class="p-4 bg-yellow-50 border-l-4 border-amarillo rounded-lg">
                        <div class="flex justify-between items-start">
                            <div class="flex-1">
                                <h3 class="font-semibold text-gray-900">{{ $user->name }}</h3>
                                <p class="text-sm text-gray-600">Email: {{ $user->email }}</p>
                                <p class="text-sm text-gray-500">Registrado el {{ $user->created_at->format('d/m/Y H:i') }}</p>
                                <p class="text-sm text-gray-500">
                                    Rol: 
                                    @foreach($user->roles as $role)
                                        <span class="px-2 py-1 bg-blue-100 text-blue-800 rounded text-xs">{{ ucfirst($role->name) }}</span>
                                    @endforeach
                                </p>
                            </div>
                            <form method="POST" action="{{ route('approvals.users.approve', $user) }}" class="inline">
                                @csrf
                                <button type="submit" class="bg-green-600 text-white px-4 py-2 rounded-md font-semibold hover:bg-green-700 transition">
                                    Aprobar
                                </button>
                            </form>
                        </div>
                    </div>
                    @endforeach
                </div>

                <div class="mt-4">
                    {{ $users->links() }}
                </div>
                @else
                <p class="text-center text-gray-500 py-8">No hay usuarios pendientes de aprobación</p>
                @endif
            </div>
        </div>
    </div>
</x-app-layout>
