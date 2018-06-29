@extends('layouts.master-layouts')

@section('container')

  @if(count($customer) > 0)
    <div class="container">
    <h1>Cliente #{{$customer->ID}}</h1>


      <div class="row clientes_detalles">



        <div class="col-xs-12 col-sm-6 col-md-5">
          <div class="panel panel-info">

          <div class="panel-heading clearfix">
            <h4 style="margin:0px;">

              <i class="fa fa-user fa-fw" aria-hidden="true"></i>
              {{$customer->nombre}} {{$customer->apellido}}

              <button title="Modificar Nombre"
                class="btn btn-xs btn-default pull-right"
                type="button"
                data-toggle="modal"
                data-target=".bs-example-modal-lg"
                onClick='getPageAjax("/clientes/{{$customer->ID}}/edit","#modal-container");'>
              <i aria-hidden="true" class="fa fa-pencil"></i>
              </button>

            </h4>
          </div>

          <div class="panel-body" style="background-color: #efefef;">

            <p>
              <i class="fa fa-envelope-o fa-fw"></i>

              <a href="#" class="btn-copiador" data-clipboard-target="#email-copy">
                <span id="email-copy">{{$customer->email}}</span>
                <i aria-hidden="true" class="fa fa-clone"></i>
              </a>

              <a
                class="btn btn-xs btn-default"
                href="https://mail.google.com/a/dixgamer.com/#search/{{ substr($customer->email, 0, strpos($customer->email, '@')) }}; "
                title="filtrar cliente en gmail"
                target="_blank">
                  <i aria-hidden="true" class="fa fa-google"></i>
                  mail
              </a>

              <button
                title="Modificar E-mail"
                class="btn btn-xs btn-default pull-right"
                style="opacity: 0.5;"
                type="button"
                data-toggle="modal"
                data-target=".bs-example-modal-lg"
                onClick='document.getElementById("ifr").src="clientes_modificar_email.php?id={{$customer->ID}}";'>

                <i aria-hidden="true" class="fa fa-pencil"></i>
              </button>
            </p>

            @if($customer->ml_user)
              <p>
                <i class="fa fa-snapchat-ghost fa-fw"></i>
                {{$customer->ml_user}}
                <a
                  title="Modificar ML user"
                  class="btn-xs text-muted"
                  style="opacity: 0.7;"
                  type="button"
                  data-toggle="modal"
                  data-target=".bs-example-modal-lg"
                  onClick='document.getElementById("ifr").src="clientes_modificar_ml_user.php?id={{$customer->ID}}"'>
                    <i aria-hidden="true" class="fa fa-pencil"></i>
                </a>
              </p>
            @endif

            <p>
              <em class="text-muted" style="opacity:0.7; font-size:0.8em;">
                <i class="fa fa-map-marker fa-fw"></i>
                {{  Helper::notEmptyShow($customer->ciudad,$customer->ciudad.', ') }}

                {{ $customer->provincia }}

                {{  Helper::notEmptyShow($customer->cp,', '.$customer->cp) }}

              <a
                title="Modificar otros datos"
                class="btn-xs text-muted"
                style="opacity: 0.7;"
                type="button"
                data-toggle="modal"
                data-target=".bs-example-modal-lg"
                onClick='document.getElementById("ifr").src="clientes_modificar_extras.php?id={{$customer->ID}}";'>
                <i aria-hidden="true" class="fa fa-pencil"></i>
              </a>
            </em>

              @if(!empty($customer->tel) || !empty($customer->cel))
                <br >
                <em class="text-muted" style="opacity:0.7; font-size:0.8em;">
                  <i class="fa fa-phone fa-fw"></i>
                  {{  Helper::notEmptyShow($customer->carac,$customer->carac) }}

                  {{  Helper::notEmptyShow($customer->tel,$customer->tel) }}

                  @if(!empty($customer->tel) && !empty($customer->cel))
                     /
                  @endif

                   {{  Helper::notEmptyShow($customer->cel,$customer->cel) }}
                 </em>
               @endif
            </p>

            @if(!empty($customer->face))
              <p>
                <i class="fa fa-facebook fa-fw"></i>
                {{$customer->face}}
                <a
                  title="Modificar FB"
                  class="btn-xs text-muted"
                  style="opacity: 0.7;"
                  type="button"
                  data-toggle="modal"
                  data-target=".bs-example-modal-lg"
                  onClick=document.getElementById("ifr").src="clientes_facebook.php?id={{$customer->ID}}";>
                  <i aria-hidden="true" class="fa fa-pencil"></i>
                </a>
              </p>
            @endif

            <p>

            <button
              class="btn btn-warning btn-xs"
              style="color: #8a6d3b; background-color:#FFDD87; opacity: 0.7"
              type="button"
              data-toggle="modal"
              data-target=".bs-example-modal-lg"
              onClick='document.getElementById("ifr").src="clientes_notas_insertar.php?id=$customer->ID";'>
                <i class="fa fa-fw fa-comment"></i>
                Agregar Nota
            </button>


            @if(empty($customer->face))
              <button
                title="Agregar FB"
                class="btn btn-xs btn-info"
                type="button"
                data-toggle="modal"
                data-target=".bs-example-modal-lg"
                onClick='document.getElementById("ifr").src="clientes_facebook.php?id=$customer->ID";'>
                  <i class="fa fa-facebook fa-fw"></i> FB
              </button>
            @endif
            @if(empty($customer->ml_user))
              <button
                title="Agregar ML User"
                class="btn btn-xs btn-info"
                type="button"
                data-toggle="modal"
                data-target=".bs-example-modal-lg"
                onClick='document.getElementById("ifr").src="clientes_modificar_ml_user.php?id=$customer->ID";'>
                  <i class="fa fa-snapchat-ghost fa-fw"></i> ML
              </button>
            @endif

            @if(strpos($customer->auto, 're') !== false)
                <a type="button"

                  @if(Helper::validateAdministrator(Auth::user()->Level))
                    href="clientes_hacer_revendedor.php?id={{$customer->ID}}&a=no"
                  @endif
                    class="btn btn-danger btn-xs pull-right">
                    Revendedor
                </a>
            @else
              <a type="button"

                @if(Helper::validateAdministrator(Auth::user()->Level))
                  href="clientes_hacer_revendedor.php?id={{$customer->ID}}&a=re"
                @endif
                  class="btn btn-danger btn-xs pull-right">
                  Revendedor
              </a>
            @endif


            </p>

            @if(Helper::validateAdministrator(Auth::user()->Level))
              <div style="display: none;">
                <p>
                  <a
                    class="btn btn-primary"
                    data-toggle="collapse"
                    href="#collapseExample"
                    role="button"
                    aria-expanded="false"
                    aria-controls="collapseExample">
                      Link with href
                  </a>
                  <button
                    class="btn btn-primary"
                    type="button"
                    data-toggle="collapse"
                    data-target="#collapseExample"
                    aria-expanded="false"
                    aria-controls="collapseExample">
                      Button with data-target
                  </button>
                </p>

                <div class="collapse" id="collapseExample">
                  <div class="card card-body">
                  Anim pariatur cliche reprehenderit, enim eiusmod high life accusamus terry richardson ad squid. Nihil anim keffiyeh helvetica, craft beer labore wes anderson cred nesciunt sapiente ea proident.
                  </div>
                </div>

              </div>
            @endif
          </div>

          @if(count($customerNotes) > 0)
            <ul class="list-group">
              <li class="list-group-item" style="background-color: #efefef;">
                @foreach($customerNotes as $note)
                  <div
                    class="alert alert-warning"
                    style="color: #8a6d3b; background-color:#FFDD87; padding: 4px 7px; margin:0px; opacity: 0.9;">
                    {{$note->Notas}}
                  </div>

                  @if (($note->Day) > "2018-03-02")

                    <em
                      class="small text-muted pull-right"
                      style="opacity: 0.7">
                      <?php echo date("d M 'y", strtotime($note->Day)); ?>
                      ({{ $note->usuario }})
                    </em>
                    <br>
                  @endif
                @endforeach
              </li>
            </ul>
          @endif
          </div>
        </div>




        @if($customer->ID === "371" && !(Helper::validateAdministrator(Auth::user()->Level)))
        @else
          <!-- Si hay ventas de usuario -->
          <?php if(count($dataCustomers) > 0): ?>
            @foreach($dataCustomers as $dataCustomer)
            <?php $i = 3;?>
            <?php  $abrirdiv = array("5", "9", "13", "17", "21", "25", "29", "33", "37", "41", "45", "49");
                if (in_array($i, $abrirdiv)) {
                  echo "<div class='row " . $i . " ' style='display:-webkit-box;'>";
                }
            ?>

            <?php
              if (strpos($dataCustomer->medio_venta, 'Web') !== false): $text = 'W'; $color1 = 'info';
              elseif (strpos($dataCustomer->medio_venta, 'Mail') !== false): $text = 'M'; $color1 = 'danger';
              elseif (strpos($dataCustomer->medio_venta, 'Mercado') !== false): $text = 'ML'; $color1 = 'warning';
              endif;
            ?>

            <?php
              if (strpos($dataCustomer->medio_cobro, 'Transferencia') !== false): $text2 = 'Bco'; $color2 = 'default';
              elseif (strpos($dataCustomer->medio_cobro, 'Ticket') !== false): $text2 = 'Cash'; $color2 = 'success';
              elseif (strpos($dataCustomer->medio_cobro, 'Mercado') !== false): $text2 = 'MP'; $color2 = 'primary';
              elseif (strpos($dataCustomer->medio_cobro, 'Fondo') !== false): $text2 = 'F'; $color2 = 'normal';
              endif;
            ?>


            <?php
              $color = Helper::userColor($dataCustomer->usuario);
            ?>


          <div class="col-xs-12 col-sm-6 col-md-3" style="display: inline-flex">
            <div class=" thumbnail" <?php if ($dataCustomer->slot == 'Secundario'): ?>style="background-color:#efefef;"<?php endif; ?>>
              <span class="pull-right" style="width: 45%;">

                <p>
                  <span class="btn-group pull-right">
                    <button
                      title="Modificar Venta"
                      class="btn btn-xs btn-default"
                      type="button"
                      data-toggle="modal"
                      data-target=".bs-example-modal-lg"
                      onClick='document.getElementById("ifr").src="ventas_modificar.php?id=<?php echo $dataCustomer->ID_ventas; ?>&c_id=<?php echo $customer->ID ?>";'>
                              <i aria-hidden="true" class="fa fa-pencil"></i>
                    </button>
                    <button title="Modificar Venta" class="btn btn-xs btn-default" type="button" data-toggle="modal" data-target=".bs-example-modal-lg" onClick='document.getElementById("ifr").src="ventas_eliminar.php?id=<?php echo $dataCustomer->ID_ventas; ?>&c_id=<?php echo $customer->ID ?>";'>
                              <i aria-hidden="true" class="fa fa-trash-o"></i>
                    </button>
                  </span>
                  <small style="color:#CFCFCF;" title="<?php echo $dataCustomer->Day; ?>">
                    <em class="fa fa-calendar-o fa-xs fa-fw" aria-hidden="true">

                    </em>
                    <?php echo date("d M 'y", strtotime($dataCustomer->Day)); ?>
                  </small>
                </p>

                <p>
                  <span
                    class="badge badge-<?php echo $color;?> pull-right"
                    style="opacity:0.7; font-weight:400;"
                    title="<?php echo $dataCustomer->usuario; ?>">
                    <?php echo substr($dataCustomer->usuario, 0, 1); ?>
                  </span>

                  <small
                    class="label label-<?php echo $color1;?>"
                    style="opacity:0.7; font-weight:400;"
                    title="<?php echo $dataCustomer->medio_venta; ?>">
                    <?php echo $text;?>
                  </small>

                  <small style="color:#CFCFCF;">
                      <?php echo $dataCustomer->ID_ventas; ?>
                  </small>

                  <?php if ($dataCustomer->estado == 'listo'):?>
                    <small style="color:#E7E7E7;"><i class="fa fa-download fa-fw" aria-hidden="true"></i></small>
                  <?php endif; ?>

                </p>

                <p>
                  <small
                    class="label label-<?php echo $color2;?>"
                    style="opacity:0.7; font-weight:400;"
                    title="<?php echo $dataCustomer->medio_cobro; ?>">
                    <?php echo $text2;?>
                  </small>

                  <small style="color:#CFCFCF;">
                    <?php // si es el admin muestro los ID de cobros como links para editar los cobros?>
                      @if(Helper::validateAdministrator(Auth::user()->Level))
                        <?php
                          $array = (explode(',', $dataCustomer->ID_cobro, 10));
                        ?>

                        @foreach($array as $valor)
                          <a
                            style='color:#CFCFCF; padding:0px 2px; font-size:0.8em;'
                            title='Editar Cobro en DB'
                            type='button'
                            href='ventas_cobro_modificar.php?id=$valor'>
                            {{$valor}}
                          </a>
                          <a
                            class='btn-xs'
                            title='Actualizar importes'
                            type='button'
                            href='control_mp_actualizar_comision.php?id={{$valor}}'>
                            <i class='fa fa-refresh' aria-hidden='true'></i>
                          </a>
                        @endforeach
                      @else
                        {{$dataCustomer->ID_cobro}}
                      @endif

                  </small>

                  <span class="btn-group pull-right">
                    <button
                      title="Agregar Cobro"
                      class="btn btn-xs btn-default"
                      type="button"
                      data-toggle="modal"
                      data-target=".bs-example-modal-lg"
                      onClick='document.getElementById("ifr").src="ventas_cobro_insertar.php?vta_id=<?php echo $dataCustomer->ID_ventas; ?>&c_id=<?php echo $customer->ID ?>";'>
                              <i aria-hidden="true" class="fa fa-plus"></i>
                    </button>
                  </span>
                  <br />

                <?php   // si hay un cobro nuevo de mp sin ref de cobro imprimir cartel de que "falta ref cobro" ?>
                  @if(($dataCustomer->ID_cobro > '3300' && $dataCustomer->ref_cobro = '') && (strpos($dataCustomer->medio_cobro, 'Mercado') !== false))
                    <a
                      href="ventas_cobro_modificar.php?id={{$dataCustomer->ID_cobro}}"
                      class="label label-danger"
                      style="font-size:0.7em;">
                        Agregar Ref de Cobro
                      </a>
                  @elseif($dataCustomer->ref_cobro != "")
                    <?php  // si hay referencia de cobro hacer el explode para mostrar todos?>
                    <?php $array = (explode(',', $dataCustomer->ref_cobro, 10)); ?>

                    @foreach ($array as $valor)
                      <small
                        style='color:#CFCFCF; font-size:0.7em;'
                        class='caption text-center'>
                          {{$valor}}
                          <a
                          title='ver cobro en MP'
                          target='_blank'
                          class='btn-xs'
                          type='button'
                          href='https://www.mercadopago.com.ar/activities?q={{$valor}}'>
                            <i aria-hidden='true' class='fa fa-external-link'></i>
                          </a>
                        </small>";
                    @endforeach
                  @endif

                  <?php  // si hay un solo cobro ID y mas de 1 ref de cobro para ese ID (caso de array importado con varias ref de cobros desde MP) habilito la modif ?>
                  @if(($dataCustomer->ref_cobro != "") & ((count(explode(',', $dataCustomer->ID_cobro, 10))) != (count(explode(',', $dataCustomer->ref_cobro, 10)))))
                    <a
                      href="ventas_cobro_modificar.php?id={{$dataCustomer->ID_cobro}}"
                      class="label label-danger"
                      style="font-size:0.7em;">
                      Modificar Ref Cobro
                    </a>
                  @endif


                  <?php  // si es venta Web pero no tiene order item id se debe corregir ?>
                  @if ((strpos($dataCustomer->medio_venta, 'Web') !== false) & (is_null($dataCustomer->order_item_id))):
                    <?php //echo '<a href="ventas_modificar.php?id=' . $dataCustomer->ID_ventas . '" class="label label-danger" style="font-size:0.7em;">Falta order_item_id</a>';?>
                    <button
                      class="label label-danger"
                      type="button"
                      data-toggle="modal"
                      data-target=".bs-example-modal-lg"
                      onClick='document.getElementById("ifr").src="ventas_agregar_oii.php?id=<?php echo $customer->ID ?>&v_id=<?php echo $dataCustomer->ID_ventas;?>"'>
                        falta order item id
                    </button>
                  @endif

              </p>

              <?php

                if (($dataCustomer->consola == 'ps4') && ($dataCustomer->slot == 'Primario')): $costo = round($dataCustomer->costo * 0.6);
                elseif (($dataCustomer->consola == 'ps4') && ($dataCustomer->slot == 'Secundario')): $costo = round($dataCustomer->costo * 0.4);
                elseif ($dataCustomer->consola == 'ps3'): $costo = round($dataCustomer->costo * (1 / (2 + (2 * $dataCustomer->q_reset))));
                elseif ($dataCustomer->titulo == 'plus-12-meses-slot'): $costo = round($dataCustomer->costo * 0.5);
                elseif (($dataCustomer->consola !== 'ps4') && ($dataCustomer->consola !== 'ps3') && ($dataCustomer->titulo !== 'plus-12-meses-slot')): $costo = round($dataCustomer->costo);
                endif;

              ?>


              @if(Helper::validateAdministrator(Auth::user()->Level))
                @if(!empty($expensesIncome))
                  <?php
                    $gtoestimado = round($expensesIncome[0]->gto_x_ing * $dataCustomer->precio);

                    $iibbestimado = round($dataCustomer->precio * 0.04);
                    $ganancia = round($dataCustomer->precio - $dataCustomer->comision - $costo - $gtoestimado - $iibbestimado);

                  ?>
                @endif


              @endif

              <p>
                @if ($dataCustomer->slot == 'Secundario')
                  <span class="label label-danger pull-right" style="opacity:0.7">2°</span>
                @endif

                <small class="text-success">
                  <i class="fa fa-dollar fa-xs fa-fw" aria-hidden="true"></i>
                  <?php echo round($dataCustomer->precio); ?>
                </small>

                @if(Helper::validateAdministrator(Auth::user()->Level))
                  /
                  <small
                    class="
                    <?php
                      if ($ganancia < '0'):?>text-danger<?php else:?>text-success<?php endif;?>">
                        <?php echo $ganancia;

                    ?>
                  </small>
                @endif

                <br />
                <small class="text-danger">
                  <i
                    class="fa fa-dollar fa-xs fa-fw"
                    aria-hidden="true">
                  </i>

                  <?php echo round($dataCustomer->comision); ?>
                  @if(Helper::validateAdministrator(Auth::user()->Level))
                    <?php echo ', ' . $gtoestimado . ', ' . $iibbestimado . ', ' . $costo; ?>
                  @endif
                </small>
              </p>

              <?php  // si es un código lo muestro:?>
              @if(($dataCustomer->code) && ($dataCustomer->slot == "No"))
                <p>
                  <small class="">
                    <?php echo $dataCustomer->code; ?>
                  </small>

                  @if(Helper::validateAdministrator(Auth::user()->Level))
                    <?php
                      echo '<br><small style="color:#CFCFCF; font-size:0.6em;" class="caption text-center">' . $dataCustomer->code_prov;
                      echo '-' . $dataCustomer->n_order . '</small>';
                    ?>
                  @endif
                </p>
              @endif
            </span>

            <img
              class="img img-responsive img-rounded full-width"
              style="width:54%; margin:0;"
              alt="<?php echo $dataCustomer->titulo;?>" src="../img/productos/<?php echo $dataCustomer->consola."/".$dataCustomer->titulo.".jpg"; ?>">
            <div class="clearfix"></div>

            <div style="opacity: 0.3; padding: 4px 2px;">
              @if($dataCustomer->order_item_id)
                <span class="badge badge-normal" style="font-weight:400; font-size: 0.8em; color:#000">
                  	oii #<?php echo $dataCustomer->order_item_id;?></span>
              @endif

              @if($dataCustomer->order_id_web)
                <span class="badge badge-normal" style="font-weight:400; font-size: 0.8em; color:#000">
                  Ped #<?php echo $dataCustomer->order_id_web;?>
                  <a
                    target="_blank"
                    href="https://dixgamer.com/wp-admin/post.php?post=<?php echo $dataCustomer->order_id_web;?>&amp;action=edit"
                    style="color:#000"
                    title="Ver Pedido en la Adm del Sitio">
                      -
                      <i class="fa fa-external-link" aria-hidden="true"></i>
                      -
                  </a>
                </span>
              @endif

              @if($dataCustomer->order_id_ml)
                <span class="badge badge-normal" style="font-weight:400; font-size: 0.8em; color:#000">
                  <a
                    target="_blank"
                    href="https://myaccount.mercadolibre.com.ar/sales/vop?orderId=<?php echo $dataCustomer->order_id_ml;?>&amp;role=buyer"
                    style="color:#000"
                    title="Ver Venta en ML">ML
                      <i class="fa fa-external-link" aria-hidden="true"> </i>
                    </a>
                  </span>

              @endif

            </div>

            <div class="caption text-center">
              <small
                style="color:#CFCFCF; line-height: 2em;"
                class="pull-left">

                  <i class="fa fa-gamepad fa-fw" aria-hidden="true"></i>
                  <?php echo $dataCustomer->ID_stock; ?>
                  @if(Helper::validateAdministrator(Auth::user()->Level))
                    @if($dataCustomer->stock_Notas)
                      <a
                        href="#"
                        data-toggle="popover"
                        data-placement="bottom"
                        data-trigger="focus"
                        title="Notas de Stock"
                        data-content="<?php echo $dataCustomer->stock_Notas; ?>"
                        style="color: #555555;">
                          <i class="fa fa-comment fa-fw"></i>
                        </a>
                    @endif
                  @endif
                  <a
                    title="Cambiar Producto"
                    class="btn-xs text-muted"
                    style="opacity: 0.7;"
                    type="button" data-toggle="modal"
                    data-target=".bs-example-modal-lg"
                    onClick='document.getElementById("ifr").src="ventas_modificar_producto.php?id=<?php echo $dataCustomer->ID_ventas; ?>&c_id=<?php echo $customer->ID ?>";'>
                      <i aria-hidden="true" class="fa fa-pencil"></i>
                  </a>
                  <a
                    title="Quita Producto (producto 0)"
                    class="btn-xs text-muted"
                    style="opacity: 0.7;"
                    type="button"
                    data-toggle="modal"
                    data-target=".bs-example-modal-lg"
                    onClick='document.getElementById("ifr").src="ventas_quitar_producto.php?id=<?php echo $dataCustomer->ID_ventas; ?>&c_id=<?php echo $customer->ID ?>";'>
                      <i aria-hidden="true" class="fa fa-remove"></i>
                  </a>
              </small>

              @if ($dataCustomer->cuentas_id)
                <a href="cuentas_detalles.php?id=<?php echo $dataCustomer->cuentas_id; ?>" class="btn btn-xs" title="Ir a Cuenta">
                  <i class="fa fa-link fa-xs fa-fw" aria-hidden="true"></i>
                  <?php echo $dataCustomer->cuentas_id; ?>
                </a>
              @endif


              <!--- inicio del analisis si deben avisar a victor -->
              @if(($dataCustomer->ID_cobro > '3300') && ($dataCustomer->ref_cobro == "") && (strpos($dataCustomer->medio_cobro, 'Mercado') !== false))
               <?php
                $colorcito = 'danger';
               ?>

              @else
                <?php
                  $colorcito = 'info';
                ?>
              @endif

              <!--- aca entran los mails de gift cards -->
              @if ( ($dataCustomer->consola === "ps") && ($dataCustomer->slot == "No") && ((strpos($dataCustomer->titulo, 'gift-card-') !== false)))


                <button
                  class="btn btn-<?php echo $colorcito;?> btn-xs"
                  type="button"
                  data-toggle="modal"
                  data-target=".bs-example-modal-lg"
                  onClick='document.getElementById("ifr").src="mail_datos_gift.php?id=<?php echo $dataCustomer->ID_ventas; ?>";'>

                  <i class="fa fa-paper-plane fa-xs fa-fw" aria-hidden="true"></i>
                  <i class="fa fa-info fa-xs fa-fw" aria-hidden="true"></i>
                  @if($dataCustomer->datos1 > 0)
                    <?php echo '('.$dataCustomer->datos1.')';  ?>
                  @endif
                </button>

              <!--- aca entran los mails de PLUS no slot -->
              @elseif( ($dataCustomer->consola === "ps") && ($dataCustomer->slot == "No") && ((strpos($dataCustomer->titulo, 'plus-') !== false)))

                <button
                  class="btn btn-<?php echo $colorcito;?> btn-xs"
                  type="button"
                  data-toggle="modal"
                  data-target=".bs-example-modal-lg"
                  onClick='document.getElementById("ifr").src="mail_datos_plus.php?id=<?php echo $dataCustomer->ID_ventas; ?>";'>
                  <i class="fa fa-paper-plane fa-xs fa-fw" aria-hidden="true"></i>
                  <i class="fa fa-info fa-xs fa-fw" aria-hidden="true"></i>
                    @if($dataCustomer->datos1 > 0)
                      <?php echo '('.$dataCustomer->datos1.')'; ?>
                    @endif
                </button>

              @else

                <!--- aca entran los mails de juegos y ps plus slot pri y secu -->
                  <button
                    class="btn btn-<?php echo $colorcito;?> btn-xs"
                    type="button"
                    data-toggle="modal"
                    data-target=".bs-example-modal-lg"
                    onClick='document.getElementById("ifr").src="mail_datos_<?php echo $dataCustomer->consola; ?><?php echo $dataCustomer->slot; ?>.php?id=<?php echo $dataCustomer->ID_ventas; ?>&c_id=<?php echo $dataCustomer->cuentas_id; ?>";'>
                    <i class="fa fa-paper-plane fa-xs fa-fw" aria-hidden="true"></i> <i class="fa fa-info fa-xs fa-fw" aria-hidden="true"></i>
                    @if($dataCustomer->datos1 > 0)
                     <?php echo '('.$dataCustomer->datos1.')';  ?>
                    @endif
                   </button>

              @endif



              <!--- filtrar en gmail -->
              <a
                class="btn btn-xs btn-default"
                href="https://mail.google.com/a/dixgamer.com/#search/<?php echo substr($dataCustomer->email, 0, strpos($dataCustomer->email, '@')) . '+' . $dataCustomer->titulo . '+(' . $dataCustomer->consola .')'; ?>"
                title="filtrar guia de descarga en gmail"
                target="_blank">
                  <i aria-hidden="true" class="fa fa-google"></i>
                  mail
              </a>
            </div>

            @if ($dataCustomer->ventas_Notas)
                <div class="alert alert-warning">
                  <i class="fa fa-comment fa-fw"></i>
                  <?php echo $dataCustomer->ventas_Notas; ?>
                </div>
            @endif

          </div>
        </div>




        <?php
          $cerrardiv = array("8", "12", "16", "20", "24", "28", "32", "36", "40", "44", "48", "52");

          if ((in_array($i, $cerrardiv)) or (($i-2) == $salesByCustomer->Q))
          {
            echo "</div>";
          }
        ?>
        @endforeach
      @endif


      <div class="clear" style="clear:both;"></div>

      <?php ///SI HAY VENTAS ELIMINADAS LAS MUESTRO ?>

      @if(count($lowSalesByCustomerIds) > 0)
      <h3>Ventas eliminadas</h3>
      @foreach($lowSalesByCustomerIds as $lowerSale)
          <div class="col-xs-12 col-sm-6 col-md-3 thumbnail" <?php if ($lowerSale->slot == 'Secundario'): ?>style="background-color:#efefef;"<?php endif; ?>>
              <span class="pull-right" style="width: 45%;">
              <p>
                  <small style="color:#CFCFCF;" title="<?php echo $lowerSale->Day; ?>"><em class="fa fa-calendar-o fa-xs fa-fw" aria-hidden="true"></em><?php echo date("d-M", strtotime($lowerSale->Day)); ?></small>
                  <small style="color:#CFCFCF;" title="<?php echo $lowerSale->Day_baja; ?>"><em class="fa fa-trash-o fa-xs fa-fw" aria-hidden="true"></em><?php echo date("d-M", strtotime($lowerSale->Day_baja)); ?></small>
              </p>
              <p><small style="color:#CFCFCF;"><i class="fa fa-gamepad fa-fw" aria-hidden="true"></i> <?php echo $lowerSale->ID_stock; ?> <?php if ($lowerSale->stock_Notas):?><a href="#" data-toggle="popover" data-placement="bottom" data-trigger="focus" title="Notas de Stock" data-content="<?php echo $lowerSale->stock_Notas; ?>" style="color: #555555;"><i class="fa fa-comment fa-fw"></i></a><?php endif; ?></small>
              <small style="color:#CFCFCF;"><i class="fa fa-shopping-bag fa-fw" aria-hidden="true"></i> <?php echo $lowerSale->ID_ventas; ?></small></p>
              <p>
              <?php
        if (strpos($lowerSale->medio_venta, 'Web') !== false): $text3 = '<i class="fa fa-shopping-basket fa-fw" aria-hidden="true"></i>';
        elseif (strpos($lowerSale->medio_venta, 'Mail') !== false): $text3 = '<i class="fa fa-envelope fa-fw"  aria-hidden="true"></i>';
        elseif (strpos($lowerSale->medio_venta, 'Mercado') !== false): $text3 = 'ML';
        endif;?>
              <?php
        if (strpos($lowerSale->medio_cobro, 'Transferencia') !== false): $text4 = '<i class="fa fa-bank fa-xs fa-fw" aria-hidden="true"></i>';
        elseif (strpos($lowerSale->medio_cobro, 'Ticket') !== false): $text4 = '<i class="fa fa-dollar fa-xs fa-fw" aria-hidden="true"></i>';
        elseif (strpos($lowerSale->medio_cobro, 'Mercado') !== false): $text4 = 'MP';
        endif;?>
        <small style="color:#CFCFCF;" title="<?php echo $lowerSale->medio_venta; ?>"><?php echo $text3;?></small> <small style="color:#CFCFCF;" title="<?php echo $lowerSale->medio_cobro; ?>"><?php echo $text4;?></small>
              <?php if($lowerSale->estado == 'listo'):?>
              <small style="color:#CFCFCF;"><i class="fa fa-download fa-fw" aria-hidden="true"></i></small>
              <?php endif; ?>
              </p>
        <?php if (($lowerSale->consola == 'ps4') && ($lowerSale->slot == 'Primario')): $costo2 = round($dataCustomer->costo * 0.6) ?>
              <?php elseif (($lowerSale->consola == 'ps4') && ($lowerSale->slot == 'Secundario')): $costo2 = round($lowerSale->costo * 0.4) ?>
              <?php elseif ($lowerSale->consola == 'ps3'): $costo2 = round($lowerSale->costo * 0.25) ?>
              <?php elseif (($lowerSale->titulo == 'plus-12-meses-slot')&& ($lowerSale->slot == 'Primario')): $costo2 = round($lowerSale->costo * 0.6) ?>
              <?php elseif (($lowerSale->titulo == 'plus-12-meses-slot')&& ($lowerSale->slot == 'Secundario')): $costo2 = round($lowerSale->costo * 0.4) ?>
              <?php elseif (($lowerSale->consola !== 'ps4') && ($lowerSale->consola !== 'ps3') && ($lowerSale->titulo !== 'plus-12-meses-slot')): $costo2 = round($lowerSale->costo) ?>
              <?php endif; ?>
              <?php $ganancia2 = round($lowerSale->precio - $lowerSale->comision - $costo2); ?>
              <p><small class="text-success"><i class="fa fa-dollar fa-xs fa-fw" aria-hidden="true"></i><?php echo round($lowerSale->precio); ?></small><br /><small class="text-danger"><i class="fa fa-dollar fa-xs fa-fw" aria-hidden="true"></i><?php echo round($lowerSale->comision); ?></small>
        @if(Helper::validateAdministrator(Auth::user()->Level))
              <br /><small class="text-danger"><i class="fa fa-dollar fa-xs fa-fw" aria-hidden="true"></i><?php echo $costo2; ?></small><hr style="margin:0px"><small class="<?php if ($ganancia2 < '0'):?>text-danger<?php else:?>text-success<?php endif;?>"><i class="fa fa-dollar fa-xs fa-fw" aria-hidden="true"></i><?php echo $ganancia2; ?></small>
        @endif

              </span>

              <img class="img img-responsive img-rounded full-width" style="width:54%; margin:0; opacity:0.8;" alt="<?php echo $dataCustomer->titulo;?>" src="../img/productos/<?php echo $lowerSale->consola."/".$lowerSale->titulo.".jpg"; ?>">
              <span class="label label-default <?php echo $lowerSale->consola; ?>" style="position: relative; bottom: 22px; left: 5px; float:left;"><?php echo $lowerSale->consola; ?></span>
              <div class="caption text-center">
              <?php if ($lowerSale->cuentas_id):?><a href="cuentas_detalles.php?id=<?php echo $lowerSale->cuentas_id; ?>" class="btn btn-xs" title="Ir a Cuenta"><i class="fa fa-link fa-xs fa-fw" aria-hidden="true"></i> <?php echo $lowerSale->cuentas_id; ?></a> <?php endif; ?>
              </div>
              <?php if ($lowerSale->ventas_Notas):?><div class="alert alert-warning"><i class="fa fa-comment fa-fw"></i> <?php echo $lowerSale->ventas_Notas; ?></div><?php endif; ?>
              <?php if ($lowerSale->Notas_baja):?><div class="alert alert-danger"><i class="fa fa-comment fa-fw"></i> <?php echo $lowerSale->Notas_baja; ?></div><?php endif; ?>
              </div>

        @endforeach
      @endif
      @endif
      </div>




      <div class="container">
        <div class="row">
          <!-- Large modal -->

          <div class="modal fade bs-example-modal-lg" id="modal-container" tabindex="-1" role="dialog" aria-labelledby="myLargeModalLabel">
            <div class="modal-dialog modal-lg" style="top:40px;">
              <div class="modal-content">

                <div class="modal-body" style="text-align:center;padding:10px;color:#000">
                </div>

              </div>
            </div>
          </div>

        </div>
      </div>
       <!--/row-->
       <!-- InstanceEndEditable -->
</div><!--/.container-->

  @else
    <h4 class="text-center">No se encontraron datos</h4>
  @endif


@endsection
