@props([
    'variant' => 'page',
    /** @var \App\Models\User|null Si se omite, se usa el usuario de la sesión actual. */
    'user' => null,
])

@php
    $isView = $variant === 'view-header';
    $iconClass = $isView ? 'view-header__icon' : 'page-header-card__icon';
    $imgClass = $isView ? 'view-header__avatar-img' : 'page-header-card__icon-img';
    $headerUser = $user ?? auth()->user();
@endphp

<div class="{{ $iconClass }}" aria-hidden="true" @if ($headerUser) data-profile-user-id="{{ $headerUser->getKey() }}" @endif>
    @if ($headerUser?->profile_photo_url)
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
