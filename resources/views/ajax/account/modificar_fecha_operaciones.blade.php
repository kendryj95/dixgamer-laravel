<div class="container text-center">
	<h1 style="color:#000">Modificar Fecha Operación</h1>
	<div class="row">
			<div class="col-sm-4">
			</div>
		<div class="col-sm-4">
			<form method="post"action="{{ url('modify_date_operations_store') }}">
				{{ csrf_field() }}
				<input type="hidden" name="tipo" value="{{ $tipo }}">
				<input type="hidden" name="id" value="{{ $id }}">
				<div class="input-group form-group">
					<span class="input-group-addon"><i class="fa fa-calendar fa-fw"></i></span>
					<input class="form-control" type="date" name="Day" id="Day" value="{{ $day }}" placeholder="Fecha">
					<input type="hidden" name="current_day" value="{{$day}}">
					<input type="hidden" name="account_id" value="{{$account_id}}">
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
			document.getElementById('Day').focus();
		},600);
	});
</script>
