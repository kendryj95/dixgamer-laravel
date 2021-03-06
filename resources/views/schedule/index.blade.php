@extends('layouts.master-layouts')

@section('title', 'Horario de '.session()->get('usuario')->Nombre)

@section('container')

<div class="container">
	<h1>Horario de {{ session()->get('usuario')->Nombre }}</h1>
    <!-- InstanceBeginEditable name="body" -->
    <div class="row">

      <div class="col-md-12">

    		@if (\Helper::operatorsRecoverSecu(session()->get('usuario')->Nombre))
				<span class="label label-danger">No olvides iniciar y cerrar día con tu usuario real</span>
				{{-- <div class="alert alert-danger">No olvides iniciar y cerrar día con tu usuario real</div> --}}
			@else
				@if((!empty($toDay->inicio) && !empty($toDay->fin)) || (!isset($toDay->inicio) && !isset($toDay->fin)))

				<form action="{{ url('horario') }}" method="post">
				{{ csrf_field() }}
				<button type="submit" class="text-center btn btn-success" title="Iniciar día"> ¡Iniciar día!</button>
				</form>

				@endif

				@if(!empty($toDay->ID) && (is_null($toDay->fin)))

					<form action="{{ url('horario',[$toDay->ID]) }}" method="POST">
						<input type="hidden" name="_method" value="PUT">
						{{ csrf_field() }}
						<button type="submit" class="text-center btn btn-danger" title="Iniciar día"> ¡Finalizar día!</button><em class="text-muted"> Iniciado a las {{ date('H:i', strtotime($toDay->inicio)) }}</em>
					</form>

				@endif
			@endif

      </div>

    </div>

    <div class="row">

      @if (!\Helper::operatorsRecoverSecu(session()->get('usuario')->Nombre))
	  <div class="col-md-6">

		<h4>Listado por día</h4>

			@if(count($shedulesDay) > 0)

					<div class="table-responsive">
							<table border="0" align="center" cellpadding="0" cellspacing="5" class="table table-striped">
								<thead>
									<tr>
										<th>Día</th>
										<th>Inicio</th>
										<th>Fin</th>
										<th>Total</th>
									</tr>
								</thead>
								<tbody>
									@foreach($shedulesDay as $shedule)
										<tr>

											<td>{{ date("d-M", strtotime($shedule->Day)) }}</td>
											<td>{{ date("H:i", strtotime($shedule->inicio)) }}</td>
											<td>{{ date("H:i", strtotime($shedule->fin)) }}</td>
											<td>{{ number_format((float)$shedule->Q_horas, 2, ',', '') }}</td>

										</tr>
									@endforeach
								</tbody>
							</table>
						</div>

			@endif

		</div>
	  @endif

      @if (!\Helper::operatorsRecoverSecu(session()->get('usuario')->Nombre))
	  <div class="col-md-5 col-md-offset-1">
			<h4>Listado por mes</h4>
				@if(count($shedulesMonth) > 0)

				<div class="table-responsive">
					<table border="0" align="center" cellpadding="0" cellspacing="5" class="table table-striped">
						<thead>
							<tr>
								<th>Mes</th>
								<th>Días</th>
								<th>Horas</th>
							</tr>
						</thead>
						<tbody>
							@foreach($shedulesMonth as $shedule)
								<tr>

									<td>{{ $shedule->mes}}</td>
									<td>{{ $shedule->Q_dias }}</td>
									<td>{{ number_format((float)$shedule->Q_horas, 2, ',', '') }}</td>

								</tr>
							@endforeach
						</tbody>
					</table>
				</div>

				@endif

		</div>
	  @endif
    </div>

  </div><!--/.container-->

@endsection
