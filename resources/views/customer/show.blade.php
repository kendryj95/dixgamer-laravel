@extends('layouts.master-layouts')

@section('title', "Cliente #$customer->ID")

@section('container')

    @if($customer)
        <div class="container">
            <div class="row">
                <div class="col-md-6">
                    <h1>
                        Cliente #{{$customer->ID}}
                        @if($customer->auto === "si")
                            <a type="button" class="text-muted btn-xs text-danger"><i class="fa fa-star fa-2x"
                                                                                      aria-hidden="true"></i></a>
                        @elseif($customer->auto === "3ps4")
                            <a type="button" class="text-muted btn-xs text-danger"><i class="fa fa-star fa-2x"
                                                                                      aria-hidden="true"></i></a>
                            <a type="button" class="text-muted btn-xs text-danger"><i class="fa fa-star fa-2x"
                                                                                      aria-hidden="true"></i></a>
                        @endif
                    </h1>
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

            @if (isset($_GET['order']) && $_GET['order'] != '')
                <div class="alert alert-success text-center">
                    Filtrando el Pedido #{{$_GET['order']}}
                </div>
            @endif

            @if ($venta_plus_sec)
                <div class="alert alert-danger text-center">
                    Este cliente tiene juego PS4 y Playstation Plus con Slot Secundario
                </div>
            @endif


            <div class="row clientes_detalles">


                <div class="col-xs-12 col-sm-6 col-md-5">
                    <div class="panel panel-info">

                        <div class="panel-heading clearfix">
                            <h4 style="margin:0px;">

                                <i class="fa fa-user fa-fw" aria-hidden="true"></i>
                                {{$customer->nombre}} {{$customer->apellido}}

                                <button title="Modificar Nombre" id="modificarNombre"
                                        onclick="inputFocus('nombres_cliente')"
                                        class="btn btn-xs btn-default pull-right" type="button"
                                        data-toggle="modal" data-target="#editarCliente"
                                        data-customer="{{ $customer->ID }}">
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
                                        href="https://mail.google.com/mail/u/1/#search/{{ substr($customer->email, 0, strpos($customer->email, '@')) }}; "
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
                                        data-target="#modificarEmailModal" data-customer="{{ $customer->ID }}">

                                    <i aria-hidden="true" class="fa fa-pencil"></i>
                                </button>
                            </p>

                            @if ($othersEmails)
                                <p style="margin-left: 19px;margin-top: 0;">
                                    @foreach ($othersEmails as $email)
                                        <small class="text-muted">{{ $email->email }} <a
                                                    href="{{ url('customer_setEmailPrimary',[$email->id,$email->clientes_id]) }}"
                                                    title="Email primario"> <i class="fa fa-check"></i> </a></small>
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
                                    {{  Helper::notEmptyShow($customer->ciudad,$customer->ciudad) }}

                                    {{  Helper::notEmptyShow($customer->provincia, ', '. $customer->provincia) }}

                                    {{  Helper::notEmptyShow($customer->cp,', '.$customer->cp) }}

                                    {{  Helper::notEmptyShow($customer->pais,', '.$customer->pais) }}

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
                                    <br>
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
                                            data-target="#modificarfb" data-customer="{{ $customer->ID }}">
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
                                        data-target="#agregarNotaModal" data-customer="{{ $customer->ID }}">
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

                                <button
                                        id="addNotesBloqueo"
                                        data-customer="{{ $customer->ID }}"
                                        class="btn btn-xs @if ($customer->auto != 'bloq') btn-default @else btn-danger @endif pull-right"
                                        type="button"
                                        data-toggle="modal"
                                        data-target="#agregarNotaBloqueoModal">
                                    @if ($customer->auto != "bloq") Bloquear @else Desbloquear @endif
                                </button>

                                @if(Helper::validateAdministrator(session()->get('usuario')->Level))
                                    @if($customer->auto != 're')
                                        <a style="margin-right: 2px" href="#!" id="doreseller"
                                           data-customer="{{ $customer->ID }}" data-update="re" type="button"
                                           class="btn btn-default btn-xs pull-right">Hacer Revendedor</a>
                                    @else
                                        <a style="margin-right: 2px" href="#!" id="doseller"
                                           data-customer="{{ $customer->ID }}" data-update="no" type="button"
                                           class="btn btn-danger btn-xs pull-right">Revendedor</a>
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
                                            Anim pariatur cliche reprehenderit, enim eiusmod high life accusamus terry
                                            richardson ad squid. Nihil anim keffiyeh helvetica, craft beer labore wes
                                            anderson cred nesciunt sapiente ea proident.
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
                                            {!! html_entity_decode($note->Notas) !!}
                                        </div>


                                        <div class="text-right">
                                            @if (($note->Day) > "2018-03-02")

                                                <em
                                                        class="small text-muted"
                                                        style="opacity: 0.7">
                                                    <?php echo date("d M 'y", strtotime($note->Day)); ?>
                                                    ({{ $note->usuario }})
                                                </em>
                                            @endif
                                            @if(\Helper::validateAdministrator(session()->get('usuario')->Level))
                                                <div class="dropdown" title="Eliminar nota"
                                                     style="display: inline-block;">
                                                    <button class="btn btn-default btn-xs dropdown-toggle" type="button"
                                                            id="dropdownMenu1" data-toggle="dropdown"
                                                            aria-haspopup="true" aria-expanded="true"
                                                            style="background: transparent;border: none;padding-top: 0px;padding-bottom: 0px;padding-right: 0px;padding-left: 5px;">
                                                        <i aria-hidden="true" class="fa fa-remove text-muted"></i>
                                                    </button>
                                                    <ul style="" class="dropdown-menu" aria-labelledby="dropdownMenu1">
                                                        <li class="dropdown-header">¿Eliminar nota del cliente?</li>
                                                        <li role="separator" class="divider"></li>
                                                        <li><a href="{{ url('delete_notes',[$note->ID,'clientes']) }}">Sí,
                                                                Eliminar</a></li>
                                                    </ul>
                                                </div>
                                            @endif
                                        </div>
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
                        if (strpos($dataCustomer->medio_venta, 'Web') !== false): $text = 'W';
                            $color1 = \Helper::medioVentaColor($dataCustomer->medio_venta);
                        elseif (strpos($dataCustomer->medio_venta, 'Mail') !== false): $text = 'M';
                            $color1 = \Helper::medioVentaColor($dataCustomer->medio_venta);
                        elseif (strpos($dataCustomer->medio_venta, 'Mercado') !== false): $text = 'ML';
                            $color1 = \Helper::medioVentaColor($dataCustomer->medio_venta);
                        endif;
                        ?>

                        <?php
                        // MEDIO DE COBRO ADMINISTRABLE
                        $text2 = $dataCustomer->abbrev_medio_cobro;
                        $color2 = $dataCustomer->color_medio_cobro;
                        ?>


                        <?php
                        $color = Helper::userColor($dataCustomer->usuario);
                        ?>


                        <div class="col-xs-12 col-sm-6 col-md-3" style="display: inline-flex">
                            <div class=" thumbnail"
                                 <?php if ($dataCustomer->slot == 'Secundario'): ?>style="background-color:#efefef;"<?php endif; ?>>
              <span class="pull-right" style="width: 45%;">
                
                  <div class="dropdown pull-right">
                    <button class="btn btn-default btn-xs dropdown-toggle" type="button" id="dropdownMenu1"
                            data-toggle="dropdown" aria-haspopup="true" aria-expanded="true">
                      <i aria-hidden="true" class="fa fa-pencil"></i>
                    </button>
                    <ul class="dropdown-menu" aria-labelledby="dropdownMenu1">
                      <li><a href="javascript:void(0)" data-toggle="modal" data-target=".modalVentas"
                             onclick="getPageAjax('{{url("customer_ventas_modificar")}}/{{$dataCustomer->ID_ventas}}/1','#modalVentas')">Modificar cliente</a></li>
                      {{-- <li><a href="javascript:void(0)" data-toggle="modal" data-target=".modalVentas" onclick="getPageAjax('{{url("customer_ventas_modificar")}}/{{$dataCustomer->ID_ventas}}/2','#modalVentas')">Modificar medio venta</a></li> --}}
                      <li><a href="javascript:void(0)" data-toggle="modal" data-target=".modalVentas"
                             onclick="getPageAjax('{{url("customer_ventas_modificar")}}/{{$dataCustomer->ID_ventas}}/3','#modalVentas')">Modificar order</a></li>
                      {{--<li><a href="javascript:void(0)" data-toggle="modal" data-target=".modalVentas" onclick="getPageAjax('{{url("customer_ventas_modificar")}}/{{$dataCustomer->ID_ventas}}/5','#modalVentas')">Modificar manual</a></li> --}}
                      <li><a href="javascript:void(0)" data-toggle="modal" data-target=".modalVentas"
                             onclick="getPageAjax('{{url("customer_duplicar_venta")}}','#modalVentas', {{$dataCustomer->ID_ventas}})">Duplicar venta</a></li>
                      <li><a href="javascript:void(0)" data-toggle="modal" data-target=".modalVentas"
                             onclick="getPageAjax('{{url("customer_ventas_eliminar")}}','#modalVentas', {{$dataCustomer->ID_ventas}})">Eliminar venta y cobros</a></li>
                        @if(\Helper::validateAdministrator(session()->get('usuario')->Level) || session()->get('usuario')->Nombre === "Leo")
                            <li><a href="javascript:void(0)" data-toggle="modal" data-target=".modalVentas"
                                   onclick="getPageAjax('{{url("customer_ventas_eliminar", $dataCustomer->ID_ventas)}}?type=contracargo','#modalVentas')">Contracargo</a></li>
                        @endif
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
                          class="badge badge-{{ $dataCustomer->color_user }} pull-right"
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
                                      onclick="getPageAjax('{{ url("customer_ventas_cobro_modificar") }}','#modalVentas',{{ $valor }})">
                            {{$valor}}
                          </a>


                              <div class="dropdown"
                                   style="display: inline-block;@if($valor == "") visibility: hidden; @endif">
                            <button class="btn btn-default btn-xs dropdown-toggle" type="button" id="dropdownMenu1"
                                    data-toggle="dropdown" aria-haspopup="true" aria-expanded="true"
                                    style="background: transparent;border: none;padding-top: 0px;padding-bottom: 0px;padding-right: 0px;padding-left: 5px;"
                                    title="Eliminar cobro">
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
                          <span style="color:#CFCFCF; padding:0px 2px; font-size:0.8em;">{{$dataCustomer->ID_cobro}}</span>
                      @endif

                  </small>

                  <span @if(!Helper::validateAdminAnalystAsistent(session()->get('usuario')->Level)) style="margin-left: 40px;"
                        @endif class="btn-group pull-right">
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
                  <br/>


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
                                @php
                                    $link_ref_cobro = "";
                                    $text_ref_cobro = "";
                                    if (strpos($dataCustomer->medio_cobro, "MP") !== false) {
                                      $link_ref_cobro = "https://www.mercadopago.com.ar/activities?q=$valor";
                                      $text_ref_cobro = "MP";
                                    } else {
                                      $link_ref_cobro = "https://www.paypal.com/activity/payment/$valor";
                                      $text_ref_cobro = "PP";
                                    }
                                @endphp
                          <a
                                  style='padding: 0'
                                  title='ver cobro en {{$text_ref_cobro}}'
                                  target='_blank'
                                  class='btn-xs'
                                  type='button'
                                  href='{{$link_ref_cobro}}'>
                            <i aria-hidden='true' class='fa fa-external-link'></i>
                          </a>
                          @if(\Helper::validateAdministrator(session()->get('usuario')->Level))
                                    <a
                                            style='padding: 0'
                                            class='btn-xs'
                                            title='Actualizar importes de {{$text_ref_cobro}}'
                                            type='button'
                                            href='{{ url("update_amounts", [$valor, $customer->ID]) }}'>
                            <i class='fa fa-refresh' aria-hidden='true'></i>
                          </a>
                                @endif
                        </small>
                        @endforeach
                    @endif

                    <?php  // si hay un solo cobro ID y mas de 1 ref de cobro para ese ID (caso de array importado con varias ref de cobros desde MP) habilito la modif ?>
                    @if((($dataCustomer->ref_cobro != "") && ((count(explode(',', $dataCustomer->ID_cobro, 10))) != (count(explode(',', $dataCustomer->ref_cobro, 10))))) || (strpos($dataCustomer->medio_cobro, 'Banco') !== false && $dataCustomer->ref_cobro == ""))
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
                    @if ((strpos($dataCustomer->medio_venta, 'Web') !== false) && (is_null($dataCustomer->order_item_id)))
                        :
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
                  if (($dataCustomer->consola == 'ps4') && ($dataCustomer->slot == 'Primario')): $costo = round($dataCustomer->costo_usd * 0.6, 2);
                  elseif (($dataCustomer->consola == 'ps4') && ($dataCustomer->slot == 'Secundario')): $costo = round($dataCustomer->costo_usd * 0.4, 2);
                  elseif ($dataCustomer->consola == 'ps3'): $costo = round($dataCustomer->costo_usd * (1 / (2 + (2 * $dataCustomer->q_reset))), 2);
                  elseif ($dataCustomer->titulo == 'plus-12-meses-slot'): $costo = round($dataCustomer->costo_usd * 0.5, 2);
                  elseif (($dataCustomer->consola !== 'ps4') && ($dataCustomer->consola !== 'ps3') && ($dataCustomer->titulo !== 'plus-12-meses-slot')): $costo = round($dataCustomer->costo_usd, 2);
                  endif;

                  ?>


                  @if(Helper::validateAdministrator(session()->get('usuario')->Level))
                      @if(!empty($expensesIncome))
                          <?php
                          // $gtoestimado = round($expensesIncome[0]->gto_x_ing * $dataCustomer->precio);
                          $gtoestimado = round($dataCustomer->precio * 0.12, 2);

                          $iibbestimado = round($dataCustomer->precio * 0.04, 2);
                          $ganancia = round($dataCustomer->precio - $dataCustomer->comision - $costo - $gtoestimado - $iibbestimado, 2);

                          ?>
                      @endif


                  @endif

              <p>
                @if ($dataCustomer->slot == 'Secundario')
                      <span class="label label-danger pull-right" style="opacity:0.7">2°</span>
                  @endif

                <small class="text-success">
                  <i class="fa fa-dollar fa-xs fa-fw" aria-hidden="true"></i>
                  <?php echo round($dataCustomer->precio, 2); ?>
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

                <br/>
                <small class="text-danger">
                  <i
                          class="fa fa-dollar fa-xs fa-fw"
                          aria-hidden="true">
                  </i>

                  <?php echo round($dataCustomer->comision, 2); ?>
                    @if(Helper::validateAdministrator(session()->get('usuario')->Level))
                        <?php echo ', ' . $gtoestimado . ', ' . $iibbestimado . ', ' . $costo; ?>
                    @endif
                </small>
              </p>

              <?php  // si es un código lo muestro:?>
                  @if(($dataCustomer->code) && ($dataCustomer->slot == "No"))
                      <p>
                  <small class="">
                    {{ \Helper::formatCodeStock($dataCustomer->code) }}
                  </small>

                  @if(Helper::validateAdministrator(session()->get('usuario')->Level))
                              <?php
                              echo '<br><small style="color:#CFCFCF; font-size:0.6em;" class="caption text-center">' . $dataCustomer->code_prov;
                              echo '-' . $dataCustomer->n_order . '</small>';
                              ?>
                          @endif
                </p>
                  @endif

                  <button class="btn btn-xs btn-default pull-right" title="Agregar nota a la venta" data-toggle="modal"
                          data-target=".modalVentas"
                          onclick="getPageAjax('{{url("customer_ventas_modificar")}}/{{$dataCustomer->ID_ventas}}/4','#modalVentas')"><i
                              class="fa fa-comment"></i></button>
            </span>

                                <img
                                        class="img img-responsive img-rounded full-width"
                                        style="width:54%; margin:0;"
                                        alt="<?php echo $dataCustomer->titulo;?>"
                                        src="{{asset('img/productos')}}/<?php echo $dataCustomer->consola . "/" . $dataCustomer->titulo . ".jpg"; ?>">
                                <div class="clearfix"></div>

                                <div style="opacity: 0.3; padding: 4px 2px;">
                                    @if($dataCustomer->order_item_id)
                                        <span class="badge badge-normal"
                                              style="font-weight:400; font-size: 0.8em; color:#000">
                    oii #<?php echo $dataCustomer->order_item_id;?></span>
                                    @endif

                                    @if($dataCustomer->order_id_web)
                                        <span class="badge @if(isset($_GET['order']) && $_GET['order'] != '' && $_GET['order'] == $dataCustomer->order_id_web) badge-warning @else badge-normal @endif"
                                              style="font-weight:400; font-size: 0.8em; color:#000;">
                  Ped #{{$dataCustomer->order_id_web}}
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
                                        <span class="badge badge-warning"
                                              style="font-weight:400; font-size: 0.8em; color:#000">
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

                                @if($dataCustomer->estado != "pendiente" && $dataCustomer->estado != "listo")
                                <div style="padding: 4px 2px; text-align:center;">
                                    <span class="badge badge-inter badge-danger btn-block">
                                        {{ ucwords(str_replace("-"," ",$dataCustomer->estado)) }}
                                    </span>
                                </div>
                                @endif

                                <div class="caption text-center">
                                    <small
                                            style="color:#CFCFCF; line-height: 2em;"
                                            class="pull-left">

                                        <i class="fa fa-gamepad fa-fw" aria-hidden="true"></i>
                                        <?php echo $dataCustomer->ID_stock; ?>
                                        @if(Helper::validateAdministrator(session()->get('usuario')->Level))
                                            {{-- @if($dataCustomer->stock_Notas)
                                              <a
                                                href="#"
                                                data-toggle="popover"
                                                data-placement="bottom"
                                                data-trigger="focus"
                                                title="Notas de Stock"
                                                data-content="{{$dataCustomer->stock_Notas}}"
                                                style="color: #555555;">
                                                  <i class="fa fa-comment fa-fw"></i>
                                                </a>
                                            @endif --}}
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
                                            <button class="btn-xs dropdown-toggle" type="button" id="dropdownMenu1"
                                                    data-toggle="dropdown" aria-haspopup="true" aria-expanded="true"
                                                    style="background: transparent;border: none;padding-top: 0px;padding-bottom: 0px;padding-right: 0px;padding-left: 5px;">
                                                <i aria-hidden="true" class="fa fa-pencil text-muted"></i>
                                            </button>
                                            <ul style="top:-75px;left:25px" class="dropdown-menu"
                                                aria-labelledby="dropdownMenu1">
                                                <li class="dropdown-header">Modificar stock</li>
                                                <li role="separator" class="divider"></li>
                                                <li><a href="javascript:;" data-toggle="modal"
                                                       data-target=".modalVentas"
                                                       onclick='getPageAjax("{{ url('customer_ventas_modificar_producto') }}", "#modalVentas", {{ $dataCustomer->ID_ventas }})'>Automatico</a>
                                                </li>
                                                <li><a href="javascript:void(0)" data-toggle="modal"
                                                       data-target=".modalVentas"
                                                       onclick="getPageAjax('{{url("customer_ventas_modificar")}}/{{$dataCustomer->ID_ventas}}/5','#modalVentas')">Manual</a>
                                                </li>
                                            </ul>
                                        </div>

                                        @if ($dataCustomer->ID_stock != 0)
                                            <div class="dropdown" style="display: inline-block;">
                                                <button class="btn btn-default btn-xs dropdown-toggle" type="button"
                                                        id="dropdownMenu1" data-toggle="dropdown" aria-haspopup="true"
                                                        aria-expanded="true"
                                                        style="background: transparent;border: none;padding-top: 0px;padding-bottom: 0px;padding-right: 0px;padding-left: 5px;">
                                                    <i aria-hidden="true" class="fa fa-remove text-muted"></i>
                                                </button>
                                                <ul style="top:-200px;left:25px" class="dropdown-menu"
                                                    aria-labelledby="dropdownMenu1">
                                                    <li>
                                                        <a href="{{ url('customer_ventas_quitar_producto',$dataCustomer->ID_ventas) }}?state=not-verify">
                                                            No verifica</a>
                                                    </li>
                                                    <li>
                                                        <a href="{{ url('customer_ventas_quitar_producto',$dataCustomer->ID_ventas) }}?state=account-stolen">
                                                            Roba cuenta</a>
                                                    </li>
                                                    <li>
                                                        <a href="{{ url('customer_ventas_quitar_producto',$dataCustomer->ID_ventas) }}?state=sale-duplicated">
                                                            Vta duplicada</a>
                                                    </li>
                                                    <li role="separator" class="divider"></li>

                                                    @if($dataCustomer->consola == 'ps4')
                                                        @if($dataCustomer->slot == 'Secundario')
                                                            <li>
                                                                <a href="{{ url('customer_ventas_quitar_producto',$dataCustomer->ID_ventas) }}?slot={{$dataCustomer->slot}}">Sí,
                                                                    tal vez no usa</a></li>
                                                            <li role="separator" class="divider"></li>

                                                        @elseif($dataCustomer->slot == 'Primario')
                                                            <li>
                                                                <a href="{{ url('customer_ventas_quitar_producto',$dataCustomer->ID_ventas) }}?slot={{$dataCustomer->slot}}">Sí,
                                                                    ps4 no activa</a></li>
                                                            <li role="separator" class="divider"></li>

                                                        @endif

                                                    @elseif($dataCustomer->consola == 'ps3')
                                                        <li>
                                                            <a href="{{ url('customer_ventas_quitar_producto',$dataCustomer->ID_ventas) }}?cons={{$dataCustomer->consola}}">Sí,
                                                                no descargó</a></li>
                                                        <li role="separator" class="divider"></li>

                                                    @endif
                                                    <li><a href="javascript:void(0)"
                                                           onclick="modalCambioProd('{{$dataCustomer->ID_ventas}}')">Cambio</a></li>
                                                    <li>
                                                        <a href="{{ url('customer_ventas_quitar_producto',$dataCustomer->ID_ventas) }}?type=devolution">Devolución</a></li>
                                                </ul>
                                            </div>
                                        @endif

                                    </small>

                                    @if ($dataCustomer->cuentas_id)
                                        <a href="{{ url('cuentas', $dataCustomer->cuentas_id) }}" class="btn btn-xs"
                                           title="Ir a Cuenta">
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

                                    @if ($venta_plus_sec && $dataCustomer->slot == "Secundario" && $dataCustomer->consola == "ps4")
                                        <div class="dropdown" style="display: inline-block;">
                                            <button class="btn btn-<?php echo $colorcito;?> @if($dataCustomer->recup == 2 || $customer->auto == "bloq") disabled @endif btn-xs dropdown-toggle"
                                                    type="button" id="dropdownMenu1" data-toggle="dropdown"
                                                    aria-haspopup="true" aria-expanded="true">
                                                <i class="fa fa-paper-plane fa-xs fa-fw" aria-hidden="true"></i> <i
                                                        class="fa fa-info fa-xs fa-fw" aria-hidden="true"></i>
                                                @if($dataCustomer->datos1 > 0)
                                                    <?php echo '(' . $dataCustomer->datos1 . ')';  ?>
                                                @endif
                                            </button>
                                            <ul style="top:-75px;left:25px" class="dropdown-menu"
                                                aria-labelledby="dropdownMenu1">
                                                <li class="dropdown-header">Por favor confirmar</li>
                                                <li class="dropdown-header">si desea enviar
                                                    este {{$dataCustomer->titulo}}</li>
                                                <li role="separator" class="divider"></li>
                                                <li><a href="javascript:;"
                                                       onclick="enviarEmailVenta('{{ $dataCustomer->ID_ventas }}',this)">Sí,
                                                        Enviar</a></li>
                                            </ul>
                                        </div>
                                    @else
                                    <!--- aca entran los mails de juegos y ps plus slot pri y secu -->
                                        <button
                                                class="btn btn-<?php echo $colorcito;?> btn-xs @if($dataCustomer->recup == 2 || $customer->auto == "bloq") disabled @endif"
                                                type="button"
                                                onclick="enviarEmailVenta('{{ $dataCustomer->ID_ventas }}',this)">
                                            <i class="fa fa-paper-plane fa-xs fa-fw" aria-hidden="true"></i> <i
                                                    class="fa fa-info fa-xs fa-fw" aria-hidden="true"></i>
                                            @if($dataCustomer->datos1 > 0)
                                                <?php echo '(' . $dataCustomer->datos1 . ')';  ?>
                                            @endif
                                        </button>
                                    @endif


                                <!--- filtrar en gmail -->
                                    @if (\Helper::operatorsRecoverPri(session()->get('usuario')->Nombre))
                                        <a
                                                class="btn btn-xs btn-default"
                                                href="https://mail.google.com/mail/u/1/#search/<?php echo substr($dataCustomer->email, 0, strpos($dataCustomer->email, '@')) . '+' . substr($dataCustomer->mail_cta, 0, strpos($dataCustomer->mail_cta, '@')) . '+' . $dataCustomer->pass; ?>"
                                                title="filtrar guia de descarga en gmail"
                                                target="_blank">
                                            <i aria-hidden="true" class="fa fa-google"></i>
                                            mail
                                        </a>
                                    @else
                                        <a
                                                class="btn btn-xs btn-default"
                                                href="https://mail.google.com/mail/u/1/#search/<?php echo substr($dataCustomer->email, 0, strpos($dataCustomer->email, '@')) . '+' . $dataCustomer->titulo . '+(' . $dataCustomer->consola . ')'; ?>"
                                                title="filtrar guia de descarga en gmail"
                                                target="_blank">
                                            <i aria-hidden="true" class="fa fa-google"></i>
                                            mail
                                        </a>

                                    @endif


                                    @php
                                        $hoy = date('Y-m-d H:i:s');
                                        $fecha_venta = $dataCustomer->Day;
                                        $new_date = strtotime("+1 day", strtotime($fecha_venta));
                                        $new_date = date('Y-m-d H:i:s', $new_date);
                                        $mostrar_marcar_enviado = false;

                                        if ($hoy > $new_date) {
                                          $mostrar_marcar_enviado = true;
                                        }
                                    @endphp

                                    @if($colorcito == 'danger' && $mostrar_marcar_enviado)
                                        <div class="dropdown text-left">
                                            <button
                                                    class="btn btn-link dropdown-toggle btn-xs"
                                                    style="margin-left: -40px"
                                                    type="button" id="vender_secu_cli2"
                                                    data-toggle="dropdown"
                                                    aria-haspopup="true"
                                                    aria-expanded="false">
                                                Marcar como enviado
                                            </button>

                                            <ul class="dropdown-menu bg-info" aria-labelledby="vender_secu_cli2">
                                                <li class="dropdown-header">¿Estas seguro?</li>
                                                <li role="separator" class="divider"></li>
                                                <li>
                                                    <a href="{{ url('marcar_enviado', [$dataCustomer->ID_ventas]) }}"
                                                       class="btn btn-danger">Sí, Seguro!</a>
                                                </li>
                                            </ul>

                                        </div>
                                    @endif

                                    <span id="email_sale_success_{{$dataCustomer->ID_ventas}}"
                                          class="label label-success pull-right" style="display:none; margin: 5px 5px;">Email enviado</span>
                                    <span id="email_sale_error_{{$dataCustomer->ID_ventas}}"
                                          class="label label-danger pull-right" style="display:none; margin: 5px 5px;">Error al enviar email</span>
                                    <div class="clearfix"></div>
                                </div>

                                @if ($dataCustomer->cuentas_id != '' && $dataCustomer->recup == 2)
                                    @if ($dataCustomer->consola == 'ps4' && $dataCustomer->slot == 'Primario')
                                        @if (\Helper::showBtnPriSigueJugando($dataCustomer->cuentas_id,$dataCustomer->ID_ventas))
                                            <div style="margin-bottom: 5px" class="dropdown pull-right">
                                                <button
                                                        class="btn-copiador btn btn-primary dropdown-toggle btn-xs"
                                                        type="button" id="priSigueJugando2"
                                                        data-toggle="dropdown"
                                                        aria-haspopup="true"
                                                        data-clipboard-target="#reactivar-copy{{$dataCustomer->ID_ventas}}"
                                                        aria-expanded="false">
                                                    <i class="fa fa-fw fa-gamepad"></i>
                                                    Pri Sigue jugando
                                                </button>

                                                <ul class="dropdown-menu bg-info" aria-labelledby="priSigueJugando2">
                                                    <li class="dropdown-header">¿Seguro deseas</li>
                                                    <li class="dropdown-header">Ejecutarlo?</li>
                                                    <li role="separator" class="divider"></li>
                                                    <li>

                                                        <a
                                                                href="{{ url('nota_siguejugandopri', $dataCustomer->cuentas_id) }}"
                                                                class="btn btn-primary"
                                                                title="Primario Sigue jugando"
                                                                id="pri_sigue_jugando"
                                                        >
                                                            Si, seguro!
                                                        </a>
                                                    </li>
                                                </ul>

                                            </div>

                                            <div class="clearfix"></div>
                                        @endif
                                    @elseif($dataCustomer->consola == 'ps4' && $dataCustomer->slot == 'Secundario')
                                        @if (\Helper::showBtnSecuSigueJugando($dataCustomer->cuentas_id,$dataCustomer->ID_ventas))
                                            <div style="margin-bottom: 5px" class="dropdown pull-right">
                                                <button
                                                        class="btn-copiador btn btn-danger dropdown-toggle btn-xs"
                                                        type="button" id="secusiguejugando2"
                                                        data-toggle="dropdown"
                                                        aria-haspopup="true"
                                                        data-clipboard-target="#avisonewemail-copy{{$dataCustomer->ID_ventas}}"
                                                        aria-expanded="false">
                                                    <i class="fa fa-fw fa-gamepad"></i>
                                                    Secu Sigue jugando
                                                </button>

                                                <ul class="dropdown-menu bg-info" aria-labelledby="secusiguejugando2">
                                                    <li class="dropdown-header">¿Seguro deseas</li>
                                                    <li class="dropdown-header">Ejecutarlo?</li>
                                                    <li role="separator" class="divider"></li>
                                                    <li>

                                                        <a
                                                                href="{{ url('nota_siguejugando', $dataCustomer->cuentas_id) }}"
                                                                class="btn btn-danger"
                                                                title="Secu Sigue jugando"
                                                                id="secu_sigue_jugando"
                                                        >
                                                            Si, seguro!
                                                        </a>
                                                    </li>
                                                </ul>

                                            </div>

                                            <div class="clearfix"></div>
                                        @endif
                                    @endif
                                    @component('components.clipboards.account')
                                        @slot('clientes_id', $dataCustomer->ID_ventas)
                                        @slot('pass', $dataCustomer->pass)
                                        @slot('nombre_cliente', $customer->nombre)
                                        @slot('titulo', $dataCustomer->titulo)
                                        @slot('mail_fake', $dataCustomer->mail_cta)
                                        @slot('account_name', $dataCustomer->name_cta)
                                        @slot('account_surname', $dataCustomer->surname_cta)
                                        @slot('oferta_fortnite', $oferta_fortnite)
                                    @endcomponent
                                @endif

                                @if ($ventas_notas)
                                    @foreach ($ventas_notas as $id_venta => $notas)
                                        @if ($id_venta == $dataCustomer->ID_ventas)
                                            @php $i_nota = 0; @endphp
                                            @foreach ($notas as $venta_nota)
                                                @php $i_nota++; @endphp
                                                @if ($i_nota <= 3)
                                                    <div class="alert alert-warning"
                                                         style="padding: 4px 7px;margin:0px;opacity: 0.7;font-size: 0.9em">
                                                        <i class="fa fa-comment fa-fw"></i>

                                                        {!! $venta_nota['nota'] !!}
                                                    </div>

                                                    <div @if($venta_nota['nota_producto']) style="display: flex; justify-content: space-between"
                                                         @endif class="text-right">
                                                        @if ($venta_nota['nota_producto'])
                                                            <div class="dropdown">
                                                                <button class="btn btn-link btn-xs dropdown-toggle"
                                                                        type="button" id="re_asignar"
                                                                        data-toggle="dropdown" aria-haspopup="true"
                                                                        aria-expanded="true">
                                                                    Re-asignar
                                                                </button>
                                                                <ul style="" class="dropdown-menu"
                                                                    aria-labelledby="re_asignar">
                                                                    <li class="dropdown-header">¿Seguro desea re
                                                                        asignar?
                                                                    </li>
                                                                    <li role="separator" class="divider"></li>
                                                                    <li><a class="btn btn-danger"
                                                                           href="javascript:void(0)"
                                                                           onclick="btnReAsignar('{{$venta_nota['titulo']}}', '{{$venta_nota['consola']}}', '{{$venta_nota['slot']}}','{{ $dataCustomer->ID_ventas }}', event)">Sí,
                                                                            re asignar</a></li>
                                                                </ul>
                                                            </div>
                                                        @endif
                                                        <div>
                                                            <em
                                                                    class="small text-muted"
                                                                    style="opacity: 0.7;font-size: 0.8em">
                                                                {{ date("d M 'y", strtotime($venta_nota['Day'])) }}
                                                                ({{ $venta_nota['usuario'] }})
                                                            </em>
                                                            @if(\Helper::validateAdministrator(session()->get('usuario')->Level))
                                                                <div class="dropdown" title="Eliminar nota"
                                                                     style="display: inline-block;">
                                                                    <button class="btn btn-default btn-xs dropdown-toggle"
                                                                            type="button" id="dropdownMenu1"
                                                                            data-toggle="dropdown" aria-haspopup="true"
                                                                            aria-expanded="true"
                                                                            style="background: transparent;border: none;padding-top: 0px;padding-bottom: 0px;padding-right: 0px;padding-left: 5px;">
                                                                        <i aria-hidden="true"
                                                                           class="fa fa-remove text-muted"></i>
                                                                    </button>
                                                                    <ul style="" class="dropdown-menu"
                                                                        aria-labelledby="dropdownMenu1">
                                                                        <li class="dropdown-header">¿Eliminar nota de
                                                                            venta?
                                                                        </li>
                                                                        <li role="separator" class="divider"></li>
                                                                        <li>
                                                                            <a href="{{ url('delete_notes',[$venta_nota['ID'],'ventas']) }}">Sí,
                                                                                Eliminar</a></li>
                                                                    </ul>
                                                                </div>
                                                            @endif
                                                        </div>
                                                    </div>
                                                @else
                                                    <div class="text-center"
                                                         id="more_notes_{{$dataCustomer->ID_ventas}}">
                                                        <a href="javascript:void(0)"
                                                           onclick="cargar_notas('{{$dataCustomer->ID_ventas}}')">Ver
                                                            más +</a>
                                                    </div>
                                                    @break
                                                @endif
                                            @endforeach
                                        @endif
                                    @endforeach
                                @endif

                            </div>
                        </div>




                        <?php
                        $cerrardiv = array("8", "12", "16", "20", "24", "28", "32", "36", "40", "44", "48", "52");

                        if ((in_array($i, $cerrardiv)) or (($i - 2) == $salesByCustomer->Q)) {
                            echo "</div>";
                        }
                        ?>
                    @endforeach
                @endif


                <div class="clear" style="clear:both;"></div>


                @endif
            </div>


            <input type="hidden" value="{{ csrf_token() }}" id="token">

            <div class="container">
                <div class="row">
                    <!-- Large modal -->


                </div>
            </div>


            <!-- Modal -->
            <div class="modal fade" id="agregarML" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel"
                 aria-hidden="true">
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
                                                <span class="input-group-addon"><i
                                                            class="fa fa-snapchat-ghost fa-fw"></i></span>
                                                <input value="" class="form-control" style="text-transform: uppercase;"
                                                       type="text" name="ml_user" id="ml_user" autocomplete="off"
                                                       spellcheck="false" placeholder="ML User" autofocus>
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
            <div class="modal fade" id="modificarEmailModal" tabindex="-1" role="dialog"
                 aria-labelledby="exampleModalLabel" aria-hidden="true">
                <div class="modal-dialog" role="document">
                    <div class="modal-content">
                        <div class="modal-body" style="color: black !important;text-align:center;padding:10px">

                            <div class="container">
                                <div class="row" style="margin-left: auto; margin-right: auto">
                                    <div class="col-md-3"></div>
                                    <div class="col-md-6">
                                        <h1>Modificar Email</h1>

                                        <div class="alert alert-danger" style="display:none" id="alert-email">
                                            <p>Correo electronico no valido.</p>
                                        </div>

                                        <label>Correo electrónico</label>
                                        {{-- <input type="text" class="form-control" id="correo_cliente" value=""> --}}
                                        <input type="hidden" id="idcustomer" value="">

                                        <div id="user-result-div2" class="input-group form-group">
                                            <span class="input-group-addon"><i
                                                        class="fa fa-envelope-o fa-fw"></i></span>
                                            <input class="form-control" style="text-transform: lowercase;" type="text"
                                                   name="correo_cliente" id="correo_cliente" autocomplete="off"
                                                   spellcheck="false" placeholder="Email" autofocus>
                                            <span class="input-group-addon"><i id="user-result" class="fa fa-pencil"
                                                                               aria-hidden="true"></i></span>
                                        </div>
                                    </div>
                                    <div class="col-md-3"></div>

                                </div>
                            </div>

                        </div>
                        <div class="modal-footer" style="margin-left: auto; margin-right: auto; width: 450px;">

                            <button type="button" id="saveEditEmail" class="btn btn-primary btn-block">Salvar Cambios
                            </button>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Modal -->
            <div class="modal fade" id="modificarMLModal" tabindex="-1" role="dialog"
                 aria-labelledby="exampleModalLabel" aria-hidden="true">
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

                            <button type="button" id="saveEditML" class="btn btn-primary btn-block">Salvar Cambios
                            </button>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Modal -->
            <div class="modal fade" id="modificarOtrosModal" tabindex="-1" role="dialog"
                 aria-labelledby="exampleModalLabel" aria-hidden="true">
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

                            <button type="button" id="saveEditOtros" class="btn btn-primary btn-block">Salvar Cambios
                            </button>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Modal -->
            <div class="modal fade" id="agregarNotaModal" tabindex="-1" role="dialog"
                 aria-labelledby="exampleModalLabel" aria-hidden="true">
                <div class="modal-dialog" role="document" style="top:40px;">
                    <div class="modal-content">

                        <div class="modal-body" style="color: black !important;text-align:center;padding:10px;">

                            <div class="container">
                                <h1 style="color:#000">Agregar Nota - Cliente #{{$customer->ID}}</h1>
                                <div class="row">

                                    <div class="input-group form-group">
                                        <span class="input-group-addon"><i class="fa fa-comment fa-fw"></i></span>
                                        <textarea class="form-control" autofocus rows="4" name="notes" id="notaCliente"
                                                  style="font-size: 22px;"></textarea>

                                    </div>
                                    <input type="hidden" id="idcustomer" value="{{$customer->ID}}">
                                    <button class="btn btn-warning btn-block" id="saveNotes" type="button">Salvar
                                        Cambios
                                    </button>

                                </div>
                            </div>

                        </div>
                    </div>
                </div>
            </div>

            <div class="modal fade" id="agregarNotaBloqueoModal" tabindex="-1" role="dialog"
                 aria-labelledby="exampleModalLabel" aria-hidden="true">
                <div class="modal-dialog" role="document" style="top:40px;">
                    <div class="modal-content">

                        <div class="modal-body" style="color: black !important;text-align:center;padding:10px;">

                            <div class="container">
                                <h1 style="color:#000">@if ($customer->auto != "bloq") Bloquear @else Desbloquear @endif
                                    - Cliente #{{$customer->ID}}</h1>
                                <div class="row">

                                    <div class="input-group form-group">
                                        <span class="input-group-addon"><i class="fa fa-comment fa-fw"></i></span>
                                        <textarea class="form-control" autofocus rows="4" id="notaClienteBloqueo"
                                                  style="font-size: 22px;"
                                                  placeholder="Explique brevemente porqué va a @if ($customer->auto != "bloq") bloquear @else desbloquear @endif a este cliente"></textarea>

                                    </div>
                                    <input type="hidden" id="idcustomer" value="">
                                    <button class="btn btn-block @if ($customer->auto != 'bloq') btn-default @else btn-danger @endif"
                                            id="bloq" data-update="{{ $customer->auto != 'bloq' ? 'bloq' : 'no' }}"
                                            type="button">@if ($customer->auto != "bloq") Bloquear @else
                                            Desbloquear @endif</button>

                                </div>
                            </div>

                        </div>
                    </div>
                </div>
            </div>

            <div class="modal fade" id="agregarNotaCambio" tabindex="-1" role="dialog"
                 aria-labelledby="exampleModalLabel" aria-hidden="true">
                <div class="modal-dialog" role="document" style="top:40px;">
                    <div class="modal-content">

                        <div class="modal-body" style="color: black !important;text-align:center;padding:10px;">

                            <div class="container">
                                <h1 style="color:#000">Cambio producto - Venta #<span id="vta_id_text"></span></h1>
                                <div class="row">

                                    <form action="{{route('cambio-prod')}}" method="post">
                                        {{ csrf_field() }}
                                        <div class="input-group form-group">
                                            <span class="input-group-addon"><i class="fa fa-comment fa-fw"></i></span>
                                            <textarea class="form-control" autofocus rows="4" name="nota"
                                                      style="font-size: 22px;"
                                                      placeholder="Explique brevemente porqué va a cambiar el producto a esta venta"></textarea>

                                        </div>
                                        <input type="hidden" name="vta_id" id="vta_id">
                                        <button class="btn btn-block btn-primary" type="submit">Realizar cambio</button>
                                    </form>

                                </div>
                            </div>

                        </div>
                    </div>
                </div>
            </div>

            <div class="modal fade" id="editarCliente" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel"
                 aria-hidden="true">
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

                            <button type="button" id="saveEditName" class="btn btn-primary btn-block">Salvar Cambios
                            </button>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Modal -->
            <div class="modal fade" id="agregarfb" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel"
                 aria-hidden="true">
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

            <div class="modal fade modalVentas" id="modalVentas" tabindex="-1" role="dialog"
                 aria-labelledby="myLargeModalLabel">
                <div class="modal-dialog modal-lg" style="top:40px;">
                    <div class="modal-content">

                        <div class="modal-body" style="text-align:center;padding:10px;">
                        </div>

                    </div>
                </div>
            </div>

            <div class="modal fade modalConfirm" id="modalConfirm" tabindex="-1" role="dialog"
                 aria-labelledby="myLargeModalLabel">
                <div class="modal-dialog modal-lg" style="top:40px;">
                    <div class="modal-content">

                        <div class="modal-body" style="text-align:center;padding:10px;">
                        </div>

                    </div>
                </div>
            </div>

            <!-- Modal -->
            <div class="modal fade" id="modificarfb" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel"
                 aria-hidden="true">
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
                data: {'_token': token, 'id': id, 'datos': datos},
                method: 'post',
                dataType: 'json',
                url: '{{url('updateStatusReseller')}}',
                success: function (result) {
                    location.reload();
                }
            });
        });

        $('#saveML').on('click', function () {

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
                data: {'_token': token, 'id': id, 'datos': datos},
                method: 'post',
                dataType: 'json',
                url: '{{url('updateStatusReseller')}}',
                success: function (result) {
                    location.reload();
                }
            });
        });

        $('#bloq').on('click', function (e) {

            var id = $('#idcustomer').val();
            var nota = $('#notaClienteBloqueo').val();
            var token = $('#token').val();
            var datos = $('#bloq').data('update');
            $.ajax({
                data: {'_token': token, 'id': id, 'datos': datos, 'nota': nota},
                method: 'post',
                dataType: 'json',
                url: '{{url('updateStatusReseller')}}',
                success: function (result) {
                    location.reload();
                }
            });
        });

        $('#modificarNombre').on('click', function (e) {
            var ID = $('#modificarNombre').data('customer');
            var token = $('#token').val();
            $.ajax({
                method: 'post',
                url: '{{url('getDataName')}}',
                data: {'_token': token, 'id': ID},
                success: function (result) {
                    console.log(result.nombre);
                    $('.modal-body #nombres_cliente').val(result.nombre);
                    $('.modal-body #idcustomer').val(ID);
                    $('.modal-body #apellidos_cliente').val(result.apellido);
                }
            });
        });

        $('#saveEditName').on('click', function (e) {
            var nombre = $('#nombres_cliente').val();
            var apellido = $('#apellidos_cliente').val();
            var id = $('#idcustomer').val();
            var token = $('#token').val();
            $.ajax({
                method: 'post',
                url: '{{url('saveDataName')}}',
                data: {'_token': token, 'nombre': nombre, 'apellido': apellido, 'id': id},
                success: function (result) {
                    location.reload();
                }
            });
        });

        $('#modificarEmailboton').on('click', function (e) {
            var ID = $('#modificarEmailboton').data('customer');
            var token = $('#token').val();
            $.ajax({
                method: 'post',
                url: '{{url('getDataName')}}',
                data: {'_token': token, 'id': ID},
                success: function (result) {
                    console.log(result.nombre);
                    $('.modal-body #correo_cliente').val(result.email);
                    $('.modal-body #idcustomer').val(ID);
                }
            });
        });

        var x_timer;
        $("#correo_cliente").keyup(function (e) {
            clearTimeout(x_timer);
            var user_name = $(this).val();
            x_timer = setTimeout(function () {

                document.getElementById("user-result").className = "fa fa-spinner fa-pulse fa-fw";

                $.post('{{ url("customer_ctrl_email") }}', {'email': user_name}, function (data) {
                    if (data) {
                        document.getElementById("user-result").className = 'fa fa-ban';
                    } else {
                        document.getElementById("user-result").className = 'fa fa-check';
                    }
                    var test = document.getElementById("user-result");
                    var testClass = test.className;

                    switch (testClass) {
                        case "fa fa-ban":
                            document.getElementById("user-result-div2").className = "input-group form-group has-error";
                            break;
                        case "fa fa-check":
                            document.getElementById("user-result-div2").className = "input-group form-group has-success";
                            break;
                    }

                });
            }, 1000);
        });

        $('#saveEditEmail').on('click', function (e) {
            var email = $('#correo_cliente').val();
            var id = $('#idcustomer').val();
            var token = $('#token').val();

            $('#alert-email').fadeOut();

            if (isEmail(email)) {
                $.ajax({
                    method: 'post',
                    url: '{{url("saveDataEmail")}}',
                    data: {'_token': token, 'email': email, 'id': id},
                    success: function (response) {

                        var data = JSON.parse(response);

                        console.log(data);

                        if (data.status == 200) {
                            location.reload();
                        } else if (data.status == 505) {
                            $('#alert-email').html('<p>Este email ya lo estás usando como primario.</p>').fadeIn();
                        } else {
                            $('#alert-email').html('<p>Este email ya existe. Pertenece a #<a href="{{url("clientes")}}/' + data.id_cliente + '" class="alert-link">' + data.id_cliente + '</a></p>').fadeIn();
                        }
                    }
                });
            } else {
                $('#alert-email').fadeIn();
            }

        });

        $('#modificarMLboton').on('click', function (e) {
            var ID = $('#modificarMLboton').data('customer');
            var token = $('#token').val();
            $.ajax({
                method: 'post',
                url: '{{url('getDataName')}}',
                data: {'_token': token, 'id': ID},
                success: function (result) {
                    console.log(result.ml_user);
                    $('.modal-body #ML_cliente').val(result.ml_user);
                    $('.modal-body #idcustomer').val(ID);
                }
            });
        });

        $('#saveEditML').on('click', function (e) {
            var ml = $('#ML_cliente').val();
            var id = $('#idcustomer').val();
            var token = $('#token').val();
            $.ajax({
                method: 'post',
                url: '{{url('saveDataML')}}',
                data: {'_token': token, 'ml': ml, 'id': id},
                success: function (result) {

                    location.reload();
                }
            });
        });

        $('#modificarOtrosboton').on('click', function (e) {
            var ID = $('#modificarOtrosboton').data('customer');
            var token = $('#token').val();
            $.ajax({
                method: 'post',
                url: '{{url('getDataName')}}',
                data: {'_token': token, 'id': ID},
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

        $('#saveEditOtros').on('click', function (e) {
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
                data: {
                    '_token': token,
                    'provincia': provincia,
                    'ciudad': ciudad,
                    'carac': carac,
                    'tel': tel,
                    'cel': cel,
                    'id': id
                },
                success: function (result) {

                    location.reload();
                }
            });

        });

        $('#addNotes').on('click', function (e) {
            var ID = $('#addNotes').data('customer');
            $('.modal-body #idcustomer').val(ID);

        });

        $('#saveNotes').on('click', function (e) {
            $(this).prop('disabled', true);
            var notes = $('#notaCliente').val();
            var id = $('#idcustomer').val();
            var token = $('#token').val();
            $.ajax({
                method: 'post',
                url: '{{url('saveNotes')}}',
                data: {'_token': token, 'notes': notes, 'id': id},
                success: function (result) {

                    location.reload();
                }
            });
        });

        $('#addNotesBloqueo').on('click', function (e) {
            var ID = $('#addNotesBloqueo').data('customer');
            $('.modal-body #idcustomer').val(ID);

        });

        $('#agregaFacebook').on('click', function (e) {
            e.preventDefault();
            var id = $('#agregaFacebook').data('customer');
            console.log(id);
            $('.modal-body #idcustomer').val(id);
        });

        $('#saveFB').on('click', function (e) {
            var face = $('#cuentafacebook').val();
            var id = $('#idcustomer').val();
            var token = $('#token').val();

            $('#alert-fb').fadeOut();

            if (isFB(face)) {
                $.ajax({
                    method: 'post',
                    url: '{{url('saveFB')}}',
                    data: {'_token': token, 'face': face, 'id': id},
                    success: function (result) {

                        location.reload();
                    }
                });
            } else {
                $('#alert-fb').fadeIn();
            }

        });

        $('#ModificarFacebook').on('click', function (e) {
            e.preventDefault();
            var id = $('#ModificarFacebook').data('customer');
            var token = $('#token').val();
            $.ajax({
                method: 'post',
                url: '{{url('locateFB')}}',
                data: {'id': id, '_token': token},
                success: function (result) {
                    console.log(result.face);
                    $('#cuentafacebookmodify').val(result.face);
                    $('#idcustomer').val(result.ID);
                }
            });
        });

        $('#saveModifyFB').on('click', function (e) {
            var face = $('#cuentafacebookmodify').val();
            var id = $('#idcustomer').val();
            var token = $('#token').val();

            if (isFB(face)) {
                $.ajax({
                    method: 'post',
                    url: '{{url('saveFB')}}',
                    data: {'_token': token, 'face': face, 'id': id},
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

        function enviarEmailVenta(ventas_id, ele) {

            $(ele).addClass('disabled')
            var url = "{{ url('enviar_email_venta') }}/" + ventas_id;

            $.ajax({
                url: url,
                type: "GET",
                dataType: "json",
                success: function (response) {
                    if (response) {
                        switch (response.status) {
                            case "success": {
                                $(`#email_sale_success_${ventas_id}`).fadeIn();
                                $(ele).removeClass('disabled')
                                $(ele).html(`
                    <i class="fa fa-paper-plane fa-xs fa-fw" aria-hidden="true"></i> <i class="fa fa-info fa-xs fa-fw" aria-hidden="true"></i> (${response.qty})
                    `);
                            }
                                break;
                            case "error": {
                                $(`#email_sale_error_${ventas_id}`).fadeIn();
                                $(ele).removeClass('disabled')
                            }
                                break;
                        }
                    }
                },
                error: function (error) {
                    console.log(error)
                }
            })

        }

        function inputFocus(input) {
            setTimeout(function () {
                document.getElementById(input).focus();
            }, 600);
        }

        function btnReAsignar(titulo, cons, slot, id_venta, e) {
            if (cons == 'ps4') {
                url = `{{ url('customer_ventas_modificar_producto_store') }}/${cons}/${titulo}/${slot}/${id_venta}`;
            } else if (cons == 'ps3') {
                url = `{{ url('customer_ventas_modificar_producto_store') }}/${cons}/${titulo}/Primario/${id_venta}`;
            } else {
                url = `{{ url('customer_ventas_modificar_producto_store') }}/${cons}/${titulo}/No/${id_venta}`;
            }

            window.location.href = url;

            e.preventDefault();
        }

        function modalCambioProd(vta_id) {
            console.log("evento modal")
            $('#vta_id_text').text(vta_id);
            $('#vta_id').val(vta_id);
            $('#agregarNotaCambio').modal('show')
        }

        function cargar_notas(id_ventas) {
            if (id_ventas != '') {
                $.ajax({
                    url: "{{url('cargar_notas_ventas')}}/" + id_ventas,
                    type: "GET",
                    dataType: 'json',
                    success: function (response) {
                        var html = '';
                        if (response != null) {
                            for (const value of response.notas[id_ventas]) {
                                html += `<div class="alert alert-warning" style="padding: 4px 7px;margin:0px;opacity: 0.7;font-size: 0.9em">
                    <i class="fa fa-comment fa-fw"></i>
    
                    ${value.nota}
                  </div>`;

                                html += value.nota_producto ? `<div style="display: flex; justify-content: space-between" class="text-right">` : `<div class="text-right">`;

                                if (value.nota_producto) {
                                    html += `<div class="dropdown">
                        <button class="btn btn-link btn-xs dropdown-toggle" type="button" id="re_asignar" data-toggle="dropdown" aria-haspopup="true" aria-expanded="true">
                          Re-asignar
                        </button>
                        <ul style="" class="dropdown-menu" aria-labelledby="re_asignar">
                          <li class="dropdown-header">¿Seguro desea re asignar?</li>
                          <li role="separator" class="divider"></li>
                          <li><a class="btn btn-danger" href="javascript:void(0)" onclick="btnReAsignar('${value.titulo}', '${value.consola}', '${value.slot}','${id_ventas}', event)">Sí, re asignar</a></li>
                        </ul>
                      </div>`;
                                }

                                html += `<div>
                      <em
                      class="small text-muted"
                      style="opacity: 0.7;font-size: 0.8em">
                      ${value.Day_format}
                      (${value.usuario})
                      </em>`;

                                if (value.administrador) {
                                    html += `<div class="dropdown" title="Eliminar nota" style="display: inline-block;">
                        <button class="btn btn-default btn-xs dropdown-toggle" type="button" id="dropdownMenu1" data-toggle="dropdown" aria-haspopup="true" aria-expanded="true" style="background: transparent;border: none;padding-top: 0px;padding-bottom: 0px;padding-right: 0px;padding-left: 5px;">
                          <i aria-hidden="true" class="fa fa-remove text-muted"></i>
                        </button>
                        <ul style="" class="dropdown-menu" aria-labelledby="dropdownMenu1">
                          <li class="dropdown-header">¿Eliminar nota de venta?</li>
                          <li role="separator" class="divider"></li>
                          <li><a href="{{ url('delete_notes') }}/${value.ID}/ventas">Sí, Eliminar</a></li>
                        </ul>
                      </div>`;
                                }
                                html += `</div>
                  </div>`;

                            }

                            $('#more_notes_' + id_ventas).removeClass("text-center").html(html);
                        }
                    },
                    error: function (error) {
                        console.log(error);

                    }
                })
            }
        }

    </script>
@endsection
@endsection
