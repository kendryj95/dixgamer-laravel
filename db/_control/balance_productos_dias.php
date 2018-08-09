<?php require_once('../../Connections/Conexion.php'); ?>
<?php
$MM_authorizedUsers = "Adm";
$MM_donotCheckaccess = "false";
require_once('../_autentificacion.php');
?>
<?php 
/*** SUPER QUERY DE STOCK ***/
$maxRows_rsCXP = 999;
$pageNum_rsCXP = 0;
if (isset($_GET['pageNum_rsCXP'])) {
  $pageNum_rsCXP = $_GET['pageNum_rsCXP'];
}
$startRow_rsCXP = $pageNum_rsCXP * $maxRows_rsCXP;

$dias = "7";
if (isset($_GET['dias'])) {
  $dias = (get_magic_quotes_gpc()) ? $_GET['dias'] : addslashes($_GET['dias']);
}
mysql_select_db($database_Conexion, $Conexion);
$query_rsCXP = "SELECT vtas.*, stk.Q_Stock FROM
(SELECT ID, AVG(costo) as costo, AVG(costo_usd) as costo_usd, titulo, consola, IFNULL(SUM(cantidadventa),0) AS q_venta, IFNULL(SUM(ingresototal),0) AS ing_total
FROM stock
RIGHT JOIN
(SELECT stock_id, COUNT(*) AS cantidadventa, SUM(precio) AS ingresototal
FROM ventas
LEFT JOIN (select ventas_id, sum(precio) as precio FROM ventas_cobro WHERE ventas_cobro.Day >= DATE(NOW() - INTERVAL '$dias' DAY) GROUP BY ventas_id) as ventas_cobro ON ventas.ID = ventas_cobro.ventas_id
WHERE ventas.Day >= DATE(NOW() - INTERVAL '$dias' DAY)
GROUP BY stock_id) AS vtas
ON stock.ID = vtas.stock_id
GROUP BY consola, titulo
ORDER BY q_venta DESC, consola ASC, titulo ASC) as vtas
LEFT JOIN

(SELECT titulo, consola, SUM(Q_Stock) as Q_Stock FROM
(SELECT
ID_stk, titulo, consola, Q_Stock
FROM
(SELECT ID AS ID_stk, titulo, consola, cuentas_id AS stk_ctas_id, ID_reseteo AS ID_reset, r_cuentas_id AS reset_ctas_id, dayreseteo AS dayreset, reset.Q_reseteado AS Q_reset, ID_vta, Q_vta, dayvta, Q_vta_pri, Q_vta_sec, COUNT(*) AS Q_Stock
FROM stock
LEFT JOIN
(SELECT ID AS ID_reseteo, cuentas_id AS r_cuentas_id, COUNT(*) AS Q_reseteado, MAX(Day) AS dayreseteo
FROM reseteo
GROUP BY cuentas_id
ORDER BY ID DESC) AS reset
ON cuentas_id = r_cuentas_id
LEFT JOIN
(SELECT ventas.ID as ID_vta, stock_id, SUM(case when slot = 'Primario' then 1 else null end) AS Q_vta_pri, SUM(case when slot = 'Secundario' then 1 else null end) AS Q_vta_sec, MAX(estado) AS vta_estado, COUNT(*) AS Q_vta, Day AS dayvta
FROM ventas
GROUP BY stock_id
ORDER BY vta_estado DESC) AS vendido
ON ID = stock_id
WHERE (consola = 'ps4' OR titulo = 'plus-12-meses-slot') AND (Q_vta_pri IS NULL)
GROUP BY consola, titulo
ORDER BY consola, titulo, ID DESC) as ps4pri
UNION ALL
SELECT ID_stk, titulo, consola, Q_Stock
FROM
(SELECT ID AS ID_stk, titulo, consola, cuentas_id AS stk_ctas_id, ID_reseteo AS ID_reset, r_cuentas_id AS reset_ctas_id, dayreseteo AS dayreset, reset.Q_reseteado AS Q_reset, ID_vta, Q_vta, dayvta, Q_vta_pri, Q_vta_sec, COUNT(*) AS Q_Stock
FROM stock
LEFT JOIN
(SELECT ID AS ID_reseteo, cuentas_id AS r_cuentas_id, COUNT(*) AS Q_reseteado, MAX(Day) AS dayreseteo
FROM reseteo
GROUP BY cuentas_id
ORDER BY ID DESC) AS reset
ON cuentas_id = r_cuentas_id
LEFT JOIN

(SELECT ventas.ID as ID_vta, stock_id, SUM(case when slot = 'Primario' then 1 else null end) AS Q_vta_pri, SUM(case when slot = 'Secundario' then 1 else null end) AS Q_vta_sec, 
 COUNT(*) AS Q_vta, Day AS dayvta
FROM ventas
GROUP BY stock_id) AS vendido
ON ID = stock_id
WHERE (consola = 'ps4' OR titulo = 'plus-12-meses-slot') AND (Q_vta_sec IS NULL)
GROUP BY  consola, titulo
ORDER BY consola, titulo, ID DESC) AS ps4sec
UNION ALL
SELECT ID_stk, titulo, consola, Q_Stock
FROM
(SELECT ID_stk, titulo, consola, stk_ctas_id, dayreset, Q_reset, days_from_reset, Q_vta, SUM(Q_Stock) AS Q_Stock FROM (SELECT ID AS ID_stk, titulo, consola, cuentas_id AS stk_ctas_id, ID_reseteo AS ID_reset, r_cuentas_id AS reset_ctas_id, dayreseteo AS dayreset, reset.Q_reseteado AS Q_reset, DATEDIFF(NOW(), dayreseteo) AS days_from_reset, ID_vta, Q_vta, dayvta, ((2 + (IFNULL(Q_reseteado, 0) * 2)) - IFNULL(Q_vta, 0)) AS Q_Stock
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
WHERE consola = 'ps3' AND (((Q_vta IS NULL) OR (Q_vta < '2')) OR (((Q_vta >= '2') AND (Q_reseteado = FLOOR(Q_vta/2)))))
GROUP BY ID
ORDER BY consola, titulo, ID DESC) AS consulta
GROUP BY consola, titulo
ORDER BY consola, titulo, ID_stk) AS ps3
UNION ALL
SELECT 
ID_stk, titulo, consola, Q_Stock
FROM
(SELECT ID AS ID_stk, titulo, consola, cuentas_id AS stk_ctas_id, ID_vtas, Q_vta, dayvta, COUNT(*) AS Q_Stock
FROM stock
LEFT JOIN
(SELECT ventas.ID as ID_vtas, stock_id, slot, COUNT(*) AS Q_vta, Day AS dayvta
FROM ventas
GROUP BY stock_id
ORDER BY ID DESC) AS vendido
ON ID = stock_id
WHERE (consola != 'ps4') AND (consola != 'ps3') AND (Q_vta IS NULL) AND (titulo != 'plus-12-meses-slot')
GROUP BY consola, titulo
ORDER BY consola, titulo DESC) as psn) as todo 
GROUP BY consola, titulo) as stk
ON vtas.titulo = stk.titulo and vtas.consola = stk.consola";
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
    <link rel="icon" href="../favicon.ico">
	
