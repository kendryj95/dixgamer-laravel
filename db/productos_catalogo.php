<?php require_once('../Connections/Conexion.php'); ?>
<?php

$MM_authorizedUsers = "Adm,Vendedor,Asistente";
$MM_donotCheckaccess = "false";
require_once('_autentificacion.php');
?>
<?php 
/*** SUPER QUERY DE STOCK ***/
$maxRows_rsCXP = 9999;
$pageNum_rsCXP = 0;
if (isset($_GET['pageNum_rsCXP'])) {
  $pageNum_rsCXP = $_GET['pageNum_rsCXP'];
}
$startRow_rsCXP = $pageNum_rsCXP * $maxRows_rsCXP;

mysql_select_db($database_Conexion, $Conexion);
$query_rsCXP = "SELECT ID, titulo, consola, max(slot) as slot, idioma, peso, max(precio) as precio, ml_url FROM (select
    p.ID,
    REPLACE(REPLACE(REPLACE(REPLACE(TRIM(LCASE(p.post_title)), ' ', '-'), '''', ''), '’', ''), '.', '') AS titulo,
    max( CASE WHEN pm.meta_key = 'consola' and p.ID = pm.post_id THEN pm.meta_value END ) as consola,
    '' as slot,
	max( CASE WHEN pm.meta_key = 'idioma' and p.ID = pm.post_id THEN pm.meta_value END ) as idioma,
    max( CASE WHEN pm.meta_key = 'peso' and p.ID = pm.post_id THEN pm.meta_value END ) as peso,
	max( CASE WHEN pm.meta_key = '_price' and p.ID = pm.post_id THEN pm.meta_value END ) as precio,
	max( CASE WHEN pm.meta_key = 'ml_url' and p.ID = pm.post_id THEN pm.meta_value END ) as ml_url
from
    cbgw_posts as p
LEFT JOIN
    cbgw_postmeta as pm
ON
   p.ID = pm.post_id
where
    post_type = 'product' and
    post_status = 'publish'
group BY
	p.ID
UNION ALL
SELECT post_parent as ID, titulo, consola, GROUP_CONCAT(slot) as slot, idioma, peso, precio, ml_url FROM (select
    p.ID,
    p.post_parent,
    SUBSTRING_INDEX(SUBSTRING_INDEX(REPLACE(REPLACE(REPLACE(REPLACE(TRIM(LCASE(p.post_title)), ' ', '-'), '''', ''), '’', ''), '.', ''),'-&ndash;',1),'---',1) as titulo,
    max( CASE WHEN pm2.meta_key = 'consola' and  p.post_parent = pm2.post_id THEN pm2.meta_value END ) as consola,
    max( CASE WHEN pm.meta_key = 'attribute_pa_slot' and p.ID = pm.post_id THEN pm.meta_value END ) as slot,
	max( CASE WHEN pm2.meta_key = 'idioma' and p.post_parent = pm2.post_id THEN pm2.meta_value END ) as idioma,
    max( CASE WHEN pm2.meta_key = 'peso' and p.post_parent = pm2.post_id THEN pm2.meta_value END ) as peso,
	max( CASE WHEN pm.meta_key = '_price' and p.ID = pm.post_id THEN pm.meta_value END ) as precio,
	max( CASE WHEN pm2.meta_key = 'ml_url' and p.post_parent = pm2.post_id THEN pm2.meta_value END ) as ml_url
from
    cbgw_posts as p
LEFT JOIN
    cbgw_postmeta as pm
ON
   p.ID = pm.post_id
LEFT JOIN
	cbgw_postmeta as pm2
ON
	p.post_parent = pm2.post_id
where
    post_type = 'product_variation' and
    post_status = 'publish'
GROUP BY
	p.ID
ORDER BY consola ASC, titulo ASC, slot ASC) as conslot
GROUP BY post_parent) as listado
GROUP BY ID, precio
ORDER BY consola ASC, titulo ASC, slot ASC";
$query_limit_rsCXP = sprintf("%s LIMIT %d, %d", $query_rsCXP, $startRow_rsCXP, $maxRows_rsCXP);
$rsCXP = mysql_query($query_limit_rsCXP, $Conexion) or die(mysql_error());
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
    <title><?php $titulo = 'Catalogo de Productos'; echo $titulo; ?></title>
	  <style>
	.highlight {font-size: 1.2em !important; background-color: burlywood !important; border: 2px solid;}

	</style>
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
	<!-- Automatizador de stock en website -->

	<table class="table table-striped table-responsive" border="0" cellpadding="0" cellspacing="5">
      <tr>
        <th width="50">Cover</th>
        <th>Titulo</th>
        <th>Consola</th>
		 <th>Peso</th>
		  <th>Idioma</th>
		  <th>Precio</th>
      </tr>
      <?php $i = 0; do { ?>
      <?php $ID = $row_rsCXP['ID'];?>
      <tr>
        <td id="<?php echo $i;?>"><img class="img-rounded" width="50" id="image-swap" src="/img/productos/<?php echo $row_rsCXP['consola']."/".$row_rsCXP['titulo'].".jpg";?>"alt="" /></td>
        <td title="<?php echo $row_rsCXP['titulo']; ?> (<?php echo $row_rsCXP['consola']; ?>)"><p style="font-weight:bold;"><?php echo str_replace("-", " ", $row_rsCXP['titulo']); ?> <?php if (strpos($row_rsCXP['slot'], 'primario') !== false): echo '<span class="label label-primary" style="opacity:0.4">1°</span>'; endif; ?> <?php if ($row_rsCXP['slot'] == 'secundario'): echo '<span class="label label-danger" style="opacity:0.4">2°</span>'; endif; ?></p>
		<p><div style="position:absolute; top:0; left:-500px;">
			<textarea id="link-web-<?php echo $i;?>" type="text" rows="1" cols="1"><?php echo 'https://dixgamer.com/?post_type=product&p=' . $row_rsCXP['ID']; ?></textarea>
			<textarea id="link-ml-<?php echo $i;?>" type="text" rows="1" cols="1"><?php echo $row_rsCXP['ml_url']; ?></textarea>
			</div>
			<?php if ($row_rsCXP['slot'] != 'secundario'): ?><a href="#<?php echo ($i - 1);?>" class="btn-copiador btn-xs btn-info label" data-clipboard-target="#link-web-<?php echo $i;?>">Link Web</a><?php endif;?>
			<a href="#<?php echo ($i - 1);?>" class="btn-copiador btn-xs btn-warning label" data-clipboard-target="#link-ml-<?php echo $i;?>">Link ML</a></p>  
		</td>
		<td><?php echo $row_rsCXP['consola']; ?></td>
        <td><?php if($row_rsCXP['peso'] > 0.00): echo $row_rsCXP['peso'] . ' GB'; endif; ?></td>
		<td><?php echo $row_rsCXP['idioma']; ?></td>
		<td>$ <?php echo $row_rsCXP['precio']; ?></td>
       </tr>   
      <?php ++$i; } while ( $row_rsCXP = mysql_fetch_assoc($rsCXP)); ?>     
    </table>
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
<script type="text/javascript">
$("tr").click(function() {
    var selected = $(this).hasClass("highlight");
    if(!selected)
			$("tr").removeClass("highlight");
            $(this).addClass("highlight");
});
</script>
    <script type="text/javascript">
		(function(){
			new Clipboard('#copy-button');
		})();
	</script>
<!-- InstanceEndEditable -->
  </body>
  
<!-- InstanceEnd --></html>
<?php
mysql_free_result($rsUsuarios);

mysql_free_result($rsCXP);
?>

