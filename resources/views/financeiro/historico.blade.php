@extends('layouts.app')
@section('title', 'Histórico Financeiro')
@section('breadcrumb')
<nav aria-label="breadcrumb"><ol class="breadcrumb"><li class="breadcrumb-item active">Histórico Financeiro</li></ol></nav>
@endsection
@section('content')
<x-page-header titulo="Histórico Financeiro" />
<div class="sabenta-card"><div class="card-body"><x-empty-state icone="hourglass-split" titulo="Em construção" descricao="Este módulo será implementado em breve." /></div></div>
@endsection
