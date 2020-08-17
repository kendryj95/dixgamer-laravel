@extends('layouts.master-layouts')
@section('title', 'Listar ventas')

@section('container')

@if (count($errors) > 0)
    <div class="alert alert-danger text-center">
      <ul>
        @foreach ($errors->all() as $error)
          <li>{{ $error }}</li>
        @endforeach
      </ul>
    </div>
@endif

    <div class="container">
	<h1>Listar ventas</h1>
    <!-- InstanceBeginEditable name="body" -->
    <div class="table-responsive">
    <table border="0" align="center" cellpadding="0" cellspacing="5" class="table table-striped">
    <thead>
              <tr>
				<th>#</th>
                <th>ID Vta</th>
                <th>Fecha</th>
                <th width="50">Cover</th>
                <th>Cliente</th>
                <th>Medios</th>
                <th>Precio</th>
                <th>Rdo</th>
              </tr>
            </thead>
		  <tbody>

	  @php $facT=0; $gciaT = 0; @endphp
      @foreach($ventas as $i => $row_rsClientes)
      <tr>

			@php

				$color1 = '';
				$text = '';

				if (strpos($row_rsClientes->medio_venta, 'Web') !== false): $text = 'W'; $color1 = 'info';
				elseif (strpos($row_rsClientes->medio_venta, 'Mail') !== false): $text = 'M'; $color1 = 'danger';
				elseif (strpos($row_rsClientes->medio_venta, 'Mercado') !== false): $text = 'ML'; $color1 = 'warning';
	        	endif;

			@endphp

	        @php 
	        	$color2 = $row_rsClientes->color_medio_cobro;
	        	$text2 = $row_rsClientes->abbrev_medio_cobro;

				$persona = $row_rsClientes->ventas_usuario;

				// Aplico un mejor criterio de "asignar" costo a las ventas de PS3, si el jugo tiene 4 o mas ventas le asigno costo proporcional, si tiene menos ventas le asigno solo el 25%
				if($row_rsClientes->q_vta > 3): $proporcional = (1 / $row_rsClientes->q_vta);
				else: $proporcional = 0.25;
				endif;

				$costo = 0;

				if (($row_rsClientes->consola == 'ps4') && ($row_rsClientes->slot == 'Primario')): $costo = round($row_rsClientes->costo * 0.6); 
				elseif (($row_rsClientes->consola == 'ps4') && ($row_rsClientes->slot == 'Secundario')): $costo = round($row_rsClientes->costo * 0.4);
				elseif ($row_rsClientes->consola == 'ps3'): $costo = round($row_rsClientes->costo * $proporcional);
				elseif (($row_rsClientes->consola !== 'ps4') && ($row_rsClientes->consola !== 'ps3') && ($row_rsClientes->titulo !== 'plus-12-meses-slot')): $costo = round($row_rsClientes->costo);
	        	endif;

	        	$gtoestimado = round($gto_x_ing * $row_rsClientes->precio);
	        	$iibbestimado = round($row_rsClientes->precio * 0.04);
	        	$ganancia = round($row_rsClientes->precio - $row_rsClientes->comision - $costo - $gtoestimado - $iibbestimado);

			@endphp
          	<td>{{ $i+1 }}</td>
          	<td>
          		{{ $row_rsClientes->ID_ventas }}@if ($row_rsClientes->ventas_Notas)<a href="#" data-toggle="popover" data-placement="bottom" data-trigger="focus" title="Notas" data-content="{{ $row_rsClientes->ventas_Notas }}" class="h6" style="color: #555555;"><i class="fa fa-comment fa-fw"></i></a>@endif
          	</td>
            <td>{{ date("d-M", strtotime($row_rsClientes->ventas_Day)) }}</td>
            <td>
            	<img class="img-rounded" width="50" id="image-swap" src="{{ asset('img/productos/'.$row_rsClientes->consola."/".$row_rsClientes->titulo.".jpg") }}" alt="" />
            </td>
            <td>
            	<a title="Ir a Cliente" href="{{ url('clientes', $row_rsClientes->clientes_id) }}">{{ $row_rsClientes->nombre }} {{ $row_rsClientes->apellido }}</a><br /><br /><span style="opacity:0.5" class="text-muted btn-xs"><i class="fa fa-gamepad fa-fw" aria-hidden="true"></i> {{ $row_rsClientes->stock_id }}</span> @if($row_rsClientes->cuentas_id) <a style="opacity:0.5" class="text-muted btn-xs" href="{{ url('cuentas', $row_rsClientes->cuentas_id) }}" title="Ir a Cuenta"><i class="fa fa-link fa-xs fa-fw" aria-hidden="true"></i> {{ $row_rsClientes->cuentas_id }}</a> @endif @if($row_rsClientes->slot == 'Secundario') <span class="label label-danger" style="opacity:0.5">2°</span> @endif 
        	</td>
            <td>
            	<small class="label label-{{ $color1 }}" style="opacity:0.7; font-weight:400;" title="{{ $row_rsClientes->medio_venta }}">{{ $text }}</small> <small class="label label-{{ $color2 }}" style="opacity:0.7; font-weight:400;" title="{{ $row_rsClientes->medio_cobro }}">{{ $text2 }}</small>
        	</td>
            <td>
            	<span class="@if ($row_rsClientes->precio < 1) badge badge-danger @endif">{{ round($row_rsClientes->precio) }}</span>
        	</td>
            <td><span class="@if ($ganancia < 0) badge badge-danger @endif">{{ $ganancia }}</span></td>
            <td>
            	<span class="badge badge-{{ $row_rsClientes->color_user }} pull-right" style="opacity:0.7; font-weight:400;" title="{{$persona}}">{{ substr($row_rsClientes->ventas_usuario,0 , 1) }}</span>
        	</td>
            
     </tr>
        	@php $facT = $facT + $row_rsClientes->precio; $gciaT = $gciaT + $ganancia;  @endphp
        @endforeach
		<tr>
        <th></th>
		<th></th>
        <th></th>
		<th></th>
		<th></th>
        <th></th>
		<th>{{$facT}}</th>
        <th>{{$gciaT}}</th>
        <th></th>
      </tr> 
        </tbody>
        </table>
        <div>
        <div class="col-md-12">
                <ul class="pager">
                	{{ $ventas->render() }}
                </ul>
              </div>
              </div>

          </div>
     <!--/row-->
     <!-- InstanceEndEditable -->
    </div><!--/.container-->


@endsection