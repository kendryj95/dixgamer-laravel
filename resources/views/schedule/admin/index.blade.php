@extends('layouts.master-layouts')

@section('title', 'Horarios')

@section('container')

<div class="container">
	<h1>Horarios</h1>
    <!-- InstanceBeginEditable name="body" -->

    <div class="row">
    	<div class="col-md-12">
    		<a class="btn @if($conFiltro == 'No') btn-success @else btn-default @endif btn-sm" href="{{ url('horarios') }}" title="Ver Todos" style="margin:5px 0 0 0;">Todos</a>

    		@foreach($usuarios as $value)

    		  <a
    		    class="btn @if($conFiltro == 'Si' && $user == $value->usuario) btn-success @else btn-default @endif btn-sm"
    		    href="{{ url('horarios', $value->usuario) }}"
    		    title="Filtrar {{$value->usuario}}"
    		    style="margin:5px 0 0 0;">

    		    {{$value->usuario}}

    		  </a>

    		@endforeach
    	</div>
    </div>

    <div class="row">

      <div class="col-md-6">

				<h4>Listado por día</h4>

    		@if(count($shedulesDayAdmin) > 0)

					<div class="table-responsive">
						<table border="0" align="center" cellpadding="0" cellspacing="5" class="table table-striped">
							<thead>
								<tr>
								  <th>Día</th>
								  <th>Inicio</th>
								  <th>Fin</th>
								  <th><i class="fa fa-user fa-fw" aria-hidden="true"></i></th>
								  <th>Total</th>
								  <th><i class="fa fa-check fa-fw" aria-hidden="true"></i></th>
								  <th><i class="fa fa-pencil fa-fw" aria-hidden="true"></i></th>
								</tr>
							</thead>
							<tbody>
								@foreach($shedulesDayAdmin as $shedule)
									<tr>

										<td>{{ date("d", strtotime($shedule->Day)) }}</td>
										<td>{{ date("H:i", strtotime($shedule->inicio)) }}</td>
										<td>{{ date("H:i", strtotime($shedule->fin)) }}</td>
										<td><span class="label label-{{$shedule->color}}"><strong>{{strtoupper(substr($shedule->usuario,0,1))}}</strong></span></td>
										<td>{{ number_format((float)$shedule->Q_horas, 2, ',', '') }}</td>
										<td>@if ($shedule->verificado == 'si') <i class="fa fa-check fa-fw" style="color:#ddd;" aria-hidden="true"></i> @else <a class="text-center btn btn-success btn-xs" title="verificar" href="{{url("horarios/verificar/$shedule->ID")}}"><i class="fa fa-check fa-fw"  aria-hidden="true"></i></a> @endif</td>
										<td><a href="javascript:void(0)" data-toggle="modal"
												data-target=".modalHorario" onclick='getPageAjax("{{ url('horarios/edit') }}", "#modalHorario", {{ $shedule->ID }})' class="btn-xs"><i class="fa fa-pencil" title="Editar horario"></i></a></td>
	
									</tr>
								@endforeach
							</tbody>
						</table>
					</div>

        @endif

      </div>

      <div class="col-md-5 col-md-offset-1">
				<h4 style="text-align: right;">Listado por mes</h4>
				@if(count($shedulesMonthAdmin) > 0)

					<div class="table-responsive" class="table table-striped">
						<table border="0" align="center" cellpadding="0" cellspacing="5" class="table table-striped">
							<thead>
								@foreach($shedulesMonthAdmin as $shedule)
									<tr>

										<td style="text-align:right">{{ $shedule->mes}}</td>
										<td style="text-align:right"><span class="label label-{{$shedule->color}}"><strong>{{strtoupper(substr($shedule->usuario,0,1))}}</strong></span></td>
										<td style="text-align:right">{{ $shedule->Q_dias }}</td>
										<td style="text-align:right"><strong>{{ number_format((float)$shedule->Q_horas, 2, ',', '') }}</strong></td>

									</tr>
								@endforeach
							</thead>
						</table>
					</div>

        @endif

      </div>
    </div>

    <div class="modal fade modalHorario" id="modalHorario" tabindex="-1" role="dialog" aria-labelledby="myLargeModalLabel">
      <div class="modal-dialog modal-lg" style="top:40px;">
        <div class="modal-content">
          
          <div class="modal-body" style="text-align:center;padding:10px;">
          </div>
          
        </div>
      </div>
    </div>

  </div><!--/.container-->

@endsection
