@extends('layouts.master-layouts')

@section('title', 'Pedidos Finalizados')

@section('container')

@push('css')

<style>
    .popover-content {
        word-wrap: break-word !important;
    }
</style>

@endpush


<div class="container">
	
    <div class="row">
        
        <div class="col-lg-12">
            <h1>Pedidos Finalizados</h1>
            <div class="table-responsive">
                <table border="0" align="center" cellpadding="0" cellspacing="5" class="table table-striped">
                    <thead>
                    <tr>
                        <th>Cantidad</th>
                        <th>Cover</th>
                        <th>Titulo</th>
                        <th>Usuarios</th>
                        <th>Fecha</th>
                        <th>Notas</th>
                    </tr>
                    </thead>
                    <tbody>
                        
                    @if(count($pedidos_finalizados) > 0)
                        @php $total_cantidad = 0 @endphp
                        @foreach($pedidos_finalizados as $i => $pedido)
                        @php $total_cantidad += $pedido->cantidad @endphp
                        <tr>
                            <td>{{ $pedido->cantidad }}</td>
                            <td>
                                <img class="img-rounded" width="50" id="image-swap" src="{{asset('img/productos')}}/{{ $pedido->consola }}/{{ $pedido->titulo.'.jpg' }} "alt="" />
                            </td>
                            <td>
                                {{ \Helper::strTitleStock($pedido->titulo) }}
                                <span class="label label-default {{$pedido->consola}}">
                                    {{$pedido->consola}}
                                </span>
                            </td>
                            <td>
                                {{ $pedido->usuario }}
                            </td>
                            <td>
                                {{ date('d/m/Y', strtotime($pedido->Day)) }}
                            </td>
                            <td style="text-align: center;">
                                <div class="alert alert-warning" style="color: #8a6d3b; background-color:#FFDD87; padding: 4px 7px; font-size: 12px; font-style:italic; margin:0px; opacity: 0.9;"><i class="fa fa-comment fa-fw"></i> {!! $pedido->Notas !!} </div>
                            </td>
                        </tr>
                        @endforeach
                        <tr>
                            <td><b>{{ $total_cantidad }}</b></td>
                            <td colspan="5"></td>
                        </tr>
                    @else
                    <tr>
                        <td colspan = '6' class="text-center">No se encontraron datos</td>
                    </tr>
                    @endif
                    </tbody>
                </table>
                
            </div>
        </div>
    </div>


</div><!--/.container-->



@endsection
