<?php
require_once('../Connections/Conexion.php');

// DEFINIR FECHA EN ARGENTINA
date_default_timezone_set('America/Argentina/Buenos_Aires');

// LISTAR TODOS LOS PRODUCTOS QUE SON VARIACION (SERIA PS4) POR AHORA SOLO HAGO JOIN CON STOCK DE PS4 YA QUE SON LOS UNICOS CON PRECIOS DE OFERTA
mysql_select_db($database_Conexion, $Conexion);
$query_rsEstado = "SELECT tt1.*, tt2.Q_Stk, tt2.costo, tt3.Q_vta, (COALESCE(Q_Stk,0) - COALESCE(Q_vta,0)) as Q_Stock FROM
(select
    p.ID,
    SUBSTRING_INDEX(SUBSTRING_INDEX(REPLACE(REPLACE(REPLACE(REPLACE(TRIM(LCASE(p.post_title)), ' ', '-'), '''', ''), '’', ''), '.', ''),'-&ndash;',1),'---',1) as titulo,
    'ps4' as consola,
    max( CASE WHEN pm.meta_key = 'attribute_pa_slot' and p.ID = pm.post_id THEN pm.meta_value END ) as slot,
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
    post_type = 'product_variation' and
    post_status = 'publish'
group by
    p.ID
ORDER BY `consola` ASC, `titulo` ASC) as tt1
LEFT JOIN
(SELECT titulo, consola, COUNT(*) as Q_Stk, AVG(costo) as costo FROM stock GROUP BY titulo, consola) tt2
ON tt1.titulo = tt2.titulo AND tt1.consola = tt2.consola
LEFT JOIN
(SELECT 
	titulo, 
 	'primario' as slot,
	SUM(case when slot = 'Primario' then 1 else 0 end) AS Q_vta
FROM 
	ventas 
LEFT JOIN 
	stock 
ON 
	ventas.stock_id = stock.ID
WHERE 
	(consola = 'ps4')
GROUP BY 
	titulo
UNION ALL
 SELECT 
	titulo, 
 	'secundario' as slot,
	SUM(case when slot = 'Secundario' then 1 else 0 end) AS Q_vta
FROM 
	ventas 
LEFT JOIN 
	stock 
ON 
	ventas.stock_id = stock.ID
WHERE 
	(consola = 'ps4')
GROUP BY 
	titulo) AS tt3
    ON tt1.titulo = tt3.titulo and tt1.slot = tt3.slot
WHERE reg_price > 0 and sale_price > 0 and (COALESCE(Q_Stk,0) - COALESCE(Q_vta,0)) < 5";
$rsEstado = mysql_query($query_rsEstado, $Conexion) or die(mysql_error());
$row_rsEstado = mysql_fetch_assoc($rsEstado);
$totalRows_rsEstado = mysql_num_rows($rsEstado);
?>
<?php do {

	$ID = $row_rsEstado['ID'];
	$producto = $row_rsEstado['titulo'];
	$consola = $row_rsEstado['consola'];
	$slot = $row_rsEstado['slot'];
	if ($slot == "primario") {$color = "primary";}else{$color="info";}
	$precio = $row_rsEstado['reg_price'];
	$oferta = $row_rsEstado['sale_price'];
	$stock = $row_rsEstado['Q_Stock'];
	$costo = $row_rsEstado['costo'];
		
		echo "<div class='col-xs-6 col-sm-1' style='text-align:center;'><a target='_blank' href='https://dixgamer.com/wp-admin/post.php?post=" . $ID . "&action=edit'><img src='/img/productos/".  $consola. "/" . $producto.  ".jpg' width='50'/></a><br /><em class='badge badge-" . $color . "'>$" . $oferta . "<span style='font-size:0.8em;'>/" . $precio . "</span></em><br /><em title='Costo Prom del Stock disponible' class='badge badge-default'>$" . round($costo,0) . "</em><br /><em class='badge badge-"; if($stock > 2): echo "normal"; else: echo "danger";endif; echo "'>" . $stock . "</em></div>" ;

	

} while ($row_rsEstado = mysql_fetch_assoc($rsEstado)); ?> 
<?php
mysql_free_result($rsEstado);
?>