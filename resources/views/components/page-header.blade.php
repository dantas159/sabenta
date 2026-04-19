@props([
    'titulo'      => '',
    'sub'         => null,
    'botao'       => null,
    'botaoHref'   => '#',
    'botaoIcone'  => 'plus-lg',
    'botaoTarget' => null,
    'botaoCor'    => 'primary',
])

<div class="page-header">
    <div class="page-header__left">
        <h1 class="page-header__title">{{ $titulo }}</h1>
        @if($sub)
            <div class="page-header__sub">{!! $sub !!}</div>
        @endif
    </div>
    @if($botao)
        <div>
            <a href="{{ $botaoHref }}"
               class="btn btn-{{ $botaoCor }}"
               {{ $botaoTarget ? "data-bs-toggle=modal data-bs-target=$botaoTarget" : '' }}>
                <i class="bi bi-{{ $botaoIcone }}"></i>
                {{ $botao }}
            </a>
        </div>
    @endif
</div>
