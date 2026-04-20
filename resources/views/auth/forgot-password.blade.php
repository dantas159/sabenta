@extends('layouts.auth')
@section('title', 'Recuperar acesso')
@section('content')
<div class="text-center mb-4">
    <div style="width:56px;height:56px;border-radius:14px;background:rgba(30,91,173,.1);display:inline-flex;align-items:center;justify-content:center;font-size:1.5rem;color:var(--sabenta-primary);margin-bottom:1rem;"><i class="bi bi-key"></i></div>
    <h1 style="font-family:var(--font-heading);font-size:1.5rem;font-weight:800;color:var(--sabenta-text);margin-bottom:.35rem;">Recuperar acesso</h1>
    <p style="font-size:.875rem;color:var(--sabenta-text-muted);max-width:320px;margin:0 auto;">Informe seu e-mail e enviaremos um link para redefinir sua senha.</p>
</div>
@if(session('status'))
<div class="alert alert-success">{{ session('status') }}</div>
@endif
<div class="sabenta-card" style="border-radius:16px;padding:0;"><div class="card-body" style="padding:2rem;">
<form method="POST" action="{{ route('password.request') }}">@csrf
<div class="mb-4">
    <label for="email" class="form-label">Seu e-mail cadastrado</label>
    <div class="input-group"><span class="input-group-text"><i class="bi bi-envelope"></i></span>
    <input type="email" id="email" name="email" class="form-control" value="{{ old('email') }}" placeholder="seu@email.com.br" required autofocus></div>
    @error('email')<div class="text-danger mt-1" style="font-size:.8rem;">{{ $message }}</div>@enderror
</div>
<button type="submit" class="btn btn-primary w-100 btn-lg"><i class="bi bi-send"></i> Enviar link de recuperação</button>
</form></div></div>
<p class="text-center mt-4" style="font-size:.875rem;">
    <a href="{{ route('login') }}" style="font-family:var(--font-heading);font-weight:600;color:var(--sabenta-primary);"><i class="bi bi-arrow-left me-1"></i>Voltar ao login</a>
</p>
@endsection
