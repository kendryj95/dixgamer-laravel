<?php require_once('../Connections/Conexion.php'); ?>
<?php
$MM_authorizedUsers = "Adm,Vendedor";
$MM_donotCheckaccess = "false";
require_once('_autentificacion.php');
?>
<?php 

/*** SUPER QUERY DE STOCK ***/
$maxRows_rsAsignarVta = 999;
$pageNum_rsAsignarVta = 0;
if (isset($_GET['pageNum_rsAsignarVta'])) {
  $pageNum_rsAsignarVta = $_GET['pageNum_rsAsignarVta'];
}
$startRow_rsAsignarVta = $pageNum_rsAsignarVta * $maxRows_rsAsignarVta;

/*** SUPER QUERY DE CLIENTES ***/
$maxRows_rsClientes = 999;
$pageNum_rsClientes = 0;
if (isset($_GET['pageNum_rsAsignarVta'])) {
  $pageNum_rsClientes = $_GET['pageNum_rsAsignarVta'];
}
$startRow_rsClientes = $pageNum_rsClientes * $maxRows_rsClientes;

$pedido = " and post_id!=''";

if (isset($_GET['pedido'])) {
  $nro_pedido = (get_magic_quotes_gpc()) ? $_GET['pedido'] : addslashes($_GET['pedido']);
  $pedido = " and post_id=" . $nro_pedido;
	
	mysql_select_db($database_Conexion, $Conexion);
	$query_rsClientes = sprintf("SELECT clientes_id, order_id_web, nombre, apellido FROM ventas LEFT JOIN clientes on ventas.clientes_id = clientes.ID where ventas.order_id_web=%s", $nro_pedido);
	$query_limit_rsClientes = sprintf("%s LIMIT %d, %d", $query_rsClientes, $startRow_rsClientes, $maxRows_rsClientes);
	$rsClientes = mysql_query($query_limit_rsClientes, $Conexion) or die(mysql_error());
	$row_rsClientes = mysql_fetch_assoc($rsClientes);
}


