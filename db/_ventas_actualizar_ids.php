<?php
mysql_select_db($database_Conexion, $Conexion);
$query_rsEstadodd = "UPDATE ventas as a
LEFT JOIN
(SELECT 
order_item_id,
order_id,
max( CASE WHEN cbgw_postmeta.meta_key='order_id_ml' and cbgw_woocommerce_order_items.order_id=cbgw_postmeta.post_id THEN cbgw_postmeta.meta_value END ) as order_id_ml
FROM cbgw_woocommerce_order_items
LEFT JOIN cbgw_postmeta 
ON order_id = cbgw_postmeta.post_id
GROUP BY order_item_id) as b
ON a.order_item_id = b.order_item_id
SET
a.order_id_web=b.order_id, a.order_id_ml=b.order_id_ml
WHERE a.order_item_id IS NOT NULL and (a.order_id_ml IS NULL and b.order_id_ml IS NOT NULL)";
$rsEstadodd = mysql_query($query_rsEstadodd, $Conexion) or die(mysql_error());
?>
