<?php require_once('../../Connections/Conexion.php'); ?>
<?php
$MM_authorizedUsers = "Adm,Vendedor";
$MM_donotCheckaccess = "false";
require_once('../_autentificacion.php');
?>
<?php
$vendedor = $_SESSION['MM_Username'];
$colname_rsClient = "-1";
if (isset($_GET['order_item_id'])) {
  $colname_rsClient = (get_magic_quotes_gpc()) ? $_GET['order_item_id'] : addslashes($_GET['order_item_id']);}
  
$colname_rsSTK = "-1";
if (isset($_GET['s_id'])) {
  $colname_rsSTK = (get_magic_quotes_gpc()) ? $_GET['s_id'] : addslashes($_GET['s_id']);}
  
$colname_rsCliente = "-1";
if (isset($_GET['c_id'])) {
  $colname_rsCliente = (get_magic_quotes_gpc()) ? $_GET['c_id'] : addslashes($_GET['c_id']);}

$colname_rsSlot = "-1";
if (isset($_GET['slot'])) {
  $colname_rsSlot = (get_magic_quotes_gpc()) ? $_GET['slot'] : addslashes($_GET['slot']);}
  
mysql_select_db($database_Conexion, $Conexion);
$query_rsClient = sprintf("select
	wco.order_item_id,
	wco.order_id,
	SUBSTRING_INDEX(SUBSTRING_INDEX(REPLACE(REPLACE(REPLACE(REPLACE(TRIM(LCASE(wco.order_item_name)), ' ', '-'), '''', ''), '’', ''), '.', ''),'-&ndash;',1),'---',1) as producto,
	p.ID as post_id,
	p.post_status as estado,
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
    max( CASE WHEN wcom.meta_key = '_product_id' and wco.order_item_id = wcom.order_item_id THEN wcom.meta_value END ) as _product_id,
    max( CASE WHEN wcom.meta_key = '_variation_id' and wco.order_item_id = wcom.order_item_id THEN wcom.meta_value END ) as _variation_id 
from
cbgw_woocommerce_order_items as wco
LEFT JOIN cbgw_posts as p ON wco.order_id = p.ID 
LEFT JOIN cbgw_postmeta as pm on wco.order_id = pm.post_id
LEFT JOIN cbgw_woocommerce_order_itemmeta as wcom ON wco.order_item_id = wcom.order_item_id
where
wco.order_item_id = '%s' and (p.post_status = 'wc-processing' or p.post_status = 'wc-completed')
group by 
wco.order_item_id
order by wco.order_item_id DESC LIMIT 800", $colname_rsClient); // aca sucede la magia ... se remplaza dentro de la consulta '%s' por $colname_rsClient
$rsClient = mysql_query($query_rsClient, $Conexion) or die(mysql_error());
$row_rsClient = mysql_fetch_assoc($rsClient);
$totalRows_rsClient = mysql_num_rows($rsClient);
  
mysql_select_db($database_Conexion, $Conexion);
$query_rsSTK = sprintf("select consola FROM stock where ID='%s'", $colname_rsSTK); // se remplaza en la consulta '%s' por $colname_rsSTK
$rsSTK = mysql_query($query_rsSTK, $Conexion) or die(mysql_error());
$row_rsSTK = mysql_fetch_assoc($rsSTK);
$totalRows_rsSTK = mysql_num_rows($rsSTK);


$date = date('Y-m-d H:i:s', time());
if ($row_rsClient){
	$clientes_id = $colname_rsCliente;
	$stock_id = $colname_rsSTK;
	$order_item_id = $row_rsClient['order_item_id'];
	
	$cons = $row_rsSTK['consola'];
	//Si es una vta de ps4 o plus slot por ML asigno el slot desde el parametro GET
	if ((($row_rsClient['user_id_ml']) && ($row_rsClient['user_id_ml'] != "")) && (($cons === "ps4") or ($row_rsClient['producto'] === "plus-12-meses-slot"))): $slot = ucwords($colname_rsSlot);
	//Si es una vta de ps4 o plus slot que NO ES de ML asigno el slot desde la consulta SQL
	elseif (($cons === "ps4") or ($row_rsClient['producto'] === "plus-12-meses-slot")): $slot = ucwords($row_rsClient['slot']);
	//Si es una vta de ps3 el slot lo defino en Primario siempre
	elseif ($cons === "ps3"): $slot = "Primario";
	//Si no cumple con ninguno de los parametros anteriores seguramente se trata de una venta de Gift Card y el slot se define en "No"
	else: $slot = "No"; endif;
	
	// SI ES VENTA DE ML DEFINO LOS VALORS CORRECTOS
	if (($row_rsClient['user_id_ml']) && ($row_rsClient['user_id_ml'] != "")){ 
	$medio_venta = "MercadoLibre";
	$order_id_ml = $row_rsClient['order_id_ml'];
		if (strpos($row_rsClient['medio_pago_2'], '_card') !== false): $medio_cobro = "Mercado Pago - Tarjeta";
		else: $medio_cobro = "Mercado Pago - Ticket"; endif;
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
		elseif (strpos($row_rsClient['medio_pago'], 'PayPal') !== false): $multiplo = "0.99"; // TODAVIA NO SE LA TASA DE PAYPAL AVERIGUAR
		else: $comision = "0.99"; endif; // HAGO ESTO PARA DETECTAR SI HAY UN ERROR FACILMENTE
	}
	
	$order_id_web = $row_rsClient['order_id'];
	$precio = $row_rsClient['precio'];
	$comision = ($multiplo * $row_rsClient['precio']);
	$estado = "pendiente";
	$Day = $row_rsClient['fecha'];
	
	if(($order_id_ml) && ($order_id_ml != "")){ //si hay order_id_ml inserto el valor del string php
  			$insertSQL = sprintf("INSERT INTO ventas (clientes_id, stock_id, order_item_id, cons, slot, medio_venta, order_id_ml, order_id_web, estado, Day, usuario) VALUES ('$clientes_id', '$stock_id', '$order_item_id', '$cons', '$slot', '$medio_venta', '$order_id_ml', '$order_id_web', '$estado', '$date', '$vendedor')",$colname_rsClient);
  	} else { // si no hay order_id_ml (deberia darse en caso de vtas por web) quito order_id_ml del SQL para que su valor quede NULL
			$insertSQL = sprintf("INSERT INTO ventas (clientes_id, stock_id, order_item_id, cons, slot, medio_venta, order_id_ml, order_id_web, estado, Day, usuario) VALUES ('$clientes_id', '$stock_id', '$order_item_id', '$cons', '$slot', '$medio_venta', NULL, '$order_id_web', '$estado', '$date', '$vendedor')",$colname_rsClient);
	}
	
	mysql_select_db($database_Conexion, $Conexion);
	$Result1 = mysql_query($insertSQL, $Conexion) or die(mysql_error());
  
    $ventaid = mysql_insert_id(); // ultimo ID de una consulta INSERT , en este caso seria el ID de la ultima venta creada
  	if("" !== trim($ref_cobro)){ //si hay ref_cobro inserto el valor del string php
  			$insertSQL222 = sprintf("INSERT INTO ventas_cobro (ventas_id, medio_cobro, ref_cobro, precio, comision, Day, usuario) VALUES ('$ventaid', '$medio_cobro', '$ref_cobro', '$precio', '$comision', '$date', '$vendedor')",$colname_rsClient);
  	} else { // si no hay ref_cobro (deberia darse en caso de transferencias bancarias) quito ref_cobro del SQL para que su valor quede NULL en la table ventas_cobro
			$insertSQL222 = sprintf("INSERT INTO ventas_cobro (ventas_id, medio_cobro, ref_cobro, precio, comision, Day, usuario) VALUES ('$ventaid', '$medio_cobro', NULL, '$precio', '$comision', '$date', '$vendedor')",$colname_rsClient);
	}
  mysql_select_db($database_Conexion, $Conexion);
  $Result222 = mysql_query($insertSQL222, $Conexion) or die(mysql_error());
  
  /*** 2018-01-01 quito la actualización del estado del pedido luego de asignar un solo juego
  
  $updateSQL = sprintf("UPDATE cbgw_posts SET post_status='wc-completed' WHERE ID=%s",$row_rsClient['order_id']);
  mysql_select_db($database_Conexion, $Conexion);
  $Result2 = mysql_query($updateSQL, $Conexion) or die(mysql_error());
  
  $post_id = $row_rsClient['order_id'];
  $meta_key = "_completed_date";
  $updateSQL3 = sprintf("INSERT INTO cbgw_postmeta (post_id, meta_key, meta_value) VALUES ('$post_id', '$meta_key', '$date')",$colname_rsClient);
  mysql_select_db($database_Conexion, $Conexion);
  $Result3 = mysql_query($updateSQL3, $Conexion) or die(mysql_error());
  */
  
}
// modifico el lugar de ésto y le agrego el exit al final para ver si deja de insertar dobles ventas 04/10/2017
$deleteGoTo = "clientes_detalles.php?id=";
$deleteGoTo .= $clientes_id;
header(sprintf("Location: %s", $deleteGoTo));
exit;
?>