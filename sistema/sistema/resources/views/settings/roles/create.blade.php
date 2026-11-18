@extends('template.administrador')

@section('title', 'ROLES')
@section('name_section', 'ROLES')
@section('descripcion', 'Nuevo rol')
@section('item_section', 'Inicio')
@section('section', 'Roles')
@section('module', 'Configuraciones')

@section('content')
<div class="card bg-white border-0 shadow-sm border-5 border-top border-secondary">
    <div class="card-body">
        <form action="{{ route('roles.store') }}" method="POST">
            @csrf
            @include('settings.roles.form')
        </form>
    </div>
</div>
@endsection