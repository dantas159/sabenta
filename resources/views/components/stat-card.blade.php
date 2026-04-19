@props([
    'titulo'  => '',
    'valor'   => '',
    'icone'   => 'graph-up',
    'cor'     => 'primary',
    'delta'   => null,
    'deltaUp' => true,
    'href'    => null,
])

@php
    $tag = $href ? 'a' : 'div';
@endphp

<{{ $tag }}
    class="stat-card stat-card--{{ $cor }}"
    {{ $href ? "href=$href" : '' }}
    style="{{ $href ? 'text-decoration:none;' : '' }}"
>
    <div class="stat-card__icon">
        <i class="bi bi-{{ $icone }}"></i>
    </div>
    <div class="stat-card__body">
        <div class="stat-card__value">{{ $valor }}</div>
        <div class="stat-card__label">{{ $titulo }}</div>
        @if($delta)
            <div class="stat-card__delta {{ $deltaUp ? 'up' : 'down' }}">
                <i class="bi bi-arrow-{{ $deltaUp ? 'up' : 'down' }}-short"></i>
                {{ $delta }}
            </div>
        @endif
    </div>
</{{ $tag }}>
