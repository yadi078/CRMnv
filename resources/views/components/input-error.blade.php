@props(['messages'])

@if ($messages)
    <div {{ $attributes->merge(['class' => 'mt-2']) }}>
        <div class="flex items-start gap-3 p-3 rounded-xl border-2 border-[#003366] bg-[#E8F4FF] shadow-sm">
            <div class="flex-shrink-0 w-8 h-8 rounded-full bg-[#003366] flex items-center justify-center">
                <svg class="w-5 h-5 text-[#FFFF00]" fill="currentColor" viewBox="0 0 20 20" aria-hidden="true">
                    <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd" />
                </svg>
            </div>
            <ul class="flex-1 text-sm text-[#003366] font-medium space-y-1 list-none p-0 m-0">
                @foreach ((array) $messages as $message)
                    <li class="flex items-center gap-2">
                        <span class="w-1.5 h-1.5 rounded-full bg-[#FFFF00] flex-shrink-0"></span>
                        {{ $message }}
                    </li>
                @endforeach
            </ul>
        </div>
    </div>
@endif
