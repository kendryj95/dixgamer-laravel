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

mysql_select_db($database_Conexion, $Conexion);
$query_rsCXP = "SELECT ID, titulo, consola, COUNT(*) AS q_stock, (COUNT(*) - SUM(slotprimario)) as s_pri, (COUNT(*) - SUM(slotsecundario)) as s_sec, round(AVG(costo),0) AS costoprom, SUM(costo) AS costototal, Min(costo) AS costomin, Max(costo) AS costomax, SUM(cantidadventa) AS q_venta, SUM(ingresototal) AS ing_total, SUM(comisiontotal) AS com_total
FROM stock
LEFT JOIN
(SELECT stock_id, COUNT(case when slot = 'Primario' then 1 else null end) AS slotprimario, COUNT(case when slot = 'Secundario' then 1 else null end) AS slotsecundario, COUNT(*) AS cantidadventa, SUM(precio) AS ingresototal, SUM(comision) AS comisiontotal 
FROM ventas
LEFT JOIN (select ventas_id, sum(precio) as precio, sum(comision) as comision FROM ventas_cobro GROUP BY ventas_id) as ventas_cobro ON ventas.ID = ventas_cobro.ventas_id
GROUP BY stock_id) AS vtas
ON stock.ID = vtas.stock_id
GROUP BY consola, titulo
ORDER BY q_venta DESC";
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
    <title><?php $titulo = 'Balance por Productos'; echo $titulo; ?></title>
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
	<table class="table table-striped" border="0" cellpadding="0" cellspacing="5">
      <tr>
		<th width="50"></th>
        <th width="50">Cover</th>
        <th>Titulo</th>
        <th title="Compras">C</th>
        <th title="Stock disponible">Stk</th>
        <th>Total</th>
        <th class="text-muted">Prom</th>
        <th class="text-muted">Min</th>
        <th class="text-muted">Max</th>
        <th title="Cantidad Ventas">Vta</th>
        <th title="Ingreso">Ing</th>
        <th title="Comisiones">Com</th>
        <th title="Ganancia Realizada">Real</th>
        <th class="text-muted" title="Ganancia Proyectada">Proy</th>
      </tr>
      <?php $compra = 0; $stock_dispo = 0; $costoprom = 0; $costototal = 0; $q_venta = 0; $ing_total = 0; $com_total = 0; $gcia_real = 0; $gcia_proyec = 0; $i = 1; do { ?>
      <tr>
        <td><?php echo $i; ?></td>
		  <td><img class="img-rounded" width="50" id="image-swap" src="/img/productos/<?php echo $row_rsCXP['consola']."/".$row_rsCXP['titulo'].".jpg";?>"alt="" /></td>
        <td title="<?php echo $row_rsCXP['titulo']; ?> (<?php echo $row_rsCXP['consola']; ?>)"><?php echo str_replace('-', ' ', $row_rsCXP['titulo']);?> (<?php echo $row_rsCXP['consola']; ?>)</td>
        <td><?php echo $row_rsCXP['q_stock']; ?></td>
        <td>
        <?php if($row_rsCXP['consola'] == 'ps3'): ?><?php $stock_disponible = ((4 * $row_rsCXP['q_stock']) - $row_rsCXP['q_venta']); ?><?php endif;?>
        <?php if(($row_rsCXP['consola'] == 'ps4') || ($row_rsCXP['titulo'] == 'plus-12-meses-slot')): ?><?php $stock_disponible = ((2 * $row_rsCXP['q_stock']) - $row_rsCXP['q_venta']); ?> <small>(<?php echo $row_rsCXP['s_pri']; ?> y <?php echo $row_rsCXP['s_sec']; ?>)</small><?php endif;?>
        <?php if(($row_rsCXP['consola'] !== 'ps4') && ($row_rsCXP['consola'] !== 'ps3') && ($row_rsCXP['titulo'] !== 'plus-12-meses-slot')): ?><?php $stock_disponible = ((1 * $row_rsCXP['q_stock']) - $row_rsCXP['q_venta']); ?><?php endif;?>
        <?php if($stock_disponible > 0):?><?php echo $stock_disponible; ?><?php endif;?>
        </td>
        <td>$ <?php echo round($row_rsCXP['costototal']); ?></td>
        <td class="text-muted"><?php echo round($row_rsCXP['costoprom']); ?></td>
		<td class="text-muted"><?php echo round($row_rsCXP['costomin']); ?></td>
        <td class="text-muted"><?php echo round($row_rsCXP['costomax']); ?></td>
        <td><?php echo $row_rsCXP['q_venta']; ?></td>
        <td>$ <?php echo round($row_rsCXP['ing_total']); ?></td>
        <td>$ <?php echo round($row_rsCXP['com_total']); ?></td>
        <td>$ <?php $calc_gananciareal = round($row_rsCXP['ing_total'] - $row_rsCXP['com_total'] - $row_rsCXP['costototal']); 
        echo $calc_gananciareal; ?>
        </td>
		  <?php // si no se vendió ni una sola vez tdv hago como si tuviese una sola venta
		  if ($row_rsCXP['q_venta'] > 0): $q_vtas = $row_rsCXP['q_venta']; else: $q_vtas =  1; endif;?>
        <td class="text-muted">$ <?php if($row_rsCXP['consola'] == 'ps3'): ?><?php $calc_gananciaproyec = round( 4 * $row_rsCXP['q_stock'] * ((($row_rsCXP['ing_total'] - $row_rsCXP['com_total']) / $q_vtas) - ($row_rsCXP['costoprom'] / 4))); ?><?php endif;?>
        <?php if(($row_rsCXP['consola'] == 'ps4') || ($row_rsCXP['titulo'] == 'plus-12-meses-slot')): ?><?php $calc_gananciaproyec = round( 2 * $row_rsCXP['q_stock'] * ((($row_rsCXP['ing_total'] - $row_rsCXP['com_total']) / $q_vtas) - ($row_rsCXP['costoprom'] / 2))); ?><?php endif;?>
        <?php if(($row_rsCXP['consola'] !== 'ps4') && ($row_rsCXP['consola'] !== 'ps3') && ($row_rsCXP['titulo'] !== 'plus-12-meses-slot')): ?>
		<?php $calc_gananciaproyec = round( 1 * $row_rsCXP['q_stock'] * ((($row_rsCXP['ing_total'] - $row_rsCXP['com_total']) / $q_vtas ) - ($row_rsCXP['costoprom'] / 1))); ?><?php endif;?>
        <?php if($calc_gananciaproyec < 1):?><?php $calc_gananciaproyec = ($stock_disponible * 35);?><?php endif;?>
		<?php echo round($calc_gananciaproyec); ?>
        </td>
      </tr>   
      <?php 
      $compra = $compra + $row_rsCXP['q_stock'];
      $stock_dispo = $stock_dispo + $stock_disponible;
      $costoprom = $costoprom + $row_rsCXP['costoprom'];
      $costototal = $costototal + $row_rsCXP['costototal'];
      $q_venta = $q_venta + $row_rsCXP['q_venta'];
      $ing_total = $ing_total + $row_rsCXP['ing_total'];
      $com_total = $com_total + $row_rsCXP['com_total'];
      $gcia_real = $gcia_real + $calc_gananciareal;
      $gcia_proyec = $gcia_proyec + $calc_gananciaproyec;
	  $i++;
      } while ($row_rsCXP = mysql_fetch_assoc($rsCXP)); ?>     
      <tr>
        <th></th>
		  <th></th>
        <th></th>
        <th><?=$compra?></th>
        <th><?=$stock_dispo?></th>
        <th><?=$costototal?></th>
        <th class="text-muted"><?=$costoprom?></th>
        <th></th>
        <th></th>
        <th><?=$q_venta?></th>
        <th><?=round($ing_total)?></th>
        <th><?=round($com_total)?></th>
        <th><?=$gcia_real?></th>
        <th class="text-muted"><?=$gcia_proyec?></th>
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
