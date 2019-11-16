@extends('layouts.master-layouts')

@section('title', 'Lista Venta sin Entregar')

@section('container')
    <div class="container">
        <h1>Listado de Ventas sin Entregar</h1>
        <!-- InstanceBeginEditable name="body" -->

        <div class="row">
            @component('components/filters/column_word')
                @slot('columns',$columns);
                @slot('path','sales/lista_sin_entregar');
            @endcomponent
        </div>


        <div class="row">
            <div class="col-md-12">
                <div class="table-responsive">
                    <table border="0" align="center" cellpadding="0" cellspacing="5" class="table table-striped">
                        <thead>
                        <tr>
                            <th>Vta ID</th>
                            <th>Cliente</th>
                            <th>Consola</th>
                            <th>Fecha</th>
                        </tr>
                        </thead>
                        <tbody>
                        @foreach($datos as $i => $sales)
                            <tr>
                
                                <td>
                                    {{$sales->ID}}
                                </td>
                                <td><a title="Ir a Cliente" href="{{ url('clientes',$sales->clientes_id) }}" target="_blank"> {{ $sales->clientes_id }} </a></td>
                                <td>{{ $sales->cons }} </td>
                                <td>{{ $sales->Day }} </td>
                
                            </tr>
                        @endforeach
                
                        </tbody>
                    </table>
                <div>
            </div>

            <div class="col-md-12">

              <ul class="pager">
                {{ $datos->appends(
                  [
                    'column' => app('request')->input('column'),
                    'word' => app('request')->input('word'),
                  ]
                  )->render() }}
              </ul>

            </div>
        </div>
    </div>

@endsection