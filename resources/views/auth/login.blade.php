@extends('layouts.auth')
@section('title', 'Entrar')
@section('content')
<div class="text-center mb-4">
    <img src="{{ asset('images/logo-sabenta.png') }}" alt="Sabenta" height="40" class="mb-3 d-none d-md-inline-block">
    <h1 style="font-family:var(--font-heading);font-size:1.5rem;font-weight:800;color:var(--sabenta-text);margin-bottom:.35rem;">Bem-vindo de volta</h1>
    <p style="font-size:.875rem;color:var(--sabenta-text-muted);">Entre na sua conta Sabenta</p>
</div>
<div class="sabenta-card" style="border-radius:16px;padding:0;">
<div class="card-body" style="padding:2rem;">
<form method="POST" action="{{ route('login') }}">
@csrf
<div class="mb-3">
    <label for="email" class="form-label">E-mail profissional</label>
    <div class="input-group">
        <span class="input-group-text"><i class="bi bi-envelope"></i></span>
        <input type="email" id="email" name="email" class="form-control @error('email') is-invalid @enderror" value="{{ old('email') }}" placeholder="seu@email.com.br" required autofocus>
    </div>
    @error('email')<div class="text-danger mt-1" style="font-size:.8rem;"><i class="bi bi-exclamation-circle me-1"></i>{{ $message }}</div>@enderror
</div>
<div class="mb-3" x-data="{ show: false }">
    <div class="d-flex justify-content-between align-items-center mb-1">
        <label for="password" class="form-label mb-0">Senha</label>
        <a href="{{ route('password.request') }}" style="font-size:.8rem;color:var(--sabenta-primary);font-family:var(--font-heading);font-weight:500;">Esqueci minha senha</a>
    </div>
    <div class="input-group">
        <span class="input-group-text"><i class="bi bi-lock"></i></span>
        <input :type="show ? 'text' : 'password'" id="password" name="password" class="form-control @error('password') is-invalid @enderror" placeholder="••••••••" required>
        <button type="button" class="input-group-text" style="cursor:pointer;background:transparent;" @click="show=!show" tabindex="-1"><i class="bi" :class="show ? 'bi-eye-slash' : 'bi-eye'"></i></button>
    </div>
    @error('password')<div class="text-danger mt-1" style="font-size:.8rem;">{{ $message }}</div>@enderror
</div>
<div class="mb-4">
    <div class="form-check">
        <input class="form-check-input" type="checkbox" name="remember" id="remember" {{ old('remember') ? 'checked' : '' }}>
        <label class="form-check-label" for="remember" style="font-size:.875rem;color:var(--sabenta-text-muted);">Manter conectado por 30 dias</label>
    </div>
</div>
<button type="submit" class="btn btn-primary w-100 btn-lg"><i class="bi bi-box-arrow-in-right"></i> Entrar na conta</button>
</form>
</div>
</div>
<p class="text-center mt-4" style="font-size:.875rem;color:var(--sabenta-text-muted);">
    Não tem conta? <a href="{{ route('register') }}" style="font-family:var(--font-heading);font-weight:600;color:var(--sabenta-primary);">Criar conta grátis — 7 dias free</a>
</p>
@endsection
