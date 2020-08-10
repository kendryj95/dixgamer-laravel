@extends('layouts.master-layouts')

@section('title',"Cuenta #$account->ID")

@php
	$saldo = 0.00; $saldoARS = 0.00;
	$color = $account->color_user;
	$saldo_ultima_cta = 0;
	$almaceno_ultima_cta_id  = 0;
	$mostrar_carga_minim = true;
@endphp

@section('container')
<div class="container">

	<div class="row">
		<div class="col-md-6">
			<h1>
				Cuenta #{{$account->ID}}

				@if (isset($back->ID))
				<a
					title="Cuenta anterior"
					style="color:#ccc;"
					id="paginaAnt"
					href="{{ url('cuentas/'.$back->ID) }}"
					target="_self">
						<
				</a>
				@endif
				@if(!empty($next))
				<a
					title="Cuenta siguiente"
					style="color:#ccc;"
					id="paginaPrev"
					href="{{ url('cuentas/'.$next->ID) }}"
					target="_self">
						>
				</a>
				@else
				@endif
			</h1>
		</div>
		<div class="col-md-6">
			@if(Session::has('alert_cuenta'))
			  <div class="alert alert-success" role="alert">
			      <span class="glyphicon glyphicon-exclamation-sign" aria-hidden="true"></span>
			      <span class="sr-only">{{ Session::get('alert_cuenta')->title }}:</span>
			      {{ Session::get('alert_cuenta')->body }}
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
										onClick='getPageAjax("{{url('cuentas/'.$account->ID.'/edit')}}","#modal-container");'>
										<i aria-hidden="true" class="fa fa-pencil"></i>
									</span>
                </span>
				       </h4>
            </div>

            <div class="panel-body" style="background-color: #efefef;">

            	@if(strpos($account->mail_fake, 'yopmail') !== false && \Helper::operatorsRecoverSecu(session()->get('usuario')->Nombre))

            	<div class="dropdown">
            		<button
            		class="btn btn-secondary btn-xs dropdown-toggle"
            		style="opacity: 0.5;"
            		type="button"
            		data-toggle="dropdown"
            		aria-haspopup="true"
            		aria-expanded="false">
            			<b><i class="fa fa-fw fa-refresh"></i> Cambiar Email a DixGamer</b>
            			<span class="caret"></span>
            		</button>

            		<ul style="top: -79px; left: 40px" class="dropdown-menu bg-info" aria-labelledby="dropdownMenu1">
            			<li class="dropdown-header">¿Seguro deseas cambiar el email?</li>
            			<li role="separator" class="divider"></li>
            			<li>
            				<a href="{{ url('change_email_dixgamer', $account->ID) }}">Sí, Cambiar</a>
            			</li>
            		</ul>
            	</div><br>

            	@endif

            	@php
            		$vendedor = session()->get('usuario')->Nombre;
            		$texto_pass = 'cambiar pass';
            		$param = null;
            	@endphp


		            <div class="dropdown pull-right">
		              <button
		                class="btn btn-default dropdown-toggle btn-xs"
		                type="button" id="dropdownMenu1"
		                data-toggle="dropdown"
		                aria-haspopup="true"
		                aria-expanded="false">
		                  <i class="fa fa-fw fa-refresh"></i>
		                  {{ $texto_pass }}
		                  <span class="caret"></span>
		              </button>

		              <ul class="dropdown-menu bg-info" aria-labelledby="dropdownMenu1">
		                <li class="dropdown-header">¿Seguro deseas</li>
		                <li class="dropdown-header">{{ $texto_pass }}?</li>
		                <li role="separator" class="divider"></li>
		                <li>
											<form class=" text-center" id="form_cambiar_pass" action="{{url('actualizar_password_cuenta',[$account->ID, $param])}}" method="post">
												{{ csrf_field() }}
												<button
													class="btn btn-danger"
								title="cambiar pass"
								id="cambiar_pass"
			                    type="button">
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

					      <!-- <a class="btn btn-normal btn-xs pull-right" href="imacros://run/?m=abrir-sony.iim" style="opacity:0.5;">
					      		              Abrir Sony
					      		            </a> -->

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
										onClick='getPageAjax("{{url('editar_direccion_cuenta')}}","#modal-container",{{$account->ID}});'>
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
												{{$account->months}}
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
										onClick='getPageAjax("{{url('crear_nota_cuenta')}}","#modal-container",{{$account->ID}});'>
										<i class="fa fa-fw fa-comment"></i> Agregar Nota
									</button>
								</p>

								@if($operador_pass || $operador_reset)

									@if($operador_pass)
									
									<div class="dropdown pull-right">
										<button
										class="btn btn-danger dropdown-toggle btn-xs"
										type="button" id="secusiguejugando"
										data-toggle="dropdown"
										aria-haspopup="true"
										aria-expanded="false">
										<i class="fa fa-fw fa-gamepad"></i>
											Secu Sigue jugando
										</button>
				
										<ul class="dropdown-menu bg-info" aria-labelledby="secusiguejugando">
										<li class="dropdown-header">¿Seguro deseas</li>
										<li class="dropdown-header">Ejecutarlo?</li>
										<li role="separator" class="divider"></li>
										<li>
															
											<a
											href="{{ url('nota_siguejugando', $account->ID) }}"
												class="btn btn-danger"
												title="Secu Sigue jugando"
												id="secu_sigue_jugando"
												>
												Si, seguro!
											</a>
										</li>
										</ul>
				
									</div>

									@endif

									@if($operador_reset)

									<div class="dropdown pull-right">
										<button
										class="btn btn-primary dropdown-toggle btn-xs"
										type="button" id="priSigueJugando"
										data-toggle="dropdown"
										aria-haspopup="true"
										aria-expanded="false">
										<i class="fa fa-fw fa-gamepad"></i>
											Pri Sigue jugando
										</button>
				
										<ul class="dropdown-menu bg-info" aria-labelledby="priSigueJugando">
										<li class="dropdown-header">¿Seguro deseas</li>
										<li class="dropdown-header">Ejecutarlo?</li>
										<li role="separator" class="divider"></li>
										<li>
															
											<a
											href="{{ url('nota_siguejugandopri', $account->ID) }}"
												class="btn btn-danger"
												title="Primario Sigue jugando"
												id="pri_sigue_jugando"
												>
												Si, seguro!
											</a>
										</li>
										</ul>
				
									</div>
									@endif


								<br>

								@endif

		          </div>

          @if(!empty($hasBalance->costo_usd))

	          <ul class="list-group">
	          	<li class="list-group-item" style="background-color: #efefef;">


								@foreach($accountBalances as $balance)
									@if(!empty($balance->costo_usd))
										@if((Helper::validateAdministrator(session()->get('usuario')->Level) || Helper::validateAdministrator(session()->get('usuario')->Nombre == "Leo")) && $balance->code != NULL)
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
								@if ($balance->code != NULL)
									@php $hoy = date('Y-m-d H:i:s') @endphp
									@if($balance->costo_usd < 10 && $balance->validacion == 'Mostrar')
									<div class="dropdown" style="display:inline;">
										<button
											class="btn btn-default dropdown-toggle btn-xs"
											type="button"
											id="dropdownMenu4"
											data-toggle="dropdown"
											aria-haspopup="true"
											aria-expanded="false">
											<i class="fa fa-fw fa-clock-o"></i>
										</button>

										<ul class="dropdown-menu bg-info" aria-labelledby="dropdownMenu4">
											<li class="dropdown-header">Congelar TC?</li>
											<li role="separator" class="divider"></li>
											<li>
												<form action="{{ url('congelar_tc') }}" method="post">
													{{ csrf_field() }}
													<input type="hidden" name="id" value="{{$balance->ID}}">
													<button type="submit" class="btn btn-danger btn-block">Si, seguro!</button>
												</form>

											</li>
										</ul>
									</div>
									@endif
									@php $colorLabel = $balance->Day > $hoy ? 'success' : 'default' @endphp
										<em>
							<small
												title="{{$balance->Day}}"
												class="label label-{{$colorLabel}}">
												{{ str_replace('-', '', $balance->code) }}
											</small>

											@if(Helper::validateAdministrator(session()->get('usuario')->Level))
												<span class="text-muted" style="font-size:0.6em;">
													({{ substr($balance->code_prov, 0 , 3) }}) {{$balance->n_order}}
												</span>
											@endif
												<span
													class="badge badge-{{$balance->color_user}}"
													style="opacity:0.5; font-weight:400;"
													title="Fondeado por {{ $balance->usuario }}">
													{{ substr($balance->usuario ,0 , 1) }}
												</span>
								@endif
			                <small
												class="pull-right text-muted">
													{{$balance->costo_usd}}
													@if($balance->costo_usd > 0 && $balance->costo_usd < 10)
														@php $mostrar_carga_minim = false @endphp
													@endif
													@if(Helper::validateAdministrator(session()->get('usuario')->Level))
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
											@if(Helper::validateAdministrator(session()->get('usuario')->Level))
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
			  @if (!$cuenta_robada)
              <li class="list-group-item" style="background-color: #efefef;">
              	@php
									$solicitud = strtotime($maxDayReset->Max_Day_Solicitado);
									$reseteo = strtotime($maxDayReset->Max_Day_Reseteado);
								@endphp

								@if ( ($maxDayReset->Max_Day_Solicitado === NULL) or (($solicitud < $reseteo) && ($maxDayReset->Max_Day_Reseteado != NULL)))

									<div class="dropdown">
										<button
										class="btn btn-normal btn-xs dropdown-toggle pull-right"
										style="opacity: 0.5;"
										type="button"
										data-toggle="dropdown"
										aria-haspopup="true"
										aria-expanded="false">
											<i class="fa fa-fw fa-power-off"></i>
											Pedir Reseteo
										</button>

										<ul style="left:355px" class="dropdown-menu bg-info" aria-labelledby="dropdownMenu1">
											<li class="dropdown-header">¿Seguro deseas</li>
											<li class="dropdown-header">pedir reseteo?</li>
											<li role="separator" class="divider"></li>
											<li>
												<form class=" text-center" id="form_reseteo" action="{{url('solicitar_reseteo_cuenta',[$account->ID])}}" method="post">
													{{ csrf_field() }}
													<button
														class="btn btn-danger btn-block"
														title="cambiar pass"
														id="reseteo"
														type="button">
														Si, seguro!
													</button>
												</form>
											</li>
										</ul>
									</div>

				  			@else
				 						<span
											class="btn btn-danger btn-xs pull-right"
											style="opacity: 0.9;">
											<i class="fa fa-fw fa-check"></i>
											Reset Pendiente
										</span>
				 				@endif

							@php
							 
							 $btnRecup = [
							
								"pri" => [
									"texto_btn" => "Recup Pri",
									"texto_msj" => "Recuperar Pri",
									"color" => "primary",
									"param" => "pri",
									"ver" => false
								],
									
								"secu" => [
										"texto_btn" => "Recup Secu",
										"texto_msj" => "Recuperar Secu",
										"color" => "danger",
										"param" => "secu",
										"ver" => false
								],
								"conj" => [
										"texto_btn" => "Recup Conj",
										"texto_msj" => "Recuperar Conj",
										"color" => "success",
										"param" => "conj",
										"ver" => false
								]
							];

							if (\Helper::operatorsRecoverPri(session()->get('usuario')->Nombre)) {
								if ($ventaPs4Pri && $ventaPs4Secu) {
									$btnRecup["conj"]["ver"] = true;
								} 
								if ($ventaPs4Pri) {
									$btnRecup["pri"]["ver"] = true;
								} 
								if ($ventaPs4Secu) {
									$btnRecup["secu"]["ver"] = true;
								}
							} 

							 @endphp
							 
							 @if ($btnRecup['secu']['ver']===true && !$operador_pass)

							 <div class="dropdown pull-left" style="margin-bottom: 2px">
								<button
									class="btn btn-{{$btnRecup['secu']['color']}} dropdown-toggle btn-xs"
									type="button" id="dropdown{{ str_replace(' ','',$btnRecup['secu']['texto_btn']) }}"
									data-toggle="dropdown"
									aria-haspopup="true"
									aria-expanded="false">
										<i class="fa fa-fw fa-power-off"></i>
										{{ $btnRecup['secu']['texto_btn'] }}
										<span class="caret"></span>
								</button>

								<ul class="dropdown-menu bg-info" aria-labelledby="dropdown{{ str_replace(' ','',$btnRecup['secu']['texto_btn']) }}">
									<li class="dropdown-header">¿Seguro deseas</li>
									<li class="dropdown-header">{{ $btnRecup['secu']['texto_msj'] }}?</li>
									<li role="separator" class="divider"></li>
									<li>
										<form class="text-center" id="form_resetear_secu" action="{{url('resetear_cuenta',[$account->ID, $btnRecup['secu']['param']])}}" method="post">
											{{ csrf_field() }}
											<button
												class="btn btn-{{$btnRecup['secu']['color']}} btn-block"
												title="{{ $btnRecup['secu']['texto_msj'] }}"
												id="resetear_secu"
												onclick="reset_recup('secu', this)"
												type="button">
												Si, seguro!
											</button>
											@if (($account->days_from_reset === null) || ($account->days_from_reset > 183))
											<button
												class="btn btn-success btn-block"
												title="Recuperar secu con reseteo"
												id="resetear_secu_reset"
												onclick="reset_recup('secu_reset', this)"
												type="button">
												Si, con reseteo!
											</button>
											@endif
											@if ($dom_excluido)
											<button
												class="btn btn-default btn-block"
												title="Recuperar secu con cambio dominio"
												id="resetear_domexclu"
												onclick="reset_recup('secu_domexclu', this)"
												type="button">
												Cambio Dominio
											</button>
											@endif
										</form>
									</li>
								</ul>

							</div>

							<div class="clearfix"></div>
								 
							 @endif

							  @if (($account->days_from_reset === null) || ($account->days_from_reset > 183))
								@foreach ($btnRecup as $tipo => $item)
									@if ($item['ver'] === true && $tipo != 'secu')
										<div class="dropdown pull-left" style="margin-bottom: 2px">
											<button
												class="btn btn-{{$item['color']}} dropdown-toggle btn-xs"
												type="button" id="dropdown{{ str_replace(' ','',$btnRecup[$tipo]['texto_btn']) }}"
												data-toggle="dropdown"
												aria-haspopup="true"
												aria-expanded="false">
													<i class="fa fa-fw fa-power-off"></i>
													{{ $item['texto_btn'] }}
													<span class="caret"></span>
											</button>

											<ul class="dropdown-menu bg-info" aria-labelledby="dropdown{{ str_replace(' ','',$btnRecup[$tipo]['texto_btn']) }}">
												<li class="dropdown-header">¿Seguro deseas</li>
												<li class="dropdown-header">{{ $item['texto_msj'] }}?</li>
												<li role="separator" class="divider"></li>
												<li>
													<form class="text-center" id="form_resetear_{{$tipo}}" action="{{url('resetear_cuenta',[$account->ID, $item['param']])}}" method="post">
														{{ csrf_field() }}
														<button
															class="btn btn-{{$item['color']}} btn-block"
															title="{{ $item['texto_msj'] }}"
															id="resetear_{{$tipo}}"
															onclick="reset_recup('{{$tipo}}', this)"
															type="button">
															Si, seguro!
														</button>
													</form>
												</li>
											</ul>

										</div>

										<div class="clearfix"></div>
									@endif
								@endforeach
								<div class="dropdown pull-left" style="margin-bottom: 2px">
									<button
										class="btn btn-default dropdown-toggle btn-xs"
										type="button" id="dropdownReset"
										data-toggle="dropdown"
										aria-haspopup="true"
										aria-expanded="false">
											<i class="fa fa-fw fa-power-off"></i>
											Resetear
											<span class="caret"></span>
									</button>
	
									<ul class="dropdown-menu bg-info" aria-labelledby="dropdownReset">
										<li class="dropdown-header">¿Seguro deseas</li>
										<li class="dropdown-header">Resetear?</li>
										<li role="separator" class="divider"></li>
										<li>
											<form class="text-center" id="form_resetear_reset" action="{{url('resetear_cuenta',[$account->ID, null])}}" method="post">
												{{ csrf_field() }}
												<button
													class="btn btn-default btn-block"
													title="Resetear"
													id="resetear_secu"
													onclick="reset_recup('reset', this)"
													type="button">
													Si, seguro!
												</button>
												<button
													class="btn btn-danger btn-block"
													title="Resetear con cambio pass"
													id="resetear_reset_pass"
													onclick="reset_recup('reset_pass', this)"
													type="button">
													Si, con cambio pass!
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
			   
			   @else
			  
			   <li class="list-group-item" style="background-color: #efefef;">
					<div class="dropdown">
							<button
							class="btn btn-success btn-xs dropdown-toggle pull-right"
							type="button" id="dropdownRecuperada"
							data-toggle="dropdown"
							aria-haspopup="true"
							aria-expanded="false">
								<i class="fa fa-fw fa-check-circle"></i>
								¿Recuperada?
							</button>

							<ul style="left:355px" class="dropdown-menu bg-info" aria-labelledby="dropdownRecuperada">
								<li class="dropdown-header">¿Se recuperó</li>
								<li class="dropdown-header">la cuenta?</li>
								<li role="separator" class="divider"></li>
								<li>
									<form class=" text-center" id="form_recuperar" action="{{url('recuperar_cuenta')}}" method="post">
										{{ csrf_field() }}
									<input type="hidden" name="account_id" value="{{ $account->ID }}">
										<button
											class="btn btn-success btn-block"
											title="Recuperada"
											id="recuperar"
											type="button">
											Si, recuperada!
										</button>
									</form>
								</li>
							</ul>
						</div>

  
						<span class="badge badge-danger pull-left"><i class="fa fa-fw fa-times-circle"></i> Cuenta Robada</span>

						<div class="clearfix"></div>

			   </li>
			   
			   @endif
            </ul>
        </div>
     </div>

  @php
  	$cant_productos = 0;
  @endphp

  @if(count($stocks) > 0)
  	<div class="col-xs-12 col-sm-6 col-md-6">
		<div style="margin-left: 0 !important" class="row">
		@foreach($stocks as $i => $stock)
			@php $cant_productos++; @endphp

			<?php
              $color = $stock->color_user;
            ?>
    	<div class="col-xs-12 col-sm-6 col-md-5" id="{{$stock->consola}}-game">
				<div class="thumbnail">
					<span class="pull-right" style="width: 45%;">
						<p>
							<span class="btn-group pull-right">
								<button
								class="btn btn-xs btn-default"
								type="button"
								data-toggle="modal"
								data-target=".bs-example-modal-lg"
								onClick='getPageAjax("{{url('actualizar_stock_cuenta')}}/{{$stock->ID_stock}}/{{$stock->stock_cuentas_id}}/1","#modal-container");'>
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

									<span
									  class="badge badge-<?php echo $color;?> pull-right"
									  style="opacity:0.7; font-weight:400;"
									  title="<?php echo $stock->usuario; ?>">
									  <?php echo substr($stock->usuario, 0, 1); ?>
									</span>
            		</p>

            		<p>
			            @if(Helper::validateAdministrator(session()->get('usuario')->Level))

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


            		@if(!empty($hasBalance->costo_usd) or Helper::validateAdministrator(session()->get('usuario')->Level))

			            <p style="opacity:0.7">
										<img src="https://exur-exur.netdna-ssl.com/realskin/images/usa.png" style="opacity:0.7">

										<small>
											<strong>
												{{$stock->costo_usd}}
												@if (Helper::validateAdministrator(session()->get('usuario')->Level))
													({{empty($stock->costo_usd_modif) ? '0.00' : $stock->costo_usd_modif}})
												@endif
											</strong> 
											<!-- <a href="javascript:;" title="Modificar Costo"><i class="fa fa-pencil"></i></a> -->
										</small>

										{{-- @if(!$quantityStock->Q > 1 ) --}}
											<a
												title="Modificar Costo"
												class="btn-xs text-muted"
												style="opacity: 0.7;"
												type="button"
												data-toggle="modal"
												data-target=".bs-example-modal-lg"
												onClick='getPageAjax("{{url('actualizar_stock_cuenta')}}/{{$stock->ID_stock}}/{{$stock->stock_cuentas_id}}/2","#modal-container");'>
												<i aria-hidden="true" class="fa fa-pencil"></i>
											</a>
										{{-- @endif --}}

									</p>

            		@endif
            	</span>

            <img
							class="img img-responsive img-rounded full-width"
							style="width:54%; margin:0;"
							alt="{{$stock->titulo}}"
							src='{{asset("img/productos/".$stock->consola."/".$stock->titulo.".jpg")}}'>

						<div class="caption text-center"></div>

						@if ($stock->consola == 'ps3')

						<div class="dropdown text-left">
						  <button
							class="btn btn-link dropdown-toggle btn-xs"
							type="button" id="vender_cli2"
							data-toggle="dropdown"
							aria-haspopup="true"
							aria-expanded="false">
							  Vender a Cliente #2
							  {{-- <span class="caret"></span> --}}
						  </button>
		
						  <ul class="dropdown-menu bg-info" aria-labelledby="vender_cli2">
							<li class="dropdown-header">¿Estas seguro?</li>
							<li role="separator" class="divider"></li>
							<li>
							  <a href="{{ url('saleToClient', [$stock->ID_stock, $stock->consola, 'Primario']) }}" class="btn btn-danger">Sí, Seguro!</a>
							</li>
						  </ul>
		
						</div>
		
					@elseif ($stock->consola == 'ps4')
		
					  <div class="dropdown text-left">
						<button
						  class="btn btn-link dropdown-toggle btn-xs"
						  type="button" id="vender_pri_cli1"
						  data-toggle="dropdown"
						  aria-haspopup="true"
						  aria-expanded="false">
							Vender Primario a Cliente #1
							{{-- <span class="caret"></span> --}}
						</button>
		
						<ul class="dropdown-menu bg-info" aria-labelledby="vender_pri_cli1">
						  <li class="dropdown-header">¿Estas seguro?</li>
						  <li role="separator" class="divider"></li>
						  <li>
							<a href="{{ url('saleToClient', [$stock->ID_stock, $stock->consola, 'Primario']) }}" class="btn btn-danger">Sí, Seguro!</a>
						  </li>
						</ul>
		
					  </div>
					  <div class="dropdown text-left">
						<button
						  class="btn btn-link dropdown-toggle btn-xs"
						  type="button" id="vender_secu_cli2"
						  data-toggle="dropdown"
						  aria-haspopup="true"
						  aria-expanded="false">
							Vender Secundario a Cliente #1
							{{-- <span class="caret"></span> --}}
						</button>
		
						<ul class="dropdown-menu bg-info" aria-labelledby="vender_secu_cli2">
						  <li class="dropdown-header">¿Estas seguro?</li>
						  <li role="separator" class="divider"></li>
						  <li>
							<a href="{{ url('saleToClient', [$stock->ID_stock, $stock->consola, 'Secundario']) }}" class="btn btn-danger">Sí, Seguro!</a>
						  </li>
						</ul>
		
					  </div>
		
					@endif

					@php $texto_x = strpos($stock->titulo,"xx-") !== false ? "Quitar" : "Agregar" @endphp

					<div class="dropdown text-left">
						<button
						  class="btn btn-link dropdown-toggle btn-xs"
						  type="button" id="vender_secu_cli2"
						  data-toggle="dropdown"
						  aria-haspopup="true"
						  aria-expanded="false">
							{{ $texto_x }} doble x
							{{-- <span class="caret"></span> --}}
						</button>
		
						<ul class="dropdown-menu bg-info" aria-labelledby="vender_secu_cli2">
						  <li class="dropdown-header">¿Estas seguro?</li>
						  <li role="separator" class="divider"></li>
						  <li>
							<a href="{{ url('update_product_x', [$stock->ID_stock, strtolower($texto_x)]) }}" class="btn btn-danger">Sí, Seguro!</a>
						  </li>
						</ul>
		
					  </div>
					
				@if(\Helper::validateAdministrator(session()->get('usuario')->Level))
					  
				  <div class="dropdown text-left">
						<button
						  class="btn btn-link dropdown-toggle btn-xs"
						  type="button" id="vender_secu_cli2"
						  data-toggle="dropdown"
						  aria-haspopup="true"
						  aria-expanded="false">
							Eliminar juego
							{{-- <span class="caret"></span> --}}
						</button>
		
						<ul class="dropdown-menu bg-info" aria-labelledby="vender_secu_cli2">
						  <li class="dropdown-header">¿Estas seguro?</li>
						  <li role="separator" class="divider"></li>
						  <li>
							<a href="{{ url('delete_product', $stock->ID_stock) }}" class="btn btn-danger">Sí, Seguro!</a>
						  </li>
						</ul>
		
				</div>

				@endif

          </div>
		</div>
			@if ($i === 1)
				<div class="clearfix"></div>
			@endif
			@endforeach
			@if (count($stocks) == 1 && !$fornite)
				<div class="col-xs-12 col-sm-6 col-md-5">
					<div class="alert alert-danger text-center">
						<h4>¿Ya compraste el Fortnite?</h4>
						<br>
						{{-- <button class="btn btn-danger btn-xl" type="button" id="fortnite">Si, ya compre!</button> --}}

						<div class="dropdown">
							<button
							class="btn btn-danger btn-xl dropdown-toggle"
							type="button"
							data-toggle="dropdown"
							aria-haspopup="true"
							aria-expanded="false">
								Si, ya compre!
							</button>

							<ul class="dropdown-menu bg-info" aria-labelledby="dropdownFornite">
								<li class="dropdown-header">¿Estas seguro?</li>
								<li role="separator" class="divider"></li>
								<li>
									<a href="{{route('cuenta-fornite', $account->ID)}}" class="btn btn-danger btn-block">Sí, seguro</a>
								</li>
							</ul>
						</div>

					</div>
				</div>
			@endif
		</div>
	  </div>
	@elseif (count($stocks) == 0 && !$fornite)
	<div class="col-xs-12 col-sm-6 col-md-6">
		<div style="margin-left: 0 !important" class="row">
			<div class="col-xs-12 col-sm-6 col-md-5">
				<div class="alert alert-danger text-center">
					<h4>¿Ya compraste el Fortnite?</h4>
					<br>
					{{-- <button class="btn btn-danger btn-xl" type="button" id="fortnite">Si, ya compre!</button> --}}

					<div class="dropdown">
						<button
						class="btn btn-danger btn-xl dropdown-toggle"
						type="button"
						data-toggle="dropdown"
						aria-haspopup="true"
						aria-expanded="false">
							Si, ya compre!
						</button>

						<ul class="dropdown-menu bg-info" aria-labelledby="dropdownFornite">
							<li class="dropdown-header">¿Estas seguro?</li>
							<li role="separator" class="divider"></li>
							<li>
								<a href="{{route('cuenta-fornite', $account->ID)}}" class="btn btn-danger btn-block">Sí, seguro</a>
							</li>
						</ul>
					</div>

				</div>
			</div>
		</div>
	</div>
    @endif





    <div class="col-md-2 pull-right">
    	@if ($cant_productos < 3)
			<p>
				<button
					class="btn btn-default btn-lg"
					type="button"
					data-toggle="modal"
					data-target=".bs-example-modal-lg"
					onClick='getPageAjax("{{url('recharge_account')}}","#modal-container",{{$account->ID}});'>
						<i class="fa fa-fw fa-dollar"></i>
						Cargar Saldo
				</button>
			</p>
		@endif

		@if($mostrar_carga_minim && !$dom_excluido)

			<p>
				<button
					class="btn btn-info btn-lg"
					type="button"
					data-toggle="modal"
					data-target=".bs-example-modal-lg"
					onClick='getPageAjax("{{url('recharge_minim_account')}}","#modal-container",{{$account->ID}});'>
						<i class="fa fa-fw fa-dollar"></i>
						Cargar Minim
				</button>
			</p>

		@endif

		@if(!$product_20_off && $cant_productos < 2 && $existeStock_product_20_off > 0)

		<p>
		<a href="{{ url('agregar_20_off',[$account->ID,'20-off-playstation','ps']) }}" class="btn btn-normal btn-lg"><i class="fa fa-tag"></i> 20% OFF</a>
		</p>

		@endif

     <?php if ($saldo > 0.00):?>

				<p>
					<button
						class="btn btn-primary btn-lg"
						type="button"
						data-toggle="modal"
						data-target=".bs-example-modal-lg"
						onClick='getPageAjax("{{url('stock_insertar_cuenta')}}","#modal-container",{{$account->ID}});'>
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
						onClick='getPageAjax("{{url('stock_pre_insertar_cuenta')}}","#modal-container",{{$account->ID}});'>
							<i class="fa fa-fw fa-th"></i>
							Carga Masiva
						</button>
					</p>

					<?php if(!($quantityStock)): ?>

						<div class="text-center" style="background-color:#f1f1f1; border-color:#fcfcfc; border: 1px solid; padding: 5px; margin: 5px 10px 5px 0">
							@foreach($lastAccountGames as $last)
								<div class="col-sm-5" >
									<div>
										<img src="{{asset('img/productos')}}/<?php echo $last->consola."/".$last->titulo.".jpg"; ?>"
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
								<form class="" action="{{ url('repetir_ultima_cuenta',[$account->ID]) }}" method="post">
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

		<?php else: ?>

		@if(count($stocks) == 0)

		<p>
			<a href="{{ url('repetir_gift_juego', $account->ID) }}" class="btn btn-default btn-lg"><i class="fa fa-gamepad"></i> Repetir Juego</a>
		</p>

		@if($lastGame)

		<img class="img img-responsive img-rounded full-width pull-left" style="width:30%;margin:0;" alt="{{ $lastGame->titulo }}" src="{{asset('img/productos')}}/<?php echo $lastGame->consola."/".$lastGame->titulo.".jpg"; ?>">
		<span class="badge badge-normal">{{ $lastGame->costo_usd }} usd</span>

		@endif

		@endif

     	<?php endif;?>
     </div>



  </div>

  @if (count($soldConcept)>0)

    <div class="">
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

            <td><?php echo date("d M 'y", strtotime($sc->Day)); ?>

            @if($sc->concepto != 'referencia') 

            	@if(\Helper::validateAdministrator(session()->get('usuario')->Level))

            	<a class="btn btn-default btn-xs" type="button"
					data-toggle="modal"
					data-target=".bs-example-modal-lg"
					onClick='getPageAjax("{{url('modify_date_operations',[$sc->id,$sc->concepto])}}","#modal-container")'><i aria-hidden="true" class="fa fa-pencil text-muted"></i></a>
				<div class="dropdown" style="display: inline-block;">
					<button
					class="btn btn-default btn-xs dropdown-toggle"
					style="opacity: 0.5;"
					type="button"
					data-toggle="dropdown"
					aria-haspopup="true"
					aria-expanded="false">
						<i class="fa fa-fw fa-close text-muted"></i>
					</button>

					<ul style="top: -79px; left: 40px" class="dropdown-menu bg-info" aria-labelledby="dropdownMenu1">
						<li class="dropdown-header">¿Seguro deseas eliminar</li>
						<li class="dropdown-header">este registro?</li>
						<li role="separator" class="divider"></li>
						<li>
							<a href="{{ url('delete_operations',[$sc->id,$sc->concepto]) }}">Sí, Eliminar</a>
						</li>
					</ul>
				</div>
				@endif
			@endif
			</td>
            <?php if ($sc->concepto == 'contra'):?>
            <td colspan="4"><em class="badge badge-default" style="font-weight:normal; opacity:0.8;"><i class="fa fa-key fa-fw"></i> Nueva contra: <?php echo $sc->new_pass;?> (<?php echo $sc->usuario;?>)</em></td>
			<?php elseif ($sc->concepto == 'notas'):?>
			<td colspan="4"><em class="alert alert-warning" style="color: #8a6d3b; background-color:#FFDD87; padding: 4px 7px; font-size: 12px; font-style:italic; margin:0px; opacity: 0.8;"><i class="fa fa-comment fa-fw"></i> {!! html_entity_decode($sc->new_pass) !!} (<?php echo $sc->usuario;?>)</em></td>
			<?php elseif ($sc->concepto == 'reset'):?>
            <td colspan="4"><em class="badge badge-danger" style="font-weight:normal; opacity:0.8;"><i class="fa fa-power-off fa-fw" aria-hidden="true"></i> Reseteado por <?php echo $sc->usuario;?></em></td>
			<?php elseif ($sc->concepto == 'resetear'):?>
            <td colspan="4"><em class="badge badge-default" style="font-weight:normal; opacity:0.8;color:red"><i class="fa fa-power-off fa-fw" aria-hidden="true"></i> Solicitud de Reseteo por <?php echo $sc->usuario;?></em></td>
            <?php elseif ($sc->concepto == 'referencia'):?>
            <td colspan="3"><em class="badge badge-success" style="font-weight:normal; opacity:0.8; width: 100%"><i class="fa fa-calendar fa-fw" aria-hidden="true"></i> FECHA DE REFERENCIA {{ \Helper::formatFechaReferencia($sc->Day) }}</em></td>
      		<td></td>
            <?php else:?>
            <td>
				<span class="text-muted small">#<?php echo $sc->clientes_id; ?> <?php echo $sc->nombre; ?> <?php echo $sc->apellido; ?></span> @if($sc->auto == 're') <label for="" class="label label-danger" style="padding: 6px; margin-left: 20px">Revendedor</label> @endif
				<?php if ($sc->ventas_Notas):?><a href="#" data-toggle="popover" data-placement="bottom" data-trigger="focus" title="Notas de Cliente" data-content="<?php echo $sc->ventas_Notas; ?>" style="color: #555555;"><i class="fa fa-comment fa-fw"></i></a><?php endif; ?>

				
				@if ($sc->recup == 2)
					@if($sc->slot == 'Primario' && $operador_reset)

					<div style="margin-top: 5px; display:inline-block" class="dropdown">
						<button
						class="btn btn-primary dropdown-toggle btn-xs"
						type="button" id="priSigueJugando2"
						data-toggle="dropdown"
						aria-haspopup="true"
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
							href="{{ url('nota_siguejugandopri', $account->ID) }}"
								class="btn btn-danger"
								title="Primario Sigue jugando"
								id="pri_sigue_jugando"
								>
								Si, seguro!
							</a>
						</li>
						</ul>

					</div>
					@endif

					@if($sc->slot == 'Secundario' && $operador_pass)
						
						<div style="margin-top: 5px; display:inline-block" class="dropdown">
							<button
							class="btn btn-danger dropdown-toggle btn-xs"
							type="button" id="secusiguejugando2"
							data-toggle="dropdown"
							aria-haspopup="true"
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
								href="{{ url('nota_siguejugando', $account->ID) }}"
									class="btn btn-danger"
									title="Secu Sigue jugando"
									id="secu_sigue_jugando"
									>
									Si, seguro!
								</a>
							</li>
							</ul>
	
						</div>
					@endif
				@endif
			</td>

            <td id="<?php echo $sc->clientes_id; ?>"><a title="Ir a Cliente" href="{{ url('clientes', $sc->clientes_id) }}"><?php echo $sc->email; ?></a> <a class="btn btn-xs btn-default" style="opacity:0.6;" href="https://mail.google.com/mail/u/1/#search/<?php echo substr($sc->email, 0, strpos($sc->email, '@')) . '+' . str_replace("-"," ",$sc->titulo); ?>" title="filtrar guia de descarga en gmail" target="_blank"><i aria-hidden="true" class="fa fa-google"></i>mail</a>

				<!--- Mensajes predefinidos con guia de re activar o cambio de contraseña -->
				<!--
				<div style="position:absolute; top:0; left:-500px;">
					<textarea id="newpass-copy" type="text" rows="1" cols="2"><?php // echo "Actualizamos la contraseña de ésta Cuenta/Usuario: " . $account->pass . "\r\n\r\nSaludos, " . $vendedor. " de DixGamer.";?></textarea>
					<textarea id="reactivar-copy" type="text" rows="1" cols="2"><?php // echo "Por favor ingresá a nuestra cuenta/usuario una vez más para RE ACTIVAR tu slot primario, una vez dentro de nuestro usuario:\r\n\r\n1) Ir a Configuración > PSN/Administración de cuentas > Activar como tu PS4 principal > Activar\r\n2) Ir a Configuración > PSN/Administración de cuentas > Restaurar Licencias > Restaurar\r\n3) Reiniciar tu consola y acceder con tu usuario personal, recordá no volver a abrir nuestro usuario.\r\n\r\nContraseña: " . $account->pass . "\r\n\r\nSaludos, " . $vendedor. " de DixGamer.";?></textarea>
				</div>
