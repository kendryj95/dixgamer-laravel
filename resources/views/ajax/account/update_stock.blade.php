@php
$saldo = $accountBalance->costo_usd - $expense->costo_usd;
@endphp
<div class="container">
	@if($account && $stock)
		<h1 style="color:#000">Modificar Producto # {{ $stock->ID }}</h1>
		<div class="row">
			<div class="col-sm-3" style="text-align:right;">
				<span id="alerta" class="label label-danger"></span>
				<img class="img-rounded pull-right" width="100" id="image-swap" src="" alt="" />
			</div>
			<div class="col-sm-6">
				<form method="post" action="{{ url('actualizar_stock_cuenta',[$account->ID]) }}">
					{{ csrf_field() }}

					<input type="hidden" name="stock_id" value="{{ $stock->ID }}">

					<div class="input-group form-group">
						<span class="input-group-addon"><i class="fa fa-gamepad fa-fw"></i></span>
						<select id="titulo-selec" name="titulo" class="selectpicker form-control" data-live-search="true" data-size="5">
								<option value="">Seleccione stock</option>
							@foreach($titles as $title)
								
								<option value="{{explode(" (",$title->nombre_web)[0]}}">{{str_replace('-', ' ', $title->nombre_web)}}</option>
							@endforeach

							</select>
					</div>

					<input type="hidden" name="consola" id="consola">

					<!-- <div class="input-group form-group">
						<span class="input-group-addon"><i class="fa fa-cube fa-fw"></i></span>
						<select id="consola" name="consola" class="selectpicker form-control">
							<option selected value="ps4" data-content="<span class='label label-primary'>ps4</span>">ps4</option>
							<option value="ps3" data-content="<span class='label label-warning' style='background-color:#000;'>ps3</span>">ps3</option>
							<option value="ps" data-content="<span class='label label-danger'>psn</span>">psn</option>
							<option value="steam" data-content="<span class='label label-default'>steam</span>">steam</option>
							<option value="psvita" data-content="<span class='label label-info'>psvita</span>">psvita</option>
						</select>
					</div> -->

					<div class="input-group form-group">
						<span class="input-group-addon"><em>Costo en USD</em></span>

						<input
							id="proporcion_usd"
							class="form-control"
							type="number"
							step="0.01"
							name="costo_usd" value="{{$stock->costo_usd}}" @if ($saldo == 0) readonly @endif>
							<input type="hidden" name="saldo_act" value="{{$saldo}}">
							<input type="hidden" name="costo_act" value="{{$stock->costo_usd}}">

							<span class="input-group-addon">
								<em style="opacity:0.7" class="text-muted">Saldo: {{$saldo}}
									<img src="https://exur-exur.netdna-ssl.com/realskin/images/usa.png" style="opacity:0.7">
								</em>
						</span>
					</div>

					<button class="btn btn-primary btn-block" type="submit" id="submiter">Guardar</button>
				</form>

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

							$('.selectpicker').selectpicker();
					})
		</script>
	@else
		<h1 style="color:#000">Cuenta no encontrada</h1>
	@endif

</div>
