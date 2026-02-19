@props(['active'])

@php
$classes = ($active ?? false)
            ? 'inline-flex items-center px-1 pt-1 border-b-2 border-[#003366] text-sm font-medium leading-5 text-[#1F2937] focus:outline-none focus:border-[#000836] transition duration-150 ease-in-out'
            : 'inline-flex items-center px-1 pt-1 border-b-2 border-transparent text-sm font-medium leading-5 text-[#6B7280] hover:text-[#1F2937] hover:border-[#E2E8F0] focus:outline-none focus:text-[#1F2937] focus:border-[#E2E8F0] transition duration-150 ease-in-out';
@endphp

<a {{ $attributes->merge(['class' => $classes]) }}>
    {{ $slot }}
</a>
