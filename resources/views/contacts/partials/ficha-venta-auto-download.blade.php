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
                n.innerHTML = '<p class="font-semibold mb-2 text-[#FFE600]">Ficha generada</p><p class="text-white/90 mb-3">Si no se abrió la descarga, permite ventanas emergentes o usa el enlace.</p><a class="inline-flex font-semibold text-[#FFE600] underline hover:text-yellow-300" href="' + url + '" target="_blank" rel="noopener noreferrer">Descargar PDF de la ficha</a>';
                document.body.appendChild(n);
            }
        })();
    </script>
@endif
