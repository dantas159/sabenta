@props(['status' => 'pendente'])

@php
    $map = [
        'confirmado' => ['label' => 'Confirmado', 'class' => 'badge-confirmado'],
        'pendente'   => ['label' => 'Pendente',   'class' => 'badge-pendente'],
        'cancelado'  => ['label' => 'Cancelado',  'class' => 'badge-cancelado'],
        'faltou'     => ['label' => 'Faltou',     'class' => 'badge-faltou'],
        'realizado'  => ['label' => 'Realizado',  'class' => 'badge-realizado'],
        'pago'       => ['label' => 'Pago',       'class' => 'badge-pago'],
    ];
    $item = $map[$status] ?? ['label' => ucfirst($status), 'class' => 'badge-cancelado'];
@endphp

<span class="sabenta-badge {{ $item['class'] }}">{{ $item['label'] }}</span>
