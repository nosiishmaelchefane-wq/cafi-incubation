@props(['width' => '20', 'height' => '40'])

<img src="{{ asset('/images/logo.png') }}" 
     alt="{{ config('app.name', 'LEHSFF') }}" 
     {{ $attributes->merge(['class' => "block h-{$height} w-{$width} object-contain"]) }}>