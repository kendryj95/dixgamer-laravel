<div class="container text-center">
	<h1 style="color:#000">Descartar Juego #{{$stock->ID}}</h1>
	<div class="row">
			<div class="col-sm-4">
			</div>
		<div class="col-sm-4">
			<form method="post"action="{{ route('descartar-juego-cuenta-post', $stock->ID) }}">
				{{ csrf_field() }}
				<input type="hidden" name="consola" value="ex4">
				<div class="input-group form-group">
					<span class="input-group-addon">Actual: {{$stock->costo_usd}}USD</span>
					<input class="form-control" type="number" name="costo_usd" id="costo_usd" value="{{ $stock->costo_usd }}" placeholder="Fecha">
				</div>

				<button class="btn btn-primary" type="submit">Actualizar</button>
			</form>
		</div>

		<div class="col-sm-4">
		</div>
	</div>

</div>

<script>
	$(document).ready(function() {
		setTimeout(function(){
			document.getElementById('costo_usd').focus();
		},600);
	});
</script>
