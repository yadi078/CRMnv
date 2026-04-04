<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1, viewport-fit=cover">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>CE CRM - {{ config('app.name', 'Laravel') }}</title>

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=inter:400,500,600,700&display=swap" rel="stylesheet" />

        <!-- Scripts -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="font-sans antialiased leading-normal">
        @auth
            @php($crmHeaderUnreadNotif = auth()->user()->unreadNotificationsCount())
        @endauth
        <div class="sidebar-layout sidebar-layout--expanded" x-data="{ mobileMenuOpen: false }">
            {{-- Barra superior móvil/tablet: logo + notificaciones + menú hamburguesa (solo < lg) --}}
            <header class="mobile-header lg:hidden fixed top-0 left-0 right-0 z-30 flex items-center justify-between min-h-touch px-4 bg-[#000836] shadow-lg safe-area-inset">
                <a href="{{ auth()->user()->esAdmin() ? route('dashboard') : route('user.dashboard') }}" class="flex items-center gap-2 shrink-0" aria-label="Ir al inicio">
                    <img src="{{ asset('img/logo-empresa.png') }}" onerror="this.onerror=null; this.src='{{ asset('img/logo.png') }}';" alt="CE" class="w-9 h-9 rounded-full object-cover border-2 border-[#FFE600]" />
                    <span class="font-semibold text-white text-fluid-lg">CE CRM</span>
                </a>
                <div class="flex items-center gap-1">
                    <a href="{{ route('notifications.index') }}" class="relative flex items-center justify-center min-w-[44px] min-h-[44px] rounded-xl text-[#FFE600] hover:bg-white/10 transition-colors" aria-label="Notificaciones">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/></svg>
                        <span class="js-header-notification-badge-wrap absolute -top-0.5 -right-0.5 flex items-center justify-center min-w-[1.25rem] h-5 px-1 rounded-full text-xs font-bold bg-red-500 text-white" style="{{ ($crmHeaderUnreadNotif ?? 0) > 0 ? '' : 'display: none;' }}">
                            <span class="js-header-notification-badge">{{ ($crmHeaderUnreadNotif ?? 0) > 0 ? min($crmHeaderUnreadNotif, 99) . ($crmHeaderUnreadNotif > 99 ? '+' : '') : '' }}</span>
                        </span>
                    </a>
                    <button type="button" @click="mobileMenuOpen = true" class="flex items-center justify-center min-w-[44px] min-h-[44px] rounded-xl text-white hover:bg-white/10 focus:outline-none focus:ring-2 focus:ring-[#FFE600] focus:ring-offset-2 focus:ring-offset-[#000836] transition-colors" aria-label="Abrir menú">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/></svg>
                    </button>
                </div>
            </header>

            {{-- Overlay cuando el drawer está abierto (solo móvil/tablet) --}}
            <div x-show="mobileMenuOpen" x-cloak x-transition:enter="ease-out duration-200" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100" x-transition:leave="ease-in duration-150" x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0" class="fixed inset-0 bg-black/60 z-40 lg:hidden backdrop-blur-sm" @click="mobileMenuOpen = false" aria-hidden="true"></div>

            {{-- Sidebar: drawer en móvil/tablet, fijo en desktop. Cerrar al hacer clic en un enlace --}}
            <div class="sidebar-drawer" :class="mobileMenuOpen ? 'sidebar-drawer--open' : ''" @click="if ($event.target.closest('a')) mobileMenuOpen = false">
                @if(auth()->user()->esAdmin())
                    <x-sidebar-nav />
                @else
                    <x-sidebar-nav-user />
                @endif
                <button type="button" @click="mobileMenuOpen = false" class="lg:hidden absolute top-4 right-4 flex items-center justify-center w-11 h-11 rounded-xl text-white hover:bg-white/10 focus:outline-none focus:ring-2 focus:ring-[#FFE600]" aria-label="Cerrar menú">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                </button>
            </div>

            {{-- Área principal: fondo blanco/gris muy claro como en diseño CE CRM --}}
            <div class="sidebar-layout__main bg-white">
                {{-- Encabezado: recuadro único (page-header-card) con título + icono notificaciones DENTRO --}}
                @isset($header)
                    <div class="sidebar-layout__header px-4 sm:px-6 py-5 border-b border-[#1a3d6b]/40">
                        <div class="max-w-7xl mx-auto min-w-0 w-full">
                            <div class="page-header-card flex justify-between items-center gap-4">
                                <div class="flex items-center gap-4 min-w-0 flex-1">
                                    {{ $header }}
                                </div>
                                <div class="flex items-center gap-2 flex-shrink-0">
                                    @unless(
                                        request()->routeIs('dashboard') ||
                                        request()->routeIs('user.dashboard') ||
                                        request()->routeIs('companies.index') ||
                                        request()->routeIs('filtros.index') ||
                                        request()->routeIs('contacts.index') ||
                                        request()->routeIs('executives.index') ||
                                        request()->routeIs('follow-ups.index') ||
                                        request()->routeIs('user.sales.index') ||
                                        request()->routeIs('user.sales.by-company') ||
                                        request()->routeIs('user.sales.by-contact') ||
                                        request()->routeIs('approvals.*') ||
                                        request()->routeIs('notifications.index') ||
                                        request()->routeIs('data-management.index') ||
                                        request()->routeIs('profile.edit')
                                    )
                                        <x-crm-back-button />
                                    @endunless
                                    <a href="{{ route('notifications.index') }}" class="relative flex items-center justify-center w-11 h-11 rounded-xl text-[#FFE600] hover:bg-white/10 transition-colors" aria-label="Notificaciones">
                                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/></svg>
                                    <span class="js-header-notification-badge-wrap absolute top-0 right-0 flex items-center justify-center min-w-[1.25rem] h-5 px-1 rounded-full text-xs font-bold bg-red-500 text-white" style="{{ ($crmHeaderUnreadNotif ?? 0) > 0 ? '' : 'display: none;' }}">
                                        <span class="js-header-notification-badge">{{ ($crmHeaderUnreadNotif ?? 0) > 0 ? min($crmHeaderUnreadNotif, 99) . ($crmHeaderUnreadNotif > 99 ? '+' : '') : '' }}</span>
                                    </span>
                                </a>
                                </div>
                            </div>
                        </div>
                    </div>
                @endisset

                {{-- Mensajes flash: en Ejecutivos el listado muestra avisos inline (evita solaparse con modales z-50+). --}}
                @unless(request()->routeIs('executives.index'))
                    @if(session('import_flash'))
                        <x-alert
                            type="success"
                            :message="session('import_flash')['message']"
                            :secondaryUrl="!empty(session('import_flash')['rejected_token']) ? route('companies.import.rejected', ['token' => session('import_flash')['rejected_token']]) : null"
                            secondaryLabel="Descargar Excel con registros rechazados"
                        />
                    @endif
                    @if(session('success'))
                        <x-alert type="success" :message="session('success')" />
                    @endif
                    @if(session('warning'))
                        <x-alert type="warning" :message="session('warning')" />
                    @endif
                    @if(session('info'))
                        <x-alert type="info" :message="session('info')" />
                    @endif
                    @if(session('error'))
                        <x-alert type="error" :message="session('error')" />
                    @endif
                    @if(session('status'))
                        <x-alert type="success" :message="match (session('status')) {
                            'profile-updated' => 'Perfil actualizado correctamente.',
                            'profile-photo-removed' => 'Foto de perfil eliminada.',
                            'password-updated' => 'Contraseña actualizada correctamente.',
                            'verification-link-sent' => 'Se ha enviado un nuevo enlace de verificación a tu correo.',
                            default => session('status'),
                        }" />
                    @endif
                @endunless

                {{-- Contenido: padding-top en móvil para no quedar bajo la barra fija --}}
                <main class="flex-1 p-3 xs:p-4 sm:p-6 md:p-8 pt-[calc(2.75rem+1rem)] lg:pt-8 min-w-0 overflow-x-hidden">
                    <div class="max-w-7xl mx-auto w-full min-w-0">
                        {{ $slot }}
                    </div>
                </main>

                {{-- Pie al final del contenido (scroll); no fijo al viewport --}}
                <footer class="institutional-footer" role="contentinfo" aria-label="Slogan corporativo">
                    <div class="institutional-footer__wrap">
                        <p class="institutional-footer__slogan">INVERTIR EN VALOR ¡ATRAE VALOR!</p>
                    </div>
                </footer>
            </div>
        </div>

        @stack('scripts')
        @auth
        <script>
        (function() {
            var url = '{{ route("notifications.unread-count") }}';
            var crmReminderSnoozeZero = @json(route('reminders.snooze', ['reminder' => 0]));
            var crmReminderEditZero = @json(route('reminders.edit', ['reminder' => 0]));
            var crmReminderConfirmZero = @json(route('reminders.confirm-alarm', ['reminder' => 0]));
            function crmReminderSnoozeUrl(reminderId) {
                return String(crmReminderSnoozeZero).replace(/\/0(\/snooze)/, '/' + encodeURIComponent(reminderId) + '$1');
            }
            function crmReminderEditUrl(reminderId) {
                return String(crmReminderEditZero).replace(/\/0(\/edit)/, '/' + encodeURIComponent(reminderId) + '$1');
            }
            function crmReminderConfirmUrl(reminderId) {
                return String(crmReminderConfirmZero).replace(/\/0(\/confirm-alarm)/, '/' + encodeURIComponent(reminderId) + '$1');
            }
            var reminderVistoAckIds = {};
            var lastReminderRingByNotifId = {};
            var REMINDER_ALARM_REPEAT_MS = 23000;
            var audioUnlocked = false;
            var alarmCtx = null;
            var vistoAckStorageKey = 'crm_reminder_visto_ack_ids_v1';

            try {
                var rawVisto = sessionStorage.getItem(vistoAckStorageKey);
                if (rawVisto) {
                    var parsedVisto = JSON.parse(rawVisto);
                    if (parsedVisto && typeof parsedVisto === 'object') {
                        reminderVistoAckIds = parsedVisto;
                    }
                }
            } catch (e) {}

            function persistReminderVistoAck() {
                try {
                    sessionStorage.setItem(vistoAckStorageKey, JSON.stringify(reminderVistoAckIds));
                } catch (e) {}
            }

            function ackReminderVistoLocally(notificationId) {
                var nid = String(notificationId || '');
                if (!nid) {
                    return;
                }
                reminderVistoAckIds[nid] = true;
                delete lastReminderRingByNotifId[nid];
                persistReminderVistoAck();
            }

            function escapeHtml(text) {
                var div = document.createElement('div');
                div.textContent = text == null ? '' : String(text);
                return div.innerHTML;
            }

            function extractHourLabel(rawTime) {
                var t = String(rawTime || '');
                var match = t.match(/(\d{1,2}:\d{2})/);
                if (match && match[1]) {
                    return match[1];
                }
                return t || 'Ahora';
            }

            function unlockAudio() {
                try {
                    var Ctx = window.AudioContext || window.webkitAudioContext;
                    if (!Ctx) {
                        return;
                    }
                    if (!alarmCtx) {
                        alarmCtx = new Ctx();
                    }
                    if (alarmCtx.state === 'suspended') {
                        alarmCtx.resume();
                    }
                    audioUnlocked = true;
                } catch (e) {}
            }

            function playReminderAlarm() {
                try {
                    if (!audioUnlocked) {
                        unlockAudio();
                    }
                    if (!alarmCtx) {
                        return;
                    }
                    if (alarmCtx.state === 'suspended') {
                        alarmCtx.resume();
                    }

                    function ring(startAt) {
                        [0, 0.24, 0.48, 0.72, 0.96].forEach(function(offset, idx) {
                            var osc = alarmCtx.createOscillator();
                            var gain = alarmCtx.createGain();
                            osc.type = 'sawtooth';
                            osc.frequency.setValueAtTime(idx % 2 === 0 ? 980 : 780, startAt + offset);
                            gain.gain.setValueAtTime(0.0001, startAt + offset);
                            gain.gain.exponentialRampToValueAtTime(0.85, startAt + offset + 0.012);
                            gain.gain.exponentialRampToValueAtTime(0.0001, startAt + offset + 0.21);
                            osc.connect(gain);
                            gain.connect(alarmCtx.destination);
                            osc.start(startAt + offset);
                            osc.stop(startAt + offset + 0.22);
                        });
                    }

                    var now = alarmCtx.currentTime;
                    ring(now);
                    ring(now + 1.25);
                } catch (e) {}
            }

            function maybeRequestBrowserNotificationPermission() {
                try {
                    if (!('Notification' in window)) {
                        return;
                    }
                    if (Notification.permission !== 'default') {
                        return;
                    }
                    if (window.isSecureContext !== true) {
                        return;
                    }
                    if (localStorage.getItem('crm-notif-permission-requested') === '1') {
                        return;
                    }
                    localStorage.setItem('crm-notif-permission-requested', '1');
                    Notification.requestPermission().catch(function() {});
                } catch (e) {}
            }

            function showBrowserReminderNotification(alertData) {
                try {
                    if (!('Notification' in window)) {
                        return;
                    }
                    if (Notification.permission !== 'granted') {
                        return;
                    }
                    if (window.isSecureContext !== true) {
                        return;
                    }
                    new Notification('Recordatorio', {
                        body: (alertData.title || 'Tienes un recordatorio') + (alertData.time ? ('\nHora: ' + alertData.time) : ''),
                        tag: 'crm-reminder-' + String(alertData.id || ''),
                        renotify: true,
                    });
                } catch (e) {}
            }

            function closeReminderDetailModal() {
                var modal = document.getElementById('crm-reminder-detail-modal');
                if (modal) {
                    modal.remove();
                }
                document.body.style.overflow = '';
            }

            function markReminderAsRead(notificationId) {
                if (!notificationId) {
                    return Promise.resolve();
                }
                var tokenMeta = document.querySelector('meta[name="csrf-token"]');
                var csrf = tokenMeta ? tokenMeta.getAttribute('content') : '';
                return fetch('/notifications/' + encodeURIComponent(notificationId) + '/read', {
                    method: 'POST',
                    headers: {
                        'Accept': 'application/json',
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': csrf,
                        'X-Requested-With': 'XMLHttpRequest'
                    },
                    body: JSON.stringify({})
                }).catch(function() {});
            }

            function rowHtml(label, value) {
                if (!value) {
                    return '';
                }
                return '<div class="py-1.5 border-b border-white/10 last:border-b-0">'
                    + '<p class="text-xs text-white/70">' + escapeHtml(label) + '</p>'
                    + '<p class="text-sm text-white font-semibold">' + escapeHtml(value) + '</p>'
                    + '</div>';
            }

            function openReminderDetailModal(alertData) {
                closeReminderDetailModal();
                var detail = alertData.detail || {};
                var ridRaw = alertData.reminder_id != null ? alertData.reminder_id : detail.reminder_id;
                var rid = ridRaw != null && ridRaw !== '' ? parseInt(ridRaw, 10) : NaN;
                if (isNaN(rid) || rid < 1) {
                    rid = null;
                }
                var actionRow = ''
                    + '<div class="flex flex-col sm:flex-row flex-wrap items-stretch sm:items-center justify-end gap-2">'
                    + (rid ? (
                        '<button type="button" id="crm-reminder-snooze" class="px-4 py-2.5 rounded-xl border border-[#FFE600]/80 text-[#FFE600] text-sm font-semibold hover:bg-[#FFE600]/10">Aplazar 5 minutos</button>'
                        + '<button type="button" id="crm-reminder-reschedule" class="px-4 py-2.5 rounded-xl border border-[#FFE600]/80 text-[#FFE600] text-sm font-semibold hover:bg-[#FFE600]/10">Reprogramar</button>'
                    ) : '')
                    + '<button type="button" id="crm-reminder-mark-seen" class="px-5 py-2.5 rounded-xl bg-[#FFE600] text-[#071A3D] text-sm font-bold hover:bg-[#ffeb3b]">' + (alertData.needs_alarm_confirm ? 'Confirmar' : 'Visto') + '</button>'
                    + '</div>';
                var html = ''
                    + '<div id="crm-reminder-detail-modal" class="fixed inset-0 z-[130] flex items-center justify-center p-4 bg-black/70 backdrop-blur-sm">'
                    + '  <div class="absolute inset-0" data-close-reminder-modal="1"></div>'
                    + '  <div class="relative w-full max-w-2xl max-h-[90vh] flex flex-col rounded-2xl border-2 border-[#FFE600] bg-[#0b2f69] shadow-2xl overflow-hidden">'
                    + '    <div class="px-5 py-3 bg-[#071A3D] border-b border-[#FFE600]/45 flex items-center justify-between flex-shrink-0">'
                    + '      <h3 class="text-base font-extrabold text-[#FFE600]">Detalle del recordatorio</h3>'
                    + '      <button type="button" class="w-8 h-8 rounded-full border border-[#FFE600]/70 text-[#FFE600] hover:bg-[#FFE600]/15" data-close-reminder-modal="1">×</button>'
                    + '    </div>'
                    + '    <div class="flex-1 min-h-0 overflow-y-auto p-5 space-y-4 text-white">'
                    + '      <div class="rounded-xl border border-white/15 bg-[#123f8f] p-4">'
                    + '        <p class="text-sm text-white/80">Título</p>'
                    + '        <p class="text-lg font-bold">' + escapeHtml(alertData.title || 'Recordatorio') + '</p>'
                    + (alertData.description ? '<p class="mt-2 text-sm text-white/90">' + escapeHtml(alertData.description) + '</p>' : '')
                    + '      </div>'
                    + '      <div class="grid grid-cols-1 md:grid-cols-2 gap-4">'
                    + '        <div class="rounded-xl border border-white/15 bg-[#0f376f] p-4">'
                    + '          <p class="text-sm font-bold text-[#FFE600] mb-2">Datos del contacto</p>'
                    +            rowHtml('Nombre', detail.nombre_cliente)
                    +            rowHtml('Empresa', detail.empresa)
                    +            rowHtml('Correo', detail.correo_electronico)
                    +            rowHtml('Teléfono', detail.numero_telefonico)
                    +            rowHtml('Extensión', detail.extension)
                    +            rowHtml('Área', detail.area)
                    +            rowHtml('Puesto', detail.puesto_trabajo)
                    + '        </div>'
                    + '        <div class="rounded-xl border border-white/15 bg-[#0f376f] p-4">'
                    + '          <p class="text-sm font-bold text-[#FFE600] mb-2">Datos del recordatorio</p>'
                    +            rowHtml('Tipo de acción', detail.tipo_accion)
                    +            rowHtml('Hora programada', detail.fecha_inicio || alertData.time)
                    +            rowHtml('Fecha límite', detail.fecha_limite)
                    +            rowHtml('Repetición', detail.repeticion)
                    + '        </div>'
                    + '      </div>'
                    + '    </div>'
                    + '    <div class="flex-shrink-0 px-5 py-3 border-t border-[#FFE600]/30 bg-[#071A3D]">'
                    +        actionRow
                    + '    </div>'
                    + '  </div>'
                    + '</div>';
                document.body.insertAdjacentHTML('beforeend', html);
                document.body.style.overflow = 'hidden';
                document.querySelectorAll('[data-close-reminder-modal="1"]').forEach(function(el) {
                    el.addEventListener('click', closeReminderDetailModal);
                });
                var tokenMeta = document.querySelector('meta[name="csrf-token"]');
                var csrf = tokenMeta ? tokenMeta.getAttribute('content') : '';
                if (rid) {
                    var snoozeBtn = document.getElementById('crm-reminder-snooze');
                    var resBtn = document.getElementById('crm-reminder-reschedule');
                    if (snoozeBtn) {
                        snoozeBtn.addEventListener('click', function() {
                            snoozeBtn.disabled = true;
                            fetch(crmReminderSnoozeUrl(rid), {
                                method: 'POST',
                                headers: {
                                    'Accept': 'application/json',
                                    'Content-Type': 'application/json',
                                    'X-CSRF-TOKEN': csrf,
                                    'X-Requested-With': 'XMLHttpRequest'
                                },
                                body: JSON.stringify({})
                            })
                                .then(function(r) { return r.json(); })
                                .then(function(data) {
                                    if (data && data.success) {
                                        ackReminderVistoLocally(alertData.id);
                                        return markReminderAsRead(alertData.id);
                                    }
                                })
                                .finally(function() {
                                    closeReminderDetailModal();
                                    updateBadge();
                                    snoozeBtn.disabled = false;
                                });
                        });
                    }
                    if (resBtn) {
                        resBtn.addEventListener('click', function() {
                            window.location.href = crmReminderEditUrl(rid);
                        });
                    }
                }
                var seenBtn = document.getElementById('crm-reminder-mark-seen');
                if (seenBtn) {
                    seenBtn.addEventListener('click', function() {
                        seenBtn.disabled = true;
                        var afterRead = function() {
                            ackReminderVistoLocally(alertData.id);
                            markReminderAsRead(alertData.id).finally(function() {
                                closeReminderDetailModal();
                                updateBadge();
                                seenBtn.disabled = false;
                            });
                        };
                        if (rid && alertData.needs_alarm_confirm) {
                            fetch(crmReminderConfirmUrl(rid), {
                                method: 'POST',
                                headers: {
                                    'Accept': 'application/json',
                                    'Content-Type': 'application/json',
                                    'X-CSRF-TOKEN': csrf,
                                    'X-Requested-With': 'XMLHttpRequest'
                                },
                                body: JSON.stringify({})
                            })
                                .then(function(r) { return r.json(); })
                                .then(function(data) {
                                    if (data && data.success) {
                                        afterRead();
                                    } else {
                                        seenBtn.disabled = false;
                                    }
                                })
                                .catch(function() {
                                    seenBtn.disabled = false;
                                });
                        } else {
                            afterRead();
                        }
                    });
                }
            }

            function removeExistingSideAlertForNotif(notificationId) {
                var nid = String(notificationId || '');
                if (!nid) {
                    return;
                }
                var host = document.getElementById('crm-reminder-side-alerts');
                if (!host) {
                    return;
                }
                var prev = host.querySelector('[data-crm-notif-id="' + nid.replace(/"/g, '') + '"]');
                if (prev && prev.parentNode) {
                    prev.remove();
                }
            }

            function showDueReminderSideAlert(alertData) {
                var nid = String(alertData.id || '');
                removeExistingSideAlertForNotif(nid);

                var host = document.getElementById('crm-reminder-side-alerts');
                if (!host) {
                    host = document.createElement('div');
                    host.id = 'crm-reminder-side-alerts';
                    host.className = 'fixed top-4 right-4 z-[120] flex flex-col gap-3 w-[min(92vw,360px)] pointer-events-none';
                    document.body.appendChild(host);
                }

                var id = 'due-reminder-' + Date.now() + '-' + Math.random().toString(36).slice(2, 8);
                var hourText = extractHourLabel(alertData.time);
                var html = ''
                    + '<div id="' + id + '" data-crm-notif-id="' + nid.replace(/"/g, '') + '" class="pointer-events-auto rounded-2xl overflow-hidden" style="background:#0b2f69;border:2px solid #FFE600;box-shadow:0 14px 30px rgba(0,0,0,0.45);">'
                    + '  <div class="px-4 py-3 flex items-center gap-2" style="background:#071A3D;color:#FFE600;border-bottom:1px solid rgba(255,230,0,0.45);">'
                    + '    <span class="inline-flex items-center justify-center w-8 h-8 rounded-full shrink-0" style="background:rgba(255,230,0,0.2);color:#FFE600;border:1px solid rgba(255,230,0,0.8);">'
                    + '      <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">'
                    + '        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>'
                    + '      </svg>'
                    + '    </span>'
                    + '    <p class="text-sm font-extrabold tracking-wide flex-1">ALERTA DE RECORDATORIO</p>'
                    + '    <button type="button" data-dismiss-id="' + id + '" class="inline-flex items-center justify-center w-7 h-7 rounded-full border border-[#ffe600]/80 text-[#ffe600] hover:bg-[#ffe600]/20" aria-label="Cerrar alerta">'
                    + '      <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M6 18L18 6M6 6l12 12"></path></svg>'
                    + '    </button>'
                    + '  </div>'
                    + '  <div class="px-4 py-3.5" style="background:#0b2f69;">'
                    + '    <p class="text-base font-extrabold leading-snug text-white">' + escapeHtml(alertData.title || 'Recordatorio') + '</p>'
                    + '    <p class="mt-1.5 text-sm text-white/90">Hora: <span class="font-extrabold text-[#FFE600]">' + escapeHtml(hourText) + '</span></p>'
                    + '  </div>'
                    + '</div>';

                host.insertAdjacentHTML('beforeend', html);
                var node = document.getElementById(id);
                if (!node) {
                    return;
                }
                var closeButton = node.querySelector('[data-dismiss-id="' + id + '"]');
                if (closeButton) {
                    closeButton.addEventListener('click', function() {
                        dismissReminderNode(node);
                    });
                }
                node.addEventListener('click', function(e) {
                    if (e.target && e.target.closest('[data-dismiss-id]')) {
                        return;
                    }
                    openReminderDetailModal(alertData);
                });
                setTimeout(function() {
                    dismissReminderNode(node);
                }, 180000);
            }

            function dismissReminderNode(node) {
                if (!node || !node.parentNode) {
                    return;
                }
                if (node.dataset.dismissing === '1') {
                    return;
                }
                node.dataset.dismissing = '1';
                requestAnimationFrame(function() {
                    node.style.transition = 'opacity 250ms ease, transform 250ms ease';
                    node.style.opacity = '0';
                    node.style.transform = 'translateX(12px)';
                    setTimeout(function() { node.remove(); }, 280);
                });
            }

            function updateBadge() {
                fetch(url, { headers: { 'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest' } })
                    .then(function(r) { return r.json(); })
                    .then(function(data) {
                        var count = data.unread_count || 0;
                        var display = data.display || (count > 99 ? '99+' : String(count));
                        document.querySelectorAll('.js-header-notification-badge').forEach(function(el) { el.textContent = display; });
                        document.querySelectorAll('.js-header-notification-badge-wrap').forEach(function(el) { el.style.display = count > 0 ? '' : 'none'; });
                        var sidebarWrap = document.getElementById('sidebar-notification-badge-wrap');
                        var sidebarBadge = document.getElementById('sidebar-notification-badge');
                        if (sidebarWrap && sidebarBadge) {
                            sidebarBadge.textContent = display;
                            sidebarWrap.style.display = count > 0 ? '' : 'none';
                        }

                        var dueAlerts = Array.isArray(data.due_reminder_alerts) ? data.due_reminder_alerts : [];
                        dueAlerts.forEach(function(alertData) {
                            var nid = String(alertData.id || '');
                            if (!nid || reminderVistoAckIds[nid]) {
                                return;
                            }
                            showDueReminderSideAlert(alertData);
                            var now = Date.now();
                            var lastRing = lastReminderRingByNotifId[nid] || 0;
                            if (now - lastRing >= REMINDER_ALARM_REPEAT_MS) {
                                lastReminderRingByNotifId[nid] = now;
                                playReminderAlarm();
                                showBrowserReminderNotification(alertData);
                            }
                        });
                    })
                    .catch(function() {});
            }
            document.addEventListener('click', unlockAudio, { once: true });
            document.addEventListener('keydown', unlockAudio, { once: true });
            document.addEventListener('visibilitychange', function() {
                if (document.visibilityState === 'visible') {
                    maybeRequestBrowserNotificationPermission();
                }
            });
            maybeRequestBrowserNotificationPermission();
            updateBadge();
            setInterval(updateBadge, 25000);
            window.updateNotificationBadge = updateBadge;
        })();
        </script>
        @endauth
    </body>
</html>
