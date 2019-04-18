@extends('layouts.master-layouts')

@section('title', 'Cargas de saldo para '. $vendedor . '-GC')

@section('container')

    <div class="container">
	<h1>Cargas de saldo para {{ $vendedor }}-GC</h1>
    <!-- InstanceBeginEditable name="body" -->
		@if (Helper::validateAdministrator(session()->get('usuario')->Level))
      @foreach($users as $user)
			 <a class="btn @if($vendedor == $user->ex_usuario) btn-info @else btn-default @endif btn-sm" href="{{ url('carga_gc', $user->ex_usuario) }}">Ver {{ str_replace('-GC', '', $user->ex_usuario) }}</a>
      @endforeach
		@endif
		@if (Helper::validateAdministrator(session()->get('usuario')->Level))
		
			<form style="margin-top: 20px" method="post" name="form1" action="{{ url('carga_gc_store') }}">

        {{ csrf_field() }}

				<div class="col-md-4">
                <div class="input-group form-group" id="div_costo_usd">
                <span class="input-group-addon">usd</span>
                  <input class="form-control" type="text" name="carga_usd" id="multiplicando" value="">
                </div><!-- /input-group -->
              </div><!-- /.col-lg-6 -->
              
              <?php //$amount = urlencode($amount);
				  //$get = file_get_contents("https://finance.google.com/finance/converter?a=1&from=USD&to=ARS");
				  //$get = explode("<span class=bld>",$get);
				  //$get = explode("</span>",$get[1]);  
				  //$converted_amount = preg_replace("/[^0-9\.]/", null, $get[0]);
				  //$cotiz =  round(($converted_amount + 0.27), 1, PHP_ROUND_HALF_UP);
				?>
              <div class="col-md-4">
                <div class="input-group form-group">
                <span class="input-group-addon">ctz</span>
                  <input class="form-control" type="text" name="carga_cotiz" id="multiplicador" value="">
                </div><!-- /input-group -->
              </div><!-- /.col-lg-6 -->
            
            <div class="col-md-4">
            <div class="input-group form-group" id="caja3">
              <span class="input-group-addon"><i class="fa fa-dollar fa-fw"></i></span>
              <input class="form-control" type="text" name="carga_ars" id="resultado" value="" style="text-align:right;">
			</div>
            </div>

				<button class="btn btn-success" type="submit">Carga a {{ str_replace('-GC', '', $vendedor) }}-GC</button>
				<input type="hidden" name="MM_insert" value="form1">
				<input type="hidden" name="usuario" value="{{ str_replace('-GC', '', $vendedor) }}-GC">
			</form>
		@endif
    <div class="row">
    <div class="col-md-4">
	<h4>Saldo Proveedor <span class="label label-normal"><?php if ($row_SaldoP): ?><?php echo ($row_SaldoP[0]->carga_usd - $row_SaldoP[0]->carga_usd); ?><?php endif;?></span></h4>
	</div>
