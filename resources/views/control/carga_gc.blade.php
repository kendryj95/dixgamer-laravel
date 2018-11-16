@extends('layouts.master-layouts')

@section('title', 'Cargas de saldo para '. $vendedor . '-GC')

@section('container')

    <div class="container">
	<h1>Cargas de saldo para {{ $vendedor }}-GC</h1>
    <!-- InstanceBeginEditable name="body" -->
		<p>@if (Helper::validateAdministrator(session()->get('usuario')->Level))
			<a class="btn btn-info" href="{{ url('carga_gc', 'Francisco') }}">Ver Francisco</a>
		@endif</p>
		@if (Helper::validateAdministrator(session()->get('usuario')->Level))
		
			<form method="post" name="form1" action="{{ url('carga_gc_store') }}">

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

				<button class="btn btn-success" type="submit">Carga a {{ $vendedor }}</button>
				<input type="hidden" name="MM_insert" value="form1">
				<input type="hidden" name="usuario" value="{{ $vendedor }}">
			</form>
		@endif
    <div class="row">
    <div class="col-md-4">
	<h4>Saldo Proveedor <span class="label label-normal"><?php if ($row_SaldoP): ?><?php echo ($row_SaldoP[0]->carga_usd - $row_SaldoP[0]->carga_usd); ?><?php endif;?></span></h4>
	</div>
		
	<div class="col-md-4">
    <?php if ($row_Diario): ?>
	<h4>Listado del Día</h4>
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
          @foreach ($row_Diario as $diario)
          <tr>
          	<td><?php echo $diario->Q; ?> x </td>
            <td><img class="img-rounded" width="50" id="image-swap" src="{{asset('img/productos')}}/<?php echo $diario->consola."/".$diario->titulo.".jpg";?>" alt="" /></td>
            <td><?php echo str_replace('-', ' ', $diario->titulo);?></td>
            
			<td><?php echo $diario->costo_usd; ?></td>
            <td><?php echo ($diario->costo_usd * $diario->Q); ?></td>
          </tr>
        <?php $q = $q + $diario->Q; $ct = $ct + ($diario->costo_usd * $diario->Q); ?>
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
		<?php if ($row_Mensual): ?>
	<h4>Listado del Mes</h4>
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
          <?php $q = 0; $ct = 0;  ?>
          @foreach ($row_Mensual as $mensual)
          <tr>
          	<td><?php echo $mensual->Q; ?> x </td>
            <td><img class="img-rounded" width="50" id="image-swap" src="{{asset('img/productos')}}/<?php echo $mensual->consola."/".$mensual->titulo.".jpg";?>" alt="" /></td>
            <td><?php echo str_replace('-', ' ', $mensual->titulo);?></td>
            
			<td><?php echo $mensual->costo_usd; ?></td>
            <td><?php echo ($mensual->costo_usd * $mensual['Q']); ?></td>
          </tr>
        <?php $q = $q + $mensual['Q']; $ct = $ct + ($mensual->costo_usd * $mensual->Q); ?> 
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
	
		
	<div class="col-md-2">
    </div>
    <div class="col-md-4 pull-right">
    
    <?php if ($row_Total): ?>
	<h4>Listado Total</h4>
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
    </div>
    
     <!--/row-->
     <!-- InstanceEndEditable -->
    </div><!--/.container-->

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