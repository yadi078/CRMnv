@props([
    'variant' => 'page',
    /** @var \App\Models\User|null Si se omite, se usa el usuario de la sesión actual. */
    'user' => null,
])

@php
    $isView = $variant === 'view-header';
    $headerUser = $user ?? auth()->user();
    $hasPhoto = (bool) $headerUser?->profile_photo_url;
    $iconClass = $isView ? 'view-header__icon' : 'page-header-card__icon';
    if ($hasPhoto) {
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
    @else
        {{ $slot }}
    @endif
</div>