</div>
<div class="row">
		
    <div class="col-md-5">
    
    <?php if ($row_Total): ?>
    <h4>Listado Total</h4>
    <div class="row">
        <form action="{{ url('carga_gc',$vendedor) }}" method="get" class="form-inline">
            <div class="form-group col-md-4">
                <label for="fecha_ini">Fecha Inicio:</label>
                <input type="date" name="fecha_ini" id="fecha_ini" value="{{ $fecha_ini}}" class="form-control input-sm">
            </div>

            <div class="form-group col-md-4">
                <label for="fecha_fin">Fecha Fin:</label>
                <input type="date" name="fecha_fin" id="fecha_fin" value="{{ $fecha_fin}}" class="form-control input-sm">
            </div>

            <div class="form-group">
              <label for="palabra">&nbsp;</label> <br>
              <button type="submit" class="btn btn-default btn-sm">Buscar</button>

            </div>
        </form>
    </div>
    <div class="table-responsive">
        <table border="0" align="center" cellpadding="0" cellspacing="5" class="table table-striped">
    <thead>
              <tr>
				<th>Q</th>
				<th>Cover</th>
				<th>Producto</th>
                
                <th>Costo USD</th>
                <th>Total USD</th>
              </tr>
            </thead>
		  <tbody>
          <?php $q = 0; $ct = 0; ?>
          @foreach ($row_Total as $total)
          <tr>
          	<td><?php echo $total->Q; ?> x </td>
            <td><img class="img-rounded" width="50" id="image-swap" src="{{asset('img/productos')}}/<?php echo $total->consola."/".$total->titulo.".jpg";?>" alt="" /></td>
            <td><?php echo str_replace('-', ' ', $total->titulo);?></td>
            
			<td><?php echo $total->costo_usd; ?></td>
            <td><?php echo ($total->costo_usd * $total->Q); ?></td>
          </tr>
        <?php $q = $q + $total->Q; $ct = $ct + ($total->costo_usd * $total->Q); ?>
        @endforeach
		<tr>
			<th><?=$q?></th>
			<th></th>
			<th></th>
			<th></th>
			<th><?=$ct?></th>
      	</tr> 
        </tbody>
        </table>
        </div>
        <?php endif; ?> 
    </div>

    <div class="col-md-2"></div>

    <div class="col-md-5">

      @if($row_saldo_prov)
      <h4>Listado Saldo Prov</h4>

      <div class="row">
          <form action="{{ url('carga_gc',$vendedor) }}" method="get" class="form-inline">
              <div class="form-group col-md-4">
                  <label for="fecha_ini2">Fecha Inicio:</label>
                  <input type="date" name="fecha_ini2" id="fecha_ini2" value="{{ $fecha_ini2}}" class="form-control input-sm">
              </div>

              <div class="form-group col-md-4">
                  <label for="fecha_fin2">Fecha Fin:</label>
                  <input type="date" name="fecha_fin2" id="fecha_fin2" value="{{ $fecha_fin2}}" class="form-control input-sm">
              </div>

              <div class="form-group">
                <label for="palabra">&nbsp;</label> <br>
                <button type="submit" class="btn btn-default btn-sm">Buscar</button>

              </div>
          </form>
      </div>

      <div class="table-responsive">
        <table border="0" align="center" cellpadding="0" cellspacing="5" class="table table-striped">
          <thead>
            <tr>
              <th>#</th>
              <th>USD</th>
              <th>Cotiz</th>
              <th>ARS</th>
              <th>Fecha</th>
              <th></th>
            </tr>
          </thead>
          <tbody>
            @foreach($row_saldo_prov as $value)
            <tr>
              <td>{{ $value->ID }}</td>
              <td>{{ $value->usd }}</td>
              <td>{{ $value->cotiz }}</td>
              <td>{{ $value->ars }}</td>
              @php
                $dia = date('d', strtotime($value->Day));
                $mes = date('n', strtotime($value->Day));
                $mes = \Helper::getMonthLetter($mes);
                $anio = date('Y', strtotime($value->Day));
                $fecha = "$dia-$mes-$anio";
                @endphp
              <td>{{ $fecha }}</td>
              <th style="vertical-align: middle;text-align: center;">
                <a href="javascript:void(0)" data-toggle="modal" data-target=".modalSaldoProv" onclick="getPageAjax('{{url("getDatosSaldoProv", $value->ID)}}','#modalSaldoProv')"><i class="fa fa-pencil"></i></a>
              </th>
            </tr>
            @endforeach
          </tbody>
        </table>
      </div>

      @endif
      
    </div>

    </div>
    
     <!--/row-->
     <!-- InstanceEndEditable -->
    </div><!--/.container-->

    <div class="modal fade modalSaldoProv" id="modalSaldoProv" tabindex="-1" role="dialog" aria-labelledby="myLargeModalLabel">
      <div class="modal-dialog modal-lg" style="top:40px;">
        <div class="modal-content">
          
          <div class="modal-body" style="text-align:center;padding:10px;">
          </div>
          
        </div>
      </div>
    </div>

@endsection

@section('scripts')

<script type="text/javascript">
  $("#multiplicando").keyup(function() {
      m1 = document.getElementById("multiplicando").value;
      m2 = document.getElementById("multiplicador").value;
      r = m1*m2;
      document.getElementById("resultado").value = r;
    });
  $("#multiplicador").keyup(function() {
      m1 = document.getElementById("multiplicando").value;
      m2 = document.getElementById("multiplicador").value;
      r = m1*m2;
      document.getElementById("resultado").value = r;
    });
  </script>

  @stop