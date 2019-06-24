@extends('layouts.master-layouts')
@section('title', 'Balance por Productos')

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
	<h1>Balance por Productos</h1>
    <!-- InstanceBeginEditable name="body" -->
	<table class="table table-striped" border="0" cellpadding="0" cellspacing="5">
      <tr>
		<th width="50"></th>
        <th width="50">Cover</th>
        <th>Titulo</th>
        <th title="Compras">C</th>
        <th title="Stock disponible">Stk</th>
        <th>Total</th>
        <th class="text-muted">Prom</th>
        <th class="text-muted">Min</th>
        <th class="text-muted">Max</th>
        <th title="Cantidad Ventas">Vta</th>
        <th title="Ingreso">Ing</th>
        <th title="Comisiones">Com</th>
        <th title="Ganancia Realizada">Real</th>
        <th class="text-muted" title="Ganancia Proyectada">Proy</th>
      </tr>
      @php $compra = 0; $stock_dispo = 0; $costoprom = 0; $costototal = 0; $q_venta = 0; $ing_total = 0; $com_total = 0; $gcia_real = 0; $gcia_proyec = 0; @endphp
      @foreach($rsCXP as $i => $row_rsCXP)
      <tr>
        <td>{{ $i+1 }}</td>
		  <td><img class="img-rounded" width="50" id="image-swap" src="{{ asset('img/productos/'.$row_rsCXP->consola."/".$row_rsCXP->titulo.".jpg") }}"alt="" /></td>
        <td title="{{ $row_rsCXP->titulo }} ({{ $row_rsCXP->consola }})"> {{ str_replace('-', ' ', $row_rsCXP->titulo) }} ({{ $row_rsCXP->consola }})</td>
        <td>{{ $row_rsCXP->q_stock }}</td>
        <td>
        @php 
        if($row_rsCXP->consola == 'ps3'): $stock_disponible = ((4 * $row_rsCXP->q_stock) - $row_rsCXP->q_venta); endif; @endphp
         @if(($row_rsCXP->consola == 'ps4') || ($row_rsCXP->titulo == 'plus-12-meses-slot')) 
         @php 
         $stock_disponible = ((2 * $row_rsCXP->q_stock) - $row_rsCXP->q_venta); @endphp <small>({{ $row_rsCXP->s_pri }} y {{ $row_rsCXP->s_sec }})</small> @endif
        @php 
        if(($row_rsCXP->consola !== 'ps4') && ($row_rsCXP->consola !== 'ps3') && ($row_rsCXP->titulo !== 'plus-12-meses-slot')): $stock_disponible = ((1 * $row_rsCXP->q_stock) - $row_rsCXP->q_venta); endif; @endphp
        @php 
        if($stock_disponible > 0): echo $stock_disponible; endif; @endphp
        </td>
        <td>$ {{ round($row_rsCXP->costototal) }}</td>
        <td class="text-muted">{{ round($row_rsCXP->costoprom) }}</td>
		<td class="text-muted">{{ round($row_rsCXP->costomin) }}</td>
        <td class="text-muted">{{ round($row_rsCXP->costomax) }}</td>
        <td>{{ $row_rsCXP->q_venta }}</td>
        <td>$ {{ round($row_rsCXP->ing_total) }}</td>
        <td>$ {{ round($row_rsCXP->com_total) }}</td>
        <td>$ @php 
        $calc_gananciareal = round($row_rsCXP->ing_total - $row_rsCXP->com_total - $row_rsCXP->costototal); 
        echo $calc_gananciareal; @endphp
        </td>
		  @php
		  if ($row_rsCXP->q_venta > 0): $q_vtas = $row_rsCXP->q_venta; else: $q_vtas =  1; endif;@endphp
        <td class="text-muted">$ @php 
        if($row_rsCXP->consola == 'ps3'): $calc_gananciaproyec = round( 4 * $row_rsCXP->q_stock * ((($row_rsCXP->ing_total - $row_rsCXP->com_total) / $q_vtas) - ($row_rsCXP->costoprom / 4))); endif; @endphp
        @php 
        if(($row_rsCXP->consola == 'ps4') || ($row_rsCXP->titulo == 'plus-12-meses-slot')): $calc_gananciaproyec = round( 2 * $row_rsCXP->q_stock * ((($row_rsCXP->ing_total - $row_rsCXP->com_total) / $q_vtas) - ($row_rsCXP->costoprom / 2))); endif; @endphp
        @php 
        if(($row_rsCXP->consola !== 'ps4') && ($row_rsCXP->consola !== 'ps3') && ($row_rsCXP->titulo !== 'plus-12-meses-slot')): @endphp
		@php 
		$calc_gananciaproyec = round( 1 * $row_rsCXP->q_stock * ((($row_rsCXP->ing_total - $row_rsCXP->com_total) / $q_vtas ) - ($row_rsCXP->costoprom / 1))); endif; @endphp
        @php 
        if($calc_gananciaproyec < 1):  $calc_gananciaproyec = ($stock_disponible * 35); endif; @endphp
		{{ round($calc_gananciaproyec) }}
        </td>
      </tr>   
      @php 
      $compra = $compra + $row_rsCXP->q_stock;
      $stock_dispo = $stock_dispo + $stock_disponible;
      $costoprom = $costoprom + $row_rsCXP->costoprom;
      $costototal = $costototal + $row_rsCXP->costototal;
      $q_venta = $q_venta + $row_rsCXP->q_venta;
      $ing_total = $ing_total + $row_rsCXP->ing_total;
      $com_total = $com_total + $row_rsCXP->com_total;
      $gcia_real = $gcia_real + $calc_gananciareal;
      $gcia_proyec = $gcia_proyec + $calc_gananciaproyec; @endphp
      @endforeach     
      <tr>
        <th></th>
		  <th></th>
        <th></th>
        <th>{{ $compra }}</th>
        <th>{{ $stock_dispo }}</th>
        <th>{{ $costototal }}</th>
        <th class="text-muted">{{ $costoprom }}</th>
        <th></th>
        <th></th>
        <th>{{ $q_venta }}</th>
        <th>{{ round($ing_total) }}</th>
        <th>{{ round($com_total) }}</th>
        <th>{{ $gcia_real }}</th>
        <th class="text-muted">{{ $gcia_proyec }}</th>
      </tr> 
    </table>
     <!--/row-->
     <!-- InstanceEndEditable -->
    </div><!--/.container-->

@endsection