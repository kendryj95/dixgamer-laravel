<?php require_once('../Connections/Conexion.php'); ?>
<?php
$MM_authorizedUsers = "Adm,Vendedor";
$MM_donotCheckaccess = "false";
require_once('_autentificacion.php');
?>
<?php
$vendedor = $_SESSION['MM_Username'];

//obtengo el OII
$colname_rsOII = "-1";
if (isset($_GET['order_item_id'])) {
  $colname_rsOII = (get_magic_quotes_gpc()) ? $_GET['order_item_id'] : addslashes($_GET['order_item_id']);}
else {$colname_rsOII = GetSQLValueString($_POST['order_item_id'], "int");}

//controlo que ya no exista una venta para ese OII
mysql_select_db($database_Conexion, $Conexion);
$query_rsExiste_OII = sprintf("SELECT order_item_id, clientes_id, usuario FROM ventas where order_item_id='%s'",$colname_rsOII); 
$rsExiste_OII = mysql_query($query_rsExiste_OII, $Conexion) or die(mysql_error());
$row_rsExiste_OII = mysql_fetch_assoc($rsExiste_OII);
$totalRows_rsExiste_OII = mysql_num_rows($rsExiste_OII);

$asignador = $row_rsExiste_OII['usuario'];
$linkCte = $row_rsExiste_OII['clientes_id'];

//Si existe venta aviso al gestor que ya se asignó antes y le muestro el link para verlo
if($totalRows_rsExiste_OII > 0){exit("Este pedido ya fue asignado por $asignador <br><a href='clientes_detalles.php?id=$linkCte' target='_blank'>Ver Venta</a>");}

//si no existe venta para ese OII avanzo en la asignación
$colname_rsCON = "-1";
if (isset($_GET['consola'])) {
  $colname_rsCON = (get_magic_quotes_gpc()) ? $_GET['consola'] : addslashes($_GET['consola']);}

$colname_rsTIT = "-1";
if (isset($_GET['titulo'])) {
  $colname_rsTIT = (get_magic_quotes_gpc()) ? $_GET['titulo'] : addslashes($_GET['titulo']);}

$colname_rsSlot = "-1";
if (isset($_GET['slot'])) {
  $colname_rsSlot = (get_magic_quotes_gpc()) ? $_GET['slot'] : addslashes($_GET['slot']);}

//cargo el stock disponible en este mismo segundo y busco el producto que quiero asignar
require_once('_stock_disponible.php');

