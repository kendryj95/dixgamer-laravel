@extends('layouts.master-layouts')

@section('title', 'Lista Cuentas Vacias')

@section('container')
    <div class="container">
        <h1>Lista Cuentas Vacias</h1>
        <!-- InstanceBeginEditable name="body" -->

        <div class="row">
            <div class="col-md-12">
                <div class="table-responsive">
                    <table border="0" align="center" cellpadding="0" cellspacing="5" class="table table-striped">
                        <thead>
                        <tr>
                            <th>#</th>
                            <th>Cuenta</th>
                            <th>Creado</th>
                            <th>Usuario</th>
                        </tr>
                        </thead>
                        <tbody>
                        @foreach($cuentas as $i => $cta)
                            <tr>
                
                                <td>
                                    <a title="Ir a Cuenta" href="{{ url('cuentas',$cta->ID) }}" target="_blank"> {{ $cta->ID }} </a>
                                </td>
                                <td><a title="Ir a Cuenta" href="{{ url('cuentas',$cta->ID) }}" target="_blank"> {{ $cta->mail_fake }} </a></td>
                                <td>
                                    @php
                                    $dia = date('d', strtotime($cta->reg_date));
                                    $mes = date('n', strtotime($cta->reg_date));
                                    $mes = \Helper::getMonthLetter($mes);
                                    $anio = date('Y', strtotime($cta->reg_date));
                                    $fecha = "$dia-$mes-$anio";
                                    @endphp
                                    
                                    <a title="Ir a cuenta." href="{{ url('/cuentas', [$cta->ID] ) }}">
                                    {{ $fecha }}
                                    </a>
                                </td>
                                <td><a title="Ir a Cuenta" href="{{ url('cuentas',$cta->ID) }}" target="_blank"> {{ $cta->usuario }} </a></td>
                
                            </tr>
                        @endforeach
                
                        </tbody>
                    </table>
                <div>
            </div>

        </div>
    </div>

@endsection