mysql_select_db($database_Conexion, $Conexion);
$query_rsAsignarVta = "
SELECT pedido.*, clientes.ID as cliente_ID, clientes.email as cliente_email, clientes.auto as cliente_auto
	FROM
	(SELECT
	conjunto2.*,
	ventas.ID as Vta_ID,
	ventas.order_item_id as Vta_oii
		FROM
		ventas
		RIGHT JOIN
		(SELECT 
		conjunto.*,
		max( CASE WHEN pm2.meta_key = 'consola' and conjunto._product_id = pm2.post_id THEN pm2.meta_value END ) as consola
			FROM
			cbgw_postmeta as pm2
			inner JOIN
				(SELECT order_item_id, order_id, producto, post_id, apellido, nombre, email, user_id_ml, order_id_ml, _payment_method_title, _payment_method, _qty, _product_id, _variation_id, post_parent, q_variation, slot as slot_original, CASE WHEN slot IS NOT NULL THEN slot Else CASE WHEN q_variation = 2 then 'primario' WHEN q_variation = 1 then 'secundario' ELSE slot END END slot FROM 
							(select
							wco.order_item_id,
							wco.order_id,
							SUBSTRING_INDEX(SUBSTRING_INDEX(REPLACE(REPLACE(REPLACE(REPLACE(TRIM(LCASE(wco.order_item_name)), ' ', '-'), '''', ''), '’', ''), '.', ''),'-&ndash;',1),'---',1) as producto,
							p.ID as post_id,
							p.post_status as estado,
							max( CASE WHEN pm.meta_key = '_billing_last_name' and wco.order_id = pm.post_id THEN pm.meta_value END ) as apellido,
							max( CASE WHEN pm.meta_key = '_billing_first_name' and wco.order_id = pm.post_id THEN pm.meta_value END ) as nombre,
								max( CASE WHEN pm.meta_key = '_billing_email' and wco.order_id = pm.post_id THEN pm.meta_value END ) as email,
								max( CASE WHEN pm.meta_key = 'user_id_ml' and wco.order_id = pm.post_id THEN pm.meta_value END ) as user_id_ml,
								max( CASE WHEN pm.meta_key = 'order_id_ml' and wco.order_id = pm.post_id THEN pm.meta_value END ) as order_id_ml,
								max( CASE WHEN pm.meta_key = '_payment_method_title' and wco.order_id = pm.post_id THEN pm.meta_value END ) as _payment_method_title, 
								max( CASE WHEN pm.meta_key = '_payment_method' and wco.order_id = pm.post_id THEN pm.meta_value END ) as _payment_method,
								max( CASE WHEN wcom.meta_key = '_qty' and wco.order_item_id = wcom.order_item_id THEN wcom.meta_value END ) as _qty,
								max( CASE WHEN wcom.meta_key = 'pa_slot' and wco.order_item_id = wcom.order_item_id THEN wcom.meta_value END ) as slot,
								max( CASE WHEN wcom.meta_key = '_product_id' and wco.order_item_id = wcom.order_item_id THEN wcom.meta_value END ) as _product_id,
								max( CASE WHEN wcom.meta_key = '_variation_id' and wco.order_item_id = wcom.order_item_id THEN wcom.meta_value END ) as _variation_id
							from
							cbgw_woocommerce_order_items as wco
							LEFT JOIN cbgw_posts as p ON wco.order_id = p.ID
							LEFT JOIN cbgw_postmeta as pm ON wco.order_id = pm.post_id
							LEFT JOIN cbgw_woocommerce_order_itemmeta as wcom ON wco.order_item_id = wcom.order_item_id
							where 
							p.post_status = 'wc-processing' and wco.order_item_name NOT LIKE 'fifa 19%' and wco.order_item_name NOT LIKE 'pes 2019%' " . $pedido . " group by 
							wco.order_item_id
							ORDER BY order_item_id DESC) as primer
				LEFT JOIN
			(SELECT post_parent, count(*) as q_variation FROM `cbgw_posts` where post_type='product_variation' group by post_parent ASC ORDER BY `cbgw_posts`.`post_parent` DESC) as variaciones
			ON primer._product_id=variaciones.post_parent ) as conjunto
			ON conjunto._product_id = pm2.post_id
			GROUP by conjunto.order_item_id) as conjunto2
		ON conjunto2.order_item_id = ventas.order_item_id
	WHERE
	ventas.order_item_id IS NUll
	GROUP by conjunto2.order_item_id) as pedido

	LEFT JOIN
	clientes
	ON pedido.email = clientes.email
GROUP BY pedido.order_item_id
ORDER BY order_item_id DESC";
$query_limit_rsAsignarVta = sprintf("%s LIMIT %d, %d", $query_rsAsignarVta, $startRow_rsAsignarVta, $maxRows_rsAsignarVta);
$rsAsignarVta = mysql_query($query_limit_rsAsignarVta, $Conexion) or die(mysql_error());
$row_rsAsignarVta = mysql_fetch_assoc($rsAsignarVta);
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
      <?php do { ?>
      <tr height="90">
      <td id="<?php echo $row_rsAsignarVta['order_item_id'];?>"><span class="label label-default" style="opacity:0.7;">pedido #<?php echo $row_rsAsignarVta['order_id']; ?></span><a target="_blank" href="https://dixgamer.com/wp-admin/post.php?post=<?php echo $row_rsAsignarVta['order_id']; ?>&action=edit" class="text-muted btn-xs" title="ver pedido en la adm del sitio"><i class="fa fa-external-link" aria-hidden="true"></i> </a><br /><br /><span class="label label-normal" style="font-weight:400; opacity:0.5;">order_item_id #<?php echo $row_rsAsignarVta['order_item_id']; ?></span></td>
        <td><img class="img-rounded" width="50" id="image-swap" src="/img/productos/<?php echo $row_rsAsignarVta['consola']."/".$row_rsAsignarVta['producto'].".jpg";?>"alt="" /></td>
        <td title="<?php echo str_replace('-', ' ', $row_rsAsignarVta['producto']);?> (<?php echo $row_rsAsignarVta['consola']; ?>)"><?php echo str_replace('-', ' ', $row_rsAsignarVta['producto']);?> (<?php echo $row_rsAsignarVta['consola']; ?>) 
        
        <?php if ($row_rsAsignarVta['cliente_email']): ?>
			<a title="Asignar" class="btn btn-info btn-xs" type="button" data-toggle="modal" data-target=".bs-example-modal-lg" onClick='document.getElementById("ifr").src="ventas_insertar_web.php?order_item_id=<?php echo $row_rsAsignarVta['order_item_id']; ?>&titulo=<?php echo $row_rsAsignarVta['producto']; ?>&consola=<?php echo $row_rsAsignarVta['consola'];?>&slot=<?php echo ucwords($row_rsAsignarVta['slot']);?>";'><i class="fa fa-plus" aria-hidden="true"></i> asignar</a>
			<a title="Asignar" class="btn btn-warning btn-xs" type="button" href="ventas_insertar_web.php?order_item_id=<?php echo $row_rsAsignarVta['order_item_id']; ?>&titulo=<?php echo $row_rsAsignarVta['producto']; ?>&consola=<?php echo $row_rsAsignarVta['consola'];?>&slot=<?php echo ucwords($row_rsAsignarVta['slot']);?>"><i class="fa fa-plus" aria-hidden="true"></i> asignar</a>
        <?php endif; ?>
        <?php if ($row_rsAsignarVta['slot'] == 'secundario'): ?> <span class="label label-danger" style="opacity:0.7"><?php echo '2°'; ?></span><?php endif;?>
		<?php if (strpos($row_rsAsignarVta['cliente_auto'], 'si') !== false):?>
            <a type="button" class="text-muted btn-xs text-danger" title="tiene historial favorable"><i class="fa fa-star" aria-hidden="true"></i></a>
		<?php endif;?>
		<br /><br />
            <?php 
			if (strpos($row_rsAsignarVta['_payment_method_title'], 'Plataforma') !== false): $color = 'primary'; $text = 'MP';
			elseif (strpos($row_rsAsignarVta['_payment_method_title'], 'Tarjeta') !== false): $color = 'primary'; $text = 'MP';
			elseif (strpos($row_rsAsignarVta['_payment_method_title'], 'Transferencia') !== false): $color = 'default';  $text = 'Bco';
			elseif (strpos($row_rsAsignarVta['_payment_method_title'], 'Ticket') !== false): $color = 'success';  $text = 'Cash';
			elseif (strpos($row_rsAsignarVta['_payment_method'], '_card') !== false): $color = 'primary'; $text = 'MP';
			elseif (strpos($row_rsAsignarVta['_payment_method'], '_card') == false): $color = 'success';  $text = 'Cash';
			endif;?>
		<span class="label label-<?php echo $color;?>" style="font-weight:400; opacity:0.7;"><?php echo $text;?></span>
        </td>
        
        <td>
        
		<?php if ($row_rsAsignarVta['cliente_email']): ?>
        <?php echo $row_rsAsignarVta[email]; ?>
			<?php if (strpos($row_rsAsignarVta['cliente_auto'], 're') !== false):?>
            <a type="button" target="_blank" href="clientes_detalles.php?id=<?php echo $row_rsAsignarVta['cliente_ID']; ?>" class="btn btn-danger btn-xs"><i class="fa fa-user" aria-hidden="true"></i> Revendedor</a>
            <?php else:?>
            <a type="button" target="_blank" href="clientes_detalles.php?id=<?php echo $row_rsAsignarVta['cliente_ID']; ?>" class="btn btn-default btn-xs"><i class="fa fa-user" aria-hidden="true"></i> Cte</a>
            <?php endif;?>
        <?php else:
        	if (strpos($row_rsAsignarVta['email'], 'mercadolibre.com') !== false):?>
				<a type="button" href="clientes_insertar_web_email.php?order_id=<?php echo $row_rsAsignarVta['order_id']; ?>" class="btn btn-normal btn-xs" title="corregir email de ML"><i class="fa fa-pencil" aria-hidden="true"></i> Modificar email de ML</a>
            <?php else:?>
            <?php echo $row_rsAsignarVta[email]; ?>
        	<a type="button" href="clientes_insertar_web.php?order_item_id=<?php echo $row_rsAsignarVta['order_item_id']; ?>" class="btn btn-info btn-xs" title="agregar cliente a base de datos"><i class="fa fa-plus" aria-hidden="true"></i> cliente</a>
        	<?php endif;?>
        <?php endif;?>
        <br /><br />
		<?php if (($row_rsAsignarVta['user_id_ml']) && ($row_rsAsignarVta['user_id_ml'] != "")):?>
        		<a target="_blank" href="https://perfil.mercadolibre.com.ar/profile/showProfile?id=<?php echo $row_rsAsignarVta['user_id_ml'];?>&role=buyer" class="btn btn-primary btn-xs" type="submit" style="font-weight:400; opacity:0.6;"> <i class="fa fa-user" aria-hidden="true"></i></a> <?php echo $row_rsAsignarVta['nombre']; ?> <?php echo $row_rsAsignarVta['apellido']; ?>
        		<a target="_blank" href="https://myaccount.mercadolibre.com.ar/messaging/orders/<?php echo $row_rsAsignarVta['order_id_ml'];?>" class="btn btn-warning btn-xs" type="submit" style="font-weight:400; opacity:0.6;"> <i class="fa fa-comments" aria-hidden="true"></i></a>
            	<a target="_blank" href="https://myaccount.mercadolibre.com.ar/sales/vop?orderId=<?php echo $row_rsAsignarVta['order_id_ml'];?>&role=buyer" class="btn btn-success btn-xs" type="submit"  style="font-weight:400; opacity:0.6;"> <i class="fa fa-shopping-bag" aria-hidden="true"></i></a>
        <?php else:?>
        	<?php echo $row_rsAsignarVta['nombre']; ?> <?php echo $row_rsAsignarVta['apellido']; ?>
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
