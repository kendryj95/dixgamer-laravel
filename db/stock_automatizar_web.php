<?php
require_once('../Connections/Conexion.php');

// DEFINIR FECHA EN ARGENTINA
date_default_timezone_set('America/Argentina/Buenos_Aires');

mysql_select_db($database_Conexion, $Conexion);
$query_rsEstado = sprintf("SELECT web.*, vtas.*, IFNULL((Q_vta_pri - Q_vta_sec),0) as libre
FROM
(select
    p.ID,
    p.post_title,
    REPLACE(REPLACE(REPLACE(REPLACE(TRIM(LCASE(p_p.post_title)), ' ', '-'), '''', ''), '’', ''), '.', '') as producto,
    max( CASE WHEN pm.meta_key = 'consola' and  p.post_parent = pm.post_id THEN pm.meta_value END ) as consola,
    max( CASE WHEN pm2.meta_key = 'attribute_pa_slot' and p.ID = pm2.post_id THEN pm2.meta_value END ) as slot,
    max( CASE WHEN pm2.meta_key = '_stock_status' and p.ID = pm2.post_id THEN pm2.meta_value END ) as stock_status,
	max( CASE WHEN pm2.meta_key = '_stock' and p.ID = pm2.post_id THEN pm2.meta_value END ) as stock,
    p_p.post_status
from
 cbgw_posts as p
 left join cbgw_posts as p_p ON p.post_parent = p_p.ID
 left join cbgw_postmeta as pm ON p.post_parent = pm.post_id
 left join cbgw_postmeta as pm2 ON p.ID = pm2.post_id 
where
    p.post_type = 'product_variation' and
    p_p.post_status = 'publish'
group by
    p.ID
    order by p.post_title) as web
    
LEFT JOIN
(SELECT 
	titulo, 
	SUM(case when slot = 'Primario' then 1 else 0 end) AS Q_vta_pri, 
	SUM(case when slot = 'Secundario' then 1 else 0 end) AS Q_vta_sec 
FROM 
	ventas 
LEFT JOIN 
	stock 
ON 
	ventas.stock_id = stock.ID
WHERE 
	(consola = 'ps4' or titulo = 'plus-12-meses-slot')
GROUP BY 
	titulo) as vtas
ON
web.producto = vtas.titulo", $colname_rsEstado);
$rsEstado = mysql_query($query_rsEstado, $Conexion) or die(mysql_error());
$row_rsEstado = mysql_fetch_assoc($rsEstado);
$totalRows_rsEstado = mysql_num_rows($rsEstado);
?>
<?php do {
if ($row_rsEstado['producto']) {
	$ID = $row_rsEstado['ID'];
	$producto = $row_rsEstado['producto'];
	$slot = $row_rsEstado['slot'];
	$stock = $row_rsEstado['stock_status'];

	if($row_rsEstado['Q_vta_pri'] === "NULL"): $qvp = 0; else: $qvp = $row_rsEstado['Q_vta_pri']; endif;
	if($row_rsEstado['Q_vta_sec'] === "NULL"): $qvs = 0; else: $qvs = $row_rsEstado['Q_vta_sec']; endif;
	if($row_rsEstado['stock'] === "NULL"): $stock_Q = 0; else: $stock_Q = $row_rsEstado['stock']; endif;
	if(($row_rsEstado['libre'] === "NULL") or ($row_rsEstado['libre'] < 0)): $libre = 0; else: $libre = $row_rsEstado['libre']; endif;
	
	// Defino el margen o GAP entre vtas primarias y secundarias permitidas de acuerdo al producto (fifa y pes x ej tiene mayor margen)
		if(strpos($row_rsEstado['producto'], 'fifa-18') !== false): $margen = 50;
		elseif(strpos($row_rsEstado['producto'], 'god-of-war') !== false): $margen = 50;
		elseif(strpos($row_rsEstado['producto'], 'gta-v') !== false): $margen = 50;
		elseif(strpos($row_rsEstado['producto'], 'mortal-kombat-x') !== false): $margen = 10;
		elseif(strpos($row_rsEstado['producto'], 'minecraft') !== false): $margen = 10;
		elseif(strpos($row_rsEstado['producto'], 'the-last-of-us') !== false): $margen = 10;
		elseif(strpos($row_rsEstado['producto'], 'pes-2018') !== false): $margen = 8;
		elseif(strpos($row_rsEstado['producto'], 'battlefield-1') !== false): $margen = 5;
		elseif(strpos($row_rsEstado['producto'], 'plus-12-meses-slot') !== false): $margen = 0;
		else: $margen = 3;
		endif;
	//
	if(($qvs * 0.1) > 10): $multi = 10; else: $multi = ($qvs * 0.1); endif;
	
	
		
		if($slot == "primario"){
			if($qvp > ($qvs  + $multi + $margen)){
				if($stock == "instock"){
					  $updateSQL = sprintf("UPDATE cbgw_postmeta SET meta_value='outofstock' WHERE meta_key='_stock_status' AND post_id=%s",$ID);//bloque la venta desde la web
					  mysql_select_db($database_Conexion, $Conexion);
					  $Result2 = mysql_query($updateSQL, $Conexion) or die(mysql_error());
					  $updateSQLSTOCK = sprintf("UPDATE cbgw_postmeta SET meta_value='2' WHERE meta_key='_stock' AND post_id=%s",$ID);//le pongo stock = 2 para que no me pause en ML si hay una venta pendiente de pago así no termino perdiendo esa venta
					  mysql_select_db($database_Conexion, $Conexion);
					  $Result2STOCK = mysql_query($updateSQLSTOCK, $Conexion) or die(mysql_error());
					  echo $producto.  " " .$slot. " quitado de stock<br>";
				}}
			if(($qvp <= ($qvs + $multi + $margen)) && ($stock == "outofstock")){
				$updateSQL = sprintf("UPDATE cbgw_postmeta SET meta_value='instock' WHERE meta_key='_stock_status' AND post_id=%s",$ID);
				  mysql_select_db($database_Conexion, $Conexion);
				  $Result2 = mysql_query($updateSQL, $Conexion) or die(mysql_error());
				  $updateSQLSTOCK = sprintf("UPDATE cbgw_postmeta SET meta_value='999' WHERE meta_key='_stock' AND post_id=%s",$ID);
				  mysql_select_db($database_Conexion, $Conexion);
				  $Result2STOCK = mysql_query($updateSQLSTOCK, $Conexion) or die(mysql_error());
				  echo $producto.  " " .$slot. " agregado a stock<br>";
			}
		}
		
		if($slot == "secundario"){
			
			if($qvs >= $qvp){
				if($stock == "instock"){
				    
					  $updateSQL = sprintf("UPDATE cbgw_postmeta SET meta_value='outofstock' WHERE meta_key='_stock_status' AND post_id=%s",$ID);
					  mysql_select_db($database_Conexion, $Conexion);
					  $Result2 = mysql_query($updateSQL, $Conexion) or die(mysql_error());
					  $updateSQLSTOCK = sprintf("UPDATE cbgw_postmeta SET meta_value='0' WHERE meta_key='_stock' AND post_id=%s",$ID);
					  mysql_select_db($database_Conexion, $Conexion);
					  $Result2STOCK = mysql_query($updateSQLSTOCK, $Conexion) or die(mysql_error());
					  echo $producto.  " " .$slot. " quitado de stock<br>";
				}}
			if(($qvs < $qvp) && ($stock == "outofstock")){
				$updateSQL = sprintf("UPDATE cbgw_postmeta SET meta_value='instock' WHERE meta_key='_stock_status' AND post_id=%s",$ID);
				  mysql_select_db($database_Conexion, $Conexion);
				  $Result2 = mysql_query($updateSQL, $Conexion) or die(mysql_error());
				  echo $producto.  " " .$slot. " agregado a stock<br>";
			}
			if( ($libre > $stock_Q) or ($libre < $stock_Q) ) {
				$updateSQLSTOCK = sprintf("UPDATE cbgw_postmeta SET meta_value=%s WHERE meta_key='_stock' AND post_id=%s",$libre,$ID);
				  mysql_select_db($database_Conexion, $Conexion);
				  $Result2STOCK = mysql_query($updateSQLSTOCK, $Conexion) or die(mysql_error());
				  echo '[' . $ID . '] ' . $producto .  " " .$slot. " cambiado a " . $libre . " stock<br>";
				  
			}
		}
}
} while ($row_rsEstado = mysql_fetch_assoc($rsEstado)); ?> 
<?php
mysql_free_result($rsEstado);
?>