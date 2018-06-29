
<div class="container">
	@if(count($account) > 0 && count($stock) > 0)
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

							@foreach($titles as $title)
								<option value="{{$title->producto}}">{{str_replace('-', ' ', $title->producto)}}</option>
							@endforeach

							</select>
					</div>

					<div class="input-group form-group">
						<span class="input-group-addon"><i class="fa fa-cube fa-fw"></i></span>
						<select id="consola" name="consola" class="selectpicker form-control">
							<option selected value="ps4" data-content="<span class='label label-primary'>ps4</span>">ps4</option>
							<option value="ps3" data-content="<span class='label label-warning' style='background-color:#000;'>ps3</span>">ps3</option>
							<option value="ps" data-content="<span class='label label-danger'>psn</span>">psn</option>
							<option value="steam" data-content="<span class='label label-default'>steam</span>">steam</option>
							<option value="psvita" data-content="<span class='label label-info'>psvita</span>">psvita</option>
						</select>
					</div>

					<button class="btn btn-primary btn-block" type="submit">Guardar</button>
				</form>

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
