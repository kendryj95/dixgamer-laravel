<?php require_once('../Connections/Conexion.php'); ?>
<?php
$MM_authorizedUsers = "Adm,Vendedor";
$MM_donotCheckaccess = "false";
require_once('_autentificacion.php');
?>
<?php 


$pedido = " and order_id!=''";

if (isset($_GET['pedido'])) {
   	$nro_pedido = (get_magic_quotes_gpc()) ? $_GET['pedido'] : addslashes($_GET['pedido']);
  	$pedido = " and order_id=" . $nro_pedido;
	
	/*** QUERY DE CLIENTES ***/
	mysql_select_db($database_Conexion, $Conexion);
	$query_rsClientes = sprintf("SELECT clientes_id, order_id_web, nombre, apellido FROM ventas LEFT JOIN clientes on ventas.clientes_id = clientes.ID where ventas.order_id_web=%s", $nro_pedido);
	$rsClientes = mysql_query($query_rsClientes, $Conexion) or die(mysql_error());
	$row_rsClientes = mysql_fetch_assoc($rsClientes);
	$totalRows_rsClientes = mysql_num_rows($rsClientes);
}

mysql_select_db($database_Conexion, $Conexion);
$query_rsAsignarVta = "SELECT
wco.order_item_id,
wco.order_id,
SUBSTRING_INDEX(SUBSTRING_INDEX(REPLACE(REPLACE(REPLACE(REPLACE(TRIM(LCASE(wco.order_item_name)), ' ', '-'), '''', ''), '’', ''), '.', ''),'-&ndash;',1),'---',1) as producto
FROM
cbgw_woocommerce_order_items as wco
LEFT JOIN cbgw_posts as p
ON wco.order_id = p.ID
WHERE p.post_status = 'wc-processing' and p.post_type='shop_order' " . $pedido . "
GROUP BY wco.order_item_id
ORDER BY order_item_id DESC";
$rsAsignarVta = mysql_query($query_rsAsignarVta, $Conexion) or die(mysql_error());
$row_rsAsignarVta = mysql_fetch_assoc($rsAsignarVta);
$totalRows_rsAsignarVta = mysql_num_rows($rsAsignarVta);

?>
<!DOCTYPE html>
<html lang="es"><!-- InstanceBegin template="/Templates/db.dwt.php" codeOutsideHTMLIsLocked="false" -->
  <head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <!-- The above 3 meta tags *must* come first in the head; any other head content must come *after* these tags -->
    <meta name="description" content="base de datos">
    <meta name="author" content="vic">
    <link rel="icon" href="favicon.ico">
	
<!-- InstanceBeginEditable name="doctitle" -->
	<script>
	  //function resizeIframe(obj) {
		//obj.style.height = obj.contentWindow.document.body.scrollHeight + 'px';
	  //}
	</script>	
	<script>
		  $( function() {
			$( "#dialog" ).dialog({
			  autoOpen: false,
			  show: {
				effect: "blind",
				duration: 1000
			  },
			  hide: {
				effect: "explode",
				duration: 1000
			  }
			});

			$( "#opener" ).on( "click", function() {
			  $( "#dialog" ).dialog( "open" );
			});
		  } );
	  </script>
	  
	  
    <title><?php $titulo = 'Pedidos Cobrados (y sin asignar aún)'; echo $titulo; ?></title>
<!-- InstanceEndEditable -->

	  
    <!-- Font Awesome style desde mi servidor -->
    <link href="css/font-awesome.min.css" rel="stylesheet">
    
    <!-- link a mi css -->
    <link href="css/bootstrap.css" rel="stylesheet">
    
    <!-- Bootstrap SITE CSS -->
    <link href="css/site.css" rel="stylesheet">

    <!-- Custom styles for this template -->
    <link href="css/offcanvas.css" rel="stylesheet">
    
	<!-- 2017-12-30 Agrego nuevo css de BootFLAT --> 
    <link href="css/bootflat.css" rel="stylesheet">
	  
	<!-- Estilo personalizado por mi -->
	<link href="css/personalizado.css" rel="stylesheet">
	 	
    <!--- BootFLAT core CSS 
    <link href="../db/css/site.min.css" rel="stylesheet">
	-->

    <!-- InstanceBeginEditable name="head" -->
    <!-- antes que termine head-->
<!-- InstanceEndEditable -->
  </head>

  <body>
  <?php include('_barra_nav.php'); ?>

    <div class="container">
	<h1><?php echo $titulo; ?></h1>
    <!-- InstanceBeginEditable name="body" -->
    <!---

	<p><a type="button" href="ventas_web_gow.php" class="btn btn-normal btn-xl"><i class="fa fa-search" aria-hidden="true"></i> Ver pedidos <b>God Of War</b> (ps3 y ps4)</a></p>

	-->
		<br />
    <?php if ($row_rsAsignarVta): ?>
	<table class="table table-striped" border="0" cellpadding="0" cellspacing="5">
      <tr>
      <th width="50">ID</th>
        <th width="50">Cover</th>
        <th title="Titulo">Titulo</th>
        <th title="Cliente">Cliente</th>
      </tr>
      <?php do {
	    
	    $pedido = $row_rsAsignarVta['order_id'];
		$oii = $row_rsAsignarVta['order_item_id'];
	
		$apellido1 = sprintf("SELECT meta_value as rdo FROM cbgw_postmeta WHERE meta_key = '_billing_last_name' and post_id= %d", $pedido);
		mysql_select_db($database_Conexion, $Conexion);
  		$apellido = mysql_query($apellido1, $Conexion) or die(mysql_error());
	
		$nombre = sprintf("SELECT meta_value as rdo FROM cbgw_postmeta WHERE meta_key = '_billing_first_name' and post_id = %d", $pedido );
	    $email = sprintf("SELECT meta_value as rdo FROM cbgw_postmeta WHERE meta_key = '_billing_email' and post_id = %d", $pedido );
		$user_id_ml = sprintf("SELECT meta_value as rdo FROM cbgw_postmeta WHERE meta_key = 'user_id_ml' and post_id = %d", $pedido );
		$order_id_ml = sprintf("SELECT meta_value as rdo FROM cbgw_postmeta WHERE meta_key = 'order_id_ml' and post_id = %d", $pedido );
		$_payment_method_title = sprintf("SELECT meta_value as rdo FROM cbgw_postmeta WHERE meta_key='_payment_method_title' and post_id=%d", $pedido );
		$_payment_method = sprintf("SELECT meta_value as rdo FROM cbgw_postmeta WHERE meta_key='_payment_method' and post_id=%d", $pedido );
	
		$_qty = sprintf("SELECT meta_value as rdo FROM cbgw_woocommerce_order_itemmeta WHERE meta_key='_qty' and order_item_id=%d", $oii );
		$pa_slot = sprintf("SELECT meta_value as rdo FROM cbgw_woocommerce_order_itemmeta WHERE meta_key='pa_slot' and order_item_id=%d", $oii );
		$_product_id = sprintf("SELECT meta_value as rdo FROM cbgw_woocommerce_order_itemmeta WHERE meta_key='_product_id' and order_item_id=%d", $oii );
		$_variation_id = sprintf("SELECT meta_value as rdo FROM cbgw_woocommerce_order_itemmeta WHERE meta_key='_variation_id' and order_item_id=%d", $oii );
	
		?>
		
      <tr height="90">
      <td id="<?php echo $oii;?>"><span class="label label-default" style="opacity:0.7;">pedido #<?php echo $pedido ?></span><a target="_blank" href="https://dixgamer.com/wp-admin/post.php?post=<?php echo $pedido ?>&action=edit" class="text-muted btn-xs" title="ver pedido en la adm del sitio"><i class="fa fa-external-link" aria-hidden="true"></i> </a><br /><br /><span class="label label-normal" style="font-weight:400; opacity:0.5;">order_item_id #<?php echo $oii; ?></span></td>
        <td><img class="img-rounded" width="50" id="image-swap" src="/img/productos/<?php echo $row_rsAsignarVta['consola']."/".$row_rsAsignarVta['producto'].".jpg";?>"alt="" /></td>
        <td title="<?php echo str_replace('-', ' ', $row_rsAsignarVta['producto']);?> (<?php echo $row_rsAsignarVta['consola']; ?>)"><?php echo str_replace('-', ' ', $row_rsAsignarVta['producto']);?> (<?php echo $row_rsAsignarVta['consola']; ?>) 
        
        <?php if ($email): ?>
			<a title="Asignar" class="btn btn-info btn-xs" type="button" data-toggle="modal" data-target=".bs-example-modal-lg" onClick='document.getElementById("ifr").src="ventas_insertar_web.php?order_item_id=<?php echo $oii; ?>&titulo=<?php echo $row_rsAsignarVta['producto']; ?>&consola=<?php echo $row_rsAsignarVta['consola'];?>&slot=<?php echo ucwords($row_rsAsignarVta['slot']);?>";'><i class="fa fa-plus" aria-hidden="true"></i> asignar</a>
			<a title="Asignar" class="btn btn-warning btn-xs" type="button" href="ventas_insertar_web.php?order_item_id=<?php echo $oii; ?>&titulo=<?php echo $row_rsAsignarVta['producto']; ?>&consola=<?php echo $row_rsAsignarVta['consola'];?>&slot=<?php echo ucwords($row_rsAsignarVta['slot']);?>"><i class="fa fa-plus" aria-hidden="true"></i> asignar</a>
        <?php endif; ?>
        <?php if ($row_rsAsignarVta['slot'] == 'secundario'): ?> <span class="label label-danger" style="opacity:0.7"><?php echo '2°'; ?></span><?php endif;?>
		<?php if (strpos($row_rsAsignarVta['cliente_auto'], 'si') !== false):?>
            <a type="button" class="text-muted btn-xs text-danger" title="tiene historial favorable"><i class="fa fa-star" aria-hidden="true"></i></a>
		<?php endif;?>
		<br /><br />
            <?php 
			if (strpos($_payment_method_title, 'lataforma') !== false): $color = 'primary'; $text = 'MP';
			elseif (strpos($_payment_method_title, 'arjeta') !== false): $color = 'primary'; $text = 'MP';
			elseif (strpos($_payment_method_title, 'ransferencia') !== false): $color = 'default';  $text = 'Bco';
			elseif (strpos($_payment_method_title, 'icket') !== false): $color = 'success';  $text = 'Cash';
			elseif (strpos($_payment_method, '_card') !== false): $color = 'primary'; $text = 'MP';
			elseif (strpos($_payment_method, '_card') == false): $color = 'success';  $text = 'Cash';
			endif;?>
		<span class="label label-<?php echo $color;?>" style="font-weight:400; opacity:0.7;"><?php echo $text;?></span>
        </td>
        
        <td>
        
		<?php if ($email): ?>
        <?php echo $email; ?>
			<?php if (strpos($row_rsAsignarVta['cliente_auto'], 're') !== false):?>
            <a type="button" target="_blank" href="clientes_detalles.php?id=<?php echo $row_rsAsignarVta['cliente_ID']; ?>" class="btn btn-danger btn-xs"><i class="fa fa-user" aria-hidden="true"></i> Revendedor</a>
            <?php else:?>
            <a type="button" target="_blank" href="clientes_detalles.php?id=<?php echo $row_rsAsignarVta['cliente_ID']; ?>" class="btn btn-default btn-xs"><i class="fa fa-user" aria-hidden="true"></i> Cte</a>
            <?php endif;?>
        <?php else:
        	if (strpos($email, 'mercadolibre.com') !== false):?>
				<a type="button" href="clientes_insertar_web_email.php?order_id=<?php echo $pedido ?>" class="btn btn-normal btn-xs" title="corregir email de ML"><i class="fa fa-pencil" aria-hidden="true"></i> Modificar email de ML</a>
            <?php else:?>
            <?php echo $email; ?>
        	<a type="button" href="clientes_insertar_web.php?order_item_id=<?php echo $oii; ?>" class="btn btn-info btn-xs" title="agregar cliente a base de datos"><i class="fa fa-plus" aria-hidden="true"></i> cliente</a>
        	<?php endif;?>
        <?php endif;?>
        <br /><br />
		<?php if (($user_id_ml) && ($user_id_ml != "")):?>
        		<a target="_blank" href="https://perfil.mercadolibre.com.ar/profile/showProfile?id=<?php echo $user_id_ml;?>&role=buyer" class="btn btn-primary btn-xs" type="submit" style="font-weight:400; opacity:0.6;"> <i class="fa fa-user" aria-hidden="true"></i></a> <?php echo $nombre; ?> <?php echo $apellido; ?> <?php $apellido['rdo']; ?>
        		<a target="_blank" href="https://myaccount.mercadolibre.com.ar/messaging/orders/<?php echo $order_id_ml;?>" class="btn btn-warning btn-xs" type="submit" style="font-weight:400; opacity:0.6;"> <i class="fa fa-comments" aria-hidden="true"></i></a>
            	<a target="_blank" href="https://myaccount.mercadolibre.com.ar/sales/vop?orderId=<?php echo $order_id_ml;?>&role=buyer" class="btn btn-success btn-xs" type="submit"  style="font-weight:400; opacity:0.6;"> <i class="fa fa-shopping-bag" aria-hidden="true"></i></a>
        <?php else:?>
        	<?php echo $nombre; ?> <?php echo $apellido; ?> <?php $apellido['rdo']; ?>
        <?php endif;?>
        </td>
        
      </tr>   
      <?php } while ($row_rsAsignarVta = mysql_fetch_assoc($rsAsignarVta)); ?>     
      
    </table>
		<div class="container">
			<div class="row">
			<!-- Large modal -->
			<div id="#modal" class="modal fade bs-example-modal-lg" tabindex="-1" role="dialog" aria-labelledby="myLargeModalLabel">
			  <div class="modal-dialog modal-lg" style="top:40px;">
				<div class="modal-content">
				<div class="modal-body" style="text-align:center;">
				  <iframe id="ifr" src="" onload="resizeIframe(this)" style="min-height: 550px; width:900px;border:0px;" ></iframe>
				  </div>
				</div>
			  </div>
			</div>
			</div>  
		</div>
	<?php endif; ?>
	<?php if ($row_rsClientes): ?>
	<?php do { ?>
    El pedido <strong><?php echo $row_rsClientes['order_id_web']; ?></strong> ya fue asignado al cliente <a href="clientes_detalles.php?id=<?php echo $row_rsClientes['clientes_id'] ;?>"><strong><?php echo $row_rsClientes['nombre'] . ' ' . $row_rsClientes['apellido'] ;?></strong></a>
	<?php } while ($row_rsClientes = mysql_fetch_assoc($rsClientes)); ?>   
	<?php endif; ?>
    
     <!--/row-->
     <!-- InstanceEndEditable -->
    </div><!--/.container-->
	<!-- Bootstrap core JavaScript
    ================================================== -->
	<!-- Placed at the end of the document so the pages load faster -->
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/1.12.4/jquery.min.js"></script>
	<script src="assets/js/docs.min.js"></script>
    <script src="js/bootstrap.min.js"></script>
    <script src="https://cdn.jsdelivr.net/clipboard.js/1.5.12/clipboard.min.js"></script>
	<script>new Clipboard('.btn-copiador');</script>
    <!-- Activar popover -->
    <script>
	$(document).ready(function(){
		$('[data-toggle="popover"]').popover();
	});
	</script>
    <!-- InstanceBeginEditable name="script-extras" -->
    <!-- extras de script y demás yerbas -->
<!-- InstanceEndEditable -->
  </body>
  
<!-- InstanceEnd --></html>
<?php
mysql_free_result($rsUsuarios);

mysql_free_result($rsAsignarVta);

mysql_free_result($rsClientes);
?>
