import './bootstrap';

import Alpine from 'alpinejs';

const CRM_MARKER_STORAGE_KEY = 'CE_CRM_ROW_MARKERS_V1';
const CRM_MARKER_STATES = ['none', 'progress', 'done'];

function crmMarkerKey(entity, id) {
    return String(entity) + ':' + String(id);
}

function crmLoadMarkers() {
    try {
        return JSON.parse(localStorage.getItem(CRM_MARKER_STORAGE_KEY) || '{}');
    } catch (e) {
        return {};
    }
}

function crmSaveMarkers(obj) {
    try {
        localStorage.setItem(CRM_MARKER_STORAGE_KEY, JSON.stringify(obj));
    } catch (e) {
        /* ignore quota / private mode */
    }
}

function crmApplyMarkerButton(btn) {
    const entity = btn.getAttribute('data-crm-marker-entity');
    const id = btn.getAttribute('data-crm-marker-id');
    if (!entity || id == null) {
        return;
    }
    const all = crmLoadMarkers();
    const raw = all[crmMarkerKey(entity, id)];
    const state = CRM_MARKER_STATES.includes(raw) ? raw : 'none';
    btn.setAttribute('data-state', state);
    btn.setAttribute('aria-pressed', state !== 'none' ? 'true' : 'false');
}

function crmCycleMarkerState(current) {
    const i = CRM_MARKER_STATES.indexOf(current);
    const from = i >= 0 ? i : 0;
    return CRM_MARKER_STATES[(from + 1) % CRM_MARKER_STATES.length];
}

function crmInitRowMarkers(root) {
    const el = root && root.nodeType === 1 ? root : document;
    el.querySelectorAll('.crm-row-marker').forEach(crmApplyMarkerButton);
}

window.crmInitRowMarkers = crmInitRowMarkers;

/**
 * Subida de foto de perfil (evita meter URL en x-data con comillas: rompe el HTML y Alpine no monta).
 */
Alpine.data('profilePhotoUploader', () => ({
    initialPreview: null,
    preview: null,
    fileLabel: '',
    dragOver: false,
    blobUrl: null,
    init() {
        const raw = this.$el.dataset.profilePhotoInitial;
        this.initialPreview = raw && raw !== '' ? raw : null;
        this.preview = this.initialPreview;
    },
    onPick(e) {
        const f = e.target.files?.[0];
        if (this.blobUrl) {
            URL.revokeObjectURL(this.blobUrl);
            this.blobUrl = null;
        }
        if (f) {
            this.fileLabel = f.name;
            this.blobUrl = URL.createObjectURL(f);
            this.preview = this.blobUrl;
        } else {
            this.fileLabel = '';
            this.preview = this.initialPreview;
        }
    },
    onDrop(e) {
        e.preventDefault();
        this.dragOver = false;
        const f = e.dataTransfer?.files?.[0];
        if (!f || !f.type.startsWith('image/')) {
            return;
        }
        const dt = new DataTransfer();
        dt.items.add(f);
        this.$refs.photoInput.files = dt.files;
        this.onPick({ target: this.$refs.photoInput });
    },
}));

/**
 * Página Ejecutivos: estado y modales (asignación, registro, transferencias).
 * Evita meter @json/@js dentro de un objeto literal en el atributo HTML: rompe Alpine si hay comillas.
 */
Alpine.data('executivesPage', (initial = {}) => ({
    transferConfirmOpen: false,
    contactTransferOpen: Boolean(initial.contactTransferOpen),
    transferFromUserId: null,
    transferContactId: null,
    transferToUserId: initial.transferToUserId ?? '',
    selectedExecutiveId: initial.selectedExecutiveId ?? null,
    selectedExecutiveName: initial.selectedExecutiveName ?? '',
    pendingContactId: null,
    filterContactId: initial.filterContactId ?? null,
    autoAssignContactId: initial.autoAssignContactId ?? null,
    /** IDs de contactos en la página actual (vista Asignaciones) */
    assignmentPageContactIds: Array.isArray(initial.assignmentPageContactIds) ? initial.assignmentPageContactIds.map(Number) : [],
    /** Selección para asignación masiva a ejecutivo */
    selectedIds: [],
    bulkExportModalOpen: Boolean(initial.bulkExportModalOpen),
    bulkExportToUserId: initial.bulkExportToUserId ?? '',
    registerModalOpen: Boolean(initial.registerModalOpen),
    registerPasswordVisible: false,
    registerPasswordConfirmVisible: false,
    modalOpen: Boolean(initial.modalOpen),
    openContactTransfer(execId, contactId) {
        this.transferFromUserId = execId;
        this.transferContactId = contactId;
        this.transferToUserId = '';
        this.contactTransferOpen = true;
    },
    selectExecutive(id, name) {
        this.selectedExecutiveId = id;
        this.selectedExecutiveName = name;
    },
    closeModal() {
        this.modalOpen = false;
        this.selectedExecutiveId = null;
        this.selectedExecutiveName = '';
        this.pendingContactId = null;
    },
    openModalForContact(contactId) {
        this.pendingContactId = contactId;
        this.selectedExecutiveId = null;
        this.selectedExecutiveName = '';
        this.modalOpen = true;
    },
    resolvedContactId() {
        if (this.pendingContactId != null && this.pendingContactId !== '') {
            return this.pendingContactId;
        }
        if (this.filterContactId != null && this.filterContactId !== '') {
            return this.filterContactId;
        }
        if (this.autoAssignContactId != null && this.autoAssignContactId !== '') {
            return this.autoAssignContactId;
        }
        return null;
    },
    canConfirmAssign() {
        return this.selectedExecutiveId && this.resolvedContactId();
    },
    closeRegisterModal() {
        this.registerModalOpen = false;
        this.registerPasswordVisible = false;
        this.registerPasswordConfirmVisible = false;
    },
    selectAllOnPage(checked) {
        const pageIds = this.assignmentPageContactIds || [];
        if (pageIds.length === 0) {
            return;
        }
        if (checked) {
            this.selectedIds = [...new Set([...this.selectedIds, ...pageIds])];
        } else {
            const drop = new Set(pageIds);
            this.selectedIds = this.selectedIds.filter((id) => !drop.has(id));
        }
    },
    allOnPageSelected() {
        const pageIds = this.assignmentPageContactIds || [];
        return pageIds.length > 0 && pageIds.every((id) => this.selectedIds.includes(id));
    },
    someOnPageSelected() {
        const pageIds = this.assignmentPageContactIds || [];
        return pageIds.some((id) => this.selectedIds.includes(id));
    },
    toggleContactSelection(id) {
        const n = Number(id);
        const i = this.selectedIds.indexOf(n);
        if (i >= 0) {
            this.selectedIds.splice(i, 1);
        } else {
            this.selectedIds.push(n);
        }
    },
    isContactSelected(id) {
        return this.selectedIds.includes(Number(id));
    },
    openBulkExportModal() {
        this.bulkExportModalOpen = true;
        this.bulkExportToUserId = '';
    },
    closeBulkExportModal() {
        this.bulkExportModalOpen = false;
    },
}));

window.Alpine = Alpine;

Alpine.start();

// Mensajes de validación en español para campos tipo email (evita tooltips en inglés del navegador)
document.addEventListener('DOMContentLoaded', function() {
    document.body.addEventListener('click', function (e) {
        const markerBtn = e.target.closest('.crm-row-marker');
        if (markerBtn) {
            e.preventDefault();
            e.stopPropagation();
            const entity = markerBtn.getAttribute('data-crm-marker-entity');
            const id = markerBtn.getAttribute('data-crm-marker-id');
            if (!entity || id == null) {
                return;
            }
            const k = crmMarkerKey(entity, id);
            const all = crmLoadMarkers();
            const curRaw = all[k];
            const cur = CRM_MARKER_STATES.includes(curRaw) ? curRaw : 'none';
            const next = crmCycleMarkerState(cur);
            if (next === 'none') {
                delete all[k];
            } else {
                all[k] = next;
            }
            crmSaveMarkers(all);
            crmApplyMarkerButton(markerBtn);
            return;
        }

        const btn = e.target.closest('[data-crm-back]');
        if (!btn || btn.tagName !== 'BUTTON') {
            return;
        }
        e.preventDefault();
        const fallback = btn.getAttribute('data-crm-back') || '/';
        // Preferir ?return= (encadenación CRM) antes que history.back(): si no, tras guardar
        // desde editar, volver iría otra vez al formulario de edición.
        const preferred = btn.getAttribute('data-crm-preferred-return');
        if (preferred) {
            window.location.assign(preferred);
            return;
        }
        if (window.history.length > 1) {
            window.history.back();
            return;
        }
        window.location.href = fallback;
    });

    crmInitRowMarkers(document);

    document.querySelectorAll('form input[type="email"]').forEach(function(input) {
        input.addEventListener('invalid', function() {
            if (this.validity.valueMissing) {
                this.setCustomValidity('El correo electrónico es obligatorio.');
            } else {
                this.setCustomValidity('El correo electrónico debe tener un formato válido.');
            }
        });
        input.addEventListener('input', function() {
            this.setCustomValidity('');
        });
        input.addEventListener('blur', function() {
            this.setCustomValidity('');
        });
    });
});

// Función global para mostrar alertas (modal flotante centrado)
window.showAlert = function(type, message, duration = 0) {
    const alertContainer = document.getElementById('alert-container') || createAlertContainer();
    const alertId = 'alert-' + Date.now();
    const alertData = getAlertData(type);
    
    const alertHtml = `
        <div id="${alertId}"
             x-data="{ show: true }"
             x-show="show"
             x-cloak
             x-transition:enter="ease-out duration-300"
             x-transition:enter-start="opacity-0"
             x-transition:enter-end="opacity-100"
             x-transition:leave="ease-in duration-200"
             x-transition:leave-start="opacity-100"
             x-transition:leave-end="opacity-0"
             class="fixed inset-0 z-[60] flex items-center justify-center p-4 bg-gray-900/50 backdrop-blur-sm"
             @keydown.escape.window="show = false; setTimeout(() => document.getElementById('${alertId}')?.remove(), 200)"
             role="alertdialog"
             aria-modal="true"
             style="display: none;">
            <div x-show="show"
                 x-transition:enter="ease-out duration-300"
                 x-transition:enter-start="opacity-0 scale-95"
                 x-transition:enter-end="opacity-100 scale-100"
                 x-transition:leave="ease-in duration-200"
                 x-transition:leave-start="opacity-100 scale-100"
                 x-transition:leave-end="opacity-0 scale-95"
                 class="w-full max-w-md bg-[#1a3d6b] rounded-2xl shadow-xl p-8 text-center border-4 border-[#FFE600]"
                 @click.outside="show = false; setTimeout(() => document.getElementById('${alertId}')?.remove(), 200)">
                <div class="mx-auto w-16 h-16 rounded-full ${alertData.iconBg} flex items-center justify-center shadow-lg mb-6">
                    <svg class="w-9 h-9 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="${alertData.icon}" />
                    </svg>
                </div>
                <h3 class="text-xl font-bold text-white mb-2">${alertData.title}</h3>
                <p class="text-white/90 text-sm mb-6">${escapeHtml(message)}</p>
                <button type="button"
                        @click="show = false; setTimeout(() => document.getElementById('${alertId}')?.remove(), 200)"
                        class="w-full py-3 px-4 rounded-xl font-semibold text-gray-900 bg-[#FFE600] hover:bg-[#E6CF00] focus:outline-none focus:ring-2 focus:ring-[#FFE600] focus:ring-offset-2 focus:ring-offset-[#1a3d6b] transition-colors">
                    Aceptar
                </button>
            </div>
        </div>
    `;
    
    alertContainer.insertAdjacentHTML('beforeend', alertHtml);
    
    const alertElement = document.getElementById(alertId);
    if (alertElement && window.Alpine) {
        window.Alpine.initTree(alertElement);
        alertElement.style.display = 'block';
    }
};

function createAlertContainer() {
    const container = document.createElement('div');
    container.id = 'alert-container';
    document.body.appendChild(container);
    return container;
}

function getAlertData(type) {
    const types = {
        'success': { title: 'Éxito', icon: 'M5 13l4 4L19 7', iconBg: 'bg-emerald-500' },
        'error': { title: 'Error', icon: 'M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z', iconBg: 'bg-red-500' },
        'warning': { title: 'Advertencia', icon: 'M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z', iconBg: 'bg-amber-500' },
        'info': { title: 'Información', icon: 'M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z', iconBg: 'bg-blue-500' },
    };
    return types[type] || types['info'];
}

function escapeHtml(text) {
    const div = document.createElement('div');
    div.textContent = text;
    return div.innerHTML;
}