mysql_select_db($database_Conexion, $Conexion);
$query_rsClient = sprintf("select
	wco.order_item_id,
	wco.order_id,
	p.ID as post_id,
	p.post_status as estado,
	max( CASE WHEN pm.meta_key = '_billing_email' and wco.order_id = pm.post_id THEN pm.meta_value END ) as email,
	max( CASE WHEN pm.meta_key = 'user_id_ml' and wco.order_id = pm.post_id THEN pm.meta_value END ) as user_id_ml,
	max( CASE WHEN pm.meta_key = 'order_id_ml' and wco.order_id = pm.post_id THEN pm.meta_value END ) as order_id_ml,
	max( CASE WHEN pm.meta_key = '_payment_method_title' and wco.order_id = pm.post_id THEN pm.meta_value END ) as medio_pago,
	max( CASE WHEN pm.meta_key = '_payment_method' and wco.order_id = pm.post_id THEN pm.meta_value END ) as medio_pago_2,
	max( CASE WHEN pm.meta_key = '_Mercado_Pago_Payment_IDs' and wco.order_id = pm.post_id THEN pm.meta_value END ) as ref_cobro,
	SUBSTRING_INDEX(SUBSTRING_INDEX(max( CASE WHEN pm.meta_key = '_transaction_details_ticket' and wco.order_id = pm.post_id THEN pm.meta_value END ), 'payment_id=', -1), '&', 1) as ref_cobro_2,
	max( CASE WHEN pm.meta_key = '_transaction_id' and wco.order_id = pm.post_id THEN pm.meta_value END ) as ref_cobro_3,
	max( CASE WHEN wcom.meta_key = '_qty' and wco.order_item_id = wcom.order_item_id THEN wcom.meta_value END ) as _qty,
	max( CASE WHEN wcom.meta_key = '_line_total' and wco.order_item_id = wcom.order_item_id THEN wcom.meta_value END ) as precio,
	max( CASE WHEN wcom.meta_key = 'pa_slot' and wco.order_item_id = wcom.order_item_id THEN wcom.meta_value END ) as slot,
	max( CASE WHEN wcom.meta_key = '_product_id' and wco.order_item_id = wcom.order_item_id THEN wcom.meta_value END ) as _product_id
from
cbgw_woocommerce_order_items as wco
LEFT JOIN cbgw_posts as p ON wco.order_id = p.ID 
LEFT JOIN cbgw_postmeta as pm on wco.order_id = pm.post_id
LEFT JOIN cbgw_woocommerce_order_itemmeta as wcom ON wco.order_item_id = wcom.order_item_id
where
wco.order_item_id = '%s' and (p.post_status = 'wc-processing' or p.post_status = 'wc-completed')
group by 
wco.order_item_id
order by wco.order_item_id DESC LIMIT 800", $colname_rsOII); // aca sucede la magia ... se remplaza dentro de la consulta '%s' por $colname_rsOII
$rsClient = mysql_query($query_rsClient, $Conexion) or die(mysql_error());
$row_rsClient = mysql_fetch_assoc($rsClient);
$totalRows_rsClient = mysql_num_rows($rsClient);

//Si no hay stock consulto el LINK de PS STORE
if(!($totalRows_rsSTK > 0)){
	$producto_catalogo = $row_rsClient['_product_id'];

	mysql_select_db($database_Conexion, $Conexion);
	$query_rsLinkPS = sprintf("SELECT GROUP_CONCAT(meta_value) as meta_value FROM cbgw_postmeta where post_id='%s' and meta_key='link_ps' GROUP BY post_id",$producto_catalogo); // se remplaza en la consulta '%s' por $colname_rsSTK
	$rsLinkPS = mysql_query($query_rsLinkPS, $Conexion) or die(mysql_error());
	$row_rsLinkPS = mysql_fetch_assoc($rsLinkPS);
	$totalRows_rsLinkPS = mysql_num_rows($rsLinkPS);

	$linkPS = $row_rsLinkPS['meta_value'];
}

//Consulto si el email de compra registrado en el pedido existe en la tabla clientes
$email_pedido = $row_rsClient['email'];

mysql_select_db($database_Conexion, $Conexion);
$query_rsClientes = sprintf("SELECT ID, nombre, apellido, email, auto FROM clientes WHERE email='%s'",$email_pedido); 
$rsClientes = mysql_query($query_rsClientes, $Conexion) or die(mysql_error());
$row_rsClientes = mysql_fetch_assoc($rsClientes);
$totalRows_rsClientes = mysql_num_rows($rsClientes);

$emailCte = $row_rsClientes['email'];

//Si NO existe el email aviso al gestor que debe agregar el email a la base de datos
if(!($row_rsClientes > 0)){exit("Este email no corresponde a ningún cliente de la base de datos: $emailCte <br>");}
?>
<!DOCTYPE html>
<html lang="es">
	<head>
	<meta charset="utf-8">
	<meta http-equiv="X-UA-Compatible" content="IE=edge">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<!-- The above 3 meta tags *must* come first in the head; any other head content must come *after* these tags -->
	<meta name="description" content="base de datos">
	<meta name="author" content="vic">
	<script>
	  function resizeIframe(obj) {
		obj.style.height = obj.contentWindow.document.body.scrollHeight + 'px';
	  }
	</script>
		<script type="text/javascript">
			window.setInterval(function() {
				  $('.modal-body',this).css({width:'auto',height:'auto', 'max-height':'100%'});
			},500);
	</script>
	<link rel="stylesheet" href="//code.jquery.com/ui/1.11.4/themes/smoothness/jquery-ui.css">
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
  <!-- antes que termine head-->
	</head>
		
  		<body style="padding-top: 0px;">
		
		<?php if(!($totalRows_rsSTK > 0)):?>
			<div>
			<h4 class="text-danger text-center">sin stock</h4>
			<table class="table" border="0" cellpadding="0" cellspacing="5" style="font-size:1em;">
			  </tr>
			  <tr height="90">
			  <td id="<?php echo $row_rsClient['order_item_id'];?>"><span class="label label-default" style="opacity:0.7;">pedido #<?php echo $row_rsClient['order_id']; ?></span><a target="_blank" href="https://dixgamer.com/wp-admin/post.php?post=<?php echo $row_rsClient['order_id']; ?>&action=edit" class="text-muted btn-xs" title="ver pedido en la adm del sitio"><i class="fa fa-external-link" aria-hidden="true"></i> </a><br /><br /><span class="label label-normal" style="font-weight:400; opacity:0.5;">order_item_id #<?php echo $row_rsClient['order_item_id']; ?></span></td>
				<td><img class="img-rounded" width="50" id="image-swap" src="/img/productos/<?php echo $colname_rsCON."/".$colname_rsTIT.".jpg";?>"alt="" /></td>
				<td title="<?php echo str_replace('-', ' ', $colname_rsTIT);?> (<?php echo $colname_rsCON; ?>)"><?php echo str_replace('-', ' ', $colname_rsTIT);?> (<?php echo $colname_rsCON; ?>) 

				
				<?php if ($colname_rsSlot == 'Secundario'): ?> <span class="label label-danger" style="opacity:0.7"><?php echo '2°'; ?></span><?php endif;?>
				<?php if (strpos($row_rsClientes['auto'], 'si') !== false):?>
					<a type="button" class="text-muted btn-xs text-danger" title="tiene historial favorable"><i class="fa fa-star" aria-hidden="true"></i></a>
				<?php endif;?>
				</td>

				<td>
				<?php if ($row_rsClientes['email']): ?>
				<?php echo $row_rsClientes['email']; ?>
					<?php if (strpos($row_rsClientes['auto'], 're') !== false):?>
					<a type="button" target="_blank" href="clientes_detalles.php?id=<?php echo $row_rsClientes['ID']; ?>" class="btn btn-danger btn-xs"><i class="fa fa-user" aria-hidden="true"></i> Revendedor</a>
					<?php else:?>
					<a type="button" target="_blank" href="clientes_detalles.php?id=<?php echo $row_rsClientes['ID']; ?>" class="btn btn-default btn-xs"><i class="fa fa-user" aria-hidden="true"></i> Cte</a>
					<?php endif;?>
				<?php else:
					if (strpos($row_rsClientes['email'], 'mercadolibre.com') !== false):?>
						<a type="button" href="clientes_insertar_web_email.php?order_id=<?php echo $row_rsClient['order_id']; ?>" class="btn btn-normal btn-xs" title="corregir email de ML"><i class="fa fa-pencil" aria-hidden="true"></i> Modificar email de ML</a>
					<?php else:?>
					<?php echo $row_rsClientes['email']; ?>
					<a type="button" href="clientes_insertar_web.php?order_item_id=<?php echo $row_rsClient['order_item_id']; ?>" class="btn btn-info btn-xs" title="agregar cliente a base de datos"><i class="fa fa-plus" aria-hidden="true"></i> cliente</a>
					<?php endif;?>
				<?php endif;?>
				<br /><br />
				<?php if (($row_rsClient['user_id_ml']) && ($row_rsClient['user_id_ml'] != "")):?>
						<a target="_blank" href="https://perfil.mercadolibre.com.ar/profile/showProfile?id=<?php echo $row_rsClient['user_id_ml'];?>&role=buyer" class="btn btn-primary btn-xs" type="submit" style="font-weight:400; opacity:0.6;"> <i class="fa fa-user" aria-hidden="true"></i></a> <?php echo $row_rsClientes['nombre']; ?> <?php echo $row_rsClientes['apellido']; ?>
						<a target="_blank" href="https://myaccount.mercadolibre.com.ar/messaging/orders/<?php echo $row_rsClient['order_id_ml'];?>" class="btn btn-warning btn-xs" type="submit" style="font-weight:400; opacity:0.6;"> <i class="fa fa-comments" aria-hidden="true"></i></a>
						<a target="_blank" href="https://myaccount.mercadolibre.com.ar/sales/vop?orderId=<?php echo $row_rsClient['order_id_ml'];?>&role=buyer" class="btn btn-success btn-xs" type="submit"  style="font-weight:400; opacity:0.6;"> <i class="fa fa-shopping-bag" aria-hidden="true"></i></a>
				<?php else:?>
					<?php echo $row_rsClientes['nombre']; ?> <?php echo $row_rsClientes['apellido']; ?>
				<?php endif;?>
				</td>
			  </tr>      
			</table>
			<?php if($linkPS && ($linkPS !== "")):?>
				<!--- <a href="#" class="btn-copiador" data-clipboard-target="#email-copy"><img src="../img/gral/ps-store.png" width="18"> <span id="email-copy"> <?php // echo substr($linkPS, 0, 15); ?></span> <i aria-hidden="true" class="fa fa-clone"></i></a>
				<a href="#" class="btn-copiador btn-lg btn-info" data-clipboard-target="#link-copy">Link PS <i aria-hidden="true" class="fa fa-clone"></i></a>
				<div style="position:absolute; top:0; left:-500px;"><span id="link-copy"> </span></div> -->
				<?php $array = (explode(',', $linkPS, 10)); foreach ($array as $valor) { echo "<a class='btn btn-default btn-sm' title='Ver en la Tienda de PS' target='_blank' href='$valor'><img src='../img/gral/ps-store.png' width='18' /> Link PS </a> "; };?> 
			<?php endif; ?>
			
			<a type="button" href="ventas_insertar_web.php?order_item_id=<?php echo $row_rsClient['order_item_id']; ?>&titulo=<?php echo $colname_rsTIT; ?>&consola=<?php echo $colname_rsCON;?>&slot=<?php echo $colname_rsSlot;?>&c_id=<?php echo $row_rsClientes['ID']; ?>" class="btn btn-info pull-right" style="margin-bottom: 20px;"><i class="fa fa-refresh fa-fw" aria-hidden="true"></i> Re Intentar Asignación</a>
			
			<?php $insertGoTo = "inicio.php";
			if (isset($_SERVER['QUERY_STRING'])) {
			$insertGoTo .= (strpos($insertGoTo, '?')) ? "&" : "?";
			$insertGoTo .= $_SERVER['QUERY_STRING'];}?>
			<a title="Asignar" class="btn btn-default pull-right" type="button" data-target=".new-example-modal-lg" onClick='document.getElementById("ifr2").src="<?php echo $insertGoTo; ?>";'><i class="fa fa-gamepad" aria-hidden="true"></i> Asignar Otro Producto</a>
			<div class="row">
					<!-- Large modal -->
					<div class="new-example-modal-lg" tabindex="-1" aria-labelledby="myLargeModalLabel">
						  <iframe id="ifr2" src="" onload="resizeIframe(this)" style="width:100%;border:0px;" ></iframe>
					</div>
			</div> 
			
		</div>
    <?php else:
		echo '<div class="container"><p>asignando...</p></div>';
		$date = date('Y-m-d H:i:s', time());
		if ($row_rsClient){
			$clientes_id = $row_rsClientes['ID'];
			$stock_id = $row_rsSTK['ID_stk'];
			$order_item_id = $row_rsClient['order_item_id'];

			$cons = $row_rsSTK['consola'];
			
			//Si es una vta de ps4 o plus slot por ML asigno el slot desde el parametro GET
			//if ((($row_rsClient['user_id_ml']) && ($row_rsClient['user_id_ml'] != "")) && (($cons === "ps4") or ($row_rsClient['producto'] === "plus-12-meses-slot"))): $slot = ucwords($colname_rsSlot);
			//Si es una vta de ps4 o plus slot que NO ES de ML asigno el slot desde la consulta SQL
			//elseif --> antes de escapar el IF anterior que iba primero
			if (($cons === "ps4") or ($row_rsSTK['titulo'] === "plus-12-meses-slot")): $slot = ucwords($colname_rsSlot); $estado = "pendiente";
			//Si es una vta de ps3 el slot lo defino en Primario siempre
			elseif ($cons === "ps3"): $slot = "Primario"; $estado = "pendiente";
			//Si no cumple con ninguno de los parametros anteriores seguramente se trata de una venta de Gift Card y el slot se define en "No"
			else: $slot = "No"; $estado = "listo";
			endif;

			// SI ES VENTA DE ML DEFINO LOS VALORS CORRECTOS
			if (($row_rsClient['user_id_ml']) && ($row_rsClient['user_id_ml'] != "")){ 
			$medio_venta = "MercadoLibre";
			$order_id_ml = $row_rsClient['order_id_ml'];
				if (strpos($row_rsClient['medio_pago_2'], '_card') !== false): $medio_cobro = "Mercado Pago - Tarjeta";
				// 2018-05-19 --> no estoy seguro si el medio de pago sale con la palabra ticket cuando es ticket
				elseif (strpos($row_rsClient['medio_pago_2'], 'ticket') !== false): $medio_cobro = "Mercado Pago - Ticket";
				else: $medio_cobro = "Mercado Pago"; endif;
			$ref_cobro = $row_rsClient['ref_cobro_3'];
			$multiplo = "0.12";
			} else { // SI ES VENTA WEB DEFINO LOS VALORES CORRECTOS
			//2017-08 Paso el ref_cobro_2 como primer alternativa para ver si se reducen los errores de REF DE COBRO WEB
			$medio_venta = "Web";
			$medio_cobro = ucwords(strtolower($row_rsClient['medio_pago']));
				if (($row_rsClient['ref_cobro_2']) && ($row_rsClient['ref_cobro_2'] != "")): $ref_cobro = $row_rsClient['ref_cobro_2'];
				elseif (($row_rsClient['ref_cobro']) && ($row_rsClient['ref_cobro'] != "")): $ref_cobro = $row_rsClient['ref_cobro'];
				endif;
				if (strpos($row_rsClient['medio_pago'], 'Transferencia') !== false): $multiplo = "0.00";
				elseif (strpos($row_rsClient['medio_pago'], 'Mercado Pago') !== false): $multiplo = "0.0538";
				elseif (strpos($row_rsClient['medio_pago'], 'Tarjeta') !== false): $multiplo = "0.0538";
				elseif (strpos($row_rsClient['medio_pago'], 'Ticket') !== false): $multiplo = "0.0538";
				elseif (strpos($row_rsClient['medio_pago'], 'PayPal') !== false): $multiplo = "0.99"; // TODAVIA NO SE LA TASA DE PAYPAL AVERIGUAR
				else: $comision = "0.99"; endif; // HAGO ESTO PARA DETECTAR SI HAY UN ERROR FACILMENTE
			}

			$order_id_web = $row_rsClient['order_id'];
			$precio = $row_rsClient['precio'];
			$comision = ($multiplo * $row_rsClient['precio']);

			if(($order_id_ml) && ($order_id_ml != "")){ //si hay order_id_ml inserto el valor del string php
					$insertSQL = sprintf("INSERT INTO ventas (clientes_id, stock_id, order_item_id, cons, slot, medio_venta, order_id_ml, order_id_web, estado, Day, usuario) VALUES ('$clientes_id', '$stock_id', '$order_item_id', '$cons', '$slot', '$medio_venta', '$order_id_ml', '$order_id_web', '$estado', '$date', '$vendedor')",$colname_rsOII);
			} else { // si no hay order_id_ml (deberia darse en caso de vtas por web) quito order_id_ml del SQL para que su valor quede NULL
					$insertSQL = sprintf("INSERT INTO ventas (clientes_id, stock_id, order_item_id, cons, slot, medio_venta, order_id_ml, order_id_web, estado, Day, usuario) VALUES ('$clientes_id', '$stock_id', '$order_item_id', '$cons', '$slot', '$medio_venta', NULL, '$order_id_web', '$estado', '$date', '$vendedor')",$colname_rsOII);
			}

			mysql_select_db($database_Conexion, $Conexion);
			$Result1 = mysql_query($insertSQL, $Conexion) or die(mysql_error());

			$ventaid = mysql_insert_id(); // ultimo ID de una consulta INSERT , en este caso seria el ID de la ultima venta creada
			if("" !== trim($ref_cobro)){ //si hay ref_cobro inserto el valor del string php
					$insertSQL222 = sprintf("INSERT INTO ventas_cobro (ventas_id, medio_cobro, ref_cobro, precio, comision, Day, usuario) VALUES ('$ventaid', '$medio_cobro', '$ref_cobro', '$precio', '$comision', '$date', '$vendedor')",$colname_rsOII);
			} else { // si no hay ref_cobro (deberia darse en caso de transferencias bancarias) quito ref_cobro del SQL para que su valor quede NULL en la table ventas_cobro
					$insertSQL222 = sprintf("INSERT INTO ventas_cobro (ventas_id, medio_cobro, ref_cobro, precio, comision, Day, usuario) VALUES ('$ventaid', '$medio_cobro', NULL, '$precio', '$comision', '$date', '$vendedor')",$colname_rsOII);
			}
		  mysql_select_db($database_Conexion, $Conexion);
		  $Result222 = mysql_query($insertSQL222, $Conexion) or die(mysql_error());

		  /*** 2018-01-01 quito la actualización del estado del pedido luego de asignar un solo juego

		  $updateSQL = sprintf("UPDATE cbgw_posts SET post_status='wc-completed' WHERE ID=%s",$row_rsClient['order_id']);
		  mysql_select_db($database_Conexion, $Conexion);
		  $Result2 = mysql_query($updateSQL, $Conexion) or die(mysql_error());

		  $post_id = $row_rsClient['order_id'];
		  $meta_key = "_completed_date";
		  $updateSQL3 = sprintf("INSERT INTO cbgw_postmeta (post_id, meta_key, meta_value) VALUES ('$post_id', '$meta_key', '$date')",$colname_rsOII);
		  mysql_select_db($database_Conexion, $Conexion);
		  $Result3 = mysql_query($updateSQL3, $Conexion) or die(mysql_error());
		  */

		// Script para redirigir el top 
		echo "<script>window.top.location.href = \"clientes_detalles.php?id=$clientes_id\";</script>";
		exit;
		;}
	endif; ?>
	<script src="https://ajax.googleapis.com/ajax/libs/jquery/1.12.4/jquery.min.js"></script>
	<script src="assets/js/docs.min.js"></script>
	<script src="js/bootstrap.min.js"></script>
	<!-- Activar popover -->
	</body>
</html>