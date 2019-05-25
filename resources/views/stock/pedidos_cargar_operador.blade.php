@extends('layouts.master-layouts')

@section('title', 'Pedidos por Cargar')

@section('container')


<div class="container">
	<h1>Pedidos por Cargar - {{ session()->get('usuario')->Nombre }}</h1>

	<div class="row">
        
        <div class="col-lg-12">
            <div class="table-responsive">
                <table border="0" align="center" cellpadding="0" cellspacing="5" class="table table-striped">
                    <thead>
                    <tr>
                        <th>Cover</th>
                        <th>Titulo</th>
                        <th>Cantidad</th>
                        <th>Link/s</th>
                    </tr>
                    </thead>
                    <tbody>
                    @if(count($pedidos) > 0)
                        @foreach($pedidos as $pedido)
                        <tr>
                            <td>
                                <img class="img-rounded" width="50" id="image-swap" src="{{asset('img/productos')}}/{{ $pedido->consola }}/{{ $pedido->titulo.'.jpg' }} "alt="" />
                            </td>
                            <td>
                                {{ $pedido->titulo }}
                                <span class="label label-default {{$pedido->consola}}">
                                    {{$pedido->consola}}
                                </span>
                            </td>
                            <td>{{ $pedido->cantidad_cargar }}</td>
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
                        </tr>
                        @endforeach
                    @endif
                    </tbody>
                </table>
                
            </div>
        </div>
    </div>


</div><!--/.container-->



@endsection
