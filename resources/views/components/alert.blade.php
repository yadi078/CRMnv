@props(['type' => 'info', 'message'])

@if($message)
    @php
        $classes = match($type) {
            'success' => 'bg-green-50 border-green-200 text-green-800',
            'error'   => 'bg-red-50 border-red-200 text-red-800',
            'warning' => 'bg-amber-50 border-amber-200 text-amber-800',
            default  => 'bg-blue-50 border-blue-200 text-blue-800',
        };
    @endphp
    <div {{ $attributes->merge(['class' => "border rounded-lg p-4 mb-4 {$classes}"]) }} role="alert">
        {{ $message }}
    </div>
@endif
