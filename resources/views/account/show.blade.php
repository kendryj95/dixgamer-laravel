@extends('layouts.master-layouts')

@php
	$saldo = 0.00; $saldoARS = 0.00;
	$color = Helper::userColor($account->usuario);
	$saldo_ultima_cta = 0;
	$almaceno_ultima_cta_id  = 0;
@endphp

@section('container')
<div class="container">
	<h1>
		Cuenta #{{$account->ID}}
		<a
			title="Cuenta anterior"
			style="color:#ccc;"
			href="{{ url('cuentas/'.((int)$account->ID-1)) }}"
			target="_self">
				<
		</a>

		<a
			title="Cuenta siguiente"
			style="color:#ccc;"
			href="{{ url('cuentas/'.((int)$account->ID+1)) }}"
			target="_self">
				>
		</a>
	</h1>
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

    	<div class="col-xs-12 col-sm-6 col-md-4">
        <div class="panel panel-warning">

            <div class="panel-heading clearfix">
              <h4 style="margin:0px;">
            		<a href="#"
									class="btn-copiador"
									data-clipboard-target="#email-copy"
									style="color:#FFF;">
									<span id="email-copy">
										{{$account->mail_fake}}
									</span>

									<i aria-hidden="true" class="fa fa-clone"></i>
								</a>
                <span class="btn-group pull-right">
                  <span
										class="btn btn-xs btn-default"
										type="button"
										data-toggle="modal"
										data-target=".bs-example-modal-lg"
										onClick='getPageAjax("/cuentas/{{$account->ID}}/edit","#modal-container");'>
										<i aria-hidden="true" class="fa fa-pencil"></i>
									</span>
                </span>
				       </h4>
            </div>

            <div class="panel-body" style="background-color: #efefef;">

		            <div class="dropdown pull-right">
		              <button
		                class="btn btn-default dropdown-toggle btn-xs"
		                type="button" id="dropdownMenu1"
		                data-toggle="dropdown"
		                aria-haspopup="true"
		                aria-expanded="false">
		                  <i class="fa fa-fw fa-refresh"></i>
		                  cambiar pass
		                  <span class="caret"></span>
		              </button>

		              <ul class="dropdown-menu bg-info" aria-labelledby="dropdownMenu1">
		                <li class="dropdown-header">¿Seguro deseas</li>
		                <li class="dropdown-header">cambiar el pass?</li>
		                <li role="separator" class="divider"></li>
		                <li>
											<form class=" text-center" action="{{url('actualizar_password_cuenta',[$account->ID])}}" method="post">
												{{ csrf_field() }}
												<button
													class="btn btn-danger"
			                    title="cambiar pass"
			                    type="submit">
		                    	Si, seguro!
		                    </button>
											</form>
		                </li>
		              </ul>

		            </div>

		            <p>
		            	<i class="fa fa-key fa-fw"></i>
		              <a href="#" class="btn-copiador" data-clipboard-target="#pass-copy">
		                <span id="pass-copy">{{$account->pass}}</span>
		                <i aria-hidden="true" class="fa fa-clone"></i>
		              </a>
		            </p>

					      <a class="btn btn-normal btn-xs pull-right" href="imacros://run/?m=abrir-sony.iim" style="opacity:0.5;">
		              Abrir Sony
		            </a>

		            <p>
		              <i class="fa fa-user fa-fw"></i>
		              <span id="name">{{$account->name}}</span>
		              <span id="surname">{{$account->surname}}</span>
		            </p>

		            @if(!empty($account->mail))

		              <p class="text-muted">

		                <i class="fa fa-user-secret fa-fw">
		                </i>

		                <span id="email-real">
		                  {{$account->mail}}
		                </span>

		                <span
		                  class="badge badge-{{$color}} pull-right"
		                  style="opacity:0.5; font-weight:400;"
		                  title="Registrado por {{$account->usuario}}">
											{{substr($account->usuario,0 , 1)}}
		                </span>

		              </p>
								@endif

		            <em
									class="text-muted"
									style="opacity:0.7; font-size:0.8em;">
										<i class="fa fa-map-marker fa-fw"></i>
										<span id="address">{{$account->address}}</span>,
										{{$account->city}},
										{{$account->state}},
										{{$account->pc}}
									</em>

									<span
										style="cursor: pointer"
										style="opacity:0.7; font-size:0.8em;"
										class="text-muted btn-xs"
										data-toggle="modal"
										data-target=".bs-example-modal-lg"
										onClick='getPageAjax("/editar_direccion_cuenta","#modal-container",{{$account->ID}});'>
											<i aria-hidden="true" class="fa fa-pencil"></i>
									</span>
									<br />

		            <p>
									<em class="text-muted" style="opacity:0.7;font-size:0.8em;"><i class="fa fa-question-circle fa-fw"></i>


										@if($account->ID > "5338")
											<span id="days">
												{{$account->days}}
											</span>
											-
											<span id="months">
												{{$account->year}}
											</span>
											-
											<span id="years">
												{{$account->years}}
											</span>
											; Nac:
											<span id="nacimiento">
												{{$account->nacimiento}}
											</span>

										@else

											@if($account->ID < "4115" || $account->ID < "4158")
												25 Dic 1990; Naciste: Corrientes
											@else
												@if (strpos($account->usuario, 'Victor') !== false)  03 Oct 1987; Visitar: Andorra
													@elseif (strpos($account->usuario, 'Manuel') !== false) 25 Dic 1990; Naciste: Corrientes
													@elseif (strpos($account->usuario, 'Leo') !== false) 02 Feb 1992; Visitar: Inglaterra
													@elseif (strpos($account->usuario, 'Hernan') !== false) 14 Nov 1986; Visitar: Italia
													@elseif (strpos($account->usuario, 'Mariano') !== false) 05 Jul 1993; Visitar: Polonia
													@elseif (strpos($account->usuario, 'Enri') !== false) 30 Nov 1987; Visitar: Egipto
												@endif
											@endif
										@endif

		            	</em>
								</p>
							  <p>
									<button
										class="btn btn-warning btn-xs"
										style="color: #8a6d3b; background-color:#FFDD87; opacity: 0.7"
										type="button"
										data-toggle="modal"
										data-target=".bs-example-modal-lg"
										onClick='getPageAjax("/crear_nota_cuenta","#modal-container",{{$account->ID}});'>
										<i class="fa fa-fw fa-comment"></i> Agregar Nota
									</button>
								</p>

		          </div>

          @if(!empty($hasBalance->costo_usd))

	          <ul class="list-group">
	          	<li class="list-group-item" style="background-color: #efefef;">


								@foreach($accountBalances as $balance)
									@if(!empty($balance->costo_usd))
										@if(Helper::validateAdministrator(Auth::user()->Level))
											<div class="dropdown" style="display:inline;">
												<button
													class="btn btn-default dropdown-toggle btn-xs"
													type="button"
													id="dropdownMenu2"
													data-toggle="dropdown"
													aria-haspopup="true"
													aria-expanded="false">
													<i class="fa fa-fw fa-trash-o"></i>
												</button>

												<ul class="dropdown-menu bg-info" aria-labelledby="dropdownMenu1">
													<li class="dropdown-header">¿Devolver GC?</li>
													<li role="separator" class="divider"></li>
													<li>
														<form action="{{ url('devolver_saldo_cuentas') }}" method="post">
															{{ csrf_field() }}
															<input type="hidden" name="id" value="{{$balance->stk_id}}">
															<input type="hidden" name="c_id" value="{{$balance->cuentas_id}}">
															<button type="submit" class="btn btn-danger btn-block">Si, seguro!</button>
														</form>

													</li>
												</ul>
											</div>
										@endif


									@endif

									@if(!empty($balance->costo_usd))
										<em>
			                <small
												class="label label-default">
												{{ str_replace('-', '', $balance->code) }}
											</small>

											@if(Helper::validateAdministrator(Auth::user()->Level))
												<span class="text-mued" style="font-size:0.6em;">
													({{ substr($balance->code_prov, 0 , 3) }}) {{$balance->n_order}}
												</span>
											@endif
												<span
													class="badge badge-{{$color}}"
													style="opacity:0.5; font-weight:400;"
													title="Fondeado por {{ $balance->usuario }}">
													{{ substr($balance->usuario ,0 , 1) }}
												</span>
			                <small
												class="pull-right text-muted">
													{{$balance->costo_usd}}
													@if(Helper::validateAdministrator(Auth::user()->Level))
														<span style="font-size:0.8em;">
															({{$balance->costo}})
														</span>
													@endif
												</small>
												<br />
			              </em>
									@endif

									@php
										$saldo = $saldo + $balance->costo_usd;
										$saldoARS = $saldoARS + $balance->costo;
									@endphp

								@endforeach

	          		<small
									class="pull-right"
									style="font-weight:bold;">
										saldo
										<img
											src="https://exur-exur.netdna-ssl.com/realskin/images/usa.png"
											style="opacity:0.7">
										<em style="border-top: 2px solid #cccccc; padding: 0 10px;">
											{{number_format($saldo , 2, '.', '')}}
											@if(Helper::validateAdministrator(Auth::user()->Level))
												<span style="font-size:0.8em;">
													({{number_format($saldoARS , 2, '.', '')}})
												</span>
											@endif
										</em>
									</small>
									<br />
							</li>

	          </ul>

          @endif
					<!-- END IF $hasBalance  -->


          <ul class="list-group">
              <li class="list-group-item" style="background-color: #efefef;">
              	@php
									$solicitud = strtotime($maxDayReset->Max_Day_Solicitado);
									$reseteo = strtotime($maxDayReset->Max_Day_Reseteado);
								@endphp

								@if ( ($maxDayReset->Max_Day_Solicitado === NULL) or (($solicitud < $reseteo) && ($maxDayReset->Max_Day_Reseteado != NULL)))

			 						<button
										class="btn btn-normal btn-xs pull-right"
										style="opacity: 0.5;"
										type="button"
										data-toggle="modal"
										data-target=".bs-example-modal-lg"
										onClick='getPageAjax("/solicitar_reseteo_cuenta","#modal-container",{{$account->ID}});'>
											<i class="fa fa-fw fa-power-off"></i>
											Pedir Reseteo
										</button>

				  			@else
				 						<span
											class="btn btn-danger btn-xs pull-right"
											style="opacity: 0.9;">
											<i class="fa fa-fw fa-check"></i>
											Reset Pendiente
										</span>
				 				@endif

						  	@if (($account->days_from_reset == NULL) || ($account->days_from_reset > 180))
									<div class="dropdown pull-left">
										<button
											class="btn btn-default dropdown-toggle btn-xs"
											type="button" id="dropdownMenu1"
											data-toggle="dropdown"
											aria-haspopup="true"
											aria-expanded="false">
												<i class="fa fa-fw fa-power-off"></i>
												Resetear
												<span class="caret"></span>
										</button>

										<ul class="dropdown-menu bg-info" aria-labelledby="dropdownMenu1">
											<li class="dropdown-header">¿Seguro deseas</li>
											<li class="dropdown-header">resetear la cuenta?</li>
											<li role="separator" class="divider"></li>
											<li>
												<form class=" text-center" action="{{url('resetear_cuenta',[$account->ID])}}" method="post">
													{{ csrf_field() }}
													<button
														class="btn btn-danger btn-block"
														title="cambiar pass"
														type="submit">
														Si, seguro!
													</button>
												</form>
											</li>
										</ul>

									</div>

									<div class="clearfix"></div>
							  @endif

                @if ($account->Q_reseteado)
					          	<em class="small" style="color:#BBB;">
												({{$account->Q_reseteado}}reset)
												hace {{$account->days_from_reset}}
												días
											</em>
								@endif

               </li>
            </ul>
        </div>
     </div>

  @if(count($stocks) > 0)
		@foreach($stocks as $stock)
    	<div class="col-xs-12 col-sm-4 col-md-3">
				<div class="thumbnail">
					<span class="pull-right" style="width: 45%;">
						<p>
							<span class="btn-group pull-right">
								<button
								class="btn btn-xs btn-default"
								type="button"
								data-toggle="modal"
								data-target=".bs-example-modal-lg"
								onClick='getPageAjax("/actualizar_stock_cuenta/{{$stock->ID_stock}}/{{$stock->stock_cuentas_id}}","#modal-container");'>
									<i class="fa fa-pencil"></i>
								</button>
							</span>

							<small
								style="color:#CFCFCF;"
								title="{{$stock->daystock}}">
									<i class="fa fa-calendar-o fa-xs fa-fw" aria-hidden="true"></i>
									{{ date("d M 'y", strtotime($stock->daystock)) }}
							</small>

						</p>

            		<p>
									<small style="color:#CFCFCF;">
										<i class="fa fa-gamepad fa-fw" aria-hidden="true"></i>
										{{ $stock->ID_stock}}

										@if(!empty($stock->stock_Notas))
						        	<a
												href="#"
												data-toggle="popover"
												data-placement="bottom"
												data-trigger="focus"
												title="Notas de Stock"
												data-content="{{$stock->stock_Notas}}"
												style="color: #555555;">
													<i class="fa fa-comment fa-fw"></i>
												</a>
										@endif
									</small>

            			<small style="color:#CFCFCF;">
										<i class="fa fa-shopping-cart fa-fw" aria-hidden="true"></i>
										{{$stock->Q_venta}}x
									</small>
            		</p>

            		<p>

									<small class="text-success">
										<i class="fa fa-dollar fa-xs fa-fw" aria-hidden="true"></i>
										{{ round($stock->total_ing) }}
									</small>
            			<br />

									<small class="text-danger">
										<i class="fa fa-dollar fa-xs fa-fw" aria-hidden="true"></i>
										{{ round($stock->total_com) }}
									</small>
									<br />

			            @if(Helper::validateAdministrator(Auth::user()->Level))

				            @php
											$gtoestimado = round($expensesIncome->gto_x_ing * $stock->total_ing);
					            $iibbestimado = round($stock->total_ing * 0.035);
											$resultado = round($stock->total_ing - $stock->total_com - $stock->costo - $iibbestimado - $gtoestimado);

				            @endphp

										<small class="text-danger">
											<i class="fa fa-dollar fa-xs fa-fw" aria-hidden="true"></i>
											{{$iibbestimado}}, {{$gtoestimado}}, {{round($stock->costo)}}
										</small>


										<hr style="margin:0px">

	            			<small class="<?php if ($resultado < '0'):?>text-danger<?php else:?>text-success<?php endif;?>">
											<i class="fa fa-dollar fa-xs fa-fw" aria-hidden="true"></i>
											{{$resultado}}
										</small>

            			@endif
            		</p>


            		@if(!empty($hasBalance->costo_usd) or Helper::validateAdministrator(Auth::user()->Level))

			            <p style="opacity:0.7">
										<img src="https://exur-exur.netdna-ssl.com/realskin/images/usa.png" style="opacity:0.7">

										<small>
											<strong>
												{{$stock->costo_usd}}
											</strong>
										</small>

										@if(!$quantityStock->Q > 1 )
											<a
												title="Modificar Costo"
												class="btn-xs text-muted"
												style="opacity: 0.7;"
												type="button"
												data-toggle="modal"
												data-target=".bs-example-modal-lg"
												onClick='document.getElementById("ifr").src="stock_modificar_costo.php?s_id={{$stock->ID_stock}}&c_id={{$stock->stock_cuentas_id}}";'>
												<i aria-hidden="true" class="fa fa-pencil"></i>
											</a>
										@endif

									</p>

            		@endif
            	</span>

            <img
							class="img img-responsive img-rounded full-width"
							style="width:54%; margin:0;"
							alt="{{$stock->titulo}}"
							src="/img/productos/{{$stock->consola}}/{{$stock->titulo}}.jpg">

						<div class="caption text-center">
            </div>

          </div>
        </div>
			@endforeach
    @endif





    <div class="col-md-2 pull-right">
			<p>
				<button
					class="btn btn-default btn-lg"
					type="button"
					data-toggle="modal"
					data-target=".bs-example-modal-lg"
					onClick='getPageAjax("/recharge_account","#modal-container",{{$account->ID}});'>
						<i class="fa fa-fw fa-dollar"></i>
						Cargar Saldo
				</button>
			</p>

     <?php if ($saldo > 0.00):?>

				<p>
					<button
						class="btn btn-primary btn-lg"
						type="button"
						data-toggle="modal"
						data-target=".bs-example-modal-lg"
						onClick='getPageAjax("/stock_insertar_cuenta","#modal-container",{{$account->ID}});'>
							<i class="fa fa-fw fa-gamepad"></i>
							Cargar Juego
					</button>
				</p>

				<p>
					<button
						class="btn btn-warning btn-lg"
						type="button"
						data-toggle="modal"
						data-target=".bs-example-modal-lg"
						onClick='getPageAjax("/stock_pre_insertar_cuenta","#modal-container",{{$account->ID}});'>
							<i class="fa fa-fw fa-th"></i>
							Carga Masiva
						</button>
					</p>

					<?php if(!($quantityStock)): ?>

						<div class="text-center" style="background-color:#f1f1f1; border-color:#fcfcfc; border: 1px solid; padding: 5px; margin: 5px 10px 5px 0">
							@foreach($lastAccountGames as $last)
								<div class="col-sm-5" >
									<div>
										<img src="/img/productos/<?php echo $last->consola."/".$last->titulo.".jpg"; ?>"
											 alt="<?php echo $last->consola." - ".$last->titulo.".jpg"; ?>" class="img img-responsive full-width" />
									</div>

									<div class="caption text-center" style="margin:5px 0;">
										<span class="badge badge-normal"><?php echo $last->costo_usd; ?> usd</span>
										<?php $saldo_ultima_cta = $saldo_ultima_cta + $last->costo_usd;?>
									</div>
								</div>
								<?php $almaceno_ultima_cta_id = $last->ID;?>
							@endforeach

							<?php if ($saldo <  $saldo_ultima_cta):?><p class="badge badge-danger">Se necesita <?php echo $saldo_ultima_cta;?> usd</p>
							<?php else:?>
								<form class="" action="{{ url('/repetir_ultima_cuenta',[$account->ID]) }}" method="post">
									{{ csrf_field() }}
									<input type="hidden" name="last_account" value="{{$almaceno_ultima_cta_id}}">
									<input type="hidden" name="saldo_usd" value="{{$saldo}}">
									<input type="hidden" name="saldo_ars" value="{{$saldoARS}}">
									<button
										class="btn btn-normal btn-xl"
										type="submit">
											<i class="fa fa-fw fa-step-backward"></i>
											Igual Ultima Cta
									</button>
								</form>
							<?php endif;?>
						</div>
					<?php endif;?>

     	<?php endif;?>
     </div>



  </div>

  @if (count($soldConcept)>0)

    <div class="table-responsive">
    <table border="0" align="center" cellpadding="0" cellspacing="5" class="table table-striped">
    <thead>
              <tr>
                <th>Fecha</th>
                <th>Cliente</th>
                <th>Email</th>
                <th>Juego</th>
                <th style="text-align:right;"></th>
              </tr>
            </thead>
		  <tbody>
        @foreach($soldConcept as $sc)

            <td><?php echo date("d M 'y", strtotime($sc->Day)); ?></td>
            <?php if ($sc->concepto == 'contra'):?>
            <td colspan="4"><em class="badge badge-default" style="font-weight:normal; opacity:0.8;"><i class="fa fa-key fa-fw"></i> Nueva contra: <?php echo $sc->new_pass;?> (<?php echo $sc->usuario;?>)</em></td>
			<?php elseif ($sc->concepto == 'notas'):?>
			<td colspan="4"><div class="alert alert-warning" style="color: #8a6d3b; background-color:#FFDD87; padding: 4px 7px; font-size: 12px; font-style:italic; margin:0px; opacity: 0.9;"><i class="fa fa-comment fa-fw"></i> <?php echo $sc->new_pass;?> (<?php echo $sc->usuario;?>)</em></td>
			<?php elseif ($sc->concepto == 'reset'):?>
            <td colspan="4"><em class="badge badge-danger" style="font-weight:normal; opacity:0.8;"><i class="fa fa-power-off fa-fw" aria-hidden="true"></i> Reseteado por <?php echo $sc->usuario;?></em></td>
					<?php elseif ($sc->concepto == 'resetear'):?>
            <td colspan="4"><em class="badge badge-danger" style="font-weight:normal; opacity:0.8;"><i class="fa fa-power-off fa-fw" aria-hidden="true"></i> Solicitud de Reseteo por <?php echo $sc->usuario;?></em></td>
            <?php else:?>
            <td><span class="text-muted small">#<?php echo $sc->clientes_id; ?></span> <span class="label label-info"><?php echo $sc->nombre; ?> <?php echo $sc->apellido; ?></span>
            <?php if ($sc->clientes_Notas):?><a href="#" data-toggle="popover" data-placement="bottom" data-trigger="focus" title="Notas de Cliente" data-content="<?php echo $sc->clientes_Notas; ?>" style="color: #555555;"><i class="fa fa-comment fa-fw"></i></a><?php endif; ?></td>

            <td id="<?php echo $sc->clientes_id; ?>"><a title="Ir a Cliente" href="clientes_detalles.php?id=<?php echo $sc->clientes_id; ?>"><?php echo $sc->email; ?></a> <a class="btn btn-xs btn-default" style="opacity:0.6;" href="https://mail.google.com/a/dixgamer.com/#search/<?php echo substr($sc->email, 0, strpos($sc->email, '@')) . '+' . substr($account->mail_fake, 0, strpos($account->mail_fake, '@')); ?>" title="filtrar guia de descarga en gmail" target="_blank"><i aria-hidden="true" class="fa fa-google"></i>mail</a>

				<!--- Mensajes predefinidos con guia de re activar o cambio de contraseña -->
				<div style="position:absolute; top:0; left:-500px;">
					<textarea id="newpass-copy" type="text" rows="1" cols="2"><?php echo "Actualizamos la contraseña de ésta Cuenta/Usuario: " . $account->pass . "\r\n\r\nSaludos, " . $vendedor. " de DixGamer.";?></textarea>
					<textarea id="reactivar-copy" type="text" rows="1" cols="2"><?php echo "Por favor ingresá a nuestra cuenta/usuario una vez más para RE ACTIVAR tu slot primario, una vez dentro de nuestro usuario:\r\n\r\n1) Ir a Configuración > PSN/Administración de cuentas > Activar como tu PS4 principal > Activar\r\n2) Ir a Configuración > PSN/Administración de cuentas > Restaurar Licencias > Restaurar\r\n3) Reiniciar tu consola y acceder con tu usuario personal, recordá no volver a abrir nuestro usuario.\r\n\r\nContraseña: " . $account->pass . "\r\n\r\nSaludos, " . $vendedor. " de DixGamer.";?></textarea>
				</div>
				<?php if (strpos($sc->consola, 'ps4') !== false):?>
					<?php if ($sc->slot == 'Primario'):?>
					<a href="#<?php echo $sc->clientes_id; ?>" class="btn-copiador btn-xs btn-info label" data-clipboard-target="#reactivar-copy">msj react <i aria-hidden="true" class="fa fa-clone"></i></a>
					<?php else: ?>
					<a href="#<?php echo $sc->clientes_id; ?>" class="btn-copiador btn-xs btn-info label" data-clipboard-target="#newpass-copy"> msj pass <i aria-hidden="true" class="fa fa-clone"></i></a>
					<?php endif;?>
				<?php endif;?>

			  </td>
            <td><span class="label <?php if ($sc->slot == 'Primario'):?>label-default<?php endif;?>"><?php echo $sc->titulo; ?></span> <?php if ($sc->slot == 'Secundario'): ?><span class="label label-danger" style="opacity:0.7">2°</span><?php endif; ?> <span class="label label-default <?php echo $sc->consola; ?>"><?php echo $sc->consola; ?></span> <?php echo $sc->ID_ventas; ?> <?php if ($sc->ventas_Notas):?><a href="#" data-toggle="popover" data-placement="bottom" data-trigger="focus" title="Notas de Venta" data-content="<?php echo $sc->ventas_Notas; ?>" style="color: #555555;"><i class="fa fa-comment fa-fw"></i></a><?php endif; ?></td>

            <td style="text-align:right;"><a class="btn btn-xs btn-default" type="button" title="Modificar venta" href="ventas_modificar.php?id=<?php echo $sc->ID_ventas; ?>"><i aria-hidden="true" class="fa fa-pencil"></i></a></td>
            <?php endif;?>
          </tr>
      @endforeach
        </tbody>
        </table>
        </div>
    @else
			<h3 class="text-center">Datos no encontrados</h3>
    @endif
	  <div class="container">
	    <div class="row">
        <!-- Large modal -->

				<div class="modal fade bs-example-modal-lg" id="modal-container" tabindex="-1" role="dialog" aria-labelledby="myLargeModalLabel">
				  <div class="modal-dialog modal-lg" style="top:40px;">
						<div class="modal-content">
							
							<div class="modal-body" style="text-align:center;padding:10px;">
						  </div>
							
						</div>
				  </div>
				</div>

			</div>
		</div>
     <!--/row-->
     <!-- InstanceEndEditable -->
    </div><!--/.container-->
    </div><!--/.container-->

@endsection
