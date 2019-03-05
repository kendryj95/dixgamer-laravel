@extends('layouts.master-layouts')

@section('title', 'Listar Stocks Cargados')

@section('container')


<div class="container">
	<h1>Listar Stocks Cargados</h1>
<div class="row">
    <form action="{{ url('stocks_cargados') }}" method="get" class="form-inline">
        <div class="form-group col-md-4">
            <label for="fecha_ini">Fecha Inicio:</label>
            <input type="date" name="fecha_ini" id="fecha_ini" value="{{ $fecha_ini}}" class="form-control">
        </div>

        <div class="form-group col-md-4">
            <label for="fecha_fin">Fecha Fin:</label>
            <input type="date" name="fecha_fin" id="fecha_fin" value="{{ $fecha_fin}}" class="form-control">
        </div>

        <div class="form-group">
          <label for="palabra">&nbsp;</label> <br>
          <button type="submit" value="Buscar" name="enviar" class="btn btn-default">Buscar</button>

        </div>
    </form>
</div>
	<div class="row">
        <div class="table-responsive">
            <table border="0" align="center" cellpadding="0" cellspacing="5" class="table table-striped">
                <thead>
                <tr>
                    <th>Cantidad</th>
                    <th>Producto</th>
                    <th>Cuentas</th>
                    <th>Usuario</th>
                </tr>
                </thead>
                <tbody>
                @if(count($cargados) > 0)
                    @foreach($cargados as $value)
                    <tr>
                        <td style="vertical-align: middle;">
                            {{ $value->Q }}
                        </td>
                        <td style="vertical-align: middle;">
                            {{ $value->titulo }}
                            <span class="label label-default {{$value->consola}}">
                                {{$value->consola}}
                            </span>
                        </td>
                        <td style="vertical-align: middle;">
                            @php $array = explode(',', $value->cuentas); @endphp
                            @foreach ($array as $valor)
                                <a href="{{ url('cuentas', $valor) }}" target="_blank">{{ $valor }}</a> <br>
                            @endforeach
                        </td>
                        <td style="vertical-align: middle;">
                            <span class="label label-{{\Helper::userColor($value->usuario)}}" title="{{ $value->usuario }}"><strong>{{strtoupper(substr($value->usuario,0,1))}}</strong></span>
                        </td>
                    </tr>
                    @endforeach
                @else
                    <td colspan = '4' class="text-center">No se encontraron datos</td>
                @endif
                </tbody>
            </table>
            
        </div>
    </div>


</div><!--/.container-->



@endsection
