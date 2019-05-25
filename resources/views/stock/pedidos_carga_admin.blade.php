@extends('layouts.master-layouts')

@section('title', 'Pedidos Carga - Admin')

@section('container')


<div class="container">
	<h1>Pedidos Carga - Admin</h1>

	<div class="row">
        <div class="col-lg-12">
            <a style="margin-bottom: 10px" href="{{ url('asignar_stock') }}" class="btn btn-primary pull-right"><i class="fa fa-plus"></i> Crear Pedido</a>
        </div>
        <div class="col-lg-12">
            <div class="table-responsive">
                <table border="0" align="center" cellpadding="0" cellspacing="5" class="table table-striped">
                    <thead>
                    <tr>
                        <th>Cantidad</th>
                        <th>Titulo</th>
                        <th>Usuarios</th>
                        <th>Acciones</th>
                    </tr>
                    </thead>
                    <tbody>
                    @if(count($pedidos) > 0)
                        @foreach($pedidos as $pedido)
                        <tr>
                            <td>{{ $pedido->cantidad }}</td>
                            <td>
                                {{ $pedido->titulo }}
                                <span class="label label-default {{$pedido->consola}}">
                                    {{$pedido->consola}}
                                </span>
                            </td>
                            <td>
                                {{ $pedido->usuarios_pedido }}
                            </td>
                            <td>
                                <a class="btn btn-info btn-sm" href="#" title="Editar"><i class="fa fa-pencil"></i></a> <a href="{{ url('confirmar_pedido', $pedido->ids) }}" class="btn btn-success btn-sm" title="Confirmar"><i class="fa fa-check"></i></a>
                            </td>
                        </tr>
                        @endforeach
                    @else
                    <tr>
                        <td colspan = '4' class="text-center">No se encontraron datos</td>
                    </tr>
                    @endif
                    </tbody>
                </table>
                
            </div>
        </div>
    </div>


</div><!--/.container-->



@endsection
