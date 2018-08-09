<?php
require_once('../Connections/Conexion.php');

// DEFINIR FECHA EN ARGENTINA
date_default_timezone_set('America/Argentina/Buenos_Aires');

// LISTAR TODOS LOS PRODUCTOS QUE NO SON VARIACION (SERIA PS3 Y PSN) POR AHORA SOLO HAGO JOIN CON STOCK DE PS3 YA QUE SON LOS UNICOS CON PRECIOS DE OFERTA, PSN NUNCA TIENE PRECIOS DE OFERTA
mysql_select_db($database_Conexion, $Conexion);
$query_rsEstado = "select t1.*, t2.Q_Stock_T FROM
(select
    p.ID,
    REPLACE(REPLACE(REPLACE(REPLACE(TRIM(LCASE(p.post_title)), ' ', '-'), '''', ''), '’', ''), '.', '') AS titulo,
    max( CASE WHEN pm.meta_key = 'consola' and  p.ID = pm.post_id THEN pm.meta_value END ) as consola,
	max( CASE WHEN pm.meta_key = '_regular_price' and p.ID = pm.post_id THEN pm.meta_value END ) as reg_price,
    max( CASE WHEN pm.meta_key = '_sale_price' and p.ID = pm.post_id THEN pm.meta_value END ) as sale_price,
    post_status
from
    cbgw_posts as p
LEFT JOIN
    cbgw_postmeta as pm
ON
   p.ID = pm.post_id
where
    post_type = 'product' and
    post_status = 'publish'
group by
    p.ID
ORDER BY `consola` ASC, `titulo` ASC) as t1
LEFT JOIN
(SELECT stk.titulo, stk.consola, (COALESCE(Q_Stock,0) + COALESCE(Q_stk_sin_reset,0)) as Q_Stock_T FROM 
(SELECT ID_stk, titulo, consola, stk_ctas_id, dayreset, Q_reset, days_from_reset, Q_vta, COALESCE(SUM(Q_Stock),0) AS Q_Stock FROM (SELECT ID AS ID_stk, titulo, consola, cuentas_id AS stk_ctas_id, ID_reseteo AS ID_reset, r_cuentas_id AS reset_ctas_id, dayreseteo AS dayreset, reset.Q_reseteado AS Q_reset, DATEDIFF(NOW(), dayreseteo) AS days_from_reset, ID_vta, vta_estado, Q_vta, dayvta, ((2 + (IFNULL(Q_reseteado, 0) * 2)) - IFNULL(Q_vta, 0)) AS Q_Stock
FROM stock 
LEFT JOIN
(SELECT ID AS ID_reseteo, cuentas_id AS r_cuentas_id, COUNT(*) AS Q_reseteado, MAX(Day) AS dayreseteo
FROM reseteo
GROUP BY cuentas_id
ORDER BY ID DESC) AS reset
ON cuentas_id = r_cuentas_id
LEFT JOIN
(SELECT ventas.ID as ID_vta, stock_id, MAX(estado) AS vta_estado, COUNT(*) AS Q_vta, Day AS dayvta
FROM ventas
GROUP BY stock_id
ORDER BY vta_estado DESC) AS vendido
ON ID = stock_id
WHERE consola = 'ps3' AND (((Q_vta IS NULL) OR (Q_vta < '2')) OR (((Q_vta >= '2') AND (Q_reseteado = FLOOR(Q_vta/2)))))
GROUP BY ID
ORDER BY Q_reset, consola, titulo, ID DESC) AS consulta
GROUP BY consola, titulo
ORDER BY consola, titulo, ID_stk) as stk
LEFT JOIN
(SELECT titulo, consola, (COUNT(*) * 2) as Q_stk_sin_reset FROM
(SELECT ID AS ID_stk, titulo, consola, cuentas_id AS stk_ctas_id, ID_reseteo AS ID_reset, r_cuentas_id AS reset_ctas_id, dayreseteo AS dayreset, reset.Q_reseteado AS Q_reset, DATEDIFF(NOW(), dayreseteo) AS days_from_reset, ID_vta, Q_vta, dayvta, (DATEDIFF(NOW(), dayvta) - 1) AS days_from_vta
FROM stock
LEFT JOIN
(SELECT ID AS ID_reseteo, cuentas_id AS r_cuentas_id, COUNT(*) AS Q_reseteado, MAX(Day) AS dayreseteo
FROM reseteo
GROUP BY cuentas_id
ORDER BY ID DESC) AS reset
ON cuentas_id = r_cuentas_id
LEFT JOIN
(SELECT ventas.ID as ID_vta, stock_id, COUNT(*) AS Q_vta, MAX(Day) AS dayvta
FROM ventas
GROUP BY stock_id) AS vendido
ON ID = stock_id
WHERE (consola = 'ps3') AND ((Q_vta >= '2' AND ID_reseteo IS NULL) OR (Q_vta >= '4' AND DATEDIFF(NOW(), dayreseteo) > '180'))
ORDER BY consola, titulo, ID DESC) as stk_sin_reset
GROUP by titulo) as stk_no_reset
ON stk.titulo = stk_no_reset.titulo AND stk.consola = stk_no_reset.consola
UNION
SELECT stk_no_reset.titulo, stk_no_reset.consola, (COALESCE(Q_Stock,0) + COALESCE(Q_stk_sin_reset,0)) as Q_Stock_T FROM 
(SELECT ID_stk, titulo, consola, stk_ctas_id, dayreset, Q_reset, days_from_reset, Q_vta, COALESCE(SUM(Q_Stock),0) AS Q_Stock FROM (SELECT ID AS ID_stk, titulo, consola, cuentas_id AS stk_ctas_id, ID_reseteo AS ID_reset, r_cuentas_id AS reset_ctas_id, dayreseteo AS dayreset, reset.Q_reseteado AS Q_reset, DATEDIFF(NOW(), dayreseteo) AS days_from_reset, ID_vta, vta_estado, Q_vta, dayvta, ((2 + (IFNULL(Q_reseteado, 0) * 2)) - IFNULL(Q_vta, 0)) AS Q_Stock
FROM stock 
LEFT JOIN
(SELECT ID AS ID_reseteo, cuentas_id AS r_cuentas_id, COUNT(*) AS Q_reseteado, MAX(Day) AS dayreseteo
FROM reseteo
GROUP BY cuentas_id
ORDER BY ID DESC) AS reset
ON cuentas_id = r_cuentas_id
LEFT JOIN
(SELECT ventas.ID as ID_vta, stock_id, MAX(estado) AS vta_estado, COUNT(*) AS Q_vta, Day AS dayvta
FROM ventas
GROUP BY stock_id
ORDER BY vta_estado DESC) AS vendido
ON ID = stock_id
WHERE consola = 'ps3' AND (((Q_vta IS NULL) OR (Q_vta < '2')) OR (((Q_vta >= '2') AND (Q_reseteado = FLOOR(Q_vta/2)))))
GROUP BY ID
ORDER BY Q_reset, consola, titulo, ID DESC) AS consulta
GROUP BY consola, titulo
ORDER BY consola, titulo, ID_stk) as stk
RIGHT JOIN
(SELECT titulo, consola, (COUNT(*) * 2) as Q_stk_sin_reset FROM
(SELECT ID AS ID_stk, titulo, consola, cuentas_id AS stk_ctas_id, ID_reseteo AS ID_reset, r_cuentas_id AS reset_ctas_id, dayreseteo AS dayreset, reset.Q_reseteado AS Q_reset, DATEDIFF(NOW(), dayreseteo) AS days_from_reset, ID_vta, Q_vta, dayvta, (DATEDIFF(NOW(), dayvta) - 1) AS days_from_vta
FROM stock
LEFT JOIN
(SELECT ID AS ID_reseteo, cuentas_id AS r_cuentas_id, COUNT(*) AS Q_reseteado, MAX(Day) AS dayreseteo
FROM reseteo
GROUP BY cuentas_id
ORDER BY ID DESC) AS reset
ON cuentas_id = r_cuentas_id
RIGHT JOIN
(SELECT ventas.ID as ID_vta, stock_id, COUNT(*) AS Q_vta, MAX(Day) AS dayvta
FROM ventas
GROUP BY stock_id) AS vendido
ON ID = stock_id
WHERE (consola = 'ps3') AND ((Q_vta >= '2' AND ID_reseteo IS NULL) OR (Q_vta >= '4' AND DATEDIFF(NOW(), dayreseteo) > '180'))
ORDER BY consola, titulo, ID DESC) as stk_sin_reset
GROUP by titulo) as stk_no_reset
ON stk.titulo = stk_no_reset.titulo AND stk.consola = stk_no_reset.consola) as t2
ON t1.titulo = t2.titulo and t1.consola = t2.consola
WHERE reg_price > 0 and sale_price > 0 and (Q_Stock_T < 5 or Q_Stock_T IS NULL)";
$rsEstado = mysql_query($query_rsEstado, $Conexion) or die(mysql_error());
$row_rsEstado = mysql_fetch_assoc($rsEstado);
$totalRows_rsEstado = mysql_num_rows($rsEstado);
?>
<?php do {

	$ID = $row_rsEstado['ID'];
	$consola = $row_rsEstado['consola'];
	$producto = $row_rsEstado['titulo'];
	$precio = $row_rsEstado['reg_price'];
	$oferta = $row_rsEstado['sale_price'];
	$stock = $row_rsEstado['Q_Stock_T'];
	
	echo "<div class='col-xs-6 col-sm-1' style='text-align:center;'><a target='_blank' href='https://dixgamer.com/wp-admin/post.php?post=" . $ID . "&action=edit'><img src='/img/productos/".  $consola. "/" . $producto.  ".jpg' width='50'/></a><br /><em class='badge badge-default'>$" . $oferta . "<span style='font-size:0.8em;'>/" . $precio . "</span></em><br /><em class='badge badge-"; if($stock > 2): echo "normal"; else: echo "danger";endif; echo "'>" .$stock . "</em></div>" ;

} while ($row_rsEstado = mysql_fetch_assoc($rsEstado)); ?> 
<?php
mysql_free_result($rsEstado);
?>