import './bootstrap';

import Alpine from 'alpinejs';

window.Alpine = Alpine;

Alpine.start();

// Mensajes de validación en español para campos tipo email (evita tooltips en inglés del navegador)
document.addEventListener('DOMContentLoaded', function() {
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
