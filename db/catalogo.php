<?php require_once('../Connections/Conexion.php'); ?>
<?php
$MM_authorizedUsers = "Adm,Revendedor";
$MM_donotCheckaccess = "false";
require_once('_autentificacion.php');
?>
<?php

/*** STOCK PS3 ***/ 
$maxRows_rsPs3 = 999;
$pageNum_rsPs3 = 0;
if (isset($_GET['pageNum_rsPs3'])) {
  $pageNum_rsPs3 = $_GET['pageNum_rsPs3'];
}
$startRow_rsPs3 = $pageNum_rsPs3 * $maxRows_rsPs3;

mysql_select_db($database_Conexion, $Conexion);
$query_rsPs3 = "SELECT * FROM
(SELECT ID_stk, titulo AS producto, consola, stk_ctas_id, dayreset, Q_reset, days_from_reset, Q_vta, SUM(Q_Stock) AS Q_Stock FROM (SELECT ID AS ID_stk, titulo, consola, cuentas_id AS stk_ctas_id, ID_reseteo AS ID_reset, r_cuentas_id AS reset_ctas_id, dayreseteo AS dayreset, reset.Q_reseteado AS Q_reset, DATEDIFF(NOW(), dayreseteo) AS days_from_reset, ID_vta, vta_estado, Q_vta, dayvta, ((2 + (IFNULL(Q_reseteado, 0) * 2)) - IFNULL(Q_vta, 0)) AS Q_Stock
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
ORDER BY consola, titulo, ID DESC) AS consulta
GROUP BY consola, titulo
ORDER BY consola, titulo, ID_stk)
AS final

LEFT JOIN
(SELECT COUNT(*) AS Q_stk2, ID AS ID_stk2, titulo AS producto2, consola as consola2, cuentas_id AS stk_ctas_id2, ID_reseteo AS ID_reset2, dayreseteo AS dayreset2, reset.Q_reseteado AS Q_reset2, DATEDIFF(NOW(), dayreseteo) AS days_from_reset2, ID_vta as ID_vta2, Q_vta as Q_vta2
FROM stock
LEFT JOIN
(SELECT ID AS ID_reseteo, cuentas_id AS r_cuentas_id, COUNT(*) AS Q_reseteado, MAX(Day) AS dayreseteo
FROM reseteo
GROUP BY cuentas_id
ORDER BY ID DESC) AS reset
ON cuentas_id = r_cuentas_id
LEFT JOIN
(SELECT ventas.ID as ID_vta, stock_id, COUNT(*) AS Q_vta, Day AS dayvta
FROM ventas
GROUP BY stock_id) AS vendido
ON ID = stock_id
WHERE (consola = 'ps3') AND ((Q_vta >= '2' AND ID_reseteo IS NULL) OR (Q_vta >= '4' AND DATEDIFF(NOW(), dayreseteo) > '180'))
GROUP BY titulo ORDER BY consola, titulo, ID DESC) AS resetear
ON final.producto = resetear.producto2

