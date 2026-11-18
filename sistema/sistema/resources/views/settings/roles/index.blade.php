@extends('template.administrador')

@section('title', 'Roles')
@section('name_section', 'Roles')
@section('description', 'Listado de Listado de roles')
@section('item_section', 'Inicio')
@section('section', 'Roles')
@section('module', 'Configuraciones')

@section('content')
    @if (session('ok'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            {{ session('ok') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Cerrar"></button>
        </div>
    @endif
    @if (session('error'))
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            {{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Cerrar"></button>
        </div>
    @endif

    <div class="card bg-white border-0 shadow-sm border-5 border-top border-secondary">
        <div class="card-header bg-white">
            <div class="row">
                <div class="col-12 col-md-3">
                    <a href="{{ route('roles.create') }}" class="btn btn-primary rounded-3 text-uppercase text-white w-100">
                        <i class="bi bi-plus-lg me-2"></i><span class="small fw-medium">Nuevo registro</span>
                    </a>
                </div>
            </div>
        </div>
        <div class="card-body">
            <h6 class="text-uppercase small text-muted mb-3">Total de registros encontrados: {{ $roles->count() }}</h6>
            <table id="display" class="table table-sm table-hover w-100 bg-white">
                <thead>
                    <tr>
                        <th class="text-center align-middle text-uppercase small">Nº</th>
                        <th class="text-center align-middle text-uppercase small">Nombre</th>
                        <th class="text-center align-middle text-uppercase small">Descripción</th>
                        
                        <th class="text-center align-middle text-uppercase small">Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($roles as $role)
                        <tr>
                            <td class="text-center align-middle">{{ $loop->iteration }}</td>
                            <td class="text-center align-middle">{{ $role->name }}</td>
                            <td class="text-center align-middle">{{ $role->description ?? '—' }}</td>
                            
                            <td class="text-center align-middle">
                                <div class="dropdown">
                                    <button class="btn btn-light" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                                        <i class="bi bi-three-dots-vertical"></i>
                                    </button>
                                    <ul class="dropdown-menu dropdown-menu-end">
                                        <li>
                                            <a class="dropdown-item" href="{{ route('roles.edit', $role) }}">Editar</a>
                                        </li>
                                        <li>
                                            <form action="{{ route('roles.destroy', $role) }}" method="POST"
                                                  onsubmit="return confirm('¿Estás seguro de eliminar este registro?');">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="dropdown-item text-danger">Eliminar</button>
                                            </form>
                                        </li>
                                    </ul>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="text-center text-muted py-3">No hay categorías registradas.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
@endsection
