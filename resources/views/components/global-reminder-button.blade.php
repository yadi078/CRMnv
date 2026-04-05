@props([
    'contact' => null,
    'executiveUser' => null,
])
@if($contact)
    <x-contact-reminder-button :contact="$contact" {{ $attributes }} />
@elseif($executiveUser)
    <x-executive-reminder-button :executive="$executiveUser" {{ $attributes }} />
@else
    <x-generic-reminder-button {{ $attributes }} />
@endif
