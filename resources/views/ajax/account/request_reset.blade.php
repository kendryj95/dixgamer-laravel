<div class="container text-center">
	<h1 style="color:#000">Solicitar reseteo</h1>
	<div class="row">
			<div class="col-sm-4">
			</div>
		<div class="col-sm-4">
			<form method="post"action="{{ url('solicitar_reseteo_cuenta',[$account->ID]) }}">
				{{ csrf_field() }}
				<div class="input-group form-group">
					<span class="input-group-addon"><i class="fa fa-comment fa-fw"></i></span>
					<input class="form-control" type="text" name="note" placeholder="Notas">
				</div>

				<button class="btn btn-danger" type="submit">Pedir Reseteo</button>
			</form>
		</div>

		<div class="col-sm-4">
		</div>
	</div>

</div>