1
				-->
				<div style="position:absolute; top:-1000; left:-1000px;">
					<div style="position: absolute; height: 100px; width: 100px;right: -50px; top: 50px;">
					<span id="newpass-copy{{$sc->clientes_id}}" style="font-size:15px; background: white; font-weight: normal; color:#111;"><p>Por mantenimiento de los servidores actualizamos la contraseña de ésta Cuenta/Usuario,<br /><br />
						La nueva contraseña es: <?php echo $account->pass;?><br /><br />

						{!! html_entity_decode($oferta_fortnite) !!}<br>

						Saludos, <?php echo session()->get('usuario')->Nombre;?> de DixGamer.<br/></p>
					</span>
					</div>

					<div style="position: absolute; height: 100px; width: 100px;right: -50px; top: 50px;">
					<span id="avisosecu-copy{{$sc->clientes_id}}" style="font-size:15px; background: white; font-weight: normal; color:#111;"><p>Hola {{ $sc->nombre }}, necesitamos que nos confirme si está usando su juego {{ strtoupper(str_replace("-"," ",$sc->titulo)) }} y si puede acceder normalmente a la cuenta para jugar. Tuvimos un error de sistema y si no puede acceder queremos ayudarle a solucionar.<br /><br />

						{!! html_entity_decode($oferta_fortnite) !!} <br>
						
						Saludos, <?php echo session()->get('usuario')->nombre_visible;?> de DixGamer.<br/></p>
					</span>
					
					<span id="avisopri-copy{{$sc->clientes_id}}" style="font-size:15px; background: white; font-weight: normal; color:#111;"><p>{{ $sc->nombre }}, necesitamos que nos confirme si está usando su juego {{ strtoupper(str_replace("-"," ",$sc->titulo)) }} y si puede usarlo con normalidad. Tuvimos un error de sistema y si no puede jugar queremos ayudarle a solucionar.<br /><br />

						{!! html_entity_decode($oferta_fortnite) !!} <br>
						
						Saludos, <?php echo session()->get('usuario')->nombre_visible;?> de DixGamer.<br/></p>
					</span>
					</div>

					<div style="position: absolute; height: 100px; width: 100px;right: -50px; top: 50px;">
					<span id="avisonewemail-copy{{$sc->clientes_id}}" style="font-size:15px; background: white; font-weight: normal; color:#111;"><p>Hola {{ $sc->nombre }}, por mantenimiento de los servidores actualizamos los datos de la cuenta.<br /><br />

						Nuevo e-mail: {{ $account->mail_fake }} <br>
						Contraseña: {{ $account->pass }} <br><br>

						{!! html_entity_decode($oferta_fortnite) !!} <br>
						Saludos, <?php echo session()->get('usuario')->nombre_visible;?> de DixGamer.<br/></p>
					</span>
					</div>
					
					<div style="position: absolute; height: 100px; width: 100px;right: -50px; top: 50px;">
					<span id="reactivar-copy{{$sc->clientes_id}}" style="font-size:15px; background: white; font-weight: normal; color:#111;"><p>Hola {{ $sc->nombre }}, por favor ingresá a nuestra cuenta/usuario con el nombre <b>{{ $account->name . " " . $account->surname }}</b> una vez más para RE ACTIVAR tu slot primario, una vez dentro de nuestro usuario:<br /><br />
						1) Ir a Configuración > PSN/Administración de cuentas > Activar como tu PS4 principal > Activar<br />
						2) Ir a Configuración > PSN/Administración de cuentas > Restaurar Licencias > Restaurar<br />
						3) Reiniciar tu consola y acceder con tu usuario personal, recordá no volver a abrir nuestro usuario.<br /><br />

						E-mail: {{$account->mail_fake}} <br>
						Contraseña: <?php echo $account->pass;?><br /><br />						
						<!-- Aprovecho para contarte que nuestros paVos de Fortnite bajaron de precio, <a href="https://dixgamer.com/categoria-producto/tarjetas/fortnite/">ver paVos baratos</a><br /><br /> -->
						{!! html_entity_decode($oferta_fortnite) !!} <br>
						Saludos, <?php echo session()->get('usuario')->nombre_visible;?> de DixGamer.<br/></p>
					</span>
					</div>
				</div>
				<?php if (strpos($sc->consola, 'ps4') !== false):?>
					<?php if ($sc->slot == 'Primario'):?>
					<br>
					@if ($sc->recup == 2)
						@if($operador_reset)

						<a href="#{{$sc->clientes_id}}" class="btn-copiador btn-xs btn-info label" data-clipboard-target="#avisopri-copy{{$sc->clientes_id}}"> RECUPERO PRI <i aria-hidden="true" class="fa fa-clone"></i></a>

						<button class="btn-xs btn-info label email-info" onclick="envioEmailInfo('btns_recu_pri','{{$sc->clientes_id}}','{{$account->ID}}','{{$sc->id}}','{{$sc->ID_stock}}',this)">email <i aria-hidden="true" class="fa fa-paper-plane"></i></button>

						<span style="display: none" id="btns_recu_pri_email_success{{$sc->clientes_id}}" class="label label-success">email enviado</span>
						<span style="display: none" id="btns_recu_pri_email_error{{$sc->clientes_id}}" class="label label-danger">error al enviar email</span>
						@endif
					@else
						<a href="#<?php echo $sc->clientes_id; ?>" class="btn-copiador btn-xs btn-info label" data-clipboard-target="#reactivar-copy{{$sc->clientes_id}}">msj react <i aria-hidden="true" class="fa fa-clone"></i></a>
						<button class="btn-xs btn-info label email-info" onclick="envioEmailInfo('msj_react','{{$sc->clientes_id}}','{{$account->ID}}','{{$sc->id}}','{{$sc->ID_stock}}',this)">email <i aria-hidden="true" class="fa fa-paper-plane"></i></button>

						<span style="display: none" id="msj_react_email_success{{$sc->clientes_id}}" class="label label-success">email enviado</span>
						<span style="display: none" id="msj_react_email_error{{$sc->clientes_id}}" class="label label-danger">error al enviar email</span>

					@endif 
					<?php else: ?>
					<br>
					@if ($sc->recup == 2)
						@if($operador_pass)
						
						<a href="#{{$sc->clientes_id}}" class="btn-copiador btn-xs btn-info label" data-clipboard-target="#avisosecu-copy{{$sc->clientes_id}}"> RECUPERO SECU <i aria-hidden="true" class="fa fa-clone"></i></a>

						<button class="btn-xs btn-info label email-info" onclick="envioEmailInfo('btns_recu_secu','{{$sc->clientes_id}}','{{$account->ID}}','{{$sc->id}}','{{$sc->ID_stock}}',this)">email <i aria-hidden="true" class="fa fa-paper-plane"></i></button>

						<span style="display: none" id="btns_recu_secu_email_success{{$sc->clientes_id}}" class="label label-success">email enviado</span>
						<span style="display: none" id="btns_recu_secu_email_error{{$sc->clientes_id}}" class="label label-danger">error al enviar email</span>
						@endif
					@else
						<a href="#<?php echo $sc->clientes_id; ?>" class="btn-copiador btn-xs btn-info label" data-clipboard-target="#avisonewemail-copy{{$sc->clientes_id}}"> msj pass <i aria-hidden="true" class="fa fa-clone"></i></a>

					<button class="btn-xs btn-info label email-info" onclick="envioEmailInfo('msj_pass','{{$sc->clientes_id}}','{{$account->ID}}','{{$sc->id}}','{{$sc->ID_stock}}',this)">email <i aria-hidden="true" class="fa fa-paper-plane"></i></button>

					<span style="display: none" id="msj_pass_email_success{{$sc->clientes_id}}" class="label label-success">email enviado</span>
					<span style="display: none" id="msj_pass_email_error{{$sc->clientes_id}}" class="label label-danger">error al enviar email</span>
						
						{{-- <a href="#{{$sc->clientes_id}}" class="btn-copiador btn-xs btn-default label" data-clipboard-target="#avisonewemail-copy{{$sc->clientes_id}}"> NUEVO EMAIL <i aria-hidden="true" class="fa fa-clone"></i></a>  --}}
					@endif
					<?php endif;?>
				<?php endif;?>

			  </td>
            <td>
				<span class="label <?php if ($sc->slot == 'Primario'):?>label-default<?php endif;?>"><?php echo $sc->titulo; ?></span> 
				<?php if ($sc->slot == 'Secundario'): ?>
				<span class="label label-danger" style="opacity:0.7">2°</span>
				<?php endif; ?> 
				<span class="label label-default <?php echo $sc->consola; ?>"><?php echo $sc->consola; ?></span> 
				<?php if ($sc->ventas_Notas):?>
				<a href="#" data-toggle="popover" data-placement="bottom" data-trigger="focus" title="Notas de Venta" data-content="<?php echo $sc->ventas_Notas; ?>" style="color: #555555;"><i class="fa fa-comment fa-fw"></i></a>
				<?php endif; ?>
			</td>

			<td style="text-align:right;">
			</td>
            <?php endif;?>
          </tr>
      @endforeach
      <!-- <tr>
      	<td>31 Mar '18</td>
      	<td><em class="badge badge-success" style="font-weight:normal; opacity:0.8;"><i class="fa fa-calendar fa-fw" aria-hidden="true"></i> FECHA DE REFERENCIA 31 de MARZO de 2018</em></td>
      	<td colspan="3"></td>
      </tr> -->
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
@section('scripts')
@parent
	<script>
		var peticion = 0;
		$(document).ready(function(){
			$('#cambiar_pass').on('click', function(){
				$(this).prop('disabled', true);
				$('#form_cambiar_pass').submit();
			});

			/*$('#resetear').on('click', function(){
				$(this).prop('disabled', true);
				$('#form_resetear').submit();
			});*/
			
			$('#recuperar').on('click', function(){
				$(this).prop('disabled', true);
				$('#form_recuperar').submit();
			});

			$('#reseteo').on('click', function(){
				$(this).prop('disabled', true);
				$('#form_reseteo').submit();
			});
		});
		/*
		$('#paginaAnt').on('click',function(e){
			e.preventDefault();
			$.ajax({
				method: 'get',
				url: '/getDataPaginaAnt',
				success: function(result){
				    console.log(result);
			}
			});
		});
		*/
		function request(e)
		{
			if (peticion == 0) {
				peticion++;
			} else {
				e.preventDefault();
				alert("Ya existe una petición abierta");
			}
		}

		function reset_recup(tipo, el)
		{
			var tipo_recu = '';
			if (tipo == 'secu_reset') {
				tipo_recu = 'secu';
				$('#form_resetear_secu').attr('action',"{{url('resetear_cuenta',[$account->ID,'secu_reset'])}}")
			} else if (tipo == 'secu_domexclu') {
				tipo_recu = 'secu';
				$('#form_resetear_secu').attr('action',"{{url('resetear_cuenta',[$account->ID,'secu_domexclu'])}}")
			}  else if (tipo == 'reset_pass') {
				tipo_recu = 'reset';
				$('#form_resetear_reset').attr('action',"{{url('resetear_cuenta',[$account->ID,'reset_pass'])}}")
			} else {
				tipo_recu = tipo;
			}

			$(el).prop('disabled', true);
			setTimeout(() => {
				$('#form_resetear_'+tipo_recu).submit();
			}, 200);
		}

		function envioEmailInfo(type,cliente,account,venta,stock,ele) {
			$(ele).prop('disabled',true);

			$.ajax({
				url: `{{url('cuentas/sendEmail')}}/${type}/${cliente}/${account}/${venta}/${stock}`,
				type: "GET",
				dataType: "json",
				success: function (response) {
					if (response) {
						switch (response.status) {
							case "success": {
								$(`#${type}_email_success${cliente}`).fadeIn();
								$(ele).prop('disabled',false);
							}
							break;
							case "error": {
								$(`#${type}_email_error${cliente}`).fadeIn();
								$(ele).prop('disabled',false);
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
	</script>
@endsection
@endsection
