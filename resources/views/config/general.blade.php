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
	        <li role="presentation"><a  data-toggle="tab" href="#menu7">Dominios para Ctas</a></li>
	        <li role="presentation"><a  data-toggle="tab" href="#menu8">Productos Excluidos Recupero</a></li>
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
	      			<a class="btn btn-info btn-sm pull-right" href="https://dixgamer.com/crontabs/download_log.php"><i class="fa fa-download"></i> Descargar Logs</a>
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
	      			<a href="{{ url('config/proceso', 'actualizar_costo_ps4') }}" class="btn btn-warning btn-lg disabled">Actualizar Costo PS4</a>
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
	      			<div class="col-md-3">
	      				<div class="form-group">
	      					<label style="margin-bottom: 25px" for="dias_modo_continuo">Días Modo Continuo</label>
	      					<input type="number" name="dias_modo_continuo" id="dias_modo_continuo" class="form-control" value="{{ $configuraciones->dias_modo_continuo }}" >
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
	      			<form action="{{ url('config/general') }}" id="productos_excluidas" method="post">
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
	      <div class="tab-pane fade" id="menu7" role="tabpanel">
	      	<div class="row">
	      		<div class="col-md-12">
	      			<form action="{{ url('config/general') }}" id="form_dominios" method="post">
	      				{{ csrf_field() }}
						  <input type="hidden" name="opt" value="6">
						  <input type="hidden" id="id_dominio" name="id_dominio">
						  <input type="hidden" id="dominio" name="dominio">
						  <input type="hidden" id="habilitado" name="habilitado">
						  <input type="hidden" name="accion" id="accion_dom" value="create-edit">
	      				
	      				<table class="table table-striped">
							  <thead>
								  <tr>
									  <th>#</th>
									  <th>Dominio</th>
									  <th>Creado</th>
									  <th>Habilitado</th>
									  <th></th>
								  </tr>
							  </thead>
							  <tbody>
								  <tr>
									  <td></td>
									  <td colspan="3">
										  <input type="text" id="dom0" data-id="0" placeholder="Escriba el nuevo dominio" class="form-control input-sm">
									  </td>
									  <td class="text-center">
										<button type="button" class="btn btn-primary btn-sm" onclick="guardarDominio('0')"><i class="fa fa-save"></i></button>
									  </td>
								  </tr>
								  @foreach($dominios as $dominio)
								  <tr>
								  	  <td> {{ $dominio->ID }} </td>
									  <td> <input style="background: transparent; border: none" type="text" data-id="{{ $dominio->ID }}" id="dom{{$dominio->ID}}" value="{{ $dominio->dominio }}" class="form-control"> </td>
									  <td> {{ date('d/m/Y H:i:s', strtotime($dominio->update_at)) }} </td>
									  <td>
										  <input type="checkbox" id="hab{{$dominio->ID}}" @if($dominio->indicador_habilitado) checked @endif>
									  </td>
									  <td class="text-center">
										  <button type="button" class="btn btn-success btn-sm" onclick="guardarDominio({{ $dominio->ID }})" title="Editar Dominio"><i class="fa fa-pencil"></i></button> 
										  <button type="button" class="btn btn-danger btn-sm" onclick="eliminarDominio({{ $dominio->ID }})"><i class="fa fa-trash"></i></button>
									  </td>
								  </tr>
								  @endforeach
							  </tbody>
						  </table>
	      			</form>
	      		</div>
	      	</div>
		  </div>
		  <div class="tab-pane fade" id="menu8" role="tabpanel">
			<div class="row">
				<div class="col-md-12">
					<form action="{{ url('config/general') }}" id="productos_excluidos_recupero" method="post">
						{{ csrf_field() }}
						<input type="hidden" name="opt" value="8">
						<div class="form-group">
							<label for="">Productos Excluidos Primario:</label><br>
							<select name="productos_excluidos_pri[]" value="" class="form-control select2-multiple select-recupero" multiple>
							  @foreach($titles as $t)
								@if ($t->consola == 'ps4')
									@php
									$selected = '';
									$titulo = explode(" (",$t->nombre_web)[0];
									if (in_array($titulo, $titulos_pri)) {
										$selected = 'selected';
									}
									@endphp
									<option value="{{ $titulo }}" {{$selected}}>{{ str_replace('-', ' ', $titulo) }}</option>
								@endif
							  @endforeach
						  </select>
						</div>
						<div class="form-group">
							<label for="">Productos Excluidos Secundario:</label><br>
							<select name="productos_excluidos_secu[]" value="" class="form-control select2-multiple select-recupero" multiple>
							  @foreach($titles as $t)
								@if ($t->consola == 'ps4')
									@php
									$selected = '';
									$titulo = explode(" (",$t->nombre_web)[0];
									if (in_array($titulo, $titulos_secu)) {
										$selected = 'selected';
									}
									@endphp
									<option value="{{ $titulo }}" {{$selected}}>{{ str_replace('-', ' ', $titulo) }}</option>
								@endif
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

		$( "#titulo-select, .select-recupero" ).select2({
            theme: "bootstrap"
        });

        setTimeout(function(){
        	$('.select2-container').css('width', '100%');
        },300)
	});

	function guardarDominio(pos) {

		var id_dominio = $("#dom"+pos).data('id');
		var dominio = $("#dom"+pos).val();
		var habilitado = $("#hab"+pos).is(':checked') ? 1 : 0;
		
		$('#id_dominio').val(id_dominio);
		$('#dominio').val(dominio);
		$('#habilitado').val(habilitado);

		setTimeout(() => {
			$('#form_dominios').submit();
		}, 300);
	}

	function eliminarDominio(pos) {
		$('#accion_dom').val('delete');
		$('#id_dominio').val($('#dom'+pos).data('id'));

		setTimeout(() => {
			$('#form_dominios').submit();
		}, 300);
	}
</script>

@endsection

@endsection

