
@php
	if(empty($expense)){
		$expense = new \stdClass;
		$expense->costo_usd = 0;
	}

@endphp
<div class="container">
	@if($account)
		<h1 style="color:#000">GUARDAR STOCK</h1>
		<div class="row">
			<div class="col-sm-2" style="text-align:right;">
				<span id="alerta" class="label label-danger"></span>
				<img class="img-rounded pull-right" width="100" id="image-swap" src="" alt="" />
			</div>
			<div class="col-sm-6">
				<form method="post" name="form1" id="form1" action="{{ url('stock_insertar_cuenta',[$account->ID]) }}">
					{{ csrf_field() }}

					<input type="text" name="cuentas_id" value="{{ $account->ID }}" hidden>

					<div class="input-group form-group">
						<span class="input-group-addon"><i class="fa fa-gamepad fa-fw"></i></span>
						<select id="titulo-selec" name="titulo" class="selectpicker form-control" data-live-search="true" data-size="5">
								<option value="">Selecciona Stock</option>
							@foreach($titles as $title)
								<option value="{{explode(" (",$title->nombre_web)[0]}}">{{str_replace('-', ' ', $title->nombre_web)}}</option>
							@endforeach

							</select>
						<!--- <input type="text" id="titulo-selec" name="titulo" class="titulo tt-query form-control" autocomplete="off" spellcheck="false" placeholder="Buscar título"> -->
					</div>

					@php

						$consolas_stock = [
							"ps4",
							"ps3",
							"psn",
							"steam",
							"psvita"
						];

					@endphp

					<input type="hidden" name="consola" id="consola">

					<!-- <div class="input-group form-group">
						<span class="input-group-addon"><i class="fa fa-cube fa-fw"></i></span>
						<select id="consola" name="consola" class="selectpicker form-control" onchange="return GameVerifyFunction();">
							@php $color = ''; @endphp
							@foreach ($consolas_stock as $cons)
					
							@if (!in_array($cons, $consolas))
					
							@php
								switch ($cons) {
									case 'ps4':
										$color = "primary";
										break;
									case 'ps3':
										$color = "warning";
										break;
									case 'psn':
										$color = "danger";
										break;
									case 'steam':
										$color = "default";
										break;
									case 'psvita':
										$color = "info";
										break;
								}
							@endphp
							
							<option value="{{$cons}}" data-content="<span @if ($cons == "ps3") style='background-color: #000' @endif class='label label-{{$color}}'>{{$cons}}</span>">{{$cons}}</option>
					
							@endif
					
							@endforeach
							
							
						</select>
					</div> -->

					<div class="input-group form-group text-center" style="width:100%">
					</div>

					<div class="input-group form-group">
						<span class="input-group-addon"><em>Costo en USD</em></span>

						<input
							<?php
								/*
								*Si ya hay un producto cargado,
								*el saldo <<USD>> será cargado al
								*segundo producto sin permitir
								*modificarlo if ($expense->costo_usd): echo 'readonly'; endif;
								*/
							?>

							id="proporcion_usd"
							class="form-control"
							type="number"
							step="0.01"
							name="costo_usd" value="{{$accountBalance->costo_usd - $expense->costo_usd}}" @if ($expense->costo_usd) readonly @endif>
							<input type="hidden" name="saldo_act" value="{{$accountBalance->costo_usd - $expense->costo_usd}}">

							<span class="input-group-addon">
								<em style="opacity:0.7" class="text-muted">Saldo: {{$accountBalance->costo_usd - $expense->costo_usd}}
									<img src="https://exur-exur.netdna-ssl.com/realskin/images/usa.png" style="opacity:0.7">
								</em>
						</span>
					</div>

					<div class="input-group form-group">
						<span class="input-group-addon"><i class="fa fa-comment fa-fw"></i></span>
						<input class="form-control" type="text" name="Notas" placeholder="Notas de stock">
					</div>

					<button class="btn btn-primary btn-block" id="submiter" type="button">Guardar</button>
				</form>

			</div>

			<div class="col-sm-4">
				@if(!($expense->costo_usd))
					<div class="popover right" style="display:inline; background-color:#eee ; border-color:#777; z-index:1;">
						<div class="arrow"></div>
						<h3 class="popover-title" style="color:#777;">Indicaciones</h3>
						<div class="popover-content">
							<p style="color:#888;">El costo en USD siempre reflejo la realidad, excepto cuando falta 1 centavo para llegar a multiplo de 10. Ejemplos:<br> A) Cuesta 6,74 &gt; Cargo 6,74<br> B) Cuesta 14,99 &gt; Cargo 14,99<br> C) Cuesta 9,99 &gt; Cargo 10<br> D) Cuesta 39,99 &gt;
								Cargo 40<br> etc...</p>
						</div>
					</div>
				@endif
			</div>

		</div>
		<br>
		<br>

		<script type="text/javascript">
			jQuery(function($) {
							$("form").on('change', function() {
									var titulo = document.getElementById('titulo-selec').value;
					var select = document.getElementById('titulo-selec');

					var consola = (select.options[select.selectedIndex].text.substr(-4)).replace(")","");

					document.getElementById('consola').value = consola;

					$("#image-swap").attr("src", (titulo !== "" &&  + consola !== "") ? "{{asset('img/productos')}}/" + consola + "/" + titulo + ".jpg" : "");
					$('#image-swap').load(function() {
									document.getElementById("alerta").innerHTML = "";
									});
					$('#image-swap').error(function() {
						document.getElementById("alerta").innerHTML = "no se encuentra";
					});

						if (titulo == "") {
							document.getElementById('submiter').disabled = true;
						} else {
							document.getElementById('submiter').disabled = false;
						}

							}).trigger('change');

							// To style only <select>s with the selectpicker class
							$('.selectpicker').selectpicker();

							$('#submiter').on('click', function(){
								$(this).prop('disabled', true);

								$('#form1').submit();
							});
					})
		</script>
	@else
		<h1 style="color:#000">Cuenta no encontrada</h1>
	@endif

</div>