LEFT JOIN
(select
    p.ID,
    REPLACE(REPLACE(REPLACE(REPLACE(TRIM(LCASE(p.post_title)), ' ', '-'), '''', ''), '’', ''), '.', '') AS titulo,
    max( CASE WHEN pm.meta_key = 'idioma' and p.ID = pm.post_id THEN pm.meta_value END ) as idioma,
    max( CASE WHEN pm.meta_key = 'consola' and  p.ID = pm.post_id THEN pm.meta_value END ) as cons,
    max( CASE WHEN pm.meta_key = 'peso' and p.ID = pm.post_id THEN pm.meta_value END ) as peso,
	max( CASE WHEN pm.meta_key = '_sale_price' and p.ID = pm.post_id THEN pm.meta_value END ) as price,
    max( CASE WHEN pm.meta_key = '_regular_price' and p.ID = pm.post_id THEN pm.meta_value END ) as reg_price,
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
ORDER BY `cons` ASC, `titulo` ASC) as web
ON final.producto = web.titulo
WHERE (producto LIKE '%combo%' OR reg_price > price) AND cons = 'ps3'";
$query_limit_rsPs3 = sprintf("%s LIMIT %d, %d", $query_rsPs3, $startRow_rsPs3, $maxRows_rsPs3);
$rsPs3 = mysql_query($query_limit_rsPs3, 
$Conexion) or die(mysql_error());
$row_rsPs3 = mysql_fetch_assoc($rsPs3);

/**** STOCK PRI ***/
$maxRows_rsPri = 999;
$pageNum_rsPri = 0;
if (isset($_GET['pageNum_rsPri'])) {
  $pageNum_rsPri = $_GET['pageNum_rsPri'];
}
$startRow_rsPri = $pageNum_rsPri * $maxRows_rsPri;

mysql_select_db($database_Conexion, $Conexion);
$query_rsPri = "SELECT * FROM
(SELECT COUNT(ID_stk) AS q_stk, REPLACE(REPLACE(REPLACE(REPLACE(TRIM(LCASE(titulo)), ' ', '-'), '''', ''), '’', ''), '.', '') AS producto, consola, SUM(Q_vta_pri) as q_vta_pri FROM 
(SELECT ID AS ID_stk, titulo, consola, ID_vta, IFNULL(Q_vta,0) AS Q_vta, IFNULL(Q_vta_pri,0) AS Q_vta_pri
FROM stock
LEFT JOIN
(SELECT ventas.ID as ID_vta, stock_id, SUM(case when slot = 'Primario' then 1 else null end) AS Q_vta_pri, COUNT(*) AS Q_vta
FROM ventas
GROUP BY stock_id) AS vendido
ON ID = stock_id
WHERE (consola = 'ps4')
ORDER BY consola, titulo, ID DESC) AS resultado
GROUP BY titulo)
AS final
LEFT JOIN
(select
    p.ID,
    REPLACE(REPLACE(REPLACE(REPLACE(TRIM(LCASE(p.post_title)), ' ', '-'), '''', ''), '’', ''), '.', '') AS titulo,
    max( CASE WHEN pm.meta_key = 'idioma' and p.ID = pm.post_id THEN pm.meta_value END ) as idioma,
    max( CASE WHEN pm.meta_key = 'consola' and  p.ID = pm.post_id THEN pm.meta_value END ) as cons,
    max( CASE WHEN pm.meta_key = 'peso' and p.ID = pm.post_id THEN pm.meta_value END ) as peso,
	max( CASE WHEN pm.meta_key = '_sale_price' and p.ID = pm.post_id THEN pm.meta_value END ) as price,
	round(max( CASE WHEN pm.meta_key = '_max_variation_regular_price' and p.ID = pm.post_id THEN pm.meta_value END )) as max_reg_price,
        round(max( CASE WHEN pm.meta_key = '_max_variation_price' and p.ID = pm.post_id THEN pm.meta_value END )) as max_price,
        round(max( CASE WHEN pm.meta_key = '_min_variation_regular_price' and p.ID = pm.post_id THEN pm.meta_value END )) as min_reg_price,
	round(max( CASE WHEN pm.meta_key = '_min_variation_price' and p.ID = pm.post_id THEN pm.meta_value END )) as min_price,
	round(max( CASE WHEN pm.meta_key = '_price' and p.ID = pm.post_id THEN pm.meta_value END )) as new_max_price,
	round(min( CASE WHEN pm.meta_key = '_price' and p.ID = pm.post_id THEN pm.meta_value END )) as new_min_price,
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
ORDER BY `cons` ASC, `titulo` ASC) as web
ON final.producto = web.titulo
WHERE q_stk > q_vta_pri AND cons = 'ps4'";
$query_limit_rsPri = sprintf("%s LIMIT %d, %d", $query_rsPri, $startRow_rsPri, $maxRows_rsPri);
$rsPri = mysql_query($query_limit_rsPri, 
$Conexion) or die(mysql_error());
$row_rsPri = mysql_fetch_assoc($rsPri);

/*** STOCK SECUNDARIO ***/
$maxRows_rsCXP = 999;
$pageNum_rsCXP = 0;
if (isset($_GET['pageNum_rsCXP'])) {
  $pageNum_rsCXP = $_GET['pageNum_rsCXP'];
}
$startRow_rsCXP = $pageNum_rsCXP * $maxRows_rsCXP;

mysql_select_db($database_Conexion, $Conexion);
$query_rsCXP = "SELECT * FROM
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
(select
    p.ID,
    REPLACE(REPLACE(REPLACE(REPLACE(TRIM(LCASE(p.post_title)), ' ', '-'), '''', ''), '’', ''), '.', '') AS titulo,
    max( CASE WHEN pm.meta_key = 'idioma' and p.ID = pm.post_id THEN pm.meta_value END ) as idioma,
    max( CASE WHEN pm.meta_key = 'consola' and  p.ID = pm.post_id THEN pm.meta_value END ) as cons,
    max( CASE WHEN pm.meta_key = 'peso' and p.ID = pm.post_id THEN pm.meta_value END ) as peso,
	max( CASE WHEN pm.meta_key = '_price' and p.ID = pm.post_id THEN pm.meta_value END ) as price,
	round(max( CASE WHEN pm.meta_key = '_max_variation_regular_price' and p.ID = pm.post_id THEN pm.meta_value END )) as max_reg_price,
    round(max( CASE WHEN pm.meta_key = '_max_variation_price' and p.ID = pm.post_id THEN pm.meta_value END )) as max_price,
    round(max( CASE WHEN pm.meta_key = '_min_variation_regular_price' and p.ID = pm.post_id THEN pm.meta_value END )) as min_reg_price,
	round(max( CASE WHEN pm.meta_key = '_min_variation_price' and p.ID = pm.post_id THEN pm.meta_value END )) as min_price,
	round(max( CASE WHEN pm.meta_key = '_price' and p.ID = pm.post_id THEN pm.meta_value END )) as new_max_price,
	round(min( CASE WHEN pm.meta_key = '_price' and p.ID = pm.post_id THEN pm.meta_value END )) as new_min_price,
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
ORDER BY `cons` ASC, `titulo` ASC) as web
ON final.producto = web.titulo
WHERE libre > 0 AND cons = 'ps4'";
$query_limit_rsCXP = sprintf("%s LIMIT %d, %d", $query_rsCXP, $startRow_rsCXP, $maxRows_rsCXP);
$rsCXP = mysql_query($query_limit_rsCXP, 
$Conexion) or die(mysql_error());
$row_rsCXP = mysql_fetch_assoc($rsCXP);
?>
<!DOCTYPE html>
<html lang="es"><!-- InstanceBegin template="/Templates/db.dwt.php" codeOutsideHTMLIsLocked="false" -->
  <head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <!-- The above 3 meta tags *must* come first in the head; any other head content must come *after* these tags -->
    <meta name="description" content="base de datos">
    <meta name="author" content="vic">
    <link rel="icon" href="favicon.ico">
	
<!-- InstanceBeginEditable name="doctitle" -->
    <title><?php $titulo = 'Catalogo'; echo $titulo; ?></title>
<!-- InstanceEndEditable -->

	  
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

    <!-- InstanceBeginEditable name="head" -->
    <!-- antes que termine head-->
<!-- InstanceEndEditable -->
  </head>

  <body>
  <?php include('_barra_nav.php'); ?>

    <div class="container">
	<h1><?php echo $titulo; ?></h1>
    <!-- InstanceBeginEditable name="body" -->
    <div class="col-xs-12 col-sm-6 col-md-6">
    <h4>Secundarios</h4>
	<table class="table table-striped table-responsive" border="0" cellpadding="0" cellspacing="5">
      <tr>
        <th width="50">Cover</th>
        <th>Titulo</th>
        <th title="Stk">Stk</th>
        <th title="Precio">Precio</th>
      </tr>
      <?php do { ?>
      <tr>
        <td><img class="img-rounded" width="50" id="image-swap" src="/img/productos/<?php echo $row_rsCXP['consola']."/".$row_rsCXP['titulo'].".jpg";?>"alt="" /></td>
        <td title="<?php echo $row_rsCXP['titulo']; ?>"><?php echo $row_rsCXP['titulo']; ?></td>
        <td><span class="badge badge-<?php if($row_rsCXP['libre'] < 3): echo 'danger'; else: echo 'success'; endif;?> pull-left"><?php if($row_rsCXP['libre'] < 3): echo $row_rsCXP['libre']; else: echo '+2'; endif;?></span></td>
        <td><span class="badge badge-info pull-left">$ 
		<?php echo round($row_rsCXP['new_min_price'] * 0.88);?></span></td>
        
      </tr>   
      <?php } while ($row_rsCXP = mysql_fetch_assoc($rsCXP)); ?>     
    </table>
    </div>
    <div class="col-xs-12 col-sm-6 col-md-6">
    <h4>Primarios</h4>
	<table class="table table-striped table-responsive" border="0" cellpadding="0" cellspacing="5">
      <tr>
        <th width="50">Cover</th>
        <th>Titulo</th>
        <th title="Stk">Stk</th>
        <th title="Precio">Precio</th>
      </tr>
      <?php do { ?>
      <tr>
        <td><img class="img-rounded" width="50" id="image-swap" src="/img/productos/<?php echo $row_rsPri['consola']."/".$row_rsPri['titulo'].".jpg";?>"alt="" /></td>
        <td title="<?php echo $row_rsPri['titulo']; ?>"><?php echo $row_rsPri['titulo']; ?></td>
        <td><span class="badge badge-<?php if(($row_rsPri['q_stk'] - $row_rsPri['q_vta_pri']) < 3): echo 'danger'; else: echo 'success'; endif;?> pull-left"><?php if(($row_rsPri['q_stk'] - $row_rsPri['q_vta_pri']) < 3): echo ($row_rsPri['q_stk'] - $row_rsPri['q_vta_pri']); else: echo '+2'; endif;?></span></td>
        <td><span class="badge badge-info pull-left">$ 
		<?php echo round($row_rsPri['new_max_price'] * 0.88);?></span></td>
        
      </tr>   
      <?php } while ($row_rsPri = mysql_fetch_assoc($rsPri)); ?>     
    </table>
    <h4>Play 3</h4>
	<table class="table table-striped table-responsive" border="0" cellpadding="0" cellspacing="5">
      <tr>
        <th width="50">Cover</th>
        <th>Titulo</th>
        <th title="Stk">Stk</th>
        <th title="Precio">Precio</th>
      </tr>
      <?php do { ?>
      <tr>
        <td><img class="img-rounded" width="50" id="image-swap" src="/img/productos/<?php echo $row_rsPs3['consola']."/".$row_rsPs3['titulo'].".jpg";?>"alt="" /></td>
        <?php $cantidad = ($row_rsPs3['Q_Stock'] + ($row_rsPs3['Q_stk2']*2));?>
        <td title="<?php echo $row_rsPs3['titulo']; ?>"><?php echo $row_rsPs3['titulo']; ?></td>
        <td><span class="badge badge-<?php if($cantidad < 3): echo 'danger'; else: echo 'success'; endif;?> pull-left"><?php if($cantidad < 3): echo $cantidad; else: echo '+2'; endif;?></span></td>
        <td><span class="badge badge-info pull-left">$ 
		<?php if ($row_rsPs3['price'] < $row_rsPs3['reg_price']): echo round($row_rsPs3['price']); else: echo round($row_rsPs3['reg_price'] * 0.88); endif;?></span></td>
        
      </tr>   
      <?php } while ($row_rsPs3 = mysql_fetch_assoc($rsPs3)); ?>     
    </table>
    </div>
     <!--/row-->
     <!-- InstanceEndEditable -->
    </div><!--/.container-->
	<!-- Bootstrap core JavaScript
    ================================================== -->
	<!-- Placed at the end of the document so the pages load faster -->
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/1.12.4/jquery.min.js"></script>
	<script src="assets/js/docs.min.js"></script>
    <script src="js/bootstrap.min.js"></script>
    <script src="https://cdn.jsdelivr.net/clipboard.js/1.5.12/clipboard.min.js"></script>
	<script>new Clipboard('.btn-copiador');</script>
    <!-- Activar popover -->
    <script>
	$(document).ready(function(){
		$('[data-toggle="popover"]').popover();
	});
	</script>
    <!-- InstanceBeginEditable name="script-extras" -->
    <!-- extras de script y demás yerbas -->
<!-- InstanceEndEditable -->
  </body>
  
<!-- InstanceEnd --></html>
<?php
mysql_free_result($rsUsuarios);

mysql_free_result($rsCXP);

mysql_free_result($rsPri);
?>
