<?php
require_once('../Connections/Conexion.php');

// DEFINIR FECHA EN ARGENTINA
date_default_timezone_set('America/Argentina/Buenos_Aires');

mysql_select_db($database_Conexion, $Conexion);
$query_rsEstado = sprintf("SELECT db.*, web.*, cbgw_posts.ID, cbgw_posts.post_status
FROM (SELECT COUNT(*) as q_db, order_id_web FROM (SELECT order_id_web FROM ventas GROUP BY order_item_id) as result GROUP BY order_id_web) as db
#primero agrupo por oii para evitar que cuente duplicado dos ventas de la base de datos correspondientes a un solo producto del pedido web ej(2 GC de 50usd por un producto GC 100usd)
LEFT JOIN (SELECT COUNT(*) as q_web, order_id FROM cbgw_woocommerce_order_items WHERE order_item_type='line_item' GROUP BY order_id) as web
#solo cuento los productos dentro de un pedido, para evitar contar cupones o descuentos que sean un item dentro del pedido
ON db.order_id_web = web.order_id
LEFT JOIN cbgw_posts 
on db.order_id_web = cbgw_posts.ID
WHERE cbgw_posts.post_status = 'wc-processing' AND q_db >= q_web", $colname_rsEstado);
$rsEstado = mysql_query($query_rsEstado, $Conexion) or die(mysql_error());
$row_rsEstado = mysql_fetch_assoc($rsEstado);
$totalRows_rsEstado = mysql_num_rows($rsEstado);
?>
<!-- Marcar pedidos WC como completados cuando ya hay una venta en base de datos por cada producto de ese pedido -->
<?php do {
if ($row_rsEstado['order_id']) {
  
  $updateSQL = sprintf("UPDATE cbgw_posts SET post_status='wc-completed' WHERE ID=%s",$row_rsEstado['order_id']);
  mysql_select_db($database_Conexion, $Conexion);
  $Result2 = mysql_query($updateSQL, $Conexion) or die(mysql_error());
  
  $post_id = $row_rsEstado['order_id'];
  $meta_key = "_completed_date";
  $date = date('Y-m-d H:i:s', time());
  $updateSQL3 = "INSERT INTO cbgw_postmeta (post_id, meta_key, meta_value) VALUES ('$post_id', '$meta_key', '$date')";
  mysql_select_db($database_Conexion, $Conexion);
  $Result3 = mysql_query($updateSQL3, $Conexion) or die(mysql_error());
	echo 'pedido ' . $post_id. ' marcado como entregado <br>' ;
}
} while ($row_rsEstado = mysql_fetch_assoc($rsEstado)); ?> 
<?php
mysql_free_result($rsEstado);
?>