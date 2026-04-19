@props([
    'tipo'      => 'info',
    'titulo'    => '',
    'descricao' => '',
    'href'      => '#',
    'icone'     => null,
])

@php
    $icones = [
        'danger'  => 'exclamation-circle-fill',
        'warning' => 'exclamation-triangle-fill',
        'info'    => 'info-circle-fill',
    ];
    $iconeReal = $icone ?? ($icones[$tipo] ?? 'info-circle-fill');
@endphp

<a href="{{ $href }}" class="alert-card alert-card--{{ $tipo }}">
    <i class="bi bi-{{ $iconeReal }} alert-card__icon"></i>
    <div>
        <div class="alert-card__title">{{ $titulo }}</div>
        @if($descricao)
            <div class="alert-card__desc">{{ $descricao }}</div>
        @endif
    </div>
</a>
