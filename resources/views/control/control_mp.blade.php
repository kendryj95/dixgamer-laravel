@extends('layouts.master-layouts')
@section('title', 'Control Mercado Pago')

@section('container')

@if (count($errors) > 0)
    <div class="alert alert-danger text-center">
      <ul>
        @foreach ($errors->all() as $error)
          <li>{{ $error }}</li>
        @endforeach
      </ul>
    </div>
@endif

    <div class="container">
	     <h1>Control Mercado Pago</h1>

        @if(count($conceptos) > 0)

          @foreach($conceptos as $value)
            <span class="label label-danger">El concepto <strong>{{ $value->concepto }}</strong> no está filtrado para control</span> <br>
          @endforeach

        @endif 

        <div class="row">
          <div class="col-lg-12 col-md-12 col-sm-12">

            <h4>Cobros MP con pareja en BD</h4> 
            
            <table class="table table-striped">
              
                <thead>
                  <tr>
                    <th>#</th>
                    <th title="número de movimiento" style="width: 120px">N° de Mov</th>
                    <th title="concepto">Concepto</th>
                    <th title="Importe MP">MP</th>
                    <th title="Importe DB">DB</th>
                    <th title="diferencia">Diferencia</th>
                    <th title="referencia">Ref</th>
                    <th><i class="fa fa-shopping-bag fa-fw" aria-hidden="true"></i></th>
                    <th><i class="fa fa-user fa-fw" aria-hidden="true"></i></th>
                  </tr>
                </thead>

                <tbody>

                  @foreach($cobros_mp_pareja as $i => $value)
                    <tr>
                      <td>{{ $i + 1 }}</td>
                      <td>
                        <small>
                          @php
                            $array = explode(',', $value->nro_mov, 10);
                          @endphp
                          @foreach($array as $valor)
                            {{ $valor }} {{--<a class='btn-xs' type='button' href="{{ url('control_mp_baja', $valor) }}"><i aria-hidden='true' class='fa fa-trash-o'></i></a>--}}<a class='btn-xs mov{{trim($valor)}}' type='button' href="javascript:void(0)" onclick="eliminarMovimiento({{trim($valor)}})"><i aria-hidden='true' class='fa fa-trash-o'></i></a><br />
                          @endforeach
                        </small>
                      </td>
                      <td>
                        <small>
                          @php
                            $array2 = explode(',', $value->concepto, 10);
                          @endphp
                          @foreach($array2 as $valor)
                            {{$valor}} <br>
                          @endforeach
                        </small>
                      </td>
                      <td><small>{{ round($value->imp_mp) }}</small></td>
                      <td><small>{{ round($value->imp_db) }}</small></td>
                      <td>
                        @php
                          $color = round($value->dif) > 0.01 ? 'success' : 'danger';
                        @endphp
                        <span style="font-size:0.9em" class="badge badge-{{$color}}">
                          {{ round($value->dif) }}
                        </span> <br>
                        <a href="{{ url('control_mp_baja_envio', [$value->dif, $value->ref_op]) }}" title="Anular ingreso por envío" class="btn-xs"><i class="fa fa-truck" aria-hidden="true"></i></a> <a href="{{ url('control_mp_crear_venta_cero', [$value->ref_op, $value->imp_mp, $value->clientes_id]) }}"><i class="fa fa-shopping-bag fa-fw" aria-hidden="true"></i></a>
                      </td>
                      <td><a href="https://www.mercadopago.com.ar/activities?q={{ $value->ref_op }}" target="_blank">{{ $value->ref_op }}</a></td>
                      <td><a href="{{ url('clientes', $value->clientes_id) }}" target="_blank">{{ $value->clientes_id }}</a></td>
                      <td>
                        <small>
                          @php $array = explode(',', $value->ventas_id, 10); @endphp
                          @if(count($array) < 2)
                            <a href="{{ url('control_mp_actualizar_importes', $value->ref_op) }}" class="btn-xs"><i class="fa fa-refresh" aria-hidden="true"></i></a>
                          @endif
                          @foreach($array as $valor)
                            {{ $valor }} <a href="{{ url('sales/list') }}?column=ID&word={{$valor}}" class="btn btn-default btn-xs" target="_blank"><i class="fa fa-search"></i></a>
                          @endforeach
                        </small>
                      </td>
                    </tr>
                  @endforeach
                  
                </tbody>


            </table>

            <h4>Cobros BD con pareja en MP</h4>

            <table class="table table-striped">
                <thead>
                  <tr>
                    <th>#</th>
                    <th title="número de movimiento">N° de Mov</th>
                    <th title="concepto">Concepto</th>
                    <th title="Importe MP">MP</th>
                    <th title="Importe DB">DB</th>
                    <th title="diferencia">Diferencia</th>
                    <th title="referencia">Ref</th>
                    <th><i class="fa fa-shopping-bag fa-fw" aria-hidden="true"></i></th>
                    <th><i class="fa fa-user fa-fw" aria-hidden="true"></i></th>
                  </tr>
              </thead>
              <tbody>
                @foreach($cobros_bd_pareja as $i => $value)
                <tr>
                  <td>{{ $i + 1 }}</td>
                  <td>
                    <small>
                      @php $array = explode(',',$value->nro_mov, 10); @endphp
                      @foreach($array as $valor)
                        {{ $valor }} {{--<a class='btn-xs' type='button' href="{{ url('control_mp_baja', $valor) }}"><i aria-hidden='true' class='fa fa-trash-o'></i></a>--}}<a class='btn-xs mov{{trim($valor)}}' type='button' href="javascript:void(0)" onclick="eliminarMovimiento({{trim($valor)}})"><i aria-hidden='true' class='fa fa-trash-o'></i></a><br />
                      @endforeach
                    </small>
                  </td>
                  <td>
                    <small>
                      @php $array2 = explode(',',$value->concepto,10); @endphp
                      @foreach($array2 as $valor)
                        {{ $valor }} <br>
                      @endforeach
                    </small>
                  </td>
                  <td>
                    <small>{{ round($value->imp_mp) }}</small>
                  </td>
                  <td>
                    <small>{{ round($value->imp_db) }}</small>
                  </td>
                  <td>
                    @php
                      $color = round($value->dif) > 0.01 ? 'success' : 'danger';
                    @endphp
                    <span style="font-size: 0.9em;" class="badge badge-{{$color}}">{{ $value->dif }}</span> <br>
                    <a href="{{ url('control_mp_baja_envio', [$value->dif, $value->ref_op]) }}" title="Anular ingreso por envío" class="btn-xs"><i class="fa fa-truck" aria-hidden="true"></i></a> <a href="{{ url('control_mp_baja_slot_libre', [$value->dif, $value->ref_op]) }}" title="Anular ingreso por no activación de slot" class="btn-xs"><i class="fa fa-money" aria-hidden="true"></i></a> <a href="{{ url('control_mp_crear_venta_cero', [$value->ref_op, $value->imp_mp, $value->clientes_id]) }}" title="Crear venta con producto 0 reflejando el ingreso sin costo" class="btn-xs"><i class="fa fa-shopping-bag fa-fw" aria-hidden="true"></i></a>
                  </td>
                  <td><a href="https://www.mercadopago.com.ar/activities?q={{ $value->ref_op }}" target="_blank">{{ $value->ref_op }}</a></td>
                  <td>
                    <small>{{ $value->ventas_id }}</small>
                  </td>
                  <td><a href="{{ url('clientes', $value->clientes_id) }}" target="_blank">{{ $value->clientes_id }}</a></td>
                </tr>
                @endforeach
              </tbody>
            </table>

            <h4>Cobros BD sin pareja en MP</h4> 

            <table class="table table-striped">
                <thead>
                  <tr>
                    <th>#</th>
                    <th title="número de movimiento">db</th>
                    <th title="referencia">Ref</th>
                    <th><i class="fa fa-shopping-bag fa-fw" aria-hidden="true"></i></th>
                    <th><i class="fa fa-user fa-fw" aria-hidden="true"></i></th>
                  </tr>
                </thead>
                <tbody>
                  @foreach($cobros_bd_sin_pareja as $i => $value)
                    <tr>
                      <td>{{ $i + 1 }}</td>
                      @php
                        $color = round($value->imp_db) > 0.01 ? 'danger' : 'success';
                      @endphp
                      <td><span style="font-size:0.9em" class="badge badge-{{$color}}">{{ $value->imp_db }}</span></td>
                      <td><a href="https://www.mercadopago.com.ar/activities?q={{ $value->ref_cobro }}" target="_blank">{{ $value->ref_cobro }}</a></td>
                      <td>{{ $value->ventas_id }}</td>
                      <td><a href="{{ url('clientes', $value->clientes_id) }}" target="_blank">{{ $value->clientes_id }}</a></td>
                    </tr>
                  @endforeach
                </tbody>
            </table>

            <h4>Cobros MP sin pareja en BD</h4>

            <table class="table table-striped">
                <thead>
                  <tr>
                    <th>#</th>
                    <th title="número de movimiento">N° de Mov</th>
                    <th title="concepto">Concepto</th>
                    <th title="Importe MP">MP</th>
                    <th title="referencia">Ref</th>
                  </tr>
                </thead>
                <tbody>
                  @foreach($cobros_mp_sin_pareja as $i => $value)
                    <tr>
                      <td>{{ $i + 1 }}</td>
                      <td>
                        <small>
                          @php $array = explode(',',$value->nro_mov,10); @endphp
                          @foreach($array as $valor)
                            {{ $valor }} {{--<a class='btn-xs' type='button' href="{{ url('control_mp_baja', $valor) }}"><i aria-hidden='true' class='fa fa-trash-o'></i></a>--}}<a class='btn-xs mov{{trim($valor)}}' type='button' href="javascript:void(0)" onclick="eliminarMovimiento({{trim($valor)}})"><i aria-hidden='true' class='fa fa-trash-o'></i></a><br />
                          @endforeach
                        </small>
                      </td>
                      <td>
                        <small>
                          @php $array2 = explode(',',$value->concepto,10); @endphp
                          @foreach($array2 as $valor)
                            {{ $valor }} <br>
                          @endforeach
                        </small>
                      </td>
                      <td>
                        @php
                          $color = round($value->imp_mp) > 0.01 ? 'success' : 'danger';
                        @endphp
                        <span style="font-size: 0.9em" class="badge badge-{{$color}}">{{ round($value->imp_mp) }}</span>
                      </td>
                      <td>
                        <a href="https://www.mercadopago.com.ar/activities?q={{ $value->ref_op }}" target="_blank">{{ $value->ref_op }}</a>
                      </td>
                    </tr>
                  @endforeach
                </tbody>
            </table>

          </div>
        </div>      
    </div><!--/.container-->

    <script>
      
      function eliminarMovimiento(valor)
      {
        $.ajax({
          url: '{{url("control_mp_baja")}}/'+valor,
          type: 'GET',
          dataType: 'json',
          success: function(response) {
            if (response.status == 1) {
              let html = '<span class="label label-danger" title="Movimiento eliminado">Elim.</span>';

              $('.mov'+valor).replaceWith(html);
            } else {
              alert('Ha ocurrido un error inesperado durante el proceso de eliminación.');
            }
          },
          error: function(error) {
            alert('Ha ocurrido un error inesperado');
          }
        });
        
      }

    </script>




@endsection