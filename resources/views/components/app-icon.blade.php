@props([
    'name',
    'class' => 'w-5 h-5'
])

@php
    $iconPath = public_path('icons/' . str_replace('.', '/', $name) . '.svg');
    $svg = file_exists($iconPath) ? file_get_contents($iconPath) : '';
    if ($svg && $class) {
        $svg = preg_replace('/<svg\s+/', '<svg class="' . e($class) . '" ', $svg, 1);
    }
@endphp

{!! $svg !!}
