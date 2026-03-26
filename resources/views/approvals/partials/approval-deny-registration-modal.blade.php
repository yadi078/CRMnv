{{-- Modal: motivo opcional al denegar una solicitud de alta (empresa/contacto) --}}
<div id="approval-deny-registration-modal"
     class="fixed inset-0 z-[70] hidden items-center justify-center bg-black/70 backdrop-blur-[2px] p-4"
     role="dialog"
     aria-modal="true"
     aria-labelledby="approval-deny-registration-modal-title">
    <div class="w-full max-w-lg rounded-2xl border border-[#FFE600]/35 bg-[#0C2D52] p-5 shadow-2xl">
        <h3 id="approval-deny-registration-modal-title" class="text-lg font-bold text-[#FFE600] mb-2 text-center">Denegar solicitud</h3>
        <p class="text-sm text-white/85 text-center mb-4">
            Puedes agregar un motivo para registrar por qué se rechaza esta solicitud.
        </p>
        <label for="approval-deny-registration-motivo" class="block text-sm font-medium text-white/90 mb-1">Motivo (opcional)</label>
        <textarea id="approval-deny-registration-motivo" rows="4" maxlength="2000"
                  class="w-full rounded-xl border border-[#FFE600] placeholder-white/60 px-3 py-2.5 focus:outline-none focus:ring-2 focus:ring-[#FFE600]/40"
                  style="background-color:#183A60;color:#FFFFFF;"
                  placeholder="Escribe el motivo de rechazo (opcional)..."></textarea>
        <div class="mt-4 flex items-center justify-end gap-2">
            <button type="button" id="approval-deny-registration-cancel"
                    class="px-4 py-2 rounded-xl border border-white/30 text-white hover:bg-white/10">
                Cancelar
            </button>
            <button type="button" id="approval-deny-registration-ok"
                    class="px-4 py-2 rounded-xl bg-red-600 text-white font-semibold hover:bg-red-500">
                Denegar
            </button>
        </div>
    </div>
</div>

<script>
(function () {
    var modal = document.getElementById('approval-deny-registration-modal');
    var textarea = document.getElementById('approval-deny-registration-motivo');
    var btnOk = document.getElementById('approval-deny-registration-ok');
    var btnCancel = document.getElementById('approval-deny-registration-cancel');
    if (!modal || !textarea || !btnOk || !btnCancel) return;

    var pendingForm = null;

    function openModal(form) {
        pendingForm = form;
        textarea.value = '';
        modal.classList.remove('hidden');
        modal.classList.add('flex');
        textarea.focus();
    }

    function closeModal() {
        modal.classList.add('hidden');
        modal.classList.remove('flex');
        pendingForm = null;
    }

    document.addEventListener('click', function (e) {
        var trigger = e.target.closest('.js-deny-registration-trigger');
        if (trigger) {
            e.preventDefault();
            var formId = trigger.getAttribute('data-form-id');
            var form = formId ? document.getElementById(formId) : trigger.closest('form');
            if (form) openModal(form);
            return;
        }

        if (e.target === modal) {
            closeModal();
        }
    });

    btnCancel.addEventListener('click', closeModal);

    btnOk.addEventListener('click', function () {
        if (!pendingForm) return;
        var existing = pendingForm.querySelector('input[name="motivo"][data-injected-deny-motivo="1"]');
        if (existing) existing.remove();
        var input = document.createElement('input');
        input.type = 'hidden';
        input.name = 'motivo';
        input.value = textarea.value || '';
        input.setAttribute('data-injected-deny-motivo', '1');
        pendingForm.appendChild(input);
        pendingForm.submit();
    });
})();
</script>
