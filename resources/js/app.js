import './bootstrap';

import Alpine from 'alpinejs';

window.Alpine = Alpine;

Alpine.start();

// Función global para mostrar alertas
window.showAlert = function(type, message, duration = 7000) {
    const alertContainer = document.getElementById('alert-container') || createAlertContainer();
    
    const alertId = 'alert-' + Date.now();
    const alertData = getAlertData(type);
    
    const alertHtml = `
        <div 
            id="${alertId}"
            x-data="{ show: true }"
            x-show="show"
            x-transition:enter="transition ease-out duration-300"
            x-transition:enter-start="opacity-0 transform translate-y-2"
            x-transition:enter-end="opacity-100 transform translate-y-0"
            x-transition:leave="transition ease-in duration-200"
            x-transition:leave-start="opacity-100 transform translate-y-0"
            x-transition:leave-end="opacity-0 transform translate-y-2"
            x-init="setTimeout(() => show = false, ${duration}); setTimeout(() => document.getElementById('${alertId}')?.remove(), ${duration + 200})"
            class="fixed top-4 right-4 z-50 max-w-md w-full mx-4"
            style="display: none;"
        >
            <div class="rounded-xl ${alertData.bg} border-2 ${alertData.border} shadow-2xl backdrop-blur-sm"
                 style="box-shadow: 0 10px 25px -5px rgba(0, 0, 0, 0.3), 0 0 0 1px rgba(230, 184, 0, 0.2), 0 0 20px rgba(230, 184, 0, 0.1);">
                <div class="px-4 py-3 flex items-start gap-3">
                    <div class="flex-shrink-0 mt-0.5">
                        <svg class="w-5 h-5 ${alertData.iconColor}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="${alertData.icon}" />
                        </svg>
                    </div>
                    <div class="flex-1 ${alertData.text} text-sm font-medium">
                        ${escapeHtml(message)}
                    </div>
                    <button 
                        @click="show = false; setTimeout(() => document.getElementById('${alertId}')?.remove(), 200)"
                        class="flex-shrink-0 text-gray-400 hover:text-white transition-colors"
                    >
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>
                </div>
            </div>
        </div>
    `;
    
    alertContainer.insertAdjacentHTML('beforeend', alertHtml);
    
    // Inicializar Alpine para el nuevo elemento
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
    const typeClasses = {
        'success': {
            'bg': 'bg-green-900/20',
            'border': 'border-green-500/40',
            'text': 'text-green-200',
            'icon': 'M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z',
            'iconColor': 'text-green-400'
        },
        'error': {
            'bg': 'bg-red-900/20',
            'border': 'border-red-500/40',
            'text': 'text-red-200',
            'icon': 'M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z',
            'iconColor': 'text-red-400'
        },
        'warning': {
            'bg': 'bg-yellow-900/20',
            'border': 'border-yellow-500/40',
            'text': 'text-yellow-200',
            'icon': 'M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z',
            'iconColor': 'text-yellow-400'
        },
        'info': {
            'bg': 'bg-blue-900/20',
            'border': 'border-blue-500/40',
            'text': 'text-blue-200',
            'icon': 'M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z',
            'iconColor': 'text-blue-400'
        },
    };
    
    return typeClasses[type] || typeClasses['success'];
}

function escapeHtml(text) {
    const div = document.createElement('div');
    div.textContent = text;
    return div.innerHTML;
}
