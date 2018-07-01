
@php
	if(empty($expense)){
		$expense = new \stdClass;
		$expense->costo_usd = 0;
	}

@endphp
<div class="container">
	@if(count($account) > 0)
		<h1 style="color:#000">GUARDAR STOCK</h1>
		<div class="row">
			<div class="col-sm-2" style="text-align:right;">
				<span id="alerta" class="label label-danger"></span>
				<img class="img-rounded pull-right" width="100" id="image-swap" src="" alt="" />
			</div>
			<div class="col-sm-6">
				<form method="post" name="form1" action="{{ url('stock_insertar_cuenta',[$account->ID]) }}">
					{{ csrf_field() }}

					<input type="text" name="cuentas_id" value="{{ $account->ID }}" hidden>

					<div class="input-group form-group">
						<span class="input-group-addon"><i class="fa fa-gamepad fa-fw"></i></span>
						<select id="titulo-selec" name="titulo" class="selectpicker form-control" data-live-search="true" data-size="5">

							@foreach($titles as $title)
								<option value="{{$title->producto}}">{{str_replace('-', ' ', $title->producto)}}</option>
							@endforeach

							</select>
						<!--- <input type="text" id="titulo-selec" name="titulo" class="titulo tt-query form-control" autocomplete="off" spellcheck="false" placeholder="Buscar título"> -->
					</div>

					<div class="input-group form-group">
						<span class="input-group-addon"><i class="fa fa-cube fa-fw"></i></span>
						<select id="consola" name="consola" class="selectpicker form-control" onchange="return GameVerifyFunction();">
							<option selected value="ps4" data-content="<span class='label label-primary'>ps4</span>">ps4</option>
							<option value="ps3" data-content="<span class='label label-warning' style='background-color:#000;'>ps3</span>">ps3</option>
							<option value="ps" data-content="<span class='label label-danger'>psn</span>">psn</option>
							<option value="steam" data-content="<span class='label label-default'>steam</span>">steam</option>
							<option value="psvita" data-content="<span class='label label-info'>psvita</span>">psvita</option>
						</select>
					</div>

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
							name="costo_usd" value="{{$accountBalance->costo_usd - $expense->costo_usd}}">

							<span class="input-group-addon">
								<em style="opacity:0.7" class="text-muted">Saldo: {{$accountBalance->costo_usd - $expense->costo_usd}}
									<img src="https://exur-exur.netdna-ssl.com/realskin/images/usa.png" style="opacity:0.7">
								</em>
						</span>
					</div>

					<div class="input-group form-group">
						<span class="input-group-addon"><i class="fa fa-comment fa-fw"></i></span>
						<input class="form-control" type="text" name="Notas" placeholder="Notas de stock" required>
					</div>

					<button class="btn btn-primary btn-block" type="submit">Guardar</button>
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
					var consola = document.getElementById('consola').value;
					$("#image-swap").attr("src", (titulo !== "" &&  + consola !== "") ? "/img/productos/" + consola + "/" + titulo + ".jpg" : "");
					$('#image-swap').load(function() {
									document.getElementById("alerta").innerHTML = "";
									});
					$('#image-swap').error(function() {
						document.getElementById("alerta").innerHTML = "no se encuentra";
					});
							}).trigger('change');
					})
		</script>
	@else
		<h1 style="color:#000">Cuenta no encontrada</h1>
	@endif

</div>
