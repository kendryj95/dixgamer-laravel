@extends('layouts.master-layouts')

@section('title', 'Falta Cargar')

@section('container')


<div class="container">
	<h1>Falta Cargar</h1>
<div class="row">
    <form action="{{ url('falta_cargar') }}" method="get" class="form-inline">
        <div class="form-group col-md-3">
            <label for="dia">Titulo:</label>
            <select name="titulos[]" id="titulo-select" value="" class="form-control select2-multiple" multiple>
                @foreach($titulos as $titulo)
                @php
                $selected = '';
                if (in_array($titulo, $titulos_params)) {
                    $selected = 'selected';
                }
                @endphp
                <option value="{{ $titulo }}" {{ $selected }}>{{ str_replace("-", " ", $titulo) }}</option>
                @endforeach
            </select>
        </div>

        <div class="form-group col-md-1">
            <label for="dia">Días:</label>
            <input type="number" name="dia" id="dia" value="{{ $dia}}" class="form-control">
        </div>

        <div class="form-group">
          <label for="palabra">&nbsp;</label> <br>
          <button type="submit" class="btn btn-default">Buscar</button>

        </div>
    </form>
</div>
	<div class="row">
        <div class="table-responsive">
            <table border="0" align="center" cellpadding="0" cellspacing="5" class="table table-striped">
                <thead>
                <tr>
                    <th>Cover</th>
                    <th>Producto</th>
                    <th>Cant. Vta</th>
                    <th>Cant. Stock</th>
                    <th>Comprar</th>
                </tr>
                </thead>
                <tbody>
                @if(count($datos) > 0)
                    @foreach($datos as $value)
                    <tr>
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
                            {{ $value->Q_Vta }}
                        </td>
                        <td style="vertical-align: middle;">
                            {{ $value->Q_Stock }}
                        </td>
                        <td style="vertical-align: middle;">
                            {{ $value->Comprar }}
                        </td>
                    </tr>
                    @endforeach
                @else
                    <td colspan = '5' class="text-center">No se encontraron datos</td>
                @endif
                </tbody>
            </table>
            
        </div>
    </div>


</div><!--/.container-->




@endsection

@section('scripts')
<script>
    {{--var titulos = JSON.parse('{!!$titulos_params!!}');--}}
    $(document).ready(function() {
        $( "#titulo-select" ).select2({
            theme: "bootstrap"
        });

        // $( "#titulo-select" ).val(titulos).trigger('change');
    });
</script>
@stop