@extends('layouts.master-layouts')

@section('title', "Cliente #$customer->ID")

@section('container')

  @if($customer)
    <div class="container">
    <div class="row">
      <div class="col-md-6">
        <h1>Cliente #{{$customer->ID}}</h1>
      </div>
      <div class="col-md-6">
        @if(Session::has('alert_cliente'))
          <div class="alert alert-success" role="alert">
              <span class="glyphicon glyphicon-exclamation-sign" aria-hidden="true"></span>
              <span class="sr-only">{{ Session::get('alert_cliente')->title }}:</span>
              {{ Session::get('alert_cliente')->body }}
          </div>
        @endif
      </div>
    </div>

    @if (count($errors) > 0)
          <div class="alert alert-danger text-center">
            <ul>
              @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
              @endforeach
            </ul>
          </div>
    @endif


      <div class="row clientes_detalles">



        <div class="col-xs-12 col-sm-6 col-md-5">
          <div class="panel panel-info">

          <div class="panel-heading clearfix">
            <h4 style="margin:0px;">

              <i class="fa fa-user fa-fw" aria-hidden="true"></i>
              {{$customer->nombre}} {{$customer->apellido}}

              <button title="Modificar Nombre" id="modificarNombre" onclick="inputFocus('nombres_cliente')" class="btn btn-xs btn-default pull-right" type="button"
                      data-toggle="modal" data-target="#editarCliente" data-customer="{{ $customer->ID }}">
              <i aria-hidden="true" class="fa fa-pencil"></i>
              </button>

            </h4>
          </div>

          <div class="panel-body" style="background-color: #efefef;">

            <p style="margin-bottom: 2px;">
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
                id="modificarEmailboton"
                onclick="inputFocus('correo_cliente')"
                data-toggle="modal"
                data-target="#modificarEmailModal"  data-customer="{{ $customer->ID }}">

                <i aria-hidden="true" class="fa fa-pencil"></i>
              </button>
            </p>

            @if ($othersEmails)
              <p style="margin-left: 19px;margin-top: 0;">
                @foreach ($othersEmails as $email)
                    <small class="text-muted">{{ $email->email }} <a href="{{ url('customer_setEmailPrimary',[$email->id,$email->clientes_id]) }}" title="Email primario"> <i class="fa fa-check"></i> </a></small>
                @endforeach
              </p>
            @endif

            @if($customer->ml_user)
              <p>
                <i class="fa fa-snapchat-ghost fa-fw"></i>
                {{$customer->ml_user}}
                <a
                  title="Modificar ML user"
                  class="btn-xs text-muted"
                  style="opacity: 0.7;"
                  type="button"
                  onclick="inputFocus('ML_cliente')"
                  data-toggle="modal"
                  id="modificarMLboton"
                  data-target="#modificarMLModal" data-customer="{{ $customer->ID }}">
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
                onclick="inputFocus('provincia')"
                id="modificarOtrosboton"
                data-toggle="modal"
                data-target="#modificarOtrosModal" data-customer="{{ $customer->ID }}">
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
                  onclick="inputFocus('cuentafacebookmodify')"
                  id="ModificarFacebook"
                  data-toggle="modal"
                  data-target="#modificarfb"  data-customer="{{ $customer->ID }}">
                  <i aria-hidden="true" class="fa fa-pencil"></i>
                </a>
              </p>
            @endif

            <p>

            <button
              class="btn btn-warning btn-xs"
              style="color: #8a6d3b; background-color:#FFDD87; opacity: 0.7"
              type="button"
              id="addNotes"
              data-toggle="modal"
              onclick="inputFocus('notaCliente')"
              data-target="#agregarNotaModal"  data-customer="{{ $customer->ID }}">
                <i class="fa fa-fw fa-comment"></i>
                Agregar Nota
            </button>


            @if(empty($customer->face))
              <button
                title="Agregar FB"
                id="agregaFacebook"
                class="btn btn-xs btn-info"
                type="button"
                data-toggle="modal"
                data-target="#agregarfb" data-customer="{{ $customer->ID }}">
                  <i class="fa fa-facebook fa-fw"></i> FB
              </button>
            @endif
            @if(empty($customer->ml_user))
              <button
                title="Agregar ML User"
                class="btn btn-xs btn-info"
                type="button"
                data-toggle="modal"
                data-target="#agregarML">
                  <i class="fa fa-snapchat-ghost fa-fw"></i> ML
              </button>
            @endif
                @if(Helper::validateAdministrator(session()->get('usuario')->Level))
                    @if($customer->auto != 're')
                        <a href="#!" id="doreseller" data-customer="{{ $customer->ID }}" data-update="re" type="button" class="btn btn-default btn-xs pull-right">Hacer Revendedor</a>
                    @else
                        <a href="#!" id="doseller" data-customer="{{ $customer->ID }}" data-update="no" type="button" class="btn btn-danger btn-xs pull-right">Revendedor</a>
                    @endif
                @endif
                @if(Helper::lessAdministrator(session()->get('usuario')->Level))
                    @if($customer->auto == 're')
                        <label for="" class="label label-danger pull-right" style="padding: 6px">Revendedor</label>
                    @endif
                @endif



            </p>

            @if(Helper::validateAdministrator(session()->get('usuario')->Level))
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




        @php $color2 = ''; $text2= '';@endphp
        @if($customer->ID === "371" && !(Helper::validateAdministrator(session()->get('usuario')->Level)))
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
              if (strpos($dataCustomer->medio_venta, 'Web') !== false): $text = 'W'; $color1 = \Helper::medioVentaColor($dataCustomer->medio_venta);
              elseif (strpos($dataCustomer->medio_venta, 'Mail') !== false): $text = 'M'; $color1 = \Helper::medioVentaColor($dataCustomer->medio_venta);
              elseif (strpos($dataCustomer->medio_venta, 'Mercado') !== false): $text = 'ML'; $color1 = \Helper::medioVentaColor($dataCustomer->medio_venta);
              endif;
            ?>

            <?php
              if (strpos($dataCustomer->medio_cobro,'Banco') !== false): $text2 = 'Bco'; $color2 = 'default';
              elseif (strpos($dataCustomer->medio_cobro,'Ticket') !== false): $text2 = 'Cash'; $color2 = 'success';
              elseif (strpos($dataCustomer->medio_cobro,'MP') !== false): $text2 = 'MP'; $color2 = 'primary';
              elseif (strpos($dataCustomer->medio_cobro,'Fondo') !== false): $text2 = 'F'; $color2 = 'normal';
              endif;
            ?>


            <?php
              $color = Helper::userColor($dataCustomer->usuario);
            ?>


          <div class="col-xs-12 col-sm-6 col-md-3" style="display: inline-flex">
            <div class=" thumbnail" <?php if ($dataCustomer->slot == 'Secundario'): ?>style="background-color:#efefef;"<?php endif; ?>>
              <span class="pull-right" style="width: 45%;">
                
                  <div class="dropdown pull-right">
                    <button class="btn btn-default btn-xs dropdown-toggle" type="button" id="dropdownMenu1" data-toggle="dropdown" aria-haspopup="true" aria-expanded="true">
                      <i aria-hidden="true" class="fa fa-pencil"></i>
                    </button>
                    <ul class="dropdown-menu" aria-labelledby="dropdownMenu1">
                      <li><a href="javascript:void(0)" data-toggle="modal" data-target=".modalVentas" onclick="getPageAjax('{{url("customer_ventas_modificar")}}/{{$dataCustomer->ID_ventas}}/1','#modalVentas')">Modificar cliente</a></li>
                      {{-- <li><a href="javascript:void(0)" data-toggle="modal" data-target=".modalVentas" onclick="getPageAjax('{{url("customer_ventas_modificar")}}/{{$dataCustomer->ID_ventas}}/2','#modalVentas')">Modificar medio venta</a></li> --}}
                      <li><a href="javascript:void(0)" data-toggle="modal" data-target=".modalVentas" onclick="getPageAjax('{{url("customer_ventas_modificar")}}/{{$dataCustomer->ID_ventas}}/3','#modalVentas')">Modificar order</a></li>
                      {{--<li><a href="javascript:void(0)" data-toggle="modal" data-target=".modalVentas" onclick="getPageAjax('{{url("customer_ventas_modificar")}}/{{$dataCustomer->ID_ventas}}/5','#modalVentas')">Modificar manual</a></li> --}}
                      <li><a href="javascript:void(0)" data-toggle="modal" data-target=".modalVentas" onclick="getPageAjax('{{url("customer_ventas_modificar")}}/{{$dataCustomer->ID_ventas}}/4','#modalVentas')">Agregar nota</a></li>
                      <li><a href="javascript:void(0)" data-toggle="modal" data-target=".modalVentas" onclick="getPageAjax('{{url("customer_duplicar_venta")}}','#modalVentas', {{$dataCustomer->ID_ventas}})">Duplicar venta</a></li>
                      <li><a href="javascript:void(0)" data-toggle="modal" data-target=".modalVentas" onclick="getPageAjax('{{url("customer_ventas_eliminar")}}','#modalVentas', {{$dataCustomer->ID_ventas}})">Eliminar venta y cobros</a></li>
                    </ul>
                  </div>
                <p>
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
                    <?php echo $text;?></small>

                  <small style="color:#CFCFCF;">
                      <?php echo $dataCustomer->ID_ventas; ?>
                  </small>

                  <?php if ($dataCustomer->estado == 'listo'):?>
                    <small style="color:#E7E7E7;"><i class="fa fa-download fa-fw" aria-hidden="true"></i></small>
                  <?php endif; ?>

                </p>

                <p style="display: inline-block;">
                  <small
                    class="label label-<?php echo $color2;?>"
                    style="opacity:0.7; font-weight:400;"
                    title="<?php echo $dataCustomer->medio_cobro; ?>">
                    <?php echo $text2;?></small>

                  <small style="color:#CFCFCF;">
                    <?php // si es el admin muestro los ID de cobros como links para editar los cobros?>
                      @if(Helper::validateAdminAnalystAsistent(session()->get('usuario')->Level))
                        <?php
                          $array = (explode(',', $dataCustomer->ID_cobro, 10));
                        ?>
                        @foreach($array as $valor)
                          <a
                            style='@if($valor == "") visibility: hidden; @endif color:#CFCFCF; padding:0px 2px; font-size:0.8em;'
                            title='Editar Cobro en DB'
                            data-toggle="modal"
                            data-target="#modalVentas"
                            href='javascript:void(0)'
                            onclick="getPageAjax('{{ url("customer_ventas_cobro_modificar") }}','#modalVentas',{{ $valor }})" >
                            {{$valor}}
                          </a>
                          
                         
                          <div class="dropdown" style="display: inline-block;@if($valor == "") visibility: hidden; @endif">
                            <button class="btn btn-default btn-xs dropdown-toggle" type="button" id="dropdownMenu1" data-toggle="dropdown" aria-haspopup="true" aria-expanded="true" style="background: transparent;border: none;padding-top: 0px;padding-bottom: 0px;padding-right: 0px;padding-left: 5px;" title="Eliminar cobro">
                              <i aria-hidden="true" class="fa fa-trash text-danger"></i>
                            </button>
                            <ul class="dropdown-menu" aria-labelledby="dropdownMenu1">
                              <li class="dropdown-header">¿Desea eliminar el cobro?</li>
                              <li role="separator" class="divider"></li>
                              <li><a href="{{ url('delete_amounts',$valor) }}">Sí, Eliminar</a></li>
                            </ul>
                          </div>
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
                      data-target="#modalVentas"
                      onclick='getPageAjax("{{ url('customer_addVentasCobro') }}/{{ $dataCustomer->ID_ventas }}/{{ $customer->ID }}","#modalVentas")'>
                              <i aria-hidden="true" class="fa fa-plus"></i>
                    </button>
                  </span>
                  <br />


                 {{-- // si hay un cobro nuevo de mp sin ref de cobro imprimir cartel de que "falta ref cobro"  --}}
                  @if(($dataCustomer->ID_cobro > '3300') && ($dataCustomer->ref_cobro == '') && (strpos($dataCustomer->medio_cobro, 'Mercado') !== false))
                    <a
                      href="ventas_cobro_modificar.php?id={{$dataCustomer->ID_cobro}}"
                      class="label label-danger"
                      style="font-size:0.7em;">
                        Agregar Ref de Cobro
                      </a>
                  @elseif($dataCustomer->ref_cobro != "")
                      {{-- // si hay referencia de cobro hacer el explode para mostrar todos --}}
                    @php 
                      $array = (explode(',', $dataCustomer->ref_cobro, 10));
                     @endphp

                    @foreach ($array as $valor)
                      <small
                        style='color:#CFCFCF; font-size:0.7em;'
                        class='caption text-center'>
                          {{$valor}}
                          <a
                          style='padding: 0'
                          title='ver cobro en MP'
                          target='_blank'
                          class='btn-xs'
                          type='button'
                          href='https://www.mercadopago.com.ar/activities?q={{$valor}}'>
                            <i aria-hidden='true' class='fa fa-external-link'></i>
                          </a>
                          @if(\Helper::validateAdministrator(session()->get('usuario')->Level))
                          <a
                          style='padding: 0'
                            class='btn-xs'
                            title='Actualizar importes de MP'
                            type='button'
                            href='{{ url("update_amounts", [$valor, $customer->ID]) }}'>
                            <i class='fa fa-refresh' aria-hidden='true'></i>
                          </a>
                          @endif
                        </small>
                    @endforeach
                  @endif

                  <?php  // si hay un solo cobro ID y mas de 1 ref de cobro para ese ID (caso de array importado con varias ref de cobros desde MP) habilito la modif ?>
                  @if(($dataCustomer->ref_cobro != "") && ((count(explode(',', $dataCustomer->ID_cobro, 10))) != (count(explode(',', $dataCustomer->ref_cobro, 10)))))
                    <a
                      data-toggle="modal"
                      data-target="#modalVentas"
                      href='javascript:void(0)'
                      onclick="getPageAjax('{{ url("customer_ventas_cobro_modificar") }}','#modalVentas',{{ $dataCustomer->ID_cobro }})"
                      class="label label-danger"
                      style="font-size:0.7em;">
                      Modificar Ref Cobro
                    </a>
                  @endif


                  <?php  // si es venta Web pero no tiene order item id se debe corregir ?>
                  @if ((strpos($dataCustomer->medio_venta, 'Web') !== false) && (is_null($dataCustomer->order_item_id))):
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

                $costo = 0;
                if (($dataCustomer->consola == 'ps4') && ($dataCustomer->slot == 'Primario')): $costo = round($dataCustomer->costo * 0.6);
                elseif (($dataCustomer->consola == 'ps4') && ($dataCustomer->slot == 'Secundario')): $costo = round($dataCustomer->costo * 0.4);
                elseif ($dataCustomer->consola == 'ps3'): $costo = round($dataCustomer->costo * (1 / (2 + (2 * $dataCustomer->q_reset))));
                elseif ($dataCustomer->titulo == 'plus-12-meses-slot'): $costo = round($dataCustomer->costo * 0.5);
                elseif (($dataCustomer->consola !== 'ps4') && ($dataCustomer->consola !== 'ps3') && ($dataCustomer->titulo !== 'plus-12-meses-slot')): $costo = round($dataCustomer->costo);
                endif;

              ?>


              @if(Helper::validateAdministrator(session()->get('usuario')->Level))
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

                @if(Helper::validateAdministrator(session()->get('usuario')->Level))
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
                  @if(Helper::validateAdministrator(session()->get('usuario')->Level))
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

                  @if(Helper::validateAdministrator(session()->get('usuario')->Level))
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
              alt="<?php echo $dataCustomer->titulo;?>" src="{{asset('img/productos')}}/<?php echo $dataCustomer->consola."/".$dataCustomer->titulo.".jpg"; ?>">
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
                <span class="badge badge-warning" style="font-weight:400; font-size: 0.8em; color:#000">
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
                  @if(Helper::validateAdministrator(session()->get('usuario')->Level))
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
                  {{--<a
                    title="Cambiar Producto"
                    class="btn-xs text-muted"
                    style="opacity: 0.7;"
                    type="button" data-toggle="modal"
                    data-target=".modalVentas"
                    onclick='getPageAjax("{{ url('customer_ventas_modificar_producto') }}", "#modalVentas", {{ $dataCustomer->ID_ventas }})'>
                      <i aria-hidden="true" class="fa fa-pencil"></i>
                  </a> --}}

                  <div class="dropdown" style="display: inline-block;">
                    <button class="btn-xs dropdown-toggle" type="button" id="dropdownMenu1" data-toggle="dropdown" aria-haspopup="true" aria-expanded="true" style="background: transparent;border: none;padding-top: 0px;padding-bottom: 0px;padding-right: 0px;padding-left: 5px;">
                      <i aria-hidden="true" class="fa fa-pencil text-muted"></i>
                    </button>
                    <ul style="top:-75px;left:25px" class="dropdown-menu" aria-labelledby="dropdownMenu1">
                      <li class="dropdown-header">Modificar stock</li>
                      <li role="separator" class="divider"></li>
                      <li><a href="javascript:;" data-toggle="modal" data-target=".modalVentas" onclick='getPageAjax("{{ url('customer_ventas_modificar_producto') }}", "#modalVentas", {{ $dataCustomer->ID_ventas }})'>Automatico</a></li>
                      <li><a href="javascript:void(0)" data-toggle="modal" data-target=".modalVentas" onclick="getPageAjax('{{url("customer_ventas_modificar")}}/{{$dataCustomer->ID_ventas}}/5','#modalVentas')">Manual</a></li>
                    </ul>
                  </div>
                  
                  @if ($dataCustomer->ID_stock != 1)
                  <div class="dropdown" style="display: inline-block;">
                    <button class="btn btn-default btn-xs dropdown-toggle" type="button" id="dropdownMenu1" data-toggle="dropdown" aria-haspopup="true" aria-expanded="true" style="background: transparent;border: none;padding-top: 0px;padding-bottom: 0px;padding-right: 0px;padding-left: 5px;">
                      <i aria-hidden="true" class="fa fa-remove text-muted"></i>
                    </button>
                    <ul style="top:-65px;left:25px" class="dropdown-menu" aria-labelledby="dropdownMenu1">
                      <li class="dropdown-header">¿Deseas quitar producto?</li>
                      <li role="separator" class="divider"></li>
                      <li><a href="{{ url('customer_ventas_quitar_producto',$dataCustomer->ID_ventas) }}">Sí, remover</a></li>
                      @if($dataCustomer->consola == 'ps4')
                      @if($dataCustomer->slot == 'Secundario')
                      <li><a href="{{ url('customer_ventas_quitar_producto',$dataCustomer->ID_ventas) }}?slot={{$dataCustomer->slot}}">Sí, tal vez no usa</a></li>
                      @elseif($dataCustomer->slot == 'Primario')
                      <li><a href="{{ url('customer_ventas_quitar_producto',$dataCustomer->ID_ventas) }}?slot={{$dataCustomer->slot}}">Sí, ps4 no activa</a></li>
                      @endif

                      @elseif($dataCustomer->consola == 'ps3')
                      <li><a href="{{ url('customer_ventas_quitar_producto',$dataCustomer->ID_ventas) }}?cons={{$dataCustomer->consola}}">Sí, no descargó</a></li>
                      @endif
                    </ul>
                  </div>
                  @endif
                  
              </small>

              @if ($dataCustomer->cuentas_id)
                <a href="{{ url('cuentas', $dataCustomer->cuentas_id) }}" class="btn btn-xs" title="Ir a Cuenta">
                  <i class="fa fa-link fa-xs fa-fw" aria-hidden="true"></i>
                  <?php echo $dataCustomer->cuentas_id; ?>
                </a>
              @endif


              <!--- inicio del analisis si deben avisar a victor -->
              @if(($dataCustomer->ID_cobro > '3300') && ($dataCustomer->ref_cobro == "") && (strpos($dataCustomer->medio_cobro, 'Mercado') !== false))
                @if($dataCustomer->datos1 > 0)
                <?php
                 $colorcito = 'info';
                ?>

                @else
               <?php
                $colorcito = 'danger';
               ?>

               @endif

              @else
                 @if($dataCustomer->datos1 > 0)
                 <?php
                  $colorcito = 'info';
                 ?>

                 @else
                <?php
                 $colorcito = 'danger';
                ?>

                @endif

              @endif

              <!--- aca entran los mails de gift cards -->
              @if ( ($dataCustomer->consola === "ps") && ($dataCustomer->slot == "No") && ((strpos($dataCustomer->titulo, 'gift-card-') !== false)))


                <button
                  class="btn btn-<?php echo $colorcito;?> btn-xs"
                  type="button"
                  onclick="enviarEmailVenta('{{ $dataCustomer->ID_ventas }}', 'Gift')">
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
                  onclick="enviarEmailVenta('{{ $dataCustomer->ID_ventas }}', 'Plus')">
                  <i class="fa fa-paper-plane fa-xs fa-fw" aria-hidden="true"></i>
                  <i class="fa fa-info fa-xs fa-fw" aria-hidden="true"></i>
                    @if($dataCustomer->datos1 > 0)
                      <?php echo '('.$dataCustomer->datos1.')'; ?>
                    @endif
                </button>

              @elseif ( ($dataCustomer->consola === "fifa-points") && ($dataCustomer->slot == "No") && ((strpos($dataCustomer->titulo, 'ps4') !== false)))

                <button
                  class="btn btn-<?php echo $colorcito;?> btn-xs"
                  type="button"
                  onclick="enviarEmailVenta('{{ $dataCustomer->ID_ventas }}', 'FifaPoints')">
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
                    onclick="enviarEmailVenta('{{ $dataCustomer->ID_ventas }}', 'Juegos','{{ $dataCustomer->consola }}', '{{ $dataCustomer->slot }}', '{{ $dataCustomer->cuentas_id }}')">
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

            @if ($ventas_notas)
              @foreach ($ventas_notas as $venta_nota)
                @if ($venta_nota->id_ventas == $dataCustomer->ID_ventas)
                <div class="alert alert-warning" style="padding: 4px 7px;margin:0px;opacity: 0.7;font-size: 0.9em">
                  <i class="fa fa-comment fa-fw"></i>

                  @if (strpos($venta_nota->Notas, "cliente") !== false)
                  
                  @php 
                  $cliente = substr($venta_nota->Notas, 26);
                  @endphp
                  Antes asignado a cliente <a href="{{ url('clientes', $cliente) }}" class="alert-link" target="_blank">#{{ $cliente }}</a>

                  @elseif(strpos($venta_nota->Notas, "Antes tenía") !== false) {{-- Solo notas para cambios de productos --}}
                   @if(strpos($venta_nota->Notas, "#", 14)) {{-- Esta validación que funcione las notas anteriores antes de colocar el link para las cuentas --}}

                    @php
                    $string = $venta_nota->Notas;
                    $pos = strripos($string, "#"); // calculando la posicion de ultima aparicion de cuenta_id
                    $cuenta = substr($string, $pos+1);
                    $nota = substr($string, 0, $pos);
                    @endphp

                    {{$nota}} <a href="{{url('cuentas',$cuenta)}}" target="_blank" class="alert-link">#{{$cuenta}}</a>

                    @else
                      {{ $venta_nota->Notas }}
                    @endif

                  @else

                  {{ ($venta_nota->Notas) }}
                  @endif
                </div>
                <em
                  class="small text-muted pull-right"
                  style="opacity: 0.7;font-size: 0.8em">
                  {{ date("d M 'y", strtotime($venta_nota->Day)) }}
                  ({{ $venta_nota->usuario }})
                </em>
                <br>
                @endif
              @endforeach
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
      <div class="row">
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
              $text4 = '';
        if (strpos($lowerSale->medio_cobro, 'Transferencia') !== false || strpos($lowerSale->medio_cobro, 'Banco') !== false || strpos($lowerSale->medio_cobro, 'Fondos') !== false): $text4 = '<i class="fa fa-bank fa-xs fa-fw" aria-hidden="true"></i>';
        elseif (strpos($lowerSale->medio_cobro, 'Ticket') !== false): $text4 = '<i class="fa fa-dollar fa-xs fa-fw" aria-hidden="true"></i>';
        elseif (strpos($lowerSale->medio_cobro, 'MP') !== false): $text4 = 'MP';
        endif;?>
        <small style="color:#CFCFCF;" title="<?php echo $lowerSale->medio_venta; ?>"><?php echo $text3;?></small> <small style="color:#CFCFCF;" title="<?php echo $lowerSale->medio_cobro; ?>"><?php echo $text4;?></small>
              <?php //if($lowerSale->estado == 'listo'):?>
              {{-- <small style="color:#CFCFCF;"><i class="fa fa-download fa-fw" aria-hidden="true"></i></small> --}}
              <?php //endif; ?>
              </p>
        <?php if (($lowerSale->consola == 'ps4') && ($lowerSale->slot == 'Primario')): $costo2 = round($lowerSale->costo * 0.6) ?>
              <?php elseif (($lowerSale->consola == 'ps4') && ($lowerSale->slot == 'Secundario')): $costo2 = round($lowerSale->costo * 0.4) ?>
              <?php elseif ($lowerSale->consola == 'ps3'): $costo2 = round($lowerSale->costo * 0.25) ?>
              <?php elseif (($lowerSale->titulo == 'plus-12-meses-slot')&& ($lowerSale->slot == 'Primario')): $costo2 = round($lowerSale->costo * 0.6) ?>
              <?php elseif (($lowerSale->titulo == 'plus-12-meses-slot')&& ($lowerSale->slot == 'Secundario')): $costo2 = round($lowerSale->costo * 0.4) ?>
              <?php elseif (($lowerSale->consola !== 'ps4') && ($lowerSale->consola !== 'ps3') && ($lowerSale->titulo !== 'plus-12-meses-slot')): $costo2 = round($lowerSale->costo) ?>
              <?php endif; ?>
              <?php $ganancia2 = round($lowerSale->precio - $lowerSale->comision - $costo2); ?>
              <p><small class="text-success"><i class="fa fa-dollar fa-xs fa-fw" aria-hidden="true"></i><?php echo round($lowerSale->precio); ?></small><br /><small class="text-danger"><i class="fa fa-dollar fa-xs fa-fw" aria-hidden="true"></i><?php echo round($lowerSale->comision); ?></small>
        @if(Helper::validateAdministrator(session()->get('usuario')->Level))
              <br /><small class="text-danger"><i class="fa fa-dollar fa-xs fa-fw" aria-hidden="true"></i><?php echo $costo2; ?></small><hr style="margin:0px"><small class="<?php if ($ganancia2 < '0'):?>text-danger<?php else:?>text-success<?php endif;?>"><i class="fa fa-dollar fa-xs fa-fw" aria-hidden="true"></i><?php echo $ganancia2; ?></small>
        @endif

              </span>

              <img class="img img-responsive img-rounded full-width" style="width:54%; margin:0; opacity:0.8;" alt="<?php echo $lowerSale->titulo;?>" src="{{asset('img/productos')}}/<?php echo $lowerSale->consola."/".$lowerSale->titulo.".jpg"; ?>">
              <span class="label label-default <?php echo $lowerSale->consola; ?>" style="position: relative; bottom: 22px; left: 5px; float:left;"><?php echo $lowerSale->consola; ?></span>
              <div class="caption text-center">
              <?php if ($lowerSale->cuentas_id):?><a href="cuentas_detalles.php?id=<?php echo $lowerSale->cuentas_id; ?>" class="btn btn-xs" title="Ir a Cuenta"><i class="fa fa-link fa-xs fa-fw" aria-hidden="true"></i> <?php echo $lowerSale->cuentas_id; ?></a> <?php endif; ?>
              </div>
              <?php if ($lowerSale->ventas_Notas):?><div class="alert alert-warning"><i class="fa fa-comment fa-fw"></i> <?php echo $lowerSale->ventas_Notas; ?></div><?php endif; ?>
              <?php if ($lowerSale->Notas_baja):?><div class="alert alert-danger"><i class="fa fa-comment fa-fw"></i> <?php echo $lowerSale->Notas_baja; ?></div><?php endif; ?>
              </div>

        @endforeach
        </div>
      @endif
      @endif
      </div>


        <input type="hidden" value="{{ csrf_token() }}" id="token">

      <div class="container">
        <div class="row">
          <!-- Large modal -->



        </div>
      </div>


        <!-- Modal -->
        <div class="modal fade" id="agregarML" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
            <div class="modal-dialog" role="document">
                <div class="modal-content">
                    <div class="modal-header" style="margin-left: auto; margin-right: auto; width: 300px;">
                        <h5 class="modal-title" id="exampleModalLabel">Agregar usuario ML</h5>

                    </div>
                    <div class="modal-body" style="color: black !important;">

                      <div class="row" style="margin-left: auto; margin-right: auto; width: 300px;">
                          <div class="card">
                              <div class="card-header">
                              </div>
                              <div class="card-body">

                                 <form action="{{ url('customer_saveML') }}" method="post" id="form_saveML">
                                   {{ csrf_field() }}

                                   <div class="alert alert-danger" style="display:none" id="alert-ml">
                                     <p>Campo ML es obligatorio.</p>
                                   </div>

                                   <div id="user-result-div" class="input-group form-group">
                                     <span class="input-group-addon"><i class="fa fa-snapchat-ghost fa-fw"></i></span>
                                     <input value="" class="form-control" style="text-transform: uppercase;" type="text" name="ml_user" id="ml_user" autocomplete="off" spellcheck="false" placeholder="ML User" autofocus>
                                   </div>

                                   <input type="hidden" name="id_cliente" value="{{$customer->ID}}">

                                 </form>

                              </div>
                          </div>

                      </div>

                    </div>
                    <div class="modal-footer" style="margin-left: auto; margin-right: auto; width: 450px;">

                        <button type="button" id="saveML" class="btn btn-primary btn-block">Salvar Cambios</button>
                    </div>
                </div>
            </div>
        </div>

        <!-- Modal -->
        <div class="modal fade" id="modificarEmailModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
            <div class="modal-dialog" role="document">
                <div class="modal-content">
                    <div class="modal-header" style="margin-left: auto; margin-right: auto; width: 300px;">
                        <h5 class="modal-title" id="exampleModalLabel">Modificar Email</h5>

                    </div>
                    <div class="modal-body" style="color: black !important;">

                        <div class="row" style="margin-left: auto; margin-right: auto; width: 300px;">
                            <div class="card">
                                <div class="card-header">
                                </div>
                                <div class="card-body">

                                    <div class="alert alert-danger" style="display:none" id="alert-email">
                                      <p>Correo electronico no valido.</p>
                                    </div>

                                    <div>
                                        <label>Correo electrónico</label>
                                        {{-- <input type="text" class="form-control" id="correo_cliente" value=""> --}}
                                        <input type="hidden" id="idcustomer" value="">

                                        <div id="user-result-div2" class="input-group form-group">
                                          <span class="input-group-addon"><i class="fa fa-envelope-o fa-fw"></i></span>
                                          <input class="form-control" style="text-transform: lowercase;" type="text" name="correo_cliente" id="correo_cliente" autocomplete="off" spellcheck="false" placeholder="Email" autofocus>
                                          <span class="input-group-addon"><i id="user-result" class="fa fa-pencil" aria-hidden="true"></i></span>
                                        </div>
                                    </div>

                                </div>
                            </div>

                        </div>

                    </div>
                    <div class="modal-footer" style="margin-left: auto; margin-right: auto; width: 450px;">

                        <button type="button" id="saveEditEmail" class="btn btn-primary btn-block">Salvar Cambios</button>
                    </div>
                </div>
            </div>
        </div>

        <!-- Modal -->
        <div class="modal fade" id="modificarMLModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
            <div class="modal-dialog" role="document">
                <div class="modal-content">
                    <div class="modal-header" style="margin-left: auto; margin-right: auto; width: 450px;">
                        <h5 class="modal-title" id="exampleModalLabel">Modificar Usuario Mercado Libre</h5>

                    </div>
                    <div class="modal-body" style="color: black !important;">

                        <div class="row" style="margin-left: auto; margin-right: auto; width: 450px;">
                            <div class="card">
                                <div class="card-header">
                                </div>
                                <div class="card-body">

                                    <div>
                                        <label>Usuario Mercado Libre</label>
                                        <input type="text" class="form-control" id="ML_cliente" value="">
                                        <input type="hidden" id="idcustomer" value="">
                                    </div>

                                </div>
                            </div>

                        </div>

                    </div>
                    <div class="modal-footer" style="margin-left: auto; margin-right: auto; width: 450px;">

                        <button type="button" id="saveEditML" class="btn btn-primary btn-block">Salvar Cambios</button>
                    </div>
                </div>
            </div>
        </div>

        <!-- Modal -->
        <div class="modal fade" id="modificarOtrosModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
            <div class="modal-dialog" role="document">
                <div class="modal-content">
                    <div class="modal-header" style="margin-left: auto; margin-right: auto; width: 450px;">
                        <h5 class="modal-title" id="exampleModalLabel">Modificar Datos Varios</h5>

                    </div>
                    <div class="modal-body" style="color: black !important;">

                        <div class="row" style="margin-left: auto; margin-right: auto; width: 450px;">
                            <div class="card">
                                <div class="card-header">
                                </div>
                                <div class="card-body">

                                  <div class="alert alert-danger" style="display:none" id="alert-datos">
                                  </div>

                                    <div>
                                        <label>Provincia</label>
                                        <input type="text" class="form-control" id="provincia" autofocus value="">
                                        <input type="hidden" id="idcustomer" value="">
                                    </div>
                                    <div>
                                        <label>Ciudad</label>
                                        <input type="text" class="form-control" id="ciudad" value="">
                                    </div>
                                    <div>
                                        <label>Código de área</label>
                                        <input type="text" class="form-control" id="carac" value="">
                                    </div>
                                    <div>
                                        <label>Teléfono</label>
                                        <input type="text" class="form-control" id="tel" value="">
                                    </div>
                                    <div>
                                        <label>Celular</label>
                                        <input type="text" class="form-control" id="cel" value="">
                                    </div>

                                </div>
                            </div>

                        </div>

                    </div>
                    <div class="modal-footer" style="margin-left: auto; margin-right: auto; width: 450px;">

                        <button type="button" id="saveEditOtros" class="btn btn-primary btn-block">Salvar Cambios</button>
                    </div>
                </div>
            </div>
        </div>

        <!-- Modal -->
        <div class="modal fade" id="agregarNotaModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
            <div class="modal-dialog" role="document" style="top:40px;">
                <div class="modal-content">
                    
                    <div class="modal-body" style="color: black !important;text-align:center;padding:10px;">

                      <div class="container">
                        <h1 style="color:#000">Agregar Nota - Cliente #{{$customer->ID}}</h1>
                        <div class="row">

                            <div class="input-group form-group">
                              <span class="input-group-addon"><i class="fa fa-comment fa-fw"></i></span>
                              <textarea class="form-control" autofocus rows="4" name="notes" id="notaCliente" style="font-size: 22px;"></textarea>

                            </div>
                            <input type="hidden" id="idcustomer" value="">
                            <button class="btn btn-warning btn-block" id="saveNotes" type="button">Salvar Cambios</button>

                        </div>
                      </div>

                    </div>
                </div>
            </div>
        </div>

        <div class="modal fade" id="editarCliente" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
            <div class="modal-dialog" role="document" style="top:40px;">
                <div class="modal-content">

                  <div class="modal-header" style="margin-left: auto; margin-right: auto; width: 450px;">
                      <h5 class="modal-title" id="exampleModalLabel">Modificar Cliente #{{$customer->ID}}</h5>

                  </div>
                    
                    <div class="modal-body">

                      <div class="container">
                        <div class="row" style="margin-left: auto; margin-right: auto; width: 450px;">

                            <div class="card">
                              <div class="card-header"></div>
                              <div class="card-body">
                                <div class="input-group form-group">
                                  <span class="input-group-addon"><i class="fa fa-user fa-fw"></i></span>
                                  <input type="text" id="nombres_cliente" class="form-control">
                                
                                </div>
                                
                                <div class="input-group form-group">
                                  <span class="input-group-addon"><i class="fa fa-user fa-fw"></i></span>
                                  <input type="text" id="apellidos_cliente" class="form-control">
                                
                                </div>
                                <input type="hidden" id="idcustomer" value="{{$customer->ID}}">
                                
                              </div>
                            </div>

                        </div>
                      </div>

                    </div>
                    <div class="modal-footer" style="margin-left: auto; margin-right: auto; width: 450px;">

                        <button type="button" id="saveEditName" class="btn btn-primary btn-block">Salvar Cambios</button>
                    </div>
                </div>
            </div>
        </div>

        <!-- Modal -->
        <div class="modal fade" id="agregarfb" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
            <div class="modal-dialog" role="document">
                <div class="modal-content">
                    <div class="modal-header" style="margin-left: auto; margin-right: auto; width: 450px;">
                        <h5 class="modal-title" id="exampleModalLabel">Agregar Cuenta de Facebook</h5>

                    </div>
                    <div class="modal-body" style="color: black !important;">

                        <div class="row" style="margin-left: auto; margin-right: auto; width: 450px;">
                            <div class="card">
                                <div class="card-header">
                                </div>
                                <div class="card-body">

                                    <div class="alert alert-danger" id="alert-fb" style="display:none">
                                      <p>Coloque una URL de su cuenta de FB valida.</p>
                                    </div>

                                    <div>
                                        <input type="text" class="form-control" id="cuentafacebook">
                                        <input type="hidden" class="form-control" id="idcustomer">
                                    </div>
                                </div>
                            </div>

                        </div>

                    </div>
                    <div class="modal-footer" style="margin-left: auto; margin-right: auto; width: 450px;">

                        <button type="button" id="saveFB" class="btn btn-primary btn-block">Salvar</button>
                    </div>
                </div>
            </div>
        </div>

        <div class="modal fade modalVentas" id="modalVentas" tabindex="-1" role="dialog" aria-labelledby="myLargeModalLabel">
          <div class="modal-dialog modal-lg" style="top:40px;">
            <div class="modal-content">
              
              <div class="modal-body" style="text-align:center;padding:10px;">
              </div>
              
            </div>
          </div>
        </div>

        <div class="modal fade modalConfirm" id="modalConfirm" tabindex="-1" role="dialog" aria-labelledby="myLargeModalLabel">
          <div class="modal-dialog modal-lg" style="top:40px;">
            <div class="modal-content">
              
              <div class="modal-body" style="text-align:center;padding:10px;">
              </div>
              
            </div>
          </div>
        </div>

        <!-- Modal -->
        <div class="modal fade" id="modificarfb" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
            <div class="modal-dialog" role="document">
                <div class="modal-content">
                    <div class="modal-header" style="margin-left: auto; margin-right: auto; width: 450px;">
                        <h5 class="modal-title" id="exampleModalLabel">Modificar Cuenta de Facebook</h5>

                    </div>
                    <div class="modal-body" style="color: black !important;">

                        <div class="row" style="margin-left: auto; margin-right: auto; width: 450px;">
                            <div class="card">
                                <div class="card-header">
                                </div>
                                <div class="card-body">

                                  <div class="alert alert-danger" id="alert-fb-mod" style="display:none">
                                    <p>Coloque una URL de su cuenta de FB valida.</p>
                                  </div>

                                    <div>
                                        <input type="text" class="form-control" id="cuentafacebookmodify">
                                        <input type="hidden" class="form-control" id="idcustomer">
                                    </div>
                                </div>
                            </div>

                        </div>

                    </div>
                    <div class="modal-footer" style="margin-left: auto; margin-right: auto; width: 450px;">

                        <button type="button" id="saveModifyFB" class="btn btn-primary btn-block">Salvar</button>
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

@section('scripts')
    @parent
    <script type="text/javascript" src="{{ asset('js/typeahead.bundle.js') }}"></script>
    <script>
        $('#doseller').on('click', function (e) {

                var id = $('#doseller').data('customer');
                var token = $('#token').val();
                var datos = $('#doseller').data('update');
                console.log(id);
                    $.ajax({
                        data: { '_token':token, 'id':id, 'datos':datos },
                        method: 'post',
                        dataType: 'json',
                        url: '{{url('updateStatusReseller')}}',
                        success: function(result){
                            location.reload();
                        }
                    });
        });

        $('#saveML').on('click', function(){

          var ml_user = $('#ml_user').val();
          $('#alert-ml').fadeOut();

          if (ml_user != "") {
            $('#form_saveML').submit();
          } else {
            $('#alert-ml').fadeIn();
          }
        });

        $('#doreseller').on('click', function (e) {

            var id = $('#doreseller').data('customer');
            var token = $('#token').val();
            var datos = $('#doreseller').data('update');
            console.log(id);
            $.ajax({
                data: { '_token':token, 'id':id, 'datos':datos },
                method: 'post',
                dataType: 'json',
                url: '{{url('updateStatusReseller')}}',
                success: function(result){
                    location.reload();
                }
            });
        });

        $('#modificarNombre').on('click',function (e) {
            var ID = $('#modificarNombre').data('customer');
            var token = $('#token').val();
            $.ajax({
                method: 'post',
                url: '{{url('getDataName')}}',
                data: { '_token':token, 'id':ID },
                success: function (result) {
                    console.log(result.nombre);
                    $('.modal-body #nombres_cliente').val(result.nombre);
                    $('.modal-body #idcustomer').val(ID);
                    $('.modal-body #apellidos_cliente').val(result.apellido);
                }
            });
        });

        $('#saveEditName').on('click',function (e) {
            var nombre = $('#nombres_cliente').val();
            var apellido = $('#apellidos_cliente').val();
            var id = $('#idcustomer').val();
            var token = $('#token').val();
                $.ajax({
                   method: 'post',
                   url: '{{url('saveDataName')}}',
                   data: { '_token':token, 'nombre':nombre,'apellido':apellido,'id':id },
                    success: function (result) {
                        location.reload();
                    }
                });
        });

        $('#modificarEmailboton').on('click',function (e) {
            var ID = $('#modificarEmailboton').data('customer');
            var token = $('#token').val();
            $.ajax({
                method: 'post',
                url: '{{url('getDataName')}}',
                data: { '_token':token, 'id':ID },
                success: function (result) {
                    console.log(result.nombre);
                    $('.modal-body #correo_cliente').val(result.email);
                    $('.modal-body #idcustomer').val(ID);
                }
            });
        });

        var x_timer;
        $("#correo_cliente").keyup(function (e){
            clearTimeout(x_timer);
            var user_name = $(this).val();
            x_timer = setTimeout(function(){

               document.getElementById("user-result").className = "fa fa-spinner fa-pulse fa-fw";

                $.post('{{ url("customer_ctrl_email") }}', {'email':user_name}, function(data) {
                  if (data) {
                    document.getElementById("user-result").className = 'fa fa-ban';
                  }else{
                    document.getElementById("user-result").className = 'fa fa-check';
                  }
                  var test = document.getElementById("user-result");
                  var testClass = test.className;

                  switch(testClass){
                    case "fa fa-ban": document.getElementById("user-result-div2").className = "input-group form-group has-error"; break;
                    case "fa fa-check": document.getElementById("user-result-div2").className = "input-group form-group has-success"; break;
                  }

                });
            }, 1000);
        });

        $('#saveEditEmail').on('click',function (e) {
            var email = $('#correo_cliente').val();
            var id = $('#idcustomer').val();
            var token = $('#token').val();

            $('#alert-email').fadeOut();

            if (isEmail(email)) {
              $.ajax({
                  method: 'post',
                  url: '{{url("saveDataEmail")}}',
                  data: { '_token':token, 'email':email,'id':id },
                  success: function (response) {

                      var data = JSON.parse(response);

                      console.log(data);

                      if (data.status == 200) {
                          location.reload();
                      } else if(data.status == 505) {
                        $('#alert-email').html('<p>Este email ya lo estás usando como primario.</p>').fadeIn();
                      } else {
                        $('#alert-email').html('<p>Este email ya existe.</p>').fadeIn();
                      }
                  }
              });
            } else {
              $('#alert-email').fadeIn();
            }
            
        });

        $('#modificarMLboton').on('click',function (e) {
            var ID = $('#modificarMLboton').data('customer');
            var token = $('#token').val();
            $.ajax({
                method: 'post',
                url: '{{url('getDataName')}}',
                data: { '_token':token, 'id':ID },
                success: function (result) {
                    console.log(result.ml_user);
                    $('.modal-body #ML_cliente').val(result.ml_user);
                    $('.modal-body #idcustomer').val(ID);
                }
            });
        });

        $('#saveEditML').on('click',function (e) {
            var ml = $('#ML_cliente').val();
            var id = $('#idcustomer').val();
            var token = $('#token').val();
            $.ajax({
                method: 'post',
                url: '{{url('saveDataML')}}',
                data: { '_token':token, 'ml':ml,'id':id },
                success: function (result) {

                    location.reload();
                }
            });
        });

        $('#modificarOtrosboton').on('click',function (e) {
            var ID = $('#modificarOtrosboton').data('customer');
            var token = $('#token').val();
            $.ajax({
                method: 'post',
                url: '{{url('getDataName')}}',
                data: { '_token':token, 'id':ID },
                success: function (result) {
                    console.log(result.ml_user);
                    $('.modal-body #provincia').val(result.provincia);
                    $('.modal-body #ciudad').val(result.ciudad);
                    $('.modal-body #carac').val(result.carac);
                    $('.modal-body #tel').val(result.tel);
                    $('.modal-body #cel').val(result.cel);
                    $('.modal-body #idcustomer').val(ID);
                }
            });
        });

        $('#saveEditOtros').on('click',function (e) {
            var provincia = $('#provincia').val();
            var ciudad = $('#ciudad').val();
            var carac = $('#carac').val();
            var tel = $('#tel').val();
            var cel = $('#cel').val();
            var id = $('#idcustomer').val();
            var token = $('#token').val();

            $('#alert-datos').fadeOut();


            if (carac != "" || tel != "" || cel != "") {

              if (!codArea(carac) && carac != "") {
                $('#alert-datos').fadeIn().html('<p>El código de área no es valido. (Solo caracteres númericos)</p>');
                return 0;
              } else if (!isTlf(tel) && tel != "") {
                $('#alert-datos').fadeIn().html('<p>El Teléfono no es valido.</p>');
                return 0;
              } else if (!isTlf(cel) && cel != "") {
                $('#alert-datos').fadeIn().html('<p>El celular no es valido.</p>');
                return 0;
              }
            }

            $.ajax({
                method: 'post',
                url: '{{url('saveDataOther')}}',
                data: { '_token':token,'provincia':provincia,'ciudad':ciudad,'carac':carac,'tel':tel,'cel':cel,'id':id },
                success: function (result) {

                    location.reload();
                }
            });
            
        });

        $('#addNotes').on('click',function (e) {
            var ID = $('#addNotes').data('customer');
            $('.modal-body #idcustomer').val(ID);

        });

        $('#saveNotes').on('click',function (e) {
            $(this).prop('disabled', true);
            var notes = $('#notaCliente').val();
            var id = $('#idcustomer').val();
            var token = $('#token').val();
            $.ajax({
                method: 'post',
                url: '{{url('saveNotes')}}',
                data: { '_token':token,'notes':notes,'id':id },
                success: function (result) {

                    location.reload();
                }
            });
        });

       $('#agregaFacebook').on('click',function(e){
          e.preventDefault();
          var id = $('#agregaFacebook').data('customer');
          console.log(id);
          $('.modal-body #idcustomer').val(id);
       });

        $('#saveFB').on('click',function (e) {
            var face = $('#cuentafacebook').val();
            var id = $('#idcustomer').val();
            var token = $('#token').val();

            $('#alert-fb').fadeOut();

            if (isFB(face)) {
              $.ajax({
                  method: 'post',
                  url: '{{url('saveFB')}}',
                  data: { '_token':token,'face':face,'id':id },
                  success: function (result) {

                      location.reload();
                  }
              });
            } else {
              $('#alert-fb').fadeIn();
            }
            
        });

        $('#ModificarFacebook').on('click',function(e){
            e.preventDefault();
            var id = $('#ModificarFacebook').data('customer');
            var token = $('#token').val();
                $.ajax({
                    method: 'post',
                    url: '{{url('locateFB')}}',
                    data: { 'id':id, '_token':token },
                    success: function(result){
                        console.log(result.face);
                        $('#cuentafacebookmodify').val(result.face);
                        $('#idcustomer').val(result.ID);
                    }
                });
        });

        $('#saveModifyFB').on('click',function (e) {
            var face = $('#cuentafacebookmodify').val();
            var id = $('#idcustomer').val();
            var token = $('#token').val();

            if (isFB(face)) {
              $.ajax({
                  method: 'post',
                  url: '{{url('saveFB')}}',
                  data: { '_token':token,'face':face,'id':id },
                  success: function (result) {

                      location.reload();
                  }
              });
            } else {
              $('#alert-fb-mod').fadeIn();
            }


        });

        function isEmail(email) {
          var regex = /^[_a-zA-Z0-9-]+(\.[_a-zA-Z0-9-]+)*@[a-z0-9-]+(\.[a-z0-9-]+)*(\.[a-z]{2,3})$/;
          return regex.test(email);
        }

        function codArea(carac) {
          var regex = /^(\+?((\([0-9]+\))|([0-9]+)))|(\d+)$/g;
          return regex.test(carac);
        }

        function isTlf(tlf) {
          var regex = /^([0-9\-\+\(\)]{7,15})+$/g;
          return regex.test(tlf);
        }

        function isFB(fb) {
          var regex = /^(https:\/\/((www.facebook)|((web.)?facebook)).com\/)(profile.php\?)?[A-Za-z0-9.\-\_\=]+(\/)?$/;
          return regex.test(fb);
        }

        function enviarEmailVenta(ventas_id, tipo, consola=null, slot=null, cuentas_id=null){

          if (consola != null) {
            window.location.assign("{{ url('enviar_email_venta') }}/"+ventas_id+"/"+tipo+"/"+consola+"/"+slot+"/"+cuentas_id);
          } else {
            window.location.assign("{{ url('enviar_email_venta') }}/"+ventas_id+"/"+tipo);
          }
          
        }

        function inputFocus(input)
        {
          setTimeout(function(){
            document.getElementById(input).focus();
          }, 600);
        }

    </script>
@endsection
@endsection
