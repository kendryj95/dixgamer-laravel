@extends('layouts.master-layouts')

@section('title', 'Listar Stocks Cargados')

@section('container')


<div class="container">
    <h1>Listar Stocks Cargados</h1>
<div class="row">
    <form action="{{ url('stocks_cargados') }}" method="get" class="form-inline">
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
                <option value="everybody" @if(app('request')->input('usuario') == "everybody") selected @endif>Todos</option>
                @foreach($usuarios as $usuario)
                <option value="{{ $usuario->usuario }}" @if(app('request')->input('usuario') == $usuario->usuario) selected @endif>{{ $usuario->usuario }}</option>
                @endforeach
            </select>
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
                    <th>Cover</th>
                    <th>Producto</th>
                    <th>Cuentas</th>
                    <th>Usuario</th>
                </tr>
                </thead>
                <tbody>
                @if(count($cargados) > 0)
                @php $total_Q = 0 @endphp
                    @foreach($cargados as $value)
                    @php $total_Q += $value->Q @endphp
                    <tr>
                        <td style="vertical-align: middle;">
                            {{ $value->Q }}
                        </td>
                        <td style="vertical-align: middle;">
                            <img class="img-rounded" width="50" id="image-swap" src="{{asset('img/productos')}}/{{ $value->consola }}/{{ $value->titulo.'.jpg' }} "alt="" />
                        </td>
                        <td style="vertical-align: middle;width: 250px">
                            {{ $value->titulo }}
                            <span class="label label-default {{$value->consola}}">
                                {{$value->consola}}
                            </span>
                        </td>
                        <td style="vertical-align: middle;">
                            @php $array = explode(',', $value->cuentas); @endphp
                            @if(count($array)>0)
                                @foreach ($array as $i => $valor)
                                    @if($valor != "")
                                    <a href="{{ url('cuentas', $valor) }}">{{ $valor }}</a>@if($i != (count($array) - 1)),&nbsp;@endif
                                    @else
                                        @php $array = explode(',', $value->stocks_id); @endphp
                                        {{ implode(', ', $array) }}
                                    @endif
                                @endforeach
                            @else
                                @php $array = explode(',', $value->stocks_id); @endphp
                                {{ implode(', ', $array) }}
                            @endif
                        </td>
                        <td style="vertical-align: middle;">
                            @php
                            $usuarios = explode(",",$value->usuario);
                            @endphp
                            @foreach($usuarios as $usuario)
                                <span class="label label-{{\Helper::userColor($usuario)}}" title="{{ $usuario }}"><strong>{{strtoupper(substr($usuario,0,1))}}</strong></span> / 
                            @endforeach
                        </td>
                    </tr>
                    @endforeach
                    <tr>
                        <td><b>{{ $total_Q }}</b></td>
                        <td colspan="4"></td>
                    </tr>
                @else
                    <td colspan = '5' class="text-center">No se encontraron datos</td>
                @endif
                </tbody>
            </table>
            
        </div>
    </div>


</div><!--/.container-->



@endsection
