@extends('layouts.auth')
@section('title', 'Redefinir senha')
@section('content')
<div class="text-center mb-4">
    <div style="width:56px;height:56px;border-radius:14px;background:rgba(30,91,173,.1);display:inline-flex;align-items:center;justify-content:center;font-size:1.5rem;color:var(--sabenta-primary);margin-bottom:1rem;"><i class="bi bi-shield-lock"></i></div>
    <h1 style="font-family:var(--font-heading);font-size:1.5rem;font-weight:800;color:var(--sabenta-text);margin-bottom:.35rem;">Criar nova senha</h1>
    <p style="font-size:.875rem;color:var(--sabenta-text-muted);">Escolha uma senha forte para proteger sua conta</p>
</div>
<div class="sabenta-card" style="border-radius:16px;padding:0;"><div class="card-body" style="padding:2rem;">
<form method="POST" action="{{ route('password.reset') }}" x-data="{ show:false, showC:false }">@csrf
<input type="hidden" name="token" value="{{ request('token') }}">
<input type="hidden" name="email" value="{{ request('email') }}">
<div class="mb-3">
    <label for="password" class="form-label">Nova senha</label>
    <div class="input-group"><span class="input-group-text"><i class="bi bi-lock"></i></span>
    <input :type="show ? 'text' : 'password'" id="password" name="password" class="form-control" placeholder="Mínimo 8 caracteres" required autofocus>
    <button type="button" class="input-group-text" style="cursor:pointer;background:transparent;" @click="show=!show" tabindex="-1"><i class="bi" :class="show ? 'bi-eye-slash' : 'bi-eye'"></i></button>
    </div>
    @error('password')<div class="text-danger mt-1" style="font-size:.8rem;">{{ $message }}</div>@enderror
</div>
<div class="mb-4">
    <label for="password_confirmation" class="form-label">Confirmar nova senha</label>
    <div class="input-group"><span class="input-group-text"><i class="bi bi-lock-fill"></i></span>
    <input :type="showC ? 'text' : 'password'" id="password_confirmation" name="password_confirmation" class="form-control" placeholder="Repita a nova senha" required>
    <button type="button" class="input-group-text" style="cursor:pointer;background:transparent;" @click="showC=!showC" tabindex="-1"><i class="bi" :class="showC ? 'bi-eye-slash' : 'bi-eye'"></i></button>
    </div>
</div>
<button type="submit" class="btn btn-primary w-100 btn-lg"><i class="bi bi-check-circle"></i> Redefinir senha e entrar</button>
</form></div></div>
@endsection
