@extends('layouts.master-layouts')
@section('title', 'Balance por Productos '. $dias . ' días')

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
  <h1>Balance por Productos {{ $dias }} días</h1>
    <!-- InstanceBeginEditable name="body" -->
    @if(!isset($acceso)) {{-- Solo tiene acceso a estos filtros el admin --}}
      @foreach($filtro_dias as $dia)
        <a class="btn btn-default btn-sm" href="{{ url('balance_productos_dias') }}?dias={{$dia}}" title="Filtrar" style="margin:5px 0 0 0;">{{ $dia }} días</a>
      @endforeach
    @endif

  <table style="margin-top: 20px" class="table table-striped" border="0" cellpadding="0" cellspacing="5">
      <tr>
    <th width="25"></th>
    
    @if(isset($acceso))
        <th width="50" title="Cantidad Ventas">Vta</th>
    @endif

        <th width="50">Cover</th>
        <th width="150">Titulo</th>

    @if(!isset($acceso)) {{-- Solo tiene acceso a estos campos el admin --}}

        <th width="50" title="Cantidad Ventas">Vta</th>

    <th width="30" title="Precio Promedio">Precio Prom</th>
      
        <th width="30" title="Costo Promedio">Costo Prom</th>
      
    <th width="30" title="Stock en unidades">Stk</th>
      
    <th width="100" title="Proyeccion 1 mes">Proy Compra<br>30 d</th>
    <th width="100" title="Proyeccion 2 meses">45 d</th>
    <th width="100" title="Proyeccion 3 meses">60 d</th>
    <th width="1"></th>

    @endif


      </tr>
      @php $q_venta = 0; @endphp

      @foreach($rsCXP as $i => $row_rsCXP)

      <tr>
        <td>{{ $i+1 }}</td>

        @if(isset($acceso)) {{-- Validación para colocar la columna de ventas en el segundo lugar --}}

      <td><span class="badge badge-default">{{ $row_rsCXP->q_venta }}</span></td>

      @endif

      <td><img class="img-rounded" width="50" id="image-swap" src="{{ asset('img/productos/'.$row_rsCXP->consola."/".$row_rsCXP->titulo.".jpg") }}" alt="" /></td>
      
        <td>
      {{ str_replace('-', ' ', $row_rsCXP->titulo) }} ({{ $row_rsCXP->consola }})
     </td>

    @if(!isset($acceso)) {{-- Solo tiene acceso a estos datos el admin --}}

      <td><span class="badge badge-default">{{ $row_rsCXP->q_venta }}</span></td>

      @php $cost = $row_rsCXP->costo; $con = $row_rsCXP->consola; 
      if($cost < 0.1): $cost = 1; endif;
      if($con == "ps3"): $cost = ($cost / 4);
      elseif($con == "ps4"): $cost = ($cost / 2);
              
      else: $cost = $cost;
      endif; 
                    
      if ($row_rsCXP->ing_total > 0): $ingresomedio = round($row_rsCXP->ing_total/$row_rsCXP->q_venta); else: $ingresomedio = 0; endif;
                    
      $rend = round((($ingresomedio/$cost)-1)*100);
      if ($rend > 500): $rend = "+500"; endif;
      @endphp 
      
        <td><p class="badge badge-success">$ {{ $ingresomedio }}</p><br>
      @php 
      if($rend >= 50 or $rend <= 10): $colorRend="color: red; font-weight:bold; opacity: 0.6;";  else: $colorRend=" opacity:0.4;"; endif; @endphp
    <small style="{{ $colorRend  }}">{{ $rend }} %</p>
    </td>
      
        <td><p class="badge badge-normal" style="opacity:0.85;">$ {{ round($cost) }}</p><br>
    <small style="opacity:0.4;">{{ round($row_rsCXP->costo_usd) }} usd</small>
    </td>
      
    <td><span class="badge badge-default">@php $stk = $row_rsCXP->Q_Stock; $con = $row_rsCXP->consola; 
      if($con == "ps3"): $stk = ($stk / 4);
      elseif($con == "ps4"): $stk = ($stk / 2);
      else: $stk = $stk;
      endif; 
      echo round($stk);
     @endphp</span>
      </td>
    @php
      $con = $row_rsCXP->consola; 
      if($con == "ps3"): $div = 4;
      elseif($con == "ps4"): $div = 2;
      else: $div = 1;
      endif;@endphp
    <td width="1">
      @php 
      $rdo1 = round((($row_rsCXP->q_venta*(30/$dias))/$div) - $stk); @endphp
      @php 
      if($rdo1 < 0): $color1="warning"; else: $color1="info"; endif; @endphp
      <span class="badge badge-{{ $color1 }}">{{ $rdo1 }}</span></td>
    <td width="1">
      @php 
      $rdo2 = round((($row_rsCXP->q_venta*(45/$dias))/$div) - $stk); @endphp
      @php 
      if($rdo2 < 0): $color2="warning"; else: $color2="info"; endif; @endphp
      <span class="badge badge-{{ $color2 }}">{{ $rdo2 }}</span></td>
    <td width="1">
      @php 
      $rdo3 = round((($row_rsCXP->q_venta*(60/$dias))/$div) - $stk); @endphp
      @php 
      if($rdo3 < 0): $color3="warning"; else: $color3="info"; endif; @endphp
      <span class="badge badge-{{ $color3 }}">{{ $rdo3 }}</span></td>
    
      <td width="1"></td>

      @endif

      </tr>   
      @php 
      $q_venta = $q_venta + $row_rsCXP->q_venta; @endphp

      @endforeach    
      <tr>
        <th></th>

        @if(isset($acceso))
        <th>{{ $q_venta }}</th>
        @endif

      <th></th>
        <th></th>

    @if(!isset($acceso)) 

        <th>{{ $q_venta }}</th>

        <th></th>
        <th></th>
       <th></th>
      <th></th>
    @endif
      </tr> 
    </table>
     <!--/row-->
     <!-- InstanceEndEditable -->
    </div><!--/.container-->

@endsection