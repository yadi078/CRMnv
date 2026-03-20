{{-- Modal de confirmación para acciones de aprobación (mismo estilo CRM) --}}
<div id="approval-confirm-modal"
     class="hidden fixed inset-0 z-[300] flex items-center justify-center p-4 bg-black/70 backdrop-blur-sm"
     role="dialog"
     aria-modal="true"
     aria-labelledby="approval-confirm-modal-title">
    <div class="w-full max-w-md rounded-2xl border-4 border-[#FFE600] bg-[#1a3d6b] shadow-xl p-6 text-center [color-scheme:dark]"
         onclick="event.stopPropagation()">
        <div class="mx-auto w-14 h-14 rounded-full bg-[#FFE600]/15 text-[#FFE600] flex items-center justify-center mb-4" aria-hidden="true">
            <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
            </svg>
        </div>
        <h3 id="approval-confirm-modal-title" class="text-lg font-bold text-[#FFE600] mb-2"></h3>
        <p id="approval-confirm-modal-message" class="text-sm text-white/90 mb-6"></p>
        <div class="flex flex-col-reverse sm:flex-row gap-2 sm:justify-end">
            <button type="button" id="approval-confirm-modal-cancel"
                class="px-4 py-2.5 rounded-xl border border-white/40 text-white text-sm font-medium hover:bg-white/10 w-full sm:w-auto">
                Cancelar
            </button>
            <button type="button" id="approval-confirm-modal-ok"
                class="px-4 py-2.5 rounded-xl font-semibold text-sm w-full sm:w-auto">
                Confirmar
            </button>
        </div>
    </div>
</div>

<script>
(function () {
    if (window.__approvalConfirmModalInit) return;
    window.__approvalConfirmModalInit = true;

    var modal = document.getElementById('approval-confirm-modal');
    var titleEl = document.getElementById('approval-confirm-modal-title');
    var messageEl = document.getElementById('approval-confirm-modal-message');
    var btnOk = document.getElementById('approval-confirm-modal-ok');
    var btnCancel = document.getElementById('approval-confirm-modal-cancel');
    if (!modal || !titleEl || !messageEl || !btnOk || !btnCancel) return;

    var pendingForm = null;
    var baseOkClass = 'px-4 py-2.5 rounded-xl font-semibold text-sm w-full sm:w-auto';

    function closeModal() {
        modal.classList.add('hidden');
        document.body.style.overflow = '';
        pendingForm = null;
    }

    function openModal(form, trigger) {
        pendingForm = form;
        titleEl.textContent = trigger.getAttribute('data-title') || 'Confirmar';
        messageEl.textContent = trigger.getAttribute('data-message') || '¿Desea continuar?';
        btnOk.textContent = trigger.getAttribute('data-confirm-text') || 'Confirmar';
        var variant = trigger.getAttribute('data-variant') || 'danger';
        btnOk.className = baseOkClass + (variant === 'amber'
            ? ' bg-[#FFE600] text-[#003366] hover:bg-[#e6cf00]'
            : ' bg-red-600 text-white hover:bg-red-500');
        modal.classList.remove('hidden');
        document.body.style.overflow = 'hidden';
        btnOk.focus();
    }

    document.addEventListener('click', function (e) {
        var trigger = e.target.closest('.js-approval-confirm-trigger');
        if (!trigger) return;
        e.preventDefault();
        var id = trigger.getAttribute('data-form-id');
        var form = id ? document.getElementById(id) : null;
        if (!form) return;
        openModal(form, trigger);
    });

    btnCancel.addEventListener('click', closeModal);
    modal.addEventListener('click', closeModal);
    document.addEventListener('keydown', function (e) {
        if (e.key === 'Escape' && !modal.classList.contains('hidden')) closeModal();
    });

    btnOk.addEventListener('click', function () {
        if (pendingForm) {
            var f = pendingForm;
            closeModal();
            f.submit();
        }
    });
})();
</script>
