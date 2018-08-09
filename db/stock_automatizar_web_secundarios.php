<?php
require_once('../Connections/Conexion.php');

// DEFINIR FECHA EN ARGENTINA
date_default_timezone_set('America/Argentina/Buenos_Aires');

mysql_select_db($database_Conexion, $Conexion);
$query_rsStock = sprintf("SELECT * FROM
(SELECT COUNT(ID_stk) AS q_stk, REPLACE(REPLACE(REPLACE(REPLACE(TRIM(LCASE(titulo)), ' ', '-'), '''', ''), '’', ''), '.', '') AS producto, consola, SUM(Q_vta) as q_vta, SUM(Q_vta_pri) as vta_pri, SUM(Q_vta_sec) as vta_sec, (SUM(Q_vta_pri) - SUM(Q_vta_sec)) as libre FROM 
(SELECT ID AS ID_stk, titulo, consola, ID_vta, IFNULL(Q_vta,0) AS Q_vta, IFNULL(Q_vta_pri,0) AS Q_vta_pri, IFNULL(Q_vta_sec,0) AS Q_vta_sec
FROM stock
LEFT JOIN
(SELECT ventas.ID as ID_vta, stock_id, SUM(case when slot = 'Primario' then 1 else null end) AS Q_vta_pri, SUM(case when slot = 'Secundario' then 1 else null end) AS Q_vta_sec, MAX(estado) AS vta_estado, COUNT(*) AS Q_vta, Day AS dayvta
FROM ventas
GROUP BY stock_id) AS vendido
ON ID = stock_id
WHERE (consola = 'ps4')
ORDER BY consola, titulo, ID DESC) AS resultado
GROUP BY titulo)
AS final
LEFT JOIN
(
select
    p.ID,
    REPLACE(REPLACE(REPLACE(REPLACE(TRIM(LCASE(p_p.post_title)), ' ', '-'), '''', ''), '’', ''), '.', '') as titulo,
    max( CASE WHEN pm.meta_key = 'consola' and  p.post_parent = pm.post_id THEN pm.meta_value END ) as cons,
    max( CASE WHEN pm2.meta_key = 'attribute_pa_slot' and p.ID = pm2.post_id THEN pm2.meta_value END ) as slot,
    max( CASE WHEN pm2.meta_key = '_stock_status' and p.ID = pm2.post_id THEN pm2.meta_value END ) as stock_status,
    max( CASE WHEN pm2.meta_key = '_stock' and p.ID = pm2.post_id THEN pm2.meta_value END ) as stock,
    p_p.post_status
from
    cbgw_posts as p
left join
	cbgw_posts as p_p
ON p.post_parent = p_p.ID
left join 
    cbgw_postmeta as pm
ON p.post_parent = pm.post_id
left join
	cbgw_postmeta as pm2
ON 	p.ID = pm2.post_id
where
    p.post_type = 'product_variation' and
    p_p.post_status = 'publish'
group by
    p.ID
    order by p.post_title) as web
ON final.producto = web.titulo
WHERE (stock - libre) != 0 AND cons = 'ps4' AND slot='secundario'");
$rsStock = mysql_query($query_rsStock, $Conexion) or die(mysql_error());
$row_rsStock = mysql_fetch_assoc($rsStock);
$totalRows_rsStock = mysql_num_rows($rsStock);
?>
<?php do {
	
	if ($row_rsStock['titulo']) {
		$ID = $row_rsStock['ID'];
		$producto = $row_rsStock['producto'];
		$slot = $row_rsStock['slot'];
		$libre = $row_rsStock['libre'];
		$stock = $row_rsStock['stock'];
			  			$updateSQLSTOCK = sprintf("UPDATE cbgw_postmeta SET meta_value='%s' WHERE meta_key='_stock' AND post_id=%s",$libre,$ID);
						  mysql_select_db($database_Conexion, $Conexion);
						  $Result2STOCK = mysql_query($updateSQLSTOCK, $Conexion) or die(mysql_error());
						  echo $producto.  " " .$slot. " cambiado a " . $libre . " stock<br>";
	}
} while ($row_rsStock = mysql_fetch_assoc($rsStock)); ?>
<?php
mysql_free_result($rsStock);
?>