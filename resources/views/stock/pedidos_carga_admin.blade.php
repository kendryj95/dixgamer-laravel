@extends('layouts.master-layouts')

@section('title', 'Pedidos Carga - Admin')

@section('container')

@push('css')

<style>
    .popover-content {
        word-wrap: break-word !important;
    }
</style>

@endpush


<div class="container">
	<h1>Pedidos Carga - Admin</h1>
    @if (count($errors) > 0)
          <div class="alert alert-danger text-center">
            <ul>
              @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
              @endforeach
            </ul>
          </div>
    @endif

	<div class="row">
        
        <div class="col-lg-12">
            <div class="table-responsive">
                <table border="0" align="center" cellpadding="0" cellspacing="5" class="table table-striped">
                    <thead>
                    <tr>
                        <th>Cantidad</th>
                        <th>Titulo</th>
                        <th>Usuarios</th>
                        <th>Notas</th>
                        <th>Acciones</th>
                    </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <form action="{{ url('asignar_stock') }}" method="post">
                            {{ csrf_field() }}
                            <td>
                                <input class="form-control input-sm" type="number" name="cantidad" id="cantidad" autocomplete="off" spellcheck="false" placeholder="Cantidad" autofocus>
                            </td>
                            <td>
                                <select name="titulo-select" class="selectpicker form-control input-sm" onchange="formatTitleAndGetData()" data-live-search="true" data-size="5" id="titulo-select">
                                  <option value="">Seleccione Titulo</option>
                                  @foreach($titles as $t)
                                   <option value="{{ explode(" (",$t->nombre_web)[0] }}">{{ str_replace('-', ' ', $t->nombre_web) }}</option>
                                  @endforeach
                                </select>
                                <input type="hidden" name="consola" id="consola">
                                <input type="hidden" name="titulo" id="titulo">
                            </td>
                            <td style="max-width: 300px">
                                <select style="width: 100%; margin: 0" name="usuarios[]" id="users-select" value="" class="form-control select2-multiple" multiple>
                                    @foreach($users as $user)
                                    <option value="{{ $user->Nombre }}">{{ $user->Nombre }}</option>
                                    @endforeach
                                </select>
                            </td>
                            <td>
                                <input type="text" class="form-control input-sm" name="Notas">
                            </td>
                            <td>
                                <button type="submit" class="btn btn-primary btn-sm" title="Guardar"><i class="fa fa-save"></i></button>
                            </td>
                            </form>
                        </tr>
                    @if(count($pedidos) > 0)
                        @foreach($pedidos as $i => $pedido)
                        <tr id="datos-{{$i}}">
                            <td>{{ $pedido->cantidad }}</td>
                            <td>
                                {{ $pedido->titulo }}
                                <span class="label label-default {{$pedido->consola}}">
                                    {{$pedido->consola}}
                                </span>
                            </td>
                            <td>
                                {{ $pedido->usuarios_pedido }}
                            </td>
                            <td style="text-align: center;">
                                <div class="alert alert-warning" style="color: #8a6d3b; background-color:#FFDD87; padding: 4px 7px; font-size: 12px; font-style:italic; margin:0px; opacity: 0.9;"><i class="fa fa-comment fa-fw"></i> {!! $pedido->Notas !!} </div>
                            </td>
                            <td>
                                <a class="btn btn-info btn-sm" href="#" title="Editar" onclick="editar('{{$pedido->ids}}','{{$i}}')"><i class="fa fa-pencil"></i></a> <a href="{{ url('confirmar_pedido', $pedido->ids) }}" class="btn btn-success btn-sm" title="Confirmar"><i class="fa fa-check"></i></a>
                            </td>
                        </tr>
                        {{-- Fila de edición --}}
                        <tr style="display: none;" id="form-edit{{ $i }}">
                            <form action="{{ url('asignar_stock') }}" method="post">
                            {{ csrf_field() }}
                            <td>
                                <input class="form-control input-sm" type="number" value="{{ $pedido->cantidad }}" name="cantidad" id="cantidad{{$i}}" autocomplete="off" spellcheck="false" placeholder="Cantidad" autofocus>
                                
                                <small>Antes: {{ $pedido->cantidad }}</small>
                            </td>
                            <td>
                                <select name="titulo-select" class="selectpicker form-control input-sm" onchange="formatTitleAndGetData('edit', '{{$i}}')" data-live-search="true" data-size="5" id="titulo-select{{$i}}">
                                  <option value="">Seleccione Titulo</option>
                                  @foreach($titles as $t)
                                    @php
                                    $titulo_original = $pedido->titulo . " ($pedido->consola)";
                                    $selected = $titulo_original == $t->nombre_web ? 'selected' : '';
                                    @endphp
                                   <option value="{{ explode(" (",$t->nombre_web)[0] }}" {{ $selected }}>{{ str_replace('-', ' ', $t->nombre_web) }}</option>
                                  @endforeach
                                </select>
                                <small>Antes: {{ $pedido->titulo }} ({{$pedido->consola}})</small>
                                <input type="hidden" name="consola" value="{{ $pedido->consola }}" id="consola{{$i}}">
                                <input type="hidden" name="titulo" value="{{ $pedido->titulo }}" id="titulo{{$i}}">
                                <input type="hidden" name="ids" value="{{ $pedido->ids }}">
                            </td>
                            <td style="max-width: 300px">
                                <select style="width: 100%; margin: 0" name="usuarios[]" id="users-select{{$i}}" value="" class="form-control select2-multiple" multiple>
                                    @php $usuarios = explode(',',$pedido->usuarios_pedido); @endphp
                                    @foreach($users as $user)
                                    @php $selected = in_array($user->Nombre,$usuarios) ? 'selected' : ''; @endphp
                                    <option value="{{ $user->Nombre }}" {{ $selected }}>{{ $user->Nombre }}</option>
                                    @endforeach
                                </select>
                                <small>Antes: {{ $pedido->usuarios_pedido }}</small>
                            </td>
                            <td>
                                <input type="text" class="form-control input-sm" value="{{ $pedido->Notas }}" name="Notas" id="Notas{{$i}}">
                                <small>Antes: {{ $pedido->Notas }}</small>
                            </td>
                            <td>
                                <button type="submit" class="btn btn-primary btn-sm" title="Editar"><i class="fa fa-pencil"></i></button>
                            </td>
                            </form>
                        </tr>
                        @endforeach
                    @else
                    <tr>
                        <td colspan = '5' class="text-center">No se encontraron datos</td>
                    </tr>
                    @endif
                    </tbody>
                </table>
                
            </div>
        </div>
    </div>

    <div class="row">
        
        <div class="col-lg-12">
            <h1>Pedidos Finalizados</h1>
            <div class="table-responsive">
                <table border="0" align="center" cellpadding="0" cellspacing="5" class="table table-striped">
                    <thead>
                    <tr>
                        <th>Cantidad</th>
                        <th>Titulo</th>
                        <th>Usuarios</th>
                        <th>Notas</th>
                    </tr>
                    </thead>
                    <tbody>
                        
                    @if(count($pedidos_finalizados) > 0)
                        @foreach($pedidos_finalizados as $i => $pedido)
                        <tr>
                            <td>{{ $pedido->cantidad }}</td>
                            <td>
                                {{ $pedido->titulo }}
                                <span class="label label-default {{$pedido->consola}}">
                                    {{$pedido->consola}}
                                </span>
                            </td>
                            <td>
                                {{ $pedido->usuarios_pedido }}
                            </td>
                            <td style="text-align: center;">
                                <div class="alert alert-warning" style="color: #8a6d3b; background-color:#FFDD87; padding: 4px 7px; font-size: 12px; font-style:italic; margin:0px; opacity: 0.9;"><i class="fa fa-comment fa-fw"></i> {!! $pedido->Notas !!} </div>
                            </td>
                        </tr>
                        @endforeach
                    @else
                    <tr>
                        <td colspan = '4' class="text-center">No se encontraron datos</td>
                    </tr>
                    @endif
                    </tbody>
                </table>
                
            </div>
        </div>
    </div>


</div><!--/.container-->



@endsection

@section('scripts')
<script type="text/javascript">

  $(document).ready(function() {
    $('.notas').popover();
    setTimeout(function(){
      initSelect2();
    },200);
  });

  function formatTitleAndGetData(accion = 'new', pos = null)
  {
    if (accion === 'new') {
        var select = document.getElementById('titulo-select');

        var consola = select.options[select.selectedIndex].text;

        var index = consola.indexOf("(");

        consola = consola.substring(index+1);

        consola = (consola.replace(")","")).replace(" ","-");

        document.getElementById('consola').value = consola.trim();
        document.getElementById('titulo').value = select.value;
    } else{
        var select = document.getElementById('titulo-select'+pos);

        var consola = select.options[select.selectedIndex].text;

        var index = consola.indexOf("(");

        consola = consola.substring(index+1);

        consola = (consola.replace(")","")).replace(" ","-");

        document.getElementById('consola'+pos).value = consola.trim();
        document.getElementById('titulo'+pos).value = select.value;
    }
  }

  function initSelect2(pos = ''){
    $( "#users-select"+pos).select2({
        theme: "bootstrap"
    });
  }

  function editar(id, pos) {
    $('#datos-'+pos).hide();
    $('#form-edit'+pos).show();
    initSelect2(pos);
  }
</script>
@stop
