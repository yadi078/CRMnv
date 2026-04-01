@props([
    'variant' => 'page',
    /** @var \App\Models\User|null Si se omite, se usa el usuario de la sesión actual. */
    'user' => null,
    /** Si no hay foto, mostrar iniciales en burbuja circular (mismo estilo que la foto). */
    'fallbackInitials' => false,
    /** Burbuja más pequeña (encabezados tipo perfiles). */
    'compact' => false,
])

@php
    $isView = $variant === 'view-header';
    $headerUser = $user ?? auth()->user();
    $hasPhoto = (bool) $headerUser?->profile_photo_url;
    $useInitialsBubble = $fallbackInitials && $headerUser && ! $hasPhoto;
    $iconClass = $isView ? 'view-header__icon' : 'page-header-card__icon';
    if ($compact && ! $isView) {
        $iconClass .= ' page-header-card__icon--compact';
    }
    if ($hasPhoto || $useInitialsBubble) {
        $iconClass .= $isView ? ' view-header__icon--with-photo' : ' page-header-card__icon--with-photo';
    }
    $imgClass = $isView ? 'view-header__avatar-img' : 'page-header-card__icon-img';
@endphp

<div class="{{ $iconClass }}" aria-hidden="true" @if ($headerUser) data-profile-user-id="{{ $headerUser->getKey() }}" @endif>
    @if ($hasPhoto)
        <img
            src="{{ $headerUser->profile_photo_url }}"
            alt=""
            class="{{ $imgClass }}"
            width="44"
            height="44"
            decoding="async"
        />
    @elseif ($useInitialsBubble)
        <span class="{{ $imgClass }} !flex items-center justify-center text-[#FFE600] text-[0.65rem] sm:text-xs font-bold tracking-tight bg-[#0f2744]">{{ $headerUser->initials }}</span>
    @else
        {{ $slot }}
    @endif
</div>
