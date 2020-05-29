@extends('layouts.master-layouts')

@section('title', 'Pedidos por Cargar')

@section('container')


<div class="container">
	<h1>Pedidos por Cargar - {{ $user != '' ? $user : session()->get('usuario')->Nombre }}</h1>


    <div class="row" style="margin-bottom: 20px">
        <div class="col-md-12">

            @foreach($users as $value)

              <a
                class="btn @if($user == $value->usuario) btn-success @else btn-default @endif btn-sm"
                href="{{ url('pedidos_cargar', $value->usuario) }}"
                title="Filtrar {{$value->usuario}}"
                style="margin:5px 0 0 0;">

                {{$value->usuario}}

              </a>

            @endforeach
        </div>
    </div>


	<div class="row">
        
        <div class="col-lg-12">
            <div class="table-responsive">
                <table border="0" align="center" cellpadding="0" cellspacing="5" class="table table-striped">
                    <thead>
                    <tr>
                        @if (\Helper::validateAdministrator(session()->get('usuario')->Level))
                        <th style="width: 20px"></th>
                        @endif
                        <th class="text-center">
                          Cantidad<br>
                          Faltan / Pedido
                        </th>
                        <th>Cover</th>
                        <th>Titulo</th>
                        <th>Link/s</th>
                        <th></th>
                    </tr>
                    </thead>
                    <tbody>
                    @if(count($pedidos) > 0)
                        @php $total_cantidad = 0 @endphp
                        @php $total_pedido = 0 @endphp
                        @foreach($pedidos as $pedido)
                        @php $falta = $pedido->cantidad_cargar < 0 ? 0 : $pedido->cantidad_cargar @endphp
                        @php $total_cantidad += $falta  @endphp
                        @php $total_pedido += $pedido->cantidad  @endphp
                        <tr>
                            @if (\Helper::validateAdministrator(session()->get('usuario')->Level))
                              <td style="width: 20px" class="text-center">
                                <a href="{{ url('confirmar_pedido', $pedido->ID) }}" class="btn btn-success btn-xs" title="Confirmar"><i class="fa fa-check"></i></a>
                              </td>
                            @endif
                            <td class="text-center">
                                @if($falta > 0) @php $color = 'warning' @endphp @else @php $color = 'success' @endphp @endif
                                <span class="label label-{{$color}}">{{ $falta }}</span> / <span class="label label-secondary">{{ $pedido->cantidad }}</span>
                            </td>
                            <td>
                                <img class="img-rounded" width="50" id="image-swap" src="{{asset('img/productos')}}/{{ $pedido->consola }}/{{ $pedido->titulo.'.jpg' }} "alt="" />
                            </td>
                            <td>
                                {{ ucwords(str_replace('-',' ',$pedido->titulo)) }}
                                <span class="label label-default {{$pedido->consola}}">
                                    {{$pedido->consola}}
                                </span>
                            </td>
                            <td title="{{ $pedido->link_ps }}">
                              @if(($pedido->link_ps) && $pedido->link_ps !== "")
                                <?php $array = (explode(',', $pedido->link_ps, 10)); ?>
                                @if(count($array) > 0)

                                  @foreach($array as $valor)
                                    <a title='ver en la tienda de PS' target='_blank' href='{{ $valor }}'>
                                      <i aria-hidden='true' class='fa fa-external-link'></i>
                                      Tienda PS
                                    </a>
                                    <br />
                                  @endforeach

                                @endif

                              @else
                                <span class="label label-danger">NO HAY LINK</span>
                              @endif
                            </td>
                            <td style="text-align: center;">
                                <div class="alert alert-warning" style="color: #8a6d3b; background-color:#FFDD87; padding: 4px 7px; font-size: 12px; font-style:italic; margin:0px; opacity: 0.9;"><i class="fa fa-comment fa-fw"></i> {!! $pedido->Notas !!} </div>
                            </td>
                        </tr>
                        @endforeach
                        <tr>
                        @if (\Helper::validateAdministrator(session()->get('usuario')->Level))
                          <td style="width: 20px"></td>
                        @endif
                        <td class="text-center"><b>{{ $total_cantidad }}</b> / <b>{{ $total_pedido }}</b></td>
                            <td colspan="4"></td>
                        </tr>
                    @else
                    <tr>
                        <td colspan="5">No hay registros para mostrar.</td>
                    </tr>
                    @endif
                    </tbody>
                </table>
                
            </div>
        </div>
    </div>


</div><!--/.container-->



@endsection
