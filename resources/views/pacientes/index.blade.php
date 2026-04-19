@extends('layouts.app')
@section('title', 'Pacientes')
@section('breadcrumb')
<nav aria-label="breadcrumb"><ol class="breadcrumb"><li class="breadcrumb-item active">Pacientes</li></ol></nav>
@endsection
@section('content')
<x-page-header titulo="Pacientes" />
<div class="sabenta-card"><div class="card-body"><x-empty-state icone="hourglass-split" titulo="Em construção" descricao="Este módulo será implementado em breve." /></div></div>
@endsection