<!-- InstanceBeginEditable name="doctitle" -->
    <title><?php $titulo = 'Balance por Productos ' . $dias . ' días' ;  echo $titulo; ?></title>
<!-- InstanceEndEditable -->

	  
    <!-- Font Awesome style desde mi servidor -->
    <link href="../css/font-awesome.min.css" rel="stylesheet">
    
    <!-- link a mi css -->
    <link href="../css/bootstrap.css" rel="stylesheet">
    
    <!-- Bootstrap SITE CSS -->
    <link href="../css/site.css" rel="stylesheet">

    <!-- Custom styles for this template -->
    <link href="../css/offcanvas.css" rel="stylesheet">
    
	<!-- 2017-12-30 Agrego nuevo css de BootFLAT --> 
    <link href="../css/bootflat.css" rel="stylesheet">
	  
	<!-- Estilo personalizado por mi -->
	<link href="../css/personalizado.css" rel="stylesheet">
	 	
    <!--- BootFLAT core CSS 
    <link href="../db/css/site.min.css" rel="stylesheet">
	-->

    <!-- InstanceBeginEditable name="head" -->
    <!-- antes que termine head-->
<!-- InstanceEndEditable -->
  </head>

  <body>
  <?php include('../_barra_nav.php'); ?>

    <div class="container">
	<h1><?php echo $titulo; ?></h1>
    <!-- InstanceBeginEditable name="body" -->
	<a class="btn btn-default btn-sm" href="balance_productos_dias.php?dias=3" title="Filtrar" style="margin:5px 0 0 0;">3 días</a>
	<a class="btn btn-default btn-sm" href="balance_productos_dias.php?dias=7" title="Filtrar" style="margin:5px 0 0 0;">7 días</a>
	<a class="btn btn-default btn-sm" href="balance_productos_dias.php?dias=14" title="Filtrar" style="margin:5px 0 0 0;">14 días</a>
	<a class="btn btn-default btn-sm" href="balance_productos_dias.php?dias=28" title="Filtrar" style="margin:5px 0 0 0;">28 días</a>
	<a class="btn btn-default btn-sm" href="balance_productos_dias.php?dias=60" title="Filtrar" style="margin:5px 0 0 0;">60 días</a>
	<a class="btn btn-default btn-sm" href="balance_productos_dias.php?dias=90" title="Filtrar" style="margin:5px 0 0 0;">90 días</a>
	<a class="btn btn-default btn-sm" href="balance_productos_dias.php?dias=180" title="Filtrar" style="margin:5px 0 0 0;">180 días</a>
	<a class="btn btn-default btn-sm" href="balance_productos_dias.php?dias=360" title="Filtrar" style="margin:5px 0 0 0;">360 días</a>
	<table class="table table-striped" border="0" cellpadding="0" cellspacing="5">
      <tr>
		<th width="25"></th>
        <th width="50">Cover</th>
        <th width="150">Titulo</th>

        <th width="50" title="Cantidad Ventas">Vta</th>
		<th width="30" title="Precio Promedio">Precio Prom</th>
		  
        <th width="30" title="Costo Promedio">Costo Prom</th>
		  
		<th width="30" title="Stock en unidades">Stk</th>
		  
		<th width="100" title="Proyeccion 1 mes">Proy Compra<br>28 d</th>
		<th width="100" title="Proyeccion 2 meses">Proy Compra<br>2 M</th>
		<th width="100" title="Proyeccion 3 meses">Proy Compra<br>3 M</th>
		<th width="1"></th>


      </tr>
      <?php $q_venta = 0; $i = 1; do { ?>

      <tr>
        <td><?php echo $i; ?></td>
		  <td><img class="img-rounded" width="50" id="image-swap" src="/img/productos/<?php echo $row_rsCXP['consola']."/".$row_rsCXP['titulo'].".jpg";?>"alt="" /></td>
		  
        <td>
			<?php echo str_replace('-', ' ', $row_rsCXP['titulo']);?> (<?php echo $row_rsCXP['consola']; ?>)
		 </td>
        
		  <td><span class="badge badge-default"><?php echo $row_rsCXP['q_venta']; ?></span></td>
		  
		  <?php $cost = $row_rsCXP['costo']; $con = $row_rsCXP['consola']; 
			if($cost < 0.1): $cost = 1;	endif;
			if($con == "ps3"): $cost = ($cost / 4);
			elseif($con == "ps4"): $cost = ($cost / 2);
     				  
			else: $cost = $cost;
			endif; 
									  
			if ($row_rsCXP['ing_total'] > 0): $ingresomedio = round($row_rsCXP['ing_total']/$row_rsCXP['q_venta']); else: $ingresomedio = 0; endif;
									  
			$rend = round((($ingresomedio/$cost)-1)*100);
			if ($rend > 200): $rend = "+200"; endif;
			?>	
		  
        <td><p class="badge badge-success">$ <?php echo $ingresomedio;?></p><br>
		<p class="label label-default" style="opacity:0.4;"><?php echo $rend; ?>%</p>
		</td>
		  
        <td><p class="badge badge-danger" style="opacity:0.85;">$ <?php echo round($cost);?></p><br>
		<p class="label label-default" style="opacity:0.4;"><?php echo round($row_rsCXP['costo_usd']); ?> usd</p>
		</td>
		  
		<td><span class="badge badge-normal"><?php $stk = $row_rsCXP['Q_Stock']; $con = $row_rsCXP['consola']; 
			if($con == "ps3"): $stk = ($stk / 4);
			elseif($con == "ps4"): $stk = ($stk / 2);
			else: $stk = $stk;
			endif; 
			echo round($stk);
		?></span>
			</td>
		<?php //calculo de proyeccion para compras de stock  
			$con = $row_rsCXP['consola']; 
			if($con == "ps3"): $div = 4;
			elseif($con == "ps4"): $div = 2;
			else: $div = 1;
			endif;?>
		<td width="1">
			<?php $rdo1 = round((($row_rsCXP['q_venta']*(28/$dias))/$div) - $stk); ?>
			<?php if($rdo1 < 0): $color1="warning"; else: $color1="info"; endif; ?>
			<span class="badge badge-<?php echo $color1;?>"><?php echo $rdo1;?></span></td>
		<td width="1">
			<?php $rdo2 = round((($row_rsCXP['q_venta']*(60/$dias))/$div) - $stk); ?>
			<?php if($rdo2 < 0): $color2="warning"; else: $color2="info"; endif; ?>
			<span class="badge badge-<?php echo $color2;?>"><?php echo $rdo2;?></span></td>
		<td width="1">
			<?php $rdo3 = round((($row_rsCXP['q_venta']*(90/$dias))/$div) - $stk); ?>
			<?php if($rdo3 < 0): $color3="warning"; else: $color3="info"; endif; ?>
			<span class="badge badge-<?php echo $color3;?>"><?php echo $rdo3;?></span></td>
		
		  <td width="1"></td>

      </tr>   
      <?php 
      $q_venta = $q_venta + $row_rsCXP['q_venta'];
	  $i++;
      } while ($row_rsCXP = mysql_fetch_assoc($rsCXP)); ?>     
      <tr>
        <th></th>
		  <th></th>
        <th></th>

        <th><?=$q_venta?></th>
        <th></th>
        <th></th>
		   <th></th>
		  <th></th>
      </tr> 
    </table>
     <!--/row-->
     <!-- InstanceEndEditable -->
    </div><!--/.container-->
	<!-- Bootstrap core JavaScript
    ================================================== -->
	<!-- Placed at the end of the document so the pages load faster -->
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/1.12.4/jquery.min.js"></script>
	<script src="../assets/js/docs.min.js"></script>
    <script src="../js/bootstrap.min.js"></script>
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
?>
