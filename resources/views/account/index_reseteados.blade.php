@extends('layouts.master-layouts')

@section('title', 'Listar Cuentas Reseteadas')

@section('container')


<div class="container">
	<h1>Listar Cuentas Reseteadas</h1>
<div class="row">
    <form action="{{ url('cuentas_reseteadas') }}" method="get" class="form-inline">
        <div class="form-group col-md-3">
            <label for="fecha_ini">Fecha Inicio:</label>
            <input type="date" name="fecha_ini" id="fecha_ini" value="{{ $fecha_ini}}" class="form-control">
        </div>

        <div class="form-group col-md-3">
            <label for="fecha_fin">Fecha Fin:</label>
            <input type="date" name="fecha_fin" id="fecha_fin" value="{{ $fecha_fin}}" class="form-control">
        </div>

        <div class="form-group col-md-3">
            <label for="usuario">Usuario:</label>
            <select name="usuario" id="usuario" class="form-control">
                <option value="">Seleccione usuario</option>
                @foreach($usuarios as $usuario)
                <option value="{{ $usuario->usuario }}" @if(app('request')->input('usuario') == $usuario->usuario) selected @endif>{{ $usuario->usuario }}</option>
                @endforeach
            </select>
        </div>

        <div class="form-group col-md-1">
          <label for="palabra">&nbsp;</label> <br>
          <button type="submit" value="Buscar" name="enviar" class="btn btn-default">Buscar</button>

        </div>

        <div style="padding: 30px 0;" class="form-group col-md-2">
            <span style="font-weight: bold" class="pull-right">
            Total: <span class="label label-success">{{$total_reseteados}}</span>
        </span>
        </div>
    </form>
</div>
	<div class="row">

        <div class="table-responsive">
            <table border="0" align="center" cellpadding="0" cellspacing="5" class="table table-striped">
                <thead>
                <tr>
                    <th>#</th>
                    <th style="width: 100px">Fecha</th>
                    <th>Cuenta</th>
                    <th>Cantidad</th>
                    <th>Usuario</th>
                </tr>
                </thead>
                <tbody>
                @if(count($reseteados) > 0)
                    @foreach($reseteados as $i => $value)
                    <tr>
                        <td style="vertical-align: middle;">
                            {{ $i + 1 }}
                        </td>
                        <td style="vertical-align: middle;">
                            @php
                            $dia = date('d', strtotime($value->Day));
                            $mes = date('n', strtotime($value->Day));
                            $mes = \Helper::getMonthLetter($mes);
                            $anio = date('Y', strtotime($value->Day));
                            $fecha = "$dia-$mes-$anio";
                            @endphp
                            {{ $fecha }}
                        </td>
                        <td style="vertical-align: middle;">
                            @php
                                $array = explode(',', $value->cuentas_id);
                            @endphp
                            @foreach($array as $j => $valor)

                                <a href="{{ url('cuentas',$valor) }}" target="_blank">{{ $valor }}</a>@if($j != (count($array) - 1)), @endif
                            @endforeach
                        </td>
                        <td style="vertical-align: middle;">
                            {{ $value->Q }}
                        </td>
                        <td style="vertical-align: middle;">
                            <span class="label label-{{$value->color_user}}" title="{{ $value->usuario_real }}"><strong>{{strtoupper(substr($value->usuario_real,0,1))}}</strong></span>
                        </td>
                    </tr>
                    @endforeach
                @else
                    <td colspan = '5' class="text-center">No se encontraron datos</td>
                @endif
                </tbody>
            </table>

            <div class="col-md-12">

              <ul class="pager">
                {{ $reseteados->appends(
                    [
                      'fecha_ini' => app('request')->input('fecha_ini'),
                      'fecha_fin' => app('request')->input('fecha_fin'),
                      'usuario' => app('request')->input('usuario')
                    ]
                    )->render() }}
              </ul>

            </div>
            
        </div>
    </div>


</div><!--/.container-->



@endsection
