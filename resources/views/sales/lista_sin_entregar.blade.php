@extends('layouts.master-layouts')

@section('title', 'Lista Venta sin Entregar')

<style>

    .bootstrap-tagsinput {
      width: 100%;
    }
    
    .tag.label {
      cursor: pointer;
    }
    
    </style>

@section('container')
    <div class="container">
        <h1>Listado de Ventas sin Entregar</h1>
        <!-- InstanceBeginEditable name="body" -->

        <div class="row">
            <form action="{{ url('config/general') }}" id="ventas_excluidas" method="post">
              <div class="col-md-8">
                  {{ csrf_field() }}
                  <input type="hidden" name="opt" value="7">
                  <div class="form-group">
                    <label for="">Ventas Excluidas:</label><br>
                    <input type="text" name="ventas_excluidas" id="cuentas" value="{{$ventas_excluidas}}" data-role="tagsinput">
                  </div>
                  
                  <div class="form-group">
                    <label for="">Clientes Excluidos:</label><br>
                    <input type="text" name="clientes_excluidos" id="clientes_excluidos" value="{{$clientes_excluidos}}" data-role="tagsinput">
                  </div>
              </div>
              <div class="col-md-4" style="padding-top: 35px">
                <button style="margin-top: 24px" type="submit" class="btn btn-primary">Actualizar Config</button>
              </div>
            </form>
          </div>

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

@section('scripts')
@parent

<script>
var band = true;
$(document).ready(function() {
  
  $("#ventas_excluidas").keypress(function(e) {
    if (e.which == 13) {
        return false;
    }
  });

  $('.bootstrap-tagsinput span.tag.label span[data-role="remove"]').on('click', function(event) {
    band = false; // Bandera que evita que haga un redirect si eliminan un tag
  })

  $('.bootstrap-tagsinput span.tag.label').on('click', function() {
      var el = $(this);
      var id_vta = el.text();

        if (band) {
            $.ajax({
                url: "{{ url('sales') }}/" + id_vta + "/cliente",
                type: "get",
                dataType: "json",
                success: function(response) {
                    if (response.id_cliente != 0) {
                        window.open("{{ url('clientes') }}/" + response.id_cliente, "_blank");
                    }
                },
                error: function(error) {
                    console.log("Error request");
                }
            })
        }
        band = true;
    })
  
})

</script>

@endsection