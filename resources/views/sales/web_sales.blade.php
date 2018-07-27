@extends('layouts.master-layouts')
@section('container')

    <div class="container">
        <h1>Pedidos Cobrados</h1>

        <br />

        <table class="table table-striped" border="0" cellpadding="0" cellspacing="5">
            <tr>
                <th width="50">ID</th>
                <th width="50">Cover</th>
                <th title="Titulo">Titulo</th>
                <th title="Cliente">Cliente</th>
            </tr>

            @foreach($row_rsAsignarVta as $ventasweb)

                    <tr height="90">
                        <td id="{{ $ventasweb->order_item_id  }}"><span class="label label-default" style="opacity:0.7;">pedido #{{ $ventasweb->order_id }}</span><a target="_blank" href="https://dixgamer.com/wp-admin/post.php?post={{ $ventasweb->order_id }}&action=edit" class="text-muted btn-xs" title="ver pedido en la adm del sitio"><i class="fa fa-external-link" aria-hidden="true"></i> </a><br /><br /><span class="label label-normal" style="font-weight:400; opacity:0.5;">order_item_id #{{ $ventasweb->order_item_id  }}</span></td>
                        <td><img class="img-rounded" width="50" id="image-swap" src="/img/productos/{{ $ventasweb->consola }}/{{ $ventasweb->producto.'.jpg' }} "alt="" /></td>
                        <td title="{{ str_replace('-', ' ', $ventasweb->producto)  }}({{ $ventasweb->consola }})">{{ str_replace('-', ' ', $ventasweb->producto)  }} ({{ $ventasweb->consola }})

                            @if($ventasweb->cliente_email)
                            <a title="Asignar" class="btn btn-info btn-xs" type="button" data-toggle="modal" data-target=".bs-example-modal-lg" onClick='document.getElementById("ifr").src="ventas_insertar_web.php?order_item_id={{ $ventasweb->order_item_id  }}&titulo={{ $ventasweb->producto }}&consola={{ $ventasweb->consola }}&slot={{ ucwords($ventasweb->slot) }}";'><i class="fa fa-plus" aria-hidden="true"></i> asignar</a>
                            <a title="Asignar" class="btn btn-warning btn-xs" type="button" href="ventas_insertar_web.php?order_item_id={{ $ventasweb->order_item_id  }}&titulo={{ $ventasweb->producto }}&consola={{ $ventasweb->consola }}&slot={{ ucwords($ventasweb->slot)  }}"><i class="fa fa-plus" aria-hidden="true"></i> asignar</a>
                            @endif
                            @if($ventasweb->slot == 'secundario')
                            <span class="label label-danger" style="opacity:0.7">2°</span>
                            @endif
                            @if(strpos($ventasweb->cliente_auto, 'si') !== false)
                            <a type="button" class="text-muted btn-xs text-danger" title="tiene historial favorable"><i class="fa fa-star" aria-hidden="true"></i></a>
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
                                    <a type="button" target="_blank" href="clientes_detalles.php?id={{ $ventasweb->cliente_ID }}" class="btn btn-danger btn-xs"><i class="fa fa-user" aria-hidden="true"></i> Revendedor</a>
                            @else
                                    <a type="button" target="_blank" href="clientes_detalles.php?id={{ $ventasweb->cliente_ID }}" class="btn btn-default btn-xs"><i class="fa fa-user" aria-hidden="true"></i> Cte</a>
                            @endif
                        @else
                            @if(strpos($ventasweb->email, 'mercadolibre.com') !== false)
                                <a type="button" target="_blank" href="clientes_insertar_web_email.php?order_id={{ $ventasweb->order_id }}" class="btn btn-danger btn-xs"><i class="fa fa-user" aria-hidden="true"></i> Revendedor</a>
                            @else
                                {{ $ventasweb->email }}
                                <a type="button" target="_blank" href="clientes_insertar_web.php?order_item_id={{ $ventasweb->order_item_id  }}" class="btn btn-default btn-xs"><i class="fa fa-user" aria-hidden="true"></i> Cte</a>
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

@endsection
@endsection