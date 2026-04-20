<?php

use Illuminate\Support\Facades\Route;

// ---- Site institucional ----
Route::get('/', fn() => view('site.home'))->name('home');
Route::get('/planos', fn() => view('site.planos'))->name('planos');
Route::get('/privacidade', fn() => view('site.privacidade'))->name('privacidade');

// ---- Autenticação ----
Route::get('/entrar', fn() => view('auth.login'))->name('login');
Route::get('/criar-conta', fn() => view('auth.register'))->name('register');
Route::get('/recuperar-senha', fn() => view('auth.forgot-password'))->name('password.request');
Route::get('/redefinir-senha', fn() => view('auth.reset-password'))->name('password.reset');

// ---- Painel interno ----
Route::prefix('painel')->name('painel.')->group(function () {
    Route::get('/', fn() => view('dashboard.index'))->name('dashboard');
    Route::get('/agenda/dia', fn() => view('agenda.dia'))->name('agenda.dia');
    Route::get('/agenda/semana', fn() => view('agenda.semana'))->name('agenda.semana');
    Route::get('/agenda/lista', fn() => view('agenda.lista'))->name('agenda.lista');
    Route::get('/pacientes', fn() => view('pacientes.index'))->name('pacientes.index');
    Route::get('/pacientes/1', fn() => view('pacientes.show'))->name('pacientes.show');
    Route::get('/pacientes/1/notas', fn() => view('pacientes.notas'))->name('pacientes.notas');
    Route::get('/financeiro', fn() => view('financeiro.index'))->name('financeiro.index');
    Route::get('/financeiro/historico', fn() => view('financeiro.historico'))->name('financeiro.historico');
    Route::get('/automacoes', fn() => view('automacoes.index'))->name('automacoes.index');
    Route::get('/configuracoes', fn() => view('configuracoes.index'))->name('configuracoes.index');
    Route::get('/minha-pagina', fn() => view('pagina-publica.editor'))->name('pagina.editor');
    Route::get('/equipe', fn() => view('equipe.index'))->name('equipe.index');
});

// ---- Agendamento público (paciente) ----
Route::prefix('agendar')->name('agendar.')->group(function () {
    Route::get('/{slug}', fn() => view('agendamento.inicio'))->name('inicio');
    Route::get('/{slug}/horario', fn() => view('agendamento.horario'))->name('horario');
    Route::get('/{slug}/confirmacao', fn() => view('agendamento.confirmacao'))->name('confirmacao');
});

// ---- Página pública do profissional ----
Route::get('/p/{slug}', fn() => view('pagina-publica.perfil'))->name('perfil.publico');
