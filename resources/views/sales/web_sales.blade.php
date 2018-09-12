@extends('layouts.master-layouts')

@section('title', 'Pedidos Cobrados')

@section('container')

    <div class="container">
        <h1>Pedidos Cobrados</h1>

        @if (count($errors) > 0)
            <div class="alert alert-danger text-center">
                <ul>
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        @if (isset($_GET['r']) && isset($_GET['c']) && isset($_GET['u']))
            <div class="alert alert-danger text-center">
                <ul>
                    <li>Este pedido ya fue asignado por {{ $_GET['u'] }} <a href="{{ url('clientes', $_GET['c']) }}" class="alert-link" target="_blank">Ver venta</a></li>
                </ul>
            </div>
        @endif

        <br />

        @if ($filtros)
        <br>


        <a href="{{url('web/sales/list')}}" class="btn btn-secondary" style="margin-bottom: 10px">Limpiar filtros</a>

        @endif

        <br>

        <table class="table table-striped" border="0" cellpadding="0" cellspacing="5">
            <tr>
                <th width="50">ID</th>
                <th width="50">Cover</th>
                <th title="Titulo">Titulo</th>
                <th title="Cliente">Cliente</th>
            </tr>

            @foreach($row_rsAsignarVta as $ventasweb)

                    <tr height="90">

                        <td id="{{ $ventasweb->order_item_id  }}"><input type="hidden" id="verificaCliente" value="prueba@cliente.com"><span class="label label-default" style="opacity:0.7;">pedido #{{ $ventasweb->order_id }}</span><a target="_blank" href="https://dixgamer.com/wp-admin/post.php?post={{ $ventasweb->order_id }}&action=edit" class="text-muted btn-xs" title="ver pedido en la adm del sitio"><i class="fa fa-external-link" aria-hidden="true"></i> </a><br /><br /><span class="label label-normal" style="font-weight:400; opacity:0.5;">order_item_id #{{ $ventasweb->order_item_id  }}</span></td>
                        <td><img class="img-rounded" width="50" id="image-swap" src="{{asset('img/productos')}}/{{ $ventasweb->consola }}/{{ $ventasweb->producto.'.jpg' }} "alt="" /></td>
                        <td title="{{ str_replace('-', ' ', $ventasweb->producto)  }}({{ $ventasweb->consola }})">{{ str_replace('-', ' ', $ventasweb->producto)  }} ({{ $ventasweb->consola }})

                            @if($ventasweb->cliente_email)
                            
                            <a title="Asignar" class="btn btn-warning btn-xs" type="button" href="{{ url('salesInsertWeb', [$ventasweb->order_item_id, $ventasweb->producto, $ventasweb->consola, ucwords($ventasweb->slot)]) }}"><i class="fa fa-plus" aria-hidden="true"></i> asignar</a>
                            @endif
                            @if($ventasweb->slot == 'secundario')
                            <span class="label label-danger" style="opacity:0.7">2°</span>
                            @endif

                            <br /><br />
                            @if(strpos($ventasweb->_payment_method_title,'Plataforma') !== false)
                                <?php $color = 'primary'; ?>
                                <?php $text = 'MP'; ?>
                                <span class="label label-{{ $color }}" style="font-weight:400; opacity:0.7;">{{ $text }}</span>
                            @elseif(strpos($ventasweb->_payment_method_title,'Tarjeta') !== false)
                                <?php $color = 'primary'; ?>
                                <?php $text = 'MP'; ?>
                                <span class="label label-{{ $color }}" style="font-weight:400; opacity:0.7;">{{ $text }}</span>
                            @elseif(strpos($ventasweb->_payment_method_title,'Transferencia') !== false)
                                <?php $color = 'default'; ?>
                                <?php $text = 'Bco'; ?>
                                <span class="label label-{{ $color }}" style="font-weight:400; opacity:0.7;">{{ $text }}</span>
                            @elseif(strpos($ventasweb->_payment_method_title,'Ticket') !== false)
                                <?php $color = 'success'; ?>
                                <?php $text = 'Cash'; ?>
                                <span class="label label-{{ $color }}" style="font-weight:400; opacity:0.7;">{{ $text }}</span>
                            @elseif(strpos($ventasweb->_payment_method_title,'_card') !== false)
                                <?php $color = 'primary'; ?>
                                <?php $text = 'MP'; ?>
                                <span class="label label-{{ $color }}" style="font-weight:400; opacity:0.7;">{{ $text }}</span>
                            @elseif(strpos($ventasweb->_payment_method_title,'_card') == false)
                                <?php $color = 'success'; ?>
                                <?php $text = 'Cash'; ?>
                                <span class="label label-{{ $color }}" style="font-weight:400; opacity:0.7;">{{ $text }}</span>
                            @endif


                        </td>

                        <td>
                        @if($ventasweb->cliente_email)
                            {{ $ventasweb->email }}
                            @if(strpos($ventasweb->cliente_auto, 're') !== false)
                                    <a type="button" target="_blank" href="{{ url('clientes', $ventasweb->cliente_ID) }}" class="btn btn-danger btn-xs"><i class="fa fa-user" aria-hidden="true"></i> Revendedor</a>
                            @else
                                    <a type="button" tarmget="_blank" href="{{ url('clientes', $ventasweb->cliente_ID) }}" class="btn btn-default btn-xs"><i class="fa fa-user" aria-hidden="true"></i> Cte</a>
                            @endif
                        @else
                            @if(strpos($ventasweb->email, 'mercadolibre.com') !== false)
                                <a type="button" target="_blank" href="clientes_insertar_web_email.php?order_id={{ $ventasweb->order_id }}" class="btn btn-secondary btn-xs" title="corregir email de ML"><i class="fa fa-pencil" aria-hidden="true"></i> Modificar email de ML</a>
                            @else
                                {{ $ventasweb->email }}
                                <a type="button" href="{{ url('createCustomerWeb',$ventasweb->order_item_id)  }}" class="btn btn-success btn-xs" title="agregar cliente a base de datos"><i class="fa fa-plus" aria-hidden="true"></i> cliente</a>
                            @endif

                        @endif
                            <br /><br />
                            @if($ventasweb->user_id_ml && $ventasweb->user_id_ml != '')
                                <a target="_blank" href="https://perfil.mercadolibre.com.ar/profile/showProfile?id={{ $ventasweb->user_id_ml }}&role=buyer" class="btn btn-primary btn-xs" type="submit" style="font-weight:400; opacity:0.6;"> <i class="fa fa-user" aria-hidden="true"></i></a>
                                {{ $ventasweb->nombre.' '.$ventasweb->apellido }}
                                <a target="_blank" href="https://myaccount.mercadolibre.com.ar/messaging/orders/{{ $ventasweb->order_id_ml }}" class="btn btn-warning btn-xs" type="submit" style="font-weight:400; opacity:0.6;"> <i class="fa fa-comments" aria-hidden="true"></i></a>
                                <a target="_blank" href="https://myaccount.mercadolibre.com.ar/sales/vop?orderId={{ $ventasweb->order_id_ml }}&role=buyer" class="btn btn-success btn-xs" type="submit"  style="font-weight:400; opacity:0.6;"> <i class="fa fa-shopping-bag" aria-hidden="true"></i></a>

                            @else
                                {{ $ventasweb->nombre.' '.$ventasweb->apellido }}
                            @endif

                        </td>

                    </tr>



            @endforeach


        </table>

    @if ($mostrar)

    <div class="col-md-12">

        <ul class="pager">
          <ul class="pagination">

            @php
                if (isset($_GET['pag'])) {
                  if ($_GET['pag'] != 1) {
                    $previous = intval($_GET['pag']) - 1;
                  } else {
                    $previous = 1;
                  }
                } else {
                  $previous = 1;
                }
            @endphp
                @if ($previous == 1 && (!isset($_GET['pag']) || $_GET['pag'] == 1))

                <li class="disabled"><span>«</span></li>
                @else
                <li><a href="{{url('web/sales/list')}}?pag={{ $previous }}">«</a></li>
                @endif 
               
               @if ($paginas >= 1 && $paginas <= 5)
                 @for ($i=0;$i<$paginas;$i++)
                   @php 
                   $active = isset($_GET['pag']) ? ($i+1) == $_GET['pag'] ? 'active' : "" : ($i+1) == 1 ? 'active' : ""; 
                   @endphp
                   <li class="{{ $active }}"><a href="{{url('web/sales/list')}}?pag=<?= $i+1 ?>"><?= $i+1 ?></a></li>
                 @endfor
               @else
                 @if (($paginaAct+4) <= ($paginas))
                   @for ($i=$paginaAct;$i<=($paginaAct+4);$i++)
                        @php
                            $active = isset($_GET['pag']) ? ($i) == $_GET['pag'] ? 'active' : "" : ($i) == 1 ? 'active' : "";
                        @endphp
                     <li class="{{ $active }}"><a href="{{url('web/sales/list')}}?pag=<?= $i ?>"><?= $i ?></a></li>
                   @endfor
                 @elseif ($paginaAct == $paginas)
                   @for ($i=($paginaAct-4);$i<=$paginas;$i++)
                        @php
                            $active = isset($_GET['pag']) ? ($i) == $_GET['pag'] ? 'active' : "" : ($i) == 1 ? 'active' : ""; 
                        @endphp
                     <li class="{{ $active }}"><a href="{{url('web/sales/list')}}?pag=<?= $i ?>"><?= $i ?></a></li>
                   @endfor
                 @else
                   @for ($i=$paginaAct;$i<=$paginas;$i++)
                         @php 
                            $active = isset($_GET['pag']) ? ($i) == $_GET['pag'] ? 'active' : "" : ($i) == 1 ? 'active' : ""; 
                         @endphp
                     <li class="{{ $active }}"><a href="{{url('web/sales/list')}}?pag=<?= $i ?>"><?= $i ?></a></li>
                   @endfor
                 @endif
                 @if (($paginaAct+4) < ($paginas))
                 <li class="disabled"><span>...</span></li>
                 <li><a href="{{url('web/sales/list')}}?pag=<?= $paginas ?>"><?= $paginas ?></a></li>
                 @endif
               @endif

                @php

                    if (isset($_GET['pag'])) {
                      if ($_GET['pag'] != $paginas) {
                        $next = intval($_GET['pag']) + 1;
                      } else {
                        $next = $paginas;
                      }
                    } else {
                      $next = 2;
                    }

                @endphp

                @if (($next == $paginas) || (!isset($_GET['pag']) && ($paginas-1)==0))

                <li class="disabled"><span>»</span></li>
                @else
                <li><a href="{{url('web/sales/list')}}?pag={{ $next }}" rel="next">»</a></li>
                @endif
            </ul>

        </ul>

    </div>
  @endif
        <div class="container">
            <div class="row">
                <!-- Large modal -->
                <div id="#modal" class="modal fade bs-example-modal-lg" tabindex="-1" role="dialog" aria-labelledby="myLargeModalLabel">
                    <div class="modal-dialog modal-lg" style="top:40px;">
                        <div class="modal-content">
                            <div class="modal-body" style="text-align:center;">
                                <iframe id="ifr" src="" onload="resizeIframe(this)" style="min-height: 550px; width:900px;border:0px;" ></iframe>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>


    <!--/row-->
        <!-- InstanceEndEditable -->
    </div>
@section('scripts')
    @parent
    <script>
        $( document ).ready(function() {
            var email = $('#verificaCliente').val();
            $.ajax({
                method: 'post',
                url: '{{url('getDataClientWebSales')}}',
                data: {'email':email},
                success: function (result) {
                    console.log(result);
                }
            });
        });
    </script>
@endsection
@endsection