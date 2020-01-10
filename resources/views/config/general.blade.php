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
	      <li role="presentation" class="active"><a  data-toggle="tab" href="#menu1">Mensaje extra email</a></li>
	        <li role="presentation"><a  data-toggle="tab" href="#menu2">Cuentas Excluidas - PS3 Resetear</a></li>
	        <li role="presentation"><a  data-toggle="tab" href="#menu3">Reporte de Ventas</a></li>
	        <li role="presentation"><a  data-toggle="tab" href="#menu4">Procesos Automaticos</a></li>
	        <li role="presentation"><a  data-toggle="tab" href="#menu5">Parametros</a></li>
	        <li role="presentation"><a  data-toggle="tab" href="#menu6">Productos Excluidos</a></li>
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
							<label for="">Texto para el mensaje extra email:</label>
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
	      <div class="tab-pane fade" id="menu3" role="tabpanel">
	      	<div class="row">
	      		<div class="col-md-12">
	      			<div class="alert alert-info" role="alert" style="background: #D9EDF7 !important">
	      				<p>Si genera la exportación del reporte, se descargará un <b>.zip</b> de <b>{{ $cantidadStock }}</b> archivos CSV que contiene información de las ventas del stock.</p>
	      			</div>
	      			<a href="{{ url('excel') }}" class="btn btn-primary btn-block">Generar reporte</a>
	      		</div>
	      	</div>
	      </div>
	      <div class="tab-pane fade" id="menu4" role="tabpanel">
	      	<div class="row">
	      		<div class="col-md-12">
	      			<a class="btn btn-info btn-sm pull-right" href="https://dixgamer.com/db/crontabs/download_log.php"><i class="fa fa-download"></i> Descargar Logs</a>
	      		</div>
	      	</div>
	      	<div style="margin-top: 20px" class="row">
	      		<div class="col-md-4">
	      			<a href="{{ url('config/proceso', 'automatizar_clientes') }}" class="btn btn-primary btn-lg">Automatizar Clientes</a>
	      		</div>
	      		<div class="col-md-4">
	      			<a href="{{ url('config/proceso', 'actualizar_estado_wc') }}" class="btn btn-primary btn-lg">Actualizar estados WC</a>
	      		</div>
	      		<div class="col-md-4">
	      			<a href="{{ url('config/proceso', 'actualizar_ids_ventas') }}" class="btn btn-primary btn-lg">Actualizar IDS Ventas</a>
	      		</div>
	      	</div>

	      	<div style="margin-top: 20px" class="row">
	      		<div class="col-md-4">
	      			<a href="{{ url('config/proceso', 'actualizar_costo_ps4') }}" class="btn btn-primary btn-lg">Actualizar Costo PS4</a>
	      		</div>
	      		<div class="col-md-4">
	      			<a href="{{ url('config/proceso', 'automatizar_stock_web') }}" class="btn btn-primary btn-lg">Automatizar Stock Web</a>
	      		</div>
	      		<div class="col-md-4">
	      			<a href="{{ url('config/proceso', 'automatizar_web_ps3') }}" class="btn btn-primary btn-lg">Automatizar Stock Web PS3</a>
	      		</div>
	      	</div>

	      	<div style="margin-top: 20px" class="row">
	      		<div class="col-md-4">
	      			<a href="{{ url('config/proceso', 'automatizar_web_ps4') }}" class="btn btn-primary btn-lg">Automatizar Stock Web PS4</a>
	      		</div>
	      	</div>
	      </div>
	      <div class="tab-pane fade" id="menu5" role="tabpanel">
	      	<form action="{{ url('config/general') }}" method="post">
				{{ csrf_field() }}
				<input type="hidden" name="opt" value="3">

	      		<div class="row">
	      			<div class="col-md-3">
	      				<div class="form-group">
	      					<label for="valor_oferta_sugerida">Valor Oferta Sugerida - Automatizar Web ps4</label>
	      					<input style="text-align: right" type="number" name="valor_oferta_sugerida" id="valor_oferta_sugerida" class="form-control" value="{{ number_format($configuraciones->valor_oferta_sugerida,2,".","") }}" step="0.01">
	      				</div>
	      			</div>
	      			<div class="col-md-3">
	      				<div class="form-group">
	      					<label for="costo_automatizar_web_ps4">Valor Costo - Automatizar Web ps4</label>
	      					<input style="text-align: right" type="number" name="costo_automatizar_web_ps4" id="costo_automatizar_web_ps4" class="form-control" value="{{ number_format($configuraciones->costo_automatizar_web_ps4,2,".","") }}" step="0.01">
	      				</div>
	      			</div>
	      			<div class="col-md-3">
	      				<div class="form-group">
	      					<label style="margin-bottom: 25px" for="fecha_referencia">Fecha Referencia</label>
	      					<input type="date" name="fecha_referencia" id="fecha_referencia" class="form-control" value="{{ $configuraciones->fecha_referencia }}" >
	      				</div>
	      			</div>
	      			<div class="col-md-3">
	      				<div class="form-group">
	      					<label style="margin-bottom: 25px" for="dias">Días Congelar TC</label>
	      					<input type="number" name="dias_congelar_tc" id="dias" class="form-control" value="{{ $configuraciones->dias_congelar_tc }}" >
	      				</div>
	      			</div>
	      			<div class="col-md-12">
	      				<button type="submit" class="btn btn-primary btn-block"><i class="fa fa-save"></i> Guardar</button>
	      			</div>
	      		</div>
	      	</form>
	      </div> <!-- TERMINA -->
	      <div class="tab-pane fade" id="menu6" role="tabpanel">
	      	<div class="row">
	      		<div class="col-md-12">
	      			<form action="{{ url('config/general') }}" id="cuentas_excluidas" method="post">
	      				{{ csrf_field() }}
	      				<input type="hidden" name="opt" value="4">
	      				<div class="form-group">
	      					<label for="">Productos Excluidos:</label><br>
	      					<select name="productos_excluidos[]" id="titulo-select" value="" class="form-control select2-multiple" multiple>
				                @foreach($titles as $t)
				                @php
				                $selected = '';
				                $titulo = explode(" (",$t->nombre_web)[0];
				                if (in_array($titulo, $titulos)) {
				                    $selected = 'selected';
				                }
				                @endphp
				                <option value="{{ $titulo }}" {{$selected}}>{{ str_replace('-', ' ', $titulo) }}</option>
				                @endforeach
				            </select>
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

		$( "#titulo-select" ).select2({
            theme: "bootstrap"
        });

        setTimeout(function(){
        	$('.select2-container').css('width', '100%');
        },300)
	});
</script>

@endsection

@endsection

