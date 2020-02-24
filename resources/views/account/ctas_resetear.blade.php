@extends('layouts.master-layouts')

@section('title', 'Lista Cuentas Resetear')

@section('container')
    <div class="container">
        <h1>Lista Cuentas Resetear</h1>
        <!-- InstanceBeginEditable name="body" -->

        <div class="row">
            <div class="col-md-12">
                <div class="table-responsive">
                    <table border="0" align="center" cellpadding="0" cellspacing="5" class="table table-striped">
                        <thead>
                        <tr>
                            <th>#</th>
                            <th>Cta ID</th>
                            <th>Días Pedido</th>
                            <th>Días desde ultimo reseteo</th>
                        </tr>
                        </thead>
                        <tbody>
                        @foreach($cuentas as $i => $cta)
                            <tr>
                
                                <td>
                                    {{$cta->ID}}
                                </td>
                                <td><a title="Ir a Cuenta" href="{{ url('cuentas',$cta->cuentas_id) }}" target="_blank"> {{ $cta->cuentas_id }} </a></td>
                                <td>{{ $cta->Day_pedido }} </td>
                                <td>{{ $cta->Day_reset }} </td>
                
                            </tr>
                        @endforeach
                
                        </tbody>
                    </table>
                <div>
            </div>

        </div>
    </div>

@endsection
