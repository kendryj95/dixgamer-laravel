<div class="container">
	<h1 style="color: #000">Editar Saldo Prov</h1>

	<div class="alert alert-danger text-center" id="alert-saldo_prov" style="display:none"></div>

	<div class="row">
		<div class="col-md-3"></div>
		<div class="col-md-6">
			<form action="{{ url('edit_saldo_prov') }}" method="post" id="form_saldo_prov">
				{{ csrf_field() }}
				<input type="hidden" name="ID" value="{{ $datos->ID }}">

				<div class="row">
					<div class="col-md-4">
						<div class="form-group">
							<label for="">USD</label>
							<div class="input-group">
								<input type="number" name="usd" value="{{ number_format($datos->usd,2,".","") }}" id="usd" class="form-control">
								<span class="input-group-addon" style="font-size: 11px">Antes: {{ number_format($datos->usd,2,".","") }}</span>
							</div>
						</div>
					</div>
					<div class="col-md-4">
						<div class="form-group">
							<label for="">Cotiz</label>
							<div class="input-group">
								<input type="number" name="cotiz" value="{{ number_format($datos->cotiz,2,".","") }}" id="cotiz" class="form-control">
								<span class="input-group-addon" style="font-size: 11px">Antes: {{ number_format($datos->cotiz,2,".","") }}</span>
							</div>
						</div>
					</div>
					<div class="col-md-4">
						<div class="form-group">
							<label for="">ARS</label>
							<div class="input-group">
								<input type="number" name="ars" value="{{ number_format($datos->ars,2,".","") }}" id="ars" class="form-control">
								<span class="input-group-addon" style="font-size: 11px">Antes: {{ number_format($datos->ars,2,".","") }}</span>
							</div>
						</div>
					</div>
					<div class="col-md-12">
						<div class="form-group">
							<label for="">Fecha</label>
							<div class="input-group">
								<input type="date" name="Day" value="{{ date('Y-m-d',strtotime($datos->Day)) }}" id="Day" class="form-control">
								<span class="input-group-addon">Antes: {{ date('d-m-Y',strtotime($datos->Day)) }}</span>
							</div>
							<input type="hidden" name="fecha_anterior" value="{{ $datos->Day }}">
						</div>
					</div>

					<button type="button" class="btn btn-primary btn-block" id="btnSubmit">Editar</button>

				</div>
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

		$("#usd, #cotiz").keyup(function() {
		    m1 = document.getElementById("usd").value;
		    m2 = document.getElementById("cotiz").value;
		    r = m1*m2;
		    document.getElementById("ars").value = r;
		  });
	});
</script>