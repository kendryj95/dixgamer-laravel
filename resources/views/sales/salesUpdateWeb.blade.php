@extends('layouts.master-layouts')

@section('title', 'Sin stock')

@section('container')

<div class="container">
	
	<div>
		<h4 class="text-danger text-center">sin stock</h4>
		<table class="table" border="0" cellpadding="0" cellspacing="5" style="font-size:1em;">
		  </tr>
		  <tr height="90">
		  <td id="{{ $venta->order_item_id }}"><span class="label label-default" style="opacity:0.7;">pedido #{{ $venta->order_id }}</span><a target="_blank" href="https://dixgamer.com/wp-admin/post.php?post={{ $venta->order_id }}&action=edit" class="text-muted btn-xs" title="ver pedido en la adm del sitio"><i class="fa fa-external-link" aria-hidden="true"></i> </a><br /><br /><span class="label label-normal" style="font-weight:400; opacity:0.5;">order_item_id #{{ $venta->order_item_id }}</span></td>
			<td><img class="img-rounded" width="50" id="image-swap" src="{{asset('img/productos')}}/<?php echo $consola."/".$titulo.".jpg";?>"alt="" /></td>
			<td title="<?php echo str_replace('-', ' ', $titulo);?> (<?php echo $consola; ?>)"><?php echo str_replace('-', ' ', $titulo);?> (<?php echo $consola; ?>) 

			
			<?php if ($slot == 'Secundario'): ?> <span class="label label-danger" style="opacity:0.7"><?php echo '2°'; ?></span><?php endif;?>
			<?php if (strpos($clientes->auto, 'si') !== false):?>
				<a type="button" class="text-muted btn-xs text-danger" title="tiene historial favorable"><i class="fa fa-star" aria-hidden="true"></i></a>
			<?php endif;?>
			</td>

			<td>
			<?php echo $clientes->email; ?>
			<?php if (strpos($clientes->auto, 're') !== false):?>
			<a type="button" target="_blank" href="{{ url('clientes', $clientes->ID) }}" class="btn btn-danger btn-xs"><i class="fa fa-user" aria-hidden="true"></i> Revendedor</a>
			<?php else:?>
			<a type="button" target="_blank" href="{{ url('clientes', $clientes->ID) }}" class="btn btn-default btn-xs"><i class="fa fa-user" aria-hidden="true"></i> Cte</a>
			<?php endif;?>
			<br /><br />
			<?php echo $clientes->nombre; ?> <?php echo $clientes->apellido; ?>
			</td>
		  </tr>      
		</table>

		<?php if($linkPS && ($linkPS !== "")):?>
					
			<?php $array = (explode(',', $linkPS, 10)); 

				foreach ($array as $valor) { echo "<a class='btn btn-default btn-sm' title='Ver en la Tienda de PS' target='_blank' href='$valor'><img src='".asset('img/gral/ps-store.png')."' width='18' /> Link PS </a> "; };
			?> 
		<?php endif; ?>
			
			<a type="button" href="{{ url('customer_ventas_modificar_producto_store',[$consola, $titulo, $slot, $venta->ID]) }}" class="btn btn-info pull-right" style="margin-bottom: 20px;"><i class="fa fa-refresh fa-fw" aria-hidden="true"></i> Re Intentar Asignación</a>
			
			<?php $insertGoTo = url('customer_ventas_modificar_producto', $venta->ID); ?>
			<a title="Asignar" class="btn btn-default pull-right" type="button" href="javascript:;" data-toggle="modal" data-target=".modalVentas" onclick='getPageAjax("{{ url('customer_ventas_modificar_producto') }}", "#modalVentas", {{ $venta->ID }})'><i class="fa fa-gamepad" aria-hidden="true"></i> Asignar Otro Producto</a>

			<br><br>

			<span class="pull-right">No olvides buscar stock disponible en la @if ($consola == "ps4") <a href="{{url('sales/recupero')}}?column=titulo&word={{$titulo}}&enviar=Buscar">lista de recuperos</a> @elseif($consola == "ps3") <a href="{{url('home')}}#reset">lista de reseteos</a> @endif</span>
			
			
		</div>

		<div class="modal fade modalVentas" id="modalVentas" tabindex="-1" role="dialog" aria-labelledby="myLargeModalLabel">
			<div class="modal-dialog modal-lg" style="top:40px;">
			  <div class="modal-content">
				
				<div class="modal-body" style="text-align:center;padding:10px;">
				</div>
				
			  </div>
			</div>
		  </div>
  
		  <div class="modal fade modalConfirm" id="modalConfirm" tabindex="-1" role="dialog" aria-labelledby="myLargeModalLabel">
			<div class="modal-dialog modal-lg" style="top:40px;">
			  <div class="modal-content">
				
				<div class="modal-body" style="text-align:center;padding:10px;">
				</div>
				
			  </div>
			</div>
		  </div>



</div>

@endsection

@section('scripts')


<script>
	  function resizeIframe(obj) {
		obj.style.height = obj.contentWindow.document.body.scrollHeight + 'px';
	  }
</script>


@stop