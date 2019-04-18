<div class="container">
	<h1 style="color: #000">Editar Saldo Prov</h1>

	<div class="alert alert-danger text-center" id="alert-saldo_prov" style="display:none"></div>

	<div class="row">
		<div class="col-md-3"></div>
		<div class="col-md-6">
			<form action="{{ url('edit_saldo_prov') }}" method="post" id="form_saldo_prov">
				{{ csrf_field() }}
				<input type="hidden" name="ID" value="{{ $datos->ID }}">
				<div class="form-group">
					<label for="">USD</label>
					<div class="input-group">
						<input type="number" name="usd" value="{{ $datos->usd }}" id="usd" class="form-control">
						<span class="input-group-addon">Antes: {{ $datos->usd }}</span>
					</div>
				</div>
				
				<div class="form-group">
					<label for="">Cotiz</label>
					<div class="input-group">
						<input type="number" name="cotiz" value="{{ $datos->cotiz }}" id="cotiz" class="form-control">
						<span class="input-group-addon">Antes: {{ $datos->cotiz }}</span>
					</div>
				</div>
				
				<div class="form-group">
					<label for="">ARS</label>
					<div class="input-group">
						<input type="number" name="ars" value="{{ $datos->ars }}" id="ars" class="form-control">
						<span class="input-group-addon">Antes: {{ $datos->ars }}</span>
					</div>
				</div>
				
				<div class="form-group">
					<label for="">Fecha</label>
					<div class="input-group">
						<input type="date" name="Day" value="{{ date('Y-m-d',strtotime($datos->Day)) }}" id="Day" class="form-control">
						<span class="input-group-addon">Antes: {{ date('Y-m-d',strtotime($datos->Day)) }}</span>
					</div>
					<input type="hidden" name="fecha_anterior" value="{{ $datos->Day }}">
				</div>
				
				<button type="button" class="btn btn-primary btn-block" id="btnSubmit">Editar</button>
			</form>
		</div>
		<div class="col-md-3"></div>
	</div>
</div>

<script>
	$(document).ready(function() {
		$('#btnSubmit').on('click', function(event) {
			event.preventDefault();

			$('#alert-saldo_prov').hide();
			
			var usd = $('#usd').val(),
				cotiz = $('#cotiz').val(),
				ars = $('#ars').val()
				fecha = $('#Day').val();

			if (usd != '' && cotiz != '' && ars != '' && fecha != '') {
				$('#form_saldo_prov').submit();
			} else {
				$('#alert-saldo_prov').fadeIn();
			}
		});
	});
</script>