@extends('layouts.master-layouts')

@section('title', 'Listar Ventas en Recupero')

@section('container')
    <div class="container">
        <h1>Listado de Ventas en Recupero</h1>
        <!-- InstanceBeginEditable name="body" -->

        <div class="row">
            @component('components/filters/column_word')
                @slot('columns',$columns);
                @slot('path','sales/recupero');
            @endcomponent
        </div>

        <div class="row">
            <div class="col-md-12">
                <div class="table-responsive">
                    <table border="0" align="center" cellpadding="0" cellspacing="5" class="table table-striped">
                        <thead>
                        <tr>
                            <th>Cte ID</th>
                            <th>Vta ID</th>
                            <th>Producto</th>
                            <th>Recup</th>
                            <th>Notas</th>
                            <th>Fecha</th>
                            <th>Operador</th>
                        </tr>
                        </thead>
                        <tbody>
                        @foreach($ventas as $sales)
                            <tr>
                                <td><a title="Ir a Cliente" href="{{ url('clientes',$sales->clientes_id) }}"> {{ $sales->clientes_id }} </a></td>
                                <td><a title="Ir a Cliente" href="{{ url('clientes',$sales->clientes_id) }}"> {{ $sales->id_ventas }} </a></td>
                                <td>
                                    {{ $sales->titulo }}
                                    <span class="label label-default {{$sales->consola}}">
                          {{$sales->consola}} 
                      </span> &nbsp; @if ($sales->slot == 'Secundario') <span class="label label-danger">2°</span> @endif
                                </td>
                                <td>
                                    {{ $sales->recup }}
                                </td>
                                <td>
                                    <div class="alert alert-warning" style="color: #8a6d3b; background-color:#FFDD87; padding: 4px 7px; font-size: 12px; font-style:italic; margin:0px; opacity: 0.9;"><i class="fa fa-comment fa-fw"></i> {!! $sales->Notas !!} </div>
                                </td>
                                <td>
                                    @php
                                    $dia = date('d', strtotime($sales->Day));
                                    $mes = date('n', strtotime($sales->Day));
                                    $mes = \Helper::getMonthLetter($mes);
                                    $anio = date('Y', strtotime($sales->Day));
                                    $fecha = "$dia-$mes-$anio";
                                    @endphp
                                    
                                    <a title="Ir a Cliente" href="{{ url('clientes',$sales->clientes_id) }}"> {{ $fecha }} </a>
                                  </td>
                                  <td>
                                    <span class="badge badge-{{ $sales->color_user }}" style="opacity:0.7; font-weight:400;" title="{{$sales->usuario}}">{{ $sales->usuario }}</span>
                                  </td>
                            </tr>
                        @endforeach
                
                        </tbody>
                    </table>
                <div>
            </div>

            <div class="col-md-12">

              <ul class="pager">
                {{ $ventas->render() }}
              </ul>

            </div>
        </div>
    </div>

@endsection