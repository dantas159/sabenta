@extends('layouts.app')
@section('title', 'Home')
@section('breadcrumb')
<nav aria-label="breadcrumb"><ol class="breadcrumb"><li class="breadcrumb-item active">Home</li></ol></nav>
@endsection
@section('content')
<x-page-header titulo="Home" />
<div class="sabenta-card"><div class="card-body"><x-empty-state icone="hourglass-split" titulo="Em construção" descricao="Este módulo será implementado em breve." /></div></div>
@endsection
