<?php require_once('../Connections/Conexion.php'); ?>
<?php
$MM_authorizedUsers = "Adm,Vendedor";
$MM_donotCheckaccess = "false";
require_once('_autentificacion.php');
?>
<?php
$vendedor = $_SESSION['MM_Username'];

$colname_rsClient = "-1";
if (isset($_GET['order_item_id'])) {
  $colname_rsClient = (get_magic_quotes_gpc()) ? $_GET['order_item_id'] : addslashes($_GET['order_item_id']);
  
mysql_select_db($database_Conexion, $Conexion);
$query_rsClient = sprintf("select
wco.order_item_id,
wco.order_id,
p.ID as post_id,
p.post_status as estado,
    max( CASE WHEN pm.meta_key = '_billing_email' and wco.order_id = pm.post_id THEN pm.meta_value END ) as email,
    max( CASE WHEN pm.meta_key = '_billing_first_name' and wco.order_id = pm.post_id THEN pm.meta_value END ) as nombre,
    max( CASE WHEN pm.meta_key = '_billing_last_name' and wco.order_id = pm.post_id THEN pm.meta_value END ) as apellido,
    max( CASE WHEN pm.meta_key = '_billing_country' and wco.order_id = pm.post_id THEN pm.meta_value END ) as pais,
    max( CASE WHEN pm.meta_key = '_billing_state' and wco.order_id = pm.post_id THEN pm.meta_value END ) as provincia,
    max( CASE WHEN pm.meta_key = '_billing_city' and wco.order_id = pm.post_id THEN pm.meta_value END ) as ciudad,
    max( CASE WHEN pm.meta_key = '_billing_phone' and wco.order_id = pm.post_id THEN pm.meta_value END ) as tel,
	max( CASE WHEN pm.meta_key = 'user_id_ml' and wco.order_id = pm.post_id THEN pm.meta_value END ) as user_id_ml
from
cbgw_woocommerce_order_items as wco
LEFT JOIN cbgw_posts as p ON wco.order_id = p.ID
LEFT JOIN cbgw_postmeta as pm ON wco.order_id = pm.post_id
LEFT JOIN cbgw_woocommerce_order_itemmeta as wcom ON wco.order_item_id = wcom.order_item_id
where wco.order_item_id = '%s'", $colname_rsClient); // aca sucede la magia ... se remplaza dentro de la consulta '%s' por $colname_rsClient
$rsClient = mysql_query($query_rsClient, $Conexion) or die(mysql_error());
$row_rsClient = mysql_fetch_assoc($rsClient);
$totalRows_rsClient = mysql_num_rows($rsClient);
}

$date = date('Y-m-d H:i:s', time());
if ($row_rsClient){
	$apellido = ucwords(strtolower($row_rsClient['apellido']));
	$nombre = ucwords(strtolower($row_rsClient['nombre']));;
	if ($row_rsClient['pais'] = "AR"): $pais = "Argentina"; else: $pais = $row_rsClient['pais']; endif;
	if ($row_rsClient['provincia'] === "C"): $prov = "Ciudad Autónoma de Buenos Aires";
	elseif ($row_rsClient['provincia'] === "B"): $prov = "Buenos Aires";
	elseif ($row_rsClient['provincia'] === "K"): $prov = "Catamarca";
	elseif ($row_rsClient['provincia'] === "H"): $prov = "Chaco";
	elseif ($row_rsClient['provincia'] === "U"): $prov = "Chubut";
	elseif ($row_rsClient['provincia'] === "X"): $prov = "Córdoba";
	elseif ($row_rsClient['provincia'] === "W"): $prov = "Corrientes";
	elseif ($row_rsClient['provincia'] === "E"): $prov = "Entre Ríos";
	elseif ($row_rsClient['provincia'] === "P"): $prov = "Formosa";
	elseif ($row_rsClient['provincia'] === "Y"): $prov = "Jujuy";
	elseif ($row_rsClient['provincia'] === "L"): $prov = "La Pampa";
	elseif ($row_rsClient['provincia'] === "F"): $prov = "La Rioja";
	elseif ($row_rsClient['provincia'] === "M"): $prov = "Mendoza";
	elseif ($row_rsClient['provincia'] === "N"): $prov = "Misiones";
	elseif ($row_rsClient['provincia'] === "Q"): $prov = "Neuquén";
	elseif ($row_rsClient['provincia'] === "R"): $prov = "Río Negro";
	elseif ($row_rsClient['provincia'] === "A"): $prov = "Salta";
	elseif ($row_rsClient['provincia'] === "J"): $prov = "San Juan";
	elseif ($row_rsClient['provincia'] === "D"): $prov = "San Luis";
	elseif ($row_rsClient['provincia'] === "Z"): $prov = "Santa Cruz";
	elseif ($row_rsClient['provincia'] === "S"): $prov = "Santa Fe";
	elseif ($row_rsClient['provincia'] === "G"): $prov = "Santiago del Estero";
	elseif ($row_rsClient['provincia'] === "V"): $prov = "Tierra del Fuego";
	elseif ($row_rsClient['provincia'] === "T"): $prov = "Tucumán"; 
	else: $prov = ucwords(strtolower($row_rsClient['provincia'])); endif;
	$ciudad = ucwords(strtolower($row_rsClient['ciudad']));
	$tel = $row_rsClient['tel'];
	$email = strtolower($row_rsClient['email']);
	$user_id_ml = $row_rsClient['user_id_ml'];

	if(($user_id_ml) && ($user_id_ml != "")){ //si hay user_id_ml inserto el valor del string php
  			 $insertSQL = sprintf("INSERT INTO clientes (apellido, nombre, pais, provincia, ciudad, tel, email, ml_user, usuario) VALUES ('$apellido', '$nombre', '$pais', '$prov', '$ciudad', '$tel', '$email', '$user_id_ml', '$vendedor')",$colname_rsClient);
  	} else { // si no hay user_id_ml (deberia darse en caso de vtas por web) quito user_id_ml del SQL para que su valor quede NULL
			 $insertSQL = sprintf("INSERT INTO clientes (apellido, nombre, pais, provincia, ciudad, tel, email, ml_user, usuario) VALUES ('$apellido', '$nombre', '$pais', '$prov', '$ciudad', '$tel', '$email', NULL, '$vendedor')",$colname_rsClient);
	}
 
  mysql_select_db($database_Conexion, $Conexion);
  $Result1 = mysql_query($insertSQL, $Conexion) or die(mysql_error());
  
  $deleteGoTo = "ventas_web.php?pedido=" . $row_rsClient['order_id'];
  header(sprintf("Location: %s", $deleteGoTo));
}
?>