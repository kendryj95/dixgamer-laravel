@extends('layouts.master-layouts')

@section('title', 'Listar ventas')

@section('container')
    <div class="container">
        <h1>Listado de Ventas</h1>
        <!-- InstanceBeginEditable name="body" -->
        <div class="table-responsive">
            <table border="0" align="center" cellpadding="0" cellspacing="5" class="table table-striped">
                <thead>
                <tr>
                    <th>#</th>
                    <th>Fecha</th>
                    <th>Titulo</th>
                    <th>Consola</th>
                    <th>Cliente</th>
                    <th>Vendido por</th>
                    <th>Cobrado por</th>
                    <th>Precio</th>
                    <th>Comision</th>
                </tr>
                </thead>
                <tbody>
                @foreach($datos as $sales)
                    <tr>

                        <td>{{ $sales->ID_ventas }}
                            @if(!empty($sales->ventas_Notas)) <a href="#" data-toggle="popover" data-placement="bottom" data-trigger="focus" title="Notas" data-content="{{ $sales->ventas_Notas }}" class="h6" style="color: #555555;">
                                <i class="fa fa-comment fa-fw"></i></a>
                            @else
                            @endif
                        </td>
                        <td>{{ date("d-M", strtotime($sales->ventas_Day)) }} {{ date("d-M", strtotime($sales->ventas_Day)) }}</td>
                        <td> {{ $sales->titulo }} </td>
                        <td><span class="label label-default {{ $sales->consola }} ">{{ $sales->consola }}</span></td>
                        <td><a title="Ir a Cliente" href="{{ url('clientes',$sales->clientes_id) }}"> {{ $sales->nombre }} {{ $sales->apellido }} </a></td>
                        <td>{{ $sales->medio_venta }} </td>
                        <td>{{ $sales->medio_cobro }}</td>
                        <td>{{ $sales->precio }}</td>
                        <td>{{ $sales->comision }}</td>

                    </tr>
                @endforeach

                </tbody>
            </table>
        <div>
    </div>

@endsection