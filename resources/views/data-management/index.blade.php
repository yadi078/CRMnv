<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center flex-wrap gap-4">
            <div class="view-header">
                <div class="view-header__icon">
                    <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 7v10c0 2.21 3.582 4 8 4s8-1.79 8-4V7M4 7c0 2.21 3.582 4 8 4s8-1.79 8-4M4 7c0-2.21 3.582-4 8-4s8 1.79 8 4m0 5c0 2.21-3.582 4-8 4s-8-1.79-8-4" />
                    </svg>
                </div>
                <div>
                    <h2 class="view-header__title">Gestión de Datos</h2>
                    <p class="view-header__subtitle">Visualiza y gestiona tus datos de Contactos y Empresas</p>
                </div>
            </div>
        </div>
    </x-slot>

    <div class="py-8 sm:py-10">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <!-- Tabs -->
            <div class="view-card mb-6">
                <div class="border-b border-gray-200">
                    <nav class="-mb-px flex space-x-8" aria-label="Tabs">
                        <a href="{{ route('data-management.index', ['tab' => 'contacts']) }}" 
                           class="tab-link {{ $tab === 'contacts' ? 'tab-link--active' : '' }}">
                            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z" />
                            </svg>
                            Contactos
                        </a>
                        <a href="{{ route('data-management.index', ['tab' => 'companies']) }}" 
                           class="tab-link {{ $tab === 'companies' ? 'tab-link--active' : '' }}">
                            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                            </svg>
                            Empresas
                        </a>
                    </nav>
                </div>
            </div>

            <!-- Tabla de Contactos -->
            @if($tab === 'contacts')
            <div class="view-card p-6">
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200" id="contacts-table">
                        <thead class="bg-azul-fuerte text-white">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider">Nombre</th>
                                <th class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider">Empresa</th>
                                <th class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider">Email</th>
                                <th class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider">Teléfono</th>
                                <th class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider">Acciones</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            @forelse($contacts as $contact)
                            <tr data-id="{{ $contact->id }}" class="hover:bg-gray-50">
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm font-medium text-gray-900">{{ $contact->nombre_completo }}</div>
                                    <div class="text-sm text-gray-500">{{ $contact->puesto_de_trabajo ?? '-' }}</div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $contact->company->nombre_comercial ?? '-' }}</td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $contact->email }}</td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $contact->celular ?? '-' }}</td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                    @if(auth()->user()->esAdmin())
                                    <div class="flex items-center gap-2">
                                        <button type="button" onclick="openEditContactModal({{ $contact->id }})" class="p-2 rounded-lg text-azul-fuerte hover:bg-azul-fuerte/10 transition-colors" title="Editar">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                            </svg>
                                        </button>
                                        <button type="button" data-delete-id="{{ $contact->id }}" data-delete-name="{{ $contact->nombre_completo }}" data-delete-type="contact" class="p-2 rounded-lg text-red-600 hover:bg-red-50 transition-colors btn-delete-contact" title="Eliminar">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                            </svg>
                                        </button>
                                    </div>
                                    @else
                                    <span class="text-gray-400 text-xs">Solicitar permiso</span>
                                    @endif
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="5" class="px-6 py-4 text-center text-gray-500">No se encontraron contactos</td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
                <div class="mt-4">
                    {{ $contacts->links() }}
                </div>
            </div>
            @endif

            <!-- Tabla de Empresas -->
            @if($tab === 'companies')
            <div class="view-card p-6">
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200" id="companies-table">
                        <thead class="bg-azul-fuerte text-white">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider">Nombre Comercial</th>
                                <th class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider">RFC</th>
                                <th class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider">Sector</th>
                                <th class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider">Estado</th>
                                <th class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider">Acciones</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            @forelse($companies as $company)
                            <tr data-id="{{ $company->id }}" class="hover:bg-gray-50">
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm font-medium text-gray-900">{{ $company->nombre_comercial }}</div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $company->rfc }}</td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $company->sector ?? '-' }}</td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $company->estado ?? '-' }}</td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                    @if(auth()->user()->esAdmin())
                                    <div class="flex items-center gap-2">
                                        <button type="button" onclick="openEditCompanyModal({{ $company->id }})" class="p-2 rounded-lg text-azul-fuerte hover:bg-azul-fuerte/10 transition-colors" title="Editar">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                            </svg>
                                        </button>
                                        <button type="button" data-delete-id="{{ $company->id }}" data-delete-name="{{ $company->nombre_comercial }}" data-delete-type="company" class="p-2 rounded-lg text-red-600 hover:bg-red-50 transition-colors btn-delete-company" title="Eliminar">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                            </svg>
                                        </button>
                                    </div>
                                    @else
                                    <span class="text-gray-400 text-xs">Solicitar permiso</span>
                                    @endif
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="5" class="px-6 py-4 text-center text-gray-500">No se encontraron empresas</td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
                <div class="mt-4">
                    {{ $companies->links() }}
                </div>
            </div>
            @endif

            <!-- Funciones de Admin: Exportar e Importar -->
            @if(auth()->user()->esAdmin())
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mt-6">
                <!-- Exportación -->
                <div class="view-card p-6">
                    <h3 class="text-lg font-semibold text-gray-900 mb-4 flex items-center">
                        <svg class="w-5 h-5 mr-2 text-azul-fuerte" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                        </svg>
                        Exportar Datos
                    </h3>
                    <p class="text-sm text-gray-600 mb-4">Selecciona las tablas que deseas exportar a Excel</p>
                    
                    <div id="export-section" class="space-y-3">
                        <div class="flex items-center justify-between mb-3">
                            <label class="flex items-center">
                                <input type="checkbox" id="select-all-tables" class="rounded border-gray-300 text-azul-fuerte focus:ring-azul-fuerte">
                                <span class="ml-2 text-sm font-medium text-gray-700">Seleccionar Todo</span>
                            </label>
                        </div>
                        <div id="tables-list" class="max-h-64 overflow-y-auto border border-gray-200 rounded-lg bg-gray-50 min-h-[120px]">
                            <div class="p-4 pr-6 space-y-2">
                                <p class="text-sm text-gray-500">Cargando tablas...</p>
                            </div>
                        </div>
                        <button type="button" id="export-btn" class="btn-primary-app w-full" disabled>
                            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                            </svg>
                            Exportar Seleccionadas
                        </button>
                    </div>
                </div>

                <!-- Importación -->
                <div class="view-card p-6">
                    <h3 class="text-lg font-semibold text-gray-900 mb-4 flex items-center">
                        <svg class="w-5 h-5 mr-2 text-azul-fuerte" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12" />
                        </svg>
                        Importar Datos
                    </h3>
                    <p class="text-sm text-gray-600 mb-4">Sube un archivo Excel para importar datos (Upsert: actualiza si existe, crea si no)</p>
                    
                    <div id="import-section" class="space-y-3">
                        <div id="drop-zone" class="border-2 border-dashed border-gray-300 rounded-lg p-8 text-center hover:border-azul-fuerte transition-colors cursor-pointer">
                            <input type="file" id="file-input" accept=".xlsx,.xls" class="hidden">
                            <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12" />
                            </svg>
                            <p class="mt-2 text-sm text-gray-600">
                                <span class="font-medium text-azul-fuerte">Haz clic para subir</span> o arrastra y suelta
                            </p>
                            <p class="text-xs text-gray-500 mt-1">Archivos Excel (.xlsx, .xls) hasta 10MB</p>
                        </div>
                        <div id="file-info" class="hidden text-sm text-gray-600"></div>
                        <button type="button" id="import-btn" class="btn-primary-app w-full" disabled>
                            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12" />
                            </svg>
                            Importar Archivo
                        </button>
                        <div id="import-result" class="hidden"></div>
                    </div>
                </div>
            </div>
            @endif
        </div>
    </div>

    {{-- Modal de edición: recuadro pequeño centrado --}}
    @if(auth()->user()->esAdmin())
    <div id="edit-modal" class="fixed inset-0 z-50 hidden overflow-y-auto" aria-labelledby="modal-title" role="dialog" aria-modal="true">
        <div class="flex min-h-screen items-center justify-center p-4">
            <div class="fixed inset-0 bg-gray-900/70 backdrop-blur-sm transition-opacity" onclick="closeEditModal()"></div>
            <div class="relative w-full max-h-[90vh] flex flex-col overflow-hidden text-left align-middle transition-all transform bg-white rounded-2xl shadow-2xl ring-1 ring-black/5" style="max-width: 600px; animation: modalIn 0.2s ease-out;">
                <div class="flex shrink-0 items-center justify-between px-4 py-2.5 bg-azul-fuerte text-white rounded-t-2xl">
                    <h3 id="modal-title" class="text-base font-semibold tracking-tight">Editar</h3>
                    <button type="button" onclick="closeEditModal()" class="p-1.5 rounded-lg text-white/90 hover:text-white hover:bg-white/15 transition-colors" aria-label="Cerrar">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>
                </div>
                <div id="modal-body" class="flex-1 min-h-0 px-4 py-3 overflow-y-auto overscroll-contain bg-gray-50/80 border-b border-gray-100" style="max-height: 55vh;">
                    {{-- Se llena dinámicamente --}}
                </div>
                <div class="flex shrink-0 justify-end gap-2 px-4 py-3 bg-white rounded-b-2xl border-t border-gray-200">
                    <button type="button" onclick="closeEditModal()" class="px-3 py-2 text-sm font-medium text-gray-600 bg-gray-100 rounded-xl hover:bg-gray-200 transition-colors">
                        Cancelar
                    </button>
                    <button type="button" id="modal-save-btn" class="px-4 py-2 text-sm font-medium text-white rounded-xl bg-azul-fuerte hover:bg-azul-fuerte/90 flex items-center gap-2 shadow-lg shadow-azul-fuerte/25 transition-colors">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                        </svg>
                        Guardar
                    </button>
                </div>
            </div>
        </div>
    </div>
    <style>
        @keyframes modalIn {
            from { opacity: 0; transform: scale(0.96); }
            to { opacity: 1; transform: scale(1); }
        }
    </style>

    {{-- Modal de confirmación de eliminación --}}
    <div id="delete-modal" class="fixed inset-0 z-50 hidden overflow-y-auto" role="dialog" aria-modal="true">
        <div class="flex items-center justify-center min-h-screen px-4">
            <div class="fixed inset-0 bg-gray-900/60 transition-opacity" onclick="closeDeleteModal()"></div>
            <div class="relative inline-block w-full max-w-md p-6 my-8 overflow-hidden text-left align-middle transition-all transform bg-white rounded-2xl shadow-xl">
                <h3 class="text-lg font-semibold text-gray-900 mb-2">¿Eliminar registro?</h3>
                <p id="delete-modal-message" class="text-sm text-gray-600 mb-4">Esta acción no se puede deshacer.</p>
                <div class="flex justify-end gap-2">
                    <button type="button" onclick="closeDeleteModal()" class="px-4 py-2 text-sm font-medium text-gray-700 bg-gray-100 rounded-lg hover:bg-gray-200">Cancelar</button>
                    <button type="button" id="delete-confirm-btn" class="px-4 py-2 text-sm font-medium text-white bg-red-600 rounded-lg hover:bg-red-700">Eliminar</button>
                </div>
            </div>
        </div>
    </div>
    @endif

    @push('scripts')
    <script>
        // Estilos para tabs
        const tabLinkStyle = 'whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm transition-colors';
        const tabLinkActive = 'border-azul-fuerte text-azul-fuerte';
        const tabLinkInactive = 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300';

        // Aplicar estilos a los tabs
        document.querySelectorAll('.tab-link').forEach(link => {
            link.className = `tab-link ${tabLinkStyle} ${link.classList.contains('tab-link--active') ? tabLinkActive : tabLinkInactive} flex items-center`;
        });

        // Modal de edición y eliminación
        @if(auth()->user()->esAdmin())
        let currentEditType = null;
        let currentEditId = null;

        window.openEditContactModal = function(id) {
            currentEditType = 'contact';
            currentEditId = id;
            document.getElementById('modal-title').textContent = 'Editar Contacto';
            document.getElementById('modal-body').innerHTML = '<div class="flex justify-center py-6"><svg class="animate-spin h-6 w-6 text-azul-fuerte" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg></div>';
            document.getElementById('edit-modal').classList.remove('hidden');

            fetch(`{{ url('data-management/contacts') }}/${id}`, {
                headers: { 'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest' }
            })
            .then(r => r.json())
            .then(data => {
                const c = data.contact;
                let options = (data.companies || []).map(co =>
                    `<option value="${co.id}" ${co.id == c.company_id ? 'selected' : ''}>${escapeHtml(co.nombre_comercial)}</option>`
                ).join('');
                const inputClass = 'w-full text-sm py-2 px-3 rounded-xl border border-gray-200 bg-white shadow-sm focus:border-azul-fuerte focus:ring-2 focus:ring-azul-fuerte/20 outline-none transition placeholder:text-gray-400';
                const labelClass = 'block text-xs font-semibold text-gray-700 mb-1 uppercase tracking-wide';
                document.getElementById('modal-body').innerHTML = `
                    <form id="edit-form" class="space-y-3">
                        <div>
                            <label class="${labelClass}">Empresa *</label>
                            <select name="company_id" class="${inputClass}" required>
                                <option value="">Seleccione empresa</option>${options}
                            </select>
                        </div>
                        <div>
                            <label class="${labelClass}">Nombre Completo *</label>
                            <input type="text" name="nombre_completo" value="${escapeHtml(c.nombre_completo)}" class="${inputClass}" required>
                        </div>
                        <div class="grid grid-cols-2 gap-2">
                            <div>
                                <label class="${labelClass}">Puesto</label>
                                <input type="text" name="puesto_de_trabajo" value="${escapeHtml(c.puesto_de_trabajo || '')}" class="${inputClass}">
                            </div>
                            <div>
                                <label class="${labelClass}">Departamento</label>
                                <input type="text" name="departamento" value="${escapeHtml(c.departamento || '')}" class="${inputClass}">
                            </div>
                        </div>
                        <div class="grid grid-cols-2 gap-2">
                            <div>
                                <label class="${labelClass}">Email *</label>
                                <input type="email" name="email" value="${escapeHtml(c.email)}" class="${inputClass}" required>
                            </div>
                            <div>
                                <label class="${labelClass}">Celular</label>
                                <input type="text" name="celular" value="${escapeHtml(c.celular || '')}" class="${inputClass}">
                            </div>
                        </div>
                        <div class="grid grid-cols-2 gap-2">
                            <div>
                                <label class="${labelClass}">Extensión</label>
                                <input type="text" name="extension" value="${escapeHtml(c.extension || '')}" class="${inputClass}">
                            </div>
                            <div>
                                <label class="${labelClass}">Municipio</label>
                                <input type="text" name="municipio" value="${escapeHtml(c.municipio || '')}" class="${inputClass}">
                            </div>
                        </div>
                        <div>
                            <label class="${labelClass}">Estado</label>
                            <input type="text" name="estado" value="${escapeHtml(c.estado || '')}" class="${inputClass}">
                        </div>
                        <div>
                            <label class="${labelClass}">Notas</label>
                            <textarea name="notas" rows="2" class="${inputClass} resize-none">${escapeHtml(c.notas || '')}</textarea>
                        </div>
                    </form>
                `;
            })
            .catch(err => {
                console.error(err);
                document.getElementById('modal-body').innerHTML = '<p class="text-red-600 text-sm">Error al cargar el contacto.</p>';
            });
        };

        window.openEditCompanyModal = function(id) {
            currentEditType = 'company';
            currentEditId = id;
            document.getElementById('modal-title').textContent = 'Editar Empresa';
            document.getElementById('modal-body').innerHTML = '<div class="flex justify-center py-6"><svg class="animate-spin h-6 w-6 text-azul-fuerte" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg></div>';
            document.getElementById('edit-modal').classList.remove('hidden');

            fetch(`{{ url('data-management/companies') }}/${id}`, {
                headers: { 'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest' }
            })
            .then(r => r.json())
            .then(data => {
                const c = data.company;
                const statusOpts = ['verde','amarillo','rojo'].map(v =>
                    `<option value="${v}" ${v === (c.status_color || '') ? 'selected' : ''}>${v.charAt(0).toUpperCase()+v.slice(1)}</option>`
                ).join('');
                const inputClass = 'w-full text-sm py-2 px-3 rounded-xl border border-gray-200 bg-white shadow-sm focus:border-azul-fuerte focus:ring-2 focus:ring-azul-fuerte/20 outline-none transition placeholder:text-gray-400';
                const labelClass = 'block text-xs font-semibold text-gray-700 mb-1 uppercase tracking-wide';
                document.getElementById('modal-body').innerHTML = `
                    <form id="edit-form" class="space-y-3">
                        <div>
                            <label class="${labelClass}">Nombre Comercial *</label>
                            <input type="text" name="nombre_comercial" value="${escapeHtml(c.nombre_comercial)}" class="${inputClass}" required>
                        </div>
                        <div>
                            <label class="${labelClass}">RFC *</label>
                            <input type="text" name="rfc" value="${escapeHtml(c.rfc)}" class="${inputClass} uppercase" maxlength="13" required>
                        </div>
                        <div>
                            <label class="${labelClass}">Sector/Giro</label>
                            <input type="text" name="sector" value="${escapeHtml(c.sector || '')}" class="${inputClass}">
                        </div>
                        <div class="grid grid-cols-2 gap-2">
                            <div>
                                <label class="${labelClass}">Municipio</label>
                                <input type="text" name="municipio" value="${escapeHtml(c.municipio || '')}" class="${inputClass}">
                            </div>
                            <div>
                                <label class="${labelClass}">Estado</label>
                                <input type="text" name="estado" value="${escapeHtml(c.estado || '')}" class="${inputClass}">
                            </div>
                        </div>
                        <div class="grid grid-cols-2 gap-2">
                            <div>
                                <label class="${labelClass}">Ejecutivo</label>
                                <input type="text" name="ejecutivo_asignado" value="${escapeHtml(c.ejecutivo_asignado || '')}" class="${inputClass}">
                            </div>
                            <div>
                                <label class="${labelClass}">Semáforo</label>
                                <select name="status_color" class="${inputClass}">${statusOpts}</select>
                            </div>
                        </div>
                        <div>
                            <label class="${labelClass}">Datos Fiscales</label>
                            <textarea name="datos_fiscales" rows="2" class="${inputClass} resize-none">${escapeHtml(c.datos_fiscales || '')}</textarea>
                        </div>
                    </form>
                `;
            })
            .catch(err => {
                console.error(err);
                document.getElementById('modal-body').innerHTML = '<p class="text-red-600 text-sm">Error al cargar la empresa.</p>';
            });
        };

        function escapeHtml(text) {
            if (!text) return '';
            const d = document.createElement('div');
            d.textContent = text;
            return d.innerHTML;
        }

        window.closeEditModal = function() {
            document.getElementById('edit-modal').classList.add('hidden');
            currentEditType = null;
            currentEditId = null;
        };

        document.getElementById('modal-save-btn').addEventListener('click', function() {
            if (!currentEditType || !currentEditId) return;
            const form = document.getElementById('edit-form');
            if (!form) return;

            const formData = new FormData(form);
            const data = {};
            formData.forEach((v, k) => data[k] = v);

            const url = currentEditType === 'contact'
                ? `{{ url('data-management/contacts') }}/${currentEditId}`
                : `{{ url('data-management/companies') }}/${currentEditId}`;

            this.disabled = true;
            this.innerHTML = '<svg class="animate-spin h-4 w-4" fill="none" viewBox="0 0 24 24">...</svg> Guardando...';

            fetch(url, {
                method: 'PUT',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                },
                body: JSON.stringify(data)
            })
            .then(r => r.json())
            .then(result => {
                if (result.success) {
                    closeEditModal();
                    if (typeof showAlert === 'function') {
                        showAlert('success', currentEditType === 'contact' ? 'Contacto guardado exitosamente' : 'Empresa guardada exitosamente');
                    } else alert('Guardado exitosamente');
                    location.reload();
                } else {
                    if (typeof showAlert === 'function') showAlert('error', result.message || 'Error al guardar');
                    else alert(result.message || 'Error al guardar');
                }
            })
            .catch(err => {
                console.error(err);
                if (typeof showAlert === 'function') showAlert('error', 'Error al guardar. Intente de nuevo.');
                else alert('Error al guardar');
            })
            .finally(() => {
                this.disabled = false;
                this.innerHTML = '<svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" /></svg> Guardar';
            });
        });

        let pendingDelete = null;
        function confirmDelete(id, name, type) {
            pendingDelete = { type, id };
            const msg = type === 'contact'
                ? `¿Eliminar el contacto "${name}"? Esta acción no se puede deshacer.`
                : `¿Eliminar la empresa "${name}"? Esta acción no se puede deshacer.`;
            document.getElementById('delete-modal-message').textContent = msg;
            document.getElementById('delete-modal').classList.remove('hidden');
        }
        document.addEventListener('click', function(e) {
            const btn = e.target.closest('[data-delete-type]');
            if (btn) {
                e.preventDefault();
                confirmDelete(parseInt(btn.dataset.deleteId), btn.dataset.deleteName || '', btn.dataset.deleteType);
            }
        });
        window.closeDeleteModal = function() {
            document.getElementById('delete-modal').classList.add('hidden');
            pendingDelete = null;
        };
        document.getElementById('delete-confirm-btn').addEventListener('click', function() {
            if (!pendingDelete) return;
            const url = pendingDelete.type === 'contact'
                ? `{{ url('data-management/contacts') }}/${pendingDelete.id}`
                : `{{ url('data-management/companies') }}/${pendingDelete.id}`;
            const row = document.querySelector(`tr[data-id="${pendingDelete.id}"]`);
            this.disabled = true;

            fetch(url, {
                method: 'DELETE',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                    'Accept': 'application/json'
                }
            })
            .then(r => r.json())
            .then(result => {
                closeDeleteModal();
                if (result.success && row) row.remove();
                if (typeof showAlert === 'function') showAlert(result.success ? 'success' : 'error', result.message);
                else alert(result.message);
                if (result.success) location.reload();
            })
            .catch(err => {
                console.error(err);
                if (typeof showAlert === 'function') showAlert('error', 'Error al eliminar');
                else alert('Error al eliminar');
            })
            .finally(() => { this.disabled = false; });
        });
        @endif

        // Exportación (solo admin)
        @if(auth()->user()->esAdmin())
        let allTables = [];
        let selectedTables = new Set();

        function loadTables() {
            const tablesList = document.getElementById('tables-list');
            if (!tablesList) {
                console.error('No se encontró el elemento tables-list');
                setTimeout(loadTables, 500); // Reintentar después de 500ms
                return;
            }

            // Mostrar mensaje de carga
            tablesList.innerHTML = '<div class="p-4 text-center"><p class="text-sm text-gray-500">Cargando tablas...</p></div>';

            const tablesUrl = '{{ route("data-management.tables") }}';
            console.log('Intentando cargar tablas desde:', tablesUrl);
            
            fetch(tablesUrl, {
                method: 'GET',
                headers: {
                    'Accept': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest'
                },
                credentials: 'same-origin'
            })
            .then(response => {
                console.log('Respuesta recibida:', response.status);
                if (!response.ok) {
                    return response.text().then(text => {
                        console.error('Error en respuesta:', text);
                        throw new Error('Error HTTP ' + response.status + ': ' + text);
                    });
                }
                return response.json();
            })
            .then(data => {
                console.log('Datos recibidos:', data);
                if (data.error) {
                    throw new Error(data.error);
                }
                
                allTables = data.tables || [];
                console.log('Tablas encontradas:', allTables.length);
                
                if (allTables.length === 0) {
                    tablesList.innerHTML = '<div class="p-4 text-center"><p class="text-sm text-gray-500">No hay tablas disponibles</p></div>';
                    const exportBtn = document.getElementById('export-btn');
                    if (exportBtn) exportBtn.disabled = true;
                    return;
                }
                
                renderTables(allTables);
            })
            .catch(error => {
                console.error('Error al cargar tablas:', error);
                const tablesList = document.getElementById('tables-list');
                if (tablesList) {
                    tablesList.innerHTML = `
                        <div class="p-4 text-center">
                            <p class="text-sm text-red-600 font-medium mb-1">Error al cargar las tablas</p>
                            <p class="text-xs text-gray-500 mb-3">${error.message || 'Error desconocido'}</p>
                            <button onclick="location.reload()" class="text-xs text-azul-fuerte hover:underline">Recargar página</button>
                        </div>
                    `;
                }
                const exportBtn = document.getElementById('export-btn');
                if (exportBtn) exportBtn.disabled = true;
            });
        }

        function renderTables(tables) {
                const tablesList = document.getElementById('tables-list');
                const contentWrapper = document.createElement('div');
                contentWrapper.className = 'p-4 space-y-2 pr-6';
                
                const importantTables = ['contacts', 'companies', 'follow_ups', 'users', 'roles', 'permissions', 'model_has_roles', 'model_has_permissions', 'role_has_permissions'];
                
                const importantFound = [];
                const otherTables = [];
                
                tables.forEach(table => {
                    if (importantTables.includes(table)) {
                        importantFound.push(table);
                    } else {
                        otherTables.push(table);
                    }
                });
                
                // Mostrar tablas importantes
                if (importantFound.length > 0) {
                    const separator = document.createElement('div');
                    separator.className = 'mb-3 pt-1';
                    separator.innerHTML = '<p class="text-xs font-semibold text-azul-fuerte uppercase tracking-wide mb-2">Tablas Principales</p>';
                    contentWrapper.appendChild(separator);
                    
                    importantFound.forEach(table => {
                        const div = document.createElement('div');
                        div.className = 'flex items-center py-1.5 mb-1';
                        div.innerHTML = `
                            <label class="flex items-center cursor-pointer w-full hover:bg-white rounded px-2 py-1.5 transition-colors">
                                <input type="checkbox" value="${table}" class="table-checkbox rounded border-gray-300 text-azul-fuerte focus:ring-azul-fuerte">
                                <span class="ml-2 text-sm font-medium text-gray-900">${table}</span>
                            </label>
                        `;
                        contentWrapper.appendChild(div);
                    });
                }
                
                // Mostrar otras tablas
                if (otherTables.length > 0) {
                    const separator = document.createElement('div');
                    separator.className = 'mt-4 mb-3 pt-3 border-t border-gray-300';
                    separator.innerHTML = '<p class="text-xs font-semibold text-gray-500 uppercase tracking-wide mb-2">Otras Tablas</p>';
                    contentWrapper.appendChild(separator);
                    
                    otherTables.forEach(table => {
                        const div = document.createElement('div');
                        div.className = 'flex items-center py-1.5 mb-1';
                        div.innerHTML = `
                            <label class="flex items-center cursor-pointer w-full hover:bg-white rounded px-2 py-1.5 transition-colors">
                                <input type="checkbox" value="${table}" class="table-checkbox rounded border-gray-300 text-azul-fuerte focus:ring-azul-fuerte">
                                <span class="ml-2 text-sm text-gray-700">${table}</span>
                            </label>
                        `;
                        contentWrapper.appendChild(div);
                    });
                }
                
                tablesList.innerHTML = '';
                tablesList.appendChild(contentWrapper);
                
                // Event listeners para checkboxes
                document.querySelectorAll('.table-checkbox').forEach(checkbox => {
                    checkbox.addEventListener('change', function() {
                        if (this.checked) {
                            selectedTables.add(this.value);
                        } else {
                            selectedTables.delete(this.value);
                        }
                        updateExportButton();
                    });
                });

                // Seleccionar todo
                const selectAllCheckbox = document.getElementById('select-all-tables');
                if (selectAllCheckbox) {
                    selectAllCheckbox.addEventListener('change', function() {
                        const checkboxes = document.querySelectorAll('.table-checkbox');
                        checkboxes.forEach(cb => {
                            cb.checked = this.checked;
                            if (this.checked) {
                                selectedTables.add(cb.value);
                            } else {
                                selectedTables.delete(cb.value);
                            }
                        });
                        updateExportButton();
                    });
                }
            }

        function updateExportButton() {
            const btn = document.getElementById('export-btn');
            if (btn) {
                btn.disabled = selectedTables.size === 0;
            }
        }

        // Cargar tablas cuando el DOM esté listo
        if (document.readyState === 'loading') {
            document.addEventListener('DOMContentLoaded', function() {
                setTimeout(loadTables, 100);
            });
        } else {
            setTimeout(loadTables, 100);
        }

        // Configurar botón de exportar
        const exportBtn = document.getElementById('export-btn');
        if (exportBtn) {
            exportBtn.addEventListener('click', function() {
                if (selectedTables.size === 0) {
                    alert('Por favor, selecciona al menos una tabla para exportar');
                    return;
                }

                const form = document.createElement('form');
                form.method = 'POST';
                form.action = '{{ route("data-management.export") }}';

                const csrf = document.createElement('input');
                csrf.type = 'hidden';
                csrf.name = '_token';
                const tokenEl = document.querySelector('meta[name="csrf-token"]');
                csrf.value = tokenEl ? tokenEl.content : '';
                form.appendChild(csrf);

                Array.from(selectedTables).forEach(table => {
                    const input = document.createElement('input');
                    input.type = 'hidden';
                    input.name = 'tables[]';
                    input.value = table;
                    form.appendChild(input);
                });

                document.body.appendChild(form);
                form.submit();
                document.body.removeChild(form);
            });
        }

        // Importación con drag & drop
        const dropZone = document.getElementById('drop-zone');
        const fileInput = document.getElementById('file-input');
        const fileInfo = document.getElementById('file-info');
        const importBtn = document.getElementById('import-btn');
        const importResult = document.getElementById('import-result');
        let selectedFile = null;

        if (dropZone) dropZone.addEventListener('click', () => fileInput && fileInput.click());
        if (dropZone) {
            dropZone.addEventListener('dragover', (e) => {
                e.preventDefault();
                dropZone.classList.add('border-azul-fuerte', 'bg-azul-fuerte/5');
            });
            dropZone.addEventListener('dragleave', () => {
                dropZone.classList.remove('border-azul-fuerte', 'bg-azul-fuerte/5');
            });
            dropZone.addEventListener('drop', (e) => {
                e.preventDefault();
                dropZone.classList.remove('border-azul-fuerte', 'bg-azul-fuerte/5');
                const files = e.dataTransfer.files;
                if (files.length > 0) handleFile(files[0]);
            });
        }
        if (fileInput) {
            fileInput.addEventListener('change', (e) => {
                if (e.target.files.length > 0) handleFile(e.target.files[0]);
            });
        }

        function handleFile(file) {
            if (!file.name.match(/\.(xlsx|xls)$/i)) {
                if (typeof showAlert === 'function') {
                    showAlert('warning', 'Por favor, selecciona un archivo Excel (.xlsx o .xls)');
                } else {
                    alert('Por favor, selecciona un archivo Excel (.xlsx o .xls)');
                }
                return;
            }

            if (file.size > 10 * 1024 * 1024) {
                if (typeof showAlert === 'function') {
                    showAlert('warning', 'El archivo es demasiado grande. Máximo 10MB');
                } else {
                    alert('El archivo es demasiado grande. Máximo 10MB');
                }
                return;
            }

            selectedFile = file;
            if (fileInfo) {
                fileInfo.textContent = `Archivo seleccionado: ${file.name} (${(file.size / 1024 / 1024).toFixed(2)} MB)`;
                fileInfo.classList.remove('hidden');
            }
            if (importBtn) importBtn.disabled = false;
        }

        if (importBtn) importBtn.addEventListener('click', function() {
            if (!selectedFile) {
                if (typeof showAlert === 'function') showAlert('warning', 'Primero selecciona un archivo Excel');
                else alert('Primero selecciona un archivo haciendo clic en la zona de subida.');
                return;
            }

            const formData = new FormData();
            formData.append('file', selectedFile);
            const tokenEl = document.querySelector('meta[name="csrf-token"]');
            const csrfToken = tokenEl ? tokenEl.content : '';
            formData.append('_token', csrfToken);

            const importUrl = '{{ route("data-management.import") }}';
            console.log('Importando archivo:', selectedFile.name, '→', importUrl);

            importBtn.disabled = true;
            importBtn.innerHTML = '<svg class="animate-spin h-5 w-5 mr-2 inline" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg> Importando...';

            fetch(importUrl, {
                method: 'POST',
                headers: {
                    'X-Requested-With': 'XMLHttpRequest',
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': csrfToken
                },
                body: formData,
                credentials: 'same-origin'
            })
            .then(response => {
                const contentType = response.headers.get('content-type') || '';
                console.log('Importación respuesta:', response.status, response.statusText);

                if (response.status === 419) {
                    return response.text().then(() => {
                        throw new Error('Sesión expirada. Recarga la página (F5) e intenta de nuevo.');
                    });
                }
                if (!response.ok) {
                    return response.text().then(text => {
                        let msg = 'Error en el servidor';
                        try {
                            const j = JSON.parse(text);
                            if (j && j.message) msg = j.message;
                            else if (j && j.errors && j.errors.file) msg = Array.isArray(j.errors.file) ? j.errors.file[0] : j.errors.file;
                        } catch (_) {
                            if (text && text.length < 300) msg = text;
                        }
                        throw new Error(msg);
                    });
                }
                if (contentType.includes('application/json')) {
                    return response.json();
                }
                return response.text().then(() => ({ success: false, message: 'Respuesta no válida del servidor' }));
            })
            .then(result => {
                console.log('Importación resultado:', result);
                if (result.success) {
                    let message = result.message || 'Archivo importado exitosamente';
                    
                    if (result.results) {
                        message += ` - Creados: ${result.results.created}, Actualizados: ${result.results.updated}`;
                        
                        if (result.results.errors && result.results.errors.length > 0) {
                            message += `. ${result.results.errors.length} error(es) encontrado(s)`;
                        }
                    }
                    
                    if (typeof showAlert === 'function') {
                        showAlert('success', message);
                    } else if (importResult) {
                        importResult.className = 'rounded-lg px-4 py-3 bg-green-50 border border-green-200 text-green-800';
                        importResult.innerHTML = `<strong>✓ Éxito:</strong> ${message}`;
                        importResult.classList.remove('hidden');
                    }
                    
                    // Recargar después de 3 segundos si no hay errores críticos
                    if (!result.results || !result.results.errors || result.results.errors.length === 0) {
                        setTimeout(() => location.reload(), 3000);
                    }
                } else {
                    const errMsg = result.message || 'Error al importar el archivo';
                    if (typeof showAlert === 'function') {
                        showAlert('error', errMsg);
                    } else if (importResult) {
                        importResult.className = 'rounded-lg px-4 py-3 bg-red-50 border border-red-200 text-red-800';
                        importResult.innerHTML = `<strong>✗ Error:</strong> ${errMsg}`;
                        importResult.classList.remove('hidden');
                    }
                }
            })
            .catch(error => {
                console.error('Error:', error);
                const errMsg = (error && error.message) ? error.message : 'No se pudo importar el archivo. Por favor, intente nuevamente.';
                if (typeof showAlert === 'function') {
                    showAlert('error', errMsg);
                } else if (importResult) {
                    importResult.className = 'rounded-lg px-4 py-3 bg-red-50 border border-red-200 text-red-800';
                    importResult.innerHTML = `<strong>Error:</strong> ${errMsg}`;
                    importResult.classList.remove('hidden');
                }
            })
            .finally(() => {
                importBtn.disabled = false;
                importBtn.innerHTML = '<svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12" /></svg> Importar Archivo';
            });
        });
        @endif
    </script>
    @endpush
</x-app-layout>
