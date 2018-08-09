<?php
require_once('../Connections/Conexion.php');

// DEFINIR FECHA EN ARGENTINA
date_default_timezone_set('America/Argentina/Buenos_Aires');

mysql_select_db($database_Conexion, $Conexion);
$query_rsEstado = sprintf("SELECT ventas.order_item_id, cbgw_woocommerce_order_items.order_item_id, cbgw_woocommerce_order_items.order_id, cbgw_posts.ID, cbgw_posts.post_status
FROM ventas
left join cbgw_woocommerce_order_items 
ON ventas.order_item_id = cbgw_woocommerce_order_items.order_item_id
left join cbgw_posts 
on cbgw_woocommerce_order_items.order_id = cbgw_posts.ID 
WHERE ventas.order_item_id IS NOT NULL AND cbgw_posts.post_status = 'wc-processing'", $colname_rsEstado);
$rsEstado = mysql_query($query_rsEstado, $Conexion) or die(mysql_error());
$row_rsEstado = mysql_fetch_assoc($rsEstado);
$totalRows_rsEstado = mysql_num_rows($rsEstado);
?>

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