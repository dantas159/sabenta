@props([
    'icone'      => 'inbox',
    'titulo'     => 'Nada por aqui',
    'descricao'  => 'Não há registros para exibir.',
    'botao'      => null,
    'botaoHref'  => '#',
    'botaoIcone' => 'plus-lg',
    'botaoTarget'=> null,
])

<div class="empty-state">
    <i class="bi bi-{{ $icone }} empty-state__icon"></i>
    <p class="empty-state__title">{{ $titulo }}</p>
    <p class="empty-state__desc">{{ $descricao }}</p>
    @if($botao)
        <a href="{{ $botaoHref }}"
           class="btn btn-primary"
           {{ $botaoTarget ? "data-bs-toggle=modal data-bs-target=$botaoTarget" : '' }}>
            <i class="bi bi-{{ $botaoIcone }}"></i>
            {{ $botao }}
        </a>
    @endif
</div>
