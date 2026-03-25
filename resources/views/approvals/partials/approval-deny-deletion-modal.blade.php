{{-- Modal: motivo obligatorio al denegar una solicitud de eliminación --}}
<div id="approval-deny-deletion-modal"
     class="hidden fixed inset-0 z-[310] flex items-center justify-center p-4 bg-black/70 backdrop-blur-sm"
     role="dialog"
     aria-modal="true"
     aria-labelledby="approval-deny-deletion-modal-title">
    <div class="w-full max-w-lg rounded-2xl border-4 border-[#FFE600] bg-[#1a3d6b] shadow-xl p-6 text-left [color-scheme:dark]"
         onclick="event.stopPropagation()">
        <h3 id="approval-deny-deletion-modal-title" class="text-lg font-bold text-[#FFE600] mb-2 text-center">Denegar eliminación</h3>
        <p class="text-sm text-white/85 mb-4 text-center">
            Indique al usuario por qué no se acepta la eliminación. Este texto se mostrará en su panel y en notificaciones.
        </p>
        <label for="approval-deny-deletion-nota" class="block text-sm font-medium text-white/90 mb-1">Motivo (obligatorio)</label>
        <textarea id="approval-deny-deletion-nota" rows="4" required maxlength="2000"
            class="w-full rounded-xl border border-white/25 bg-white/10 text-white placeholder-white/45 px-3 py-2 text-sm focus:ring-2 focus:ring-[#FFE600] focus:border-[#FFE600]"
            placeholder="Ej.: La empresa tiene seguimientos activos que deben cerrarse antes…"></textarea>
        <p id="approval-deny-deletion-error" class="text-red-300 text-sm mt-2 hidden" role="alert"></p>
        <div class="flex flex-col-reverse sm:flex-row gap-2 sm:justify-end mt-6">
            <button type="button" id="approval-deny-deletion-cancel"
                class="px-4 py-2.5 rounded-xl border border-white/40 text-white text-sm font-medium hover:bg-white/10 w-full sm:w-auto">
                Cancelar
            </button>
            <button type="button" id="approval-deny-deletion-ok"
                class="px-4 py-2.5 rounded-xl font-semibold text-sm w-full sm:w-auto bg-red-600 text-white hover:bg-red-500">
                Enviar y denegar
            </button>
        </div>
    </div>
</div>

<script>
(function () {
    if (window.__approvalDenyDeletionModalInit) return;
    window.__approvalDenyDeletionModalInit = true;

    var modal = document.getElementById('approval-deny-deletion-modal');
    var textarea = document.getElementById('approval-deny-deletion-nota');
    var btnOk = document.getElementById('approval-deny-deletion-ok');
    var btnCancel = document.getElementById('approval-deny-deletion-cancel');
    var errEl = document.getElementById('approval-deny-deletion-error');
    if (!modal || !textarea || !btnOk || !btnCancel) return;

    var pendingForm = null;

    function closeModal() {
        modal.classList.add('hidden');
        document.body.style.overflow = '';
        pendingForm = null;
        textarea.value = '';
        if (errEl) {
            errEl.classList.add('hidden');
            errEl.textContent = '';
        }
    }

    function openModal(form) {
        pendingForm = form;
        textarea.value = '';
        if (errEl) {
            errEl.classList.add('hidden');
            errEl.textContent = '';
        }
        modal.classList.remove('hidden');
        document.body.style.overflow = 'hidden';
        textarea.focus();
    }

    document.addEventListener('click', function (e) {
        var trigger = e.target.closest('.js-deny-deletion-trigger');
        if (!trigger) return;
        e.preventDefault();
        var id = trigger.getAttribute('data-form-id');
        var form = id ? document.getElementById(id) : null;
        if (!form) return;
        openModal(form);
    });

    btnCancel.addEventListener('click', closeModal);
    modal.addEventListener('click', closeModal);
    document.addEventListener('keydown', function (e) {
        if (e.key === 'Escape' && !modal.classList.contains('hidden')) closeModal();
    });

    btnOk.addEventListener('click', function () {
        var note = (textarea.value || '').trim();
        if (!note) {
            if (errEl) {
                errEl.textContent = 'Escriba el motivo antes de continuar.';
                errEl.classList.remove('hidden');
            }
            return;
        }
        if (!pendingForm) return;

        var existing = pendingForm.querySelector('input[name="nota_admin"][data-injected-deny-note="1"]');
        if (existing) existing.remove();

        var input = document.createElement('input');
        input.type = 'hidden';
        input.name = 'nota_admin';
        input.value = note;
        input.setAttribute('data-injected-deny-note', '1');
        pendingForm.appendChild(input);

        var f = pendingForm;
        closeModal();
        f.submit();
    });
})();
</script>
