@extends('layouts.master-layouts')

@section('title', 'Sin stock')

@section('container')

<div class="container">
	
	<?php if(true):?>
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
				<?php if ($venta->email): ?>
				<?php echo $venta->email; ?>
					<?php if (strpos($clientes->auto, 're') !== false):?>
					<a type="button" target="_blank" href="{{ url('clientes', $clientes->ID) }}" class="btn btn-danger btn-xs"><i class="fa fa-user" aria-hidden="true"></i> Revendedor</a>
					<?php else:?>
					<a type="button" target="_blank" href="{{ url('clientes', $clientes->ID) }}" class="btn btn-default btn-xs"><i class="fa fa-user" aria-hidden="true"></i> Cte</a>
					<?php endif;?>
				<?php else:
					if (strpos($venta->email, 'mercadolibre.com') !== false):?>
						<a type="button" href="clientes_insertar_web_email.php?order_id=<?php echo $venta->order_id; ?>" class="btn btn-normal btn-xs" title="corregir email de ML"><i class="fa fa-pencil" aria-hidden="true"></i> Modificar email de ML</a>
					<?php else:?>
					<?php echo $venta->email; ?>
					<a type="button" href="clientes_insertar_web.php?order_item_id=<?php echo $venta->order_item_id; ?>" class="btn btn-info btn-xs" title="agregar cliente a base de datos"><i class="fa fa-plus" aria-hidden="true"></i> cliente</a>
					<?php endif;?>
				<?php endif;?>
				<br /><br />
				<?php if (($venta->user_id_ml) && ($venta->user_id_ml != "")):?>
						<a target="_blank" href="https://perfil.mercadolibre.com.ar/profile/showProfile?id=<?php echo $venta->user_id_ml;?>&role=buyer" class="btn btn-primary btn-xs" type="submit" style="font-weight:400; opacity:0.6;"> <i class="fa fa-user" aria-hidden="true"></i></a> <?php echo $clientes->nombre; ?> <?php echo $clientes->apellido; ?>
						<a target="_blank" href="https://myaccount.mercadolibre.com.ar/messaging/orders/<?php echo $venta->order_id_ml;?>" class="btn btn-warning btn-xs" type="submit" style="font-weight:400; opacity:0.6;"> <i class="fa fa-comments" aria-hidden="true"></i></a>
						<a target="_blank" href="https://myaccount.mercadolibre.com.ar/sales/vop?orderId=<?php echo $venta->order_id_ml;?>&role=buyer" class="btn btn-success btn-xs" type="submit"  style="font-weight:400; opacity:0.6;"> <i class="fa fa-shopping-bag" aria-hidden="true"></i></a>
				<?php else:?>
					<?php echo $clientes->nombre; ?> <?php echo $clientes->apellido; ?>
				<?php endif;?>
				</td>
			  </tr>      
			</table>
				<?php if($linkPS && ($linkPS !== "")):?>
					
					<?php $array = (explode(',', $linkPS, 10)); 

						foreach ($array as $valor) { echo "<a class='btn btn-default btn-sm' title='Ver en la Tienda de PS' target='_blank' href='$valor'><img src='".asset('img/gral/ps-store.png')."' width='18' /> Link PS </a> "; };
					?> 
				<?php endif; ?>
				
				<a type="button" href="{{ url('salesInsertWeb',[$venta->order_item_id, $titulo, $consola, $slot]) }}?c_id={{ $clientes->ID }}" class="btn btn-info pull-right" style="margin-bottom: 20px;"><i class="fa fa-refresh fa-fw" aria-hidden="true"></i> Re Intentar Asignación</a>
				
				<?php $insertGoTo = url('asignar_producto') . "?order_item_id=$venta->order_item_id";?>
				<a title="Asignar" class="btn btn-default pull-right" type="button" data-target=".new-example-modal-lg" onClick='document.getElementById("ifr2").src="<?php echo $insertGoTo; ?>";'><i class="fa fa-gamepad" aria-hidden="true"></i> Asignar Otro Producto</a>

				<br><br>

				@if($consola == "ps4" || $consola == "ps3")
				<span class="pull-right">No olvides buscar stock disponible en la @if ($consola == "ps4") <a href="{{url('sales/recupero')}}?column=titulo&word={{$titulo}}&enviar=Buscar">lista de recuperos</a> @elseif($consola == "ps3") <a href="{{url('home')}}#reset">lista de reseteos</a> @endif</span>

				<br>

				@endif

				<div class="row">
						<!-- Large modal -->
						<div class="new-example-modal-lg" tabindex="-1" aria-labelledby="myLargeModalLabel">
							  <iframe id="ifr2" src="" onload="resizeIframe(this)" style="width:100%;border:0px;" ></iframe>
						</div>
				</div> 
				
			</div>
	<?php endif; ?>	



</div>

@endsection

@section('scripts')


<script>
	  function resizeIframe(obj) {
		obj.style.height = obj.contentWindow.document.body.scrollHeight + 'px';
	  }
</script>


@stop