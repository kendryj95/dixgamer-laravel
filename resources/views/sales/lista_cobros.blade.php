@extends('layouts.master-layouts')

@section('title', 'Listar Cobros')

@section('container')
    <div class="container">
        <h1>Listado de Cobros</h1>
        <!-- InstanceBeginEditable name="body" -->

        <div class="row">
            @component('components/filters/column_word')
                @slot('columns',$columns);
                @slot('path','sales/lista_cobro');
            @endcomponent
        </div>


        <div class="row">
            <div class="col-md-12">
                <div class="table-responsive">
                    <table border="0" align="center" cellpadding="0" cellspacing="5" class="table table-striped">
                        <thead>
                        <tr>
                            <th>#</th>
                            <th>Cliente</th>
                            <th>Ref. Cobro</th>
                            <th>Vendido por</th>
                            <th>Cobrado por</th>
                            <th>Precio</th>
                            <th>Comision</th>
                        </tr>
                        </thead>
                        <tbody>
                        @foreach($datos as $i => $sales)
                            <tr>
                
                                <td>
                                    {{$sales->id_venta_cobro}}
                                </td>
                                <td><a title="Ir a Cliente" href="{{ url('clientes',$sales->clientes_id) }}" target="_blank"> {{ $sales->cliente }} </a></td>
                                <td>{{ $sales->ref_cobro }} </td>
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