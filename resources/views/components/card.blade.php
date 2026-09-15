@props(['highlight' => false])

<div @class(['card', 'highlight' => $highlight])>
    {{ $slot }}

    <a {{ $attributes->merge(['class' => 'btn btn-espresso']) }}>View piece</a>
</div>
