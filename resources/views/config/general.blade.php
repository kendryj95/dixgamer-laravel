@extends('layouts.master-layouts')

@section('title', 'Configuración General')

@section('container')

<style>
	.bootstrap-tagsinput {
		width: 100% !important;
	}
</style>

<div class="container">

	<h1>Configuraciones Generales</h1>

	<div class="row">
	  <div class="col-lg-3">
	    <ul class="nav nav-pills nav-stacked">
	      <li role="presentation" class="active"><a  data-toggle="tab" href="#menu1">Oferta Fortnite</a></li>
	        <li role="presentation"><a  data-toggle="tab" href="#menu2">Cuentas Excluidas - PS3 Resetear</a></li>
	    </ul>
	  </div>
	  <div class="col-lg-8 col-md-offset-1">
	    <div class="tab-content" id="v-pills-tabContent">
	      <div class="tab-pane fade in active" id="menu1" role="tabpanel">
			<div class="row">
				<div class="col-md-12">
					<form action="{{ url('config/general') }}" method="post">
						{{ csrf_field() }}
						<input type="hidden" name="opt" value="1">
						<div class="form-group">
							<label for="">Texto para ofertas Fortnite:</label>
							<textarea name="oferta_fortnite" id="form" cols="30" rows="10" class="form-control">{{ $oferta_fortnite }}</textarea>
						</div>
						<button type="submit" class="btn btn-primary btn-block">Actualizar</button>
					</form>
				</div>
			</div>
	      </div>
	      <div class="tab-pane fade" id="menu2" role="tabpanel">
	      	<div class="row">
	      		<div class="col-md-12">
	      			<form action="{{ url('config/general') }}" id="cuentas_excluidas" method="post">
	      				{{ csrf_field() }}
	      				<input type="hidden" name="opt" value="2">
	      				<div class="form-group">
	      					<label for="">Cuentas Excluidas:</label><br>
	      					<input type="text" name="cuentas_excluidas" value="{{$cuentas_excluidas}}" data-role="tagsinput">
	      				</div>
	      				<button type="submit" class="btn btn-primary btn-block">Actualizar</button>
	      			</form>
	      		</div>
	      	</div>
	      </div>
	    </div>
	  </div>
	</div>
</div>

@section('scripts')
@parent

<script>
	$(document).ready(function() {
		tinymce.init({
			selector: '#form',
			height: 150,
			plugins: [
				'advlist lists preview',
				'visualblocks',
				'contextmenu paste link'
			], //3 media 1 link image autolink
			toolbar: 'insertfile undo redo | styleselect | bold italic | alignleft aligncenter alignright alignjustify | bullist numlist outdent indent | link image',
			language: 'es'
		});

		$("#cuentas_excluidas").keypress(function(e) {
		        if (e.which == 13) {
		            return false;
		        }
		});
	});
</script>

@endsection

@endsection

