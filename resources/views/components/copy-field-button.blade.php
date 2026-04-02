{{-- Copia al portapapeles el texto del select o input indicado por target-id --}}
@props([
    'targetId' => 'company_id',
    'label' => 'Copiar',
])
<button
    type="button"
    class="inline-flex items-center gap-1.5 shrink-0 text-sm font-medium text-gray-600 hover:text-gray-900 border border-gray-300 rounded-lg px-2.5 py-1 bg-white hover:bg-gray-50 transition-colors focus:outline-none focus:ring-2 focus:ring-[#FFE600]/60 dark:border-white/25 dark:text-white/85 dark:bg-white/10 dark:hover:bg-white/15 dark:hover:text-white"
    title="Copiar al portapapeles"
    data-copy-target="{{ $targetId }}"
    onclick="window.crmCopyFieldValue(this)"
>
    <svg class="w-4 h-4 shrink-0 opacity-80" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z" />
    </svg>
    <span class="copy-field-btn__label">{{ $label }}</span>
</button>
@once
<script>
(function() {
    if (window.crmCopyFieldValue) return;
    window.crmCopyFieldValue = function(btn) {
        var id = btn.getAttribute('data-copy-target');
        var el = id ? document.getElementById(id) : null;
        if (!el) return;
        var text = el.tagName === 'SELECT'
            ? (el.options[el.selectedIndex] ? el.options[el.selectedIndex].text : '')
            : (el.value || '');
        text = String(text).trim();
        if (!text) return;
        var labelEl = btn.querySelector('.copy-field-btn__label');
        var prev = labelEl ? labelEl.textContent : '';
        var done = function() {
            if (labelEl) {
                labelEl.textContent = 'Copiado';
                setTimeout(function() { labelEl.textContent = prev; }, 1600);
            }
        };
        if (navigator.clipboard && navigator.clipboard.writeText) {
            navigator.clipboard.writeText(text).then(done).catch(function() {
                var ta = document.createElement('textarea');
                ta.value = text;
                ta.style.position = 'fixed';
                ta.style.left = '-9999px';
                document.body.appendChild(ta);
                ta.select();
                try { document.execCommand('copy'); } catch (e) {}
                document.body.removeChild(ta);
                done();
            });
        } else {
            var ta = document.createElement('textarea');
            ta.value = text;
            ta.style.position = 'fixed';
            ta.style.left = '-9999px';
            document.body.appendChild(ta);
            ta.select();
            try { document.execCommand('copy'); } catch (e) {}
            document.body.removeChild(ta);
            done();
        }
    };
})();
</script>
@endonce
