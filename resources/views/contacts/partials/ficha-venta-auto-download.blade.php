@if(session('download_ficha_sale_id'))
    @php
        $fichaSaleId = (int) session('download_ficha_sale_id');
    @endphp
    <script>
        (function () {
            var url = @json(route('user.sales.ficha-pdf', ['sale' => $fichaSaleId]));
            var w = window.open(url, '_blank', 'noopener,noreferrer');
            if (!w || w.closed || typeof w.closed === 'undefined') {
                var n = document.createElement('div');
                n.className = 'fixed bottom-4 right-4 z-[300] max-w-sm rounded-xl border border-[#FFE600]/40 bg-[#071A3D] text-white p-4 shadow-xl text-sm';
                n.setAttribute('role', 'status');
                n.innerHTML = ''
                    + '<div class="flex items-start justify-between gap-2 mb-2">'
                    + '<p class="font-semibold text-[#FFE600] leading-tight pr-1">Ficha generada</p>'
                    + '<button type="button" class="js-ficha-toast-close shrink-0 flex h-8 w-8 items-center justify-center rounded-lg border border-[#FFE600]/60 text-[#FFE600] text-xl leading-none hover:bg-[#FFE600]/15 focus:outline-none focus:ring-2 focus:ring-[#FFE600]/45" aria-label="Cerrar aviso">&times;</button>'
                    + '</div>'
                    + '<p class="text-white/90 mb-3">Si no se abrió la descarga, permite ventanas emergentes o usa el enlace.</p>'
                    + '<a class="inline-flex font-semibold text-[#FFE600] underline hover:text-yellow-300" href="' + url + '" target="_blank" rel="noopener noreferrer">Descargar PDF de la ficha</a>';
                document.body.appendChild(n);
                var closeBtn = n.querySelector('.js-ficha-toast-close');
                if (closeBtn) {
                    closeBtn.addEventListener('click', function () {
                        n.remove();
                    });
                }
            }
        })();
    </script>
@endif
