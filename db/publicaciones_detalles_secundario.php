<?php require_once('../Connections/Conexion.php'); ?>
<?php
$MM_authorizedUsers = "Adm,Vendedor";
$MM_donotCheckaccess = "false";
require_once('_autentificacion.php');
?>
<?php 
$nombresito = $_SESSION['MM_Username'];
mysql_select_db($database_Conexion, $Conexion);
$query_rsUsuarios = sprintf("SELECT * FROM usuarios WHERE Nombre = '$nombresito' ORDER BY Nombre ASC",$nombresito);
$rsUsuarios = mysql_query($query_rsUsuarios, $Conexion) or die(mysql_error());
$row_rsUsuarios = mysql_fetch_assoc($rsUsuarios);
$totalRows_rsUsuarios = mysql_num_rows($rsUsuarios);


/***     STOCK     ***/
mysql_select_db($database_Conexion, $Conexion);
// Cancelo el query anterior que revisaba el stock, incorporo el nuevo que consulta los titulos sin necesidad que exista en stock
//Uso mismo select para listar titulos en titulos.php

//$query_rsStock = sprintf("SELECT stock.titulo AS Tit, stock.consola, titulos.descripcion, ps3_peso, ps4_peso, idioma FROM stock LEFT JOIN titulos ON stock.titulo = titulos.titulo GROUP BY stock.titulo, stock.consola ORDER BY stock.consola, stock.titulo DESC");

/*** QUERY UTILIZADO HASTA EL 31/01/2017 */
/*** $query_rsStock = sprintf("SELECT titulo,ps3_peso,ps4_peso,idioma,descripcion,'ps3' as consola
FROM titulos
WHERE ps3_peso IS NOT NULL
UNION ALL
SELECT titulo,ps3_peso,ps4_peso,idioma,descripcion,'ps4' as consola
FROM titulos
WHERE ps4_peso IS NOT NULL
UNION ALL
SELECT titulo,ps3_peso,ps4_peso,idioma,descripcion,'ps' as consola
FROM titulos
WHERE ps4_peso IS NULL AND ps3_peso IS NULL
UNION ALL
SELECT titulo,ps3_peso,ps4_peso,idioma,descripcion,'steam' as consola
FROM titulos
WHERE ps4_peso IS NULL AND ps3_peso IS NULL
ORDER BY consola, titulo ASC");*/
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
(select
    p.ID,
    REPLACE(REPLACE(REPLACE(REPLACE(TRIM(LCASE(p.post_title)), ' ', '-'), '''', ''), '’', ''), '.', '') AS titulo,
    max( CASE WHEN pm.meta_key = 'idioma' and p.ID = pm.post_id THEN pm.meta_value END ) as idioma,
    max( CASE WHEN pm.meta_key = 'consola' and  p.ID = pm.post_id THEN pm.meta_value END ) as cons,
    max( CASE WHEN pm.meta_key = 'peso' and p.ID = pm.post_id THEN pm.meta_value END ) as peso,
	max( CASE WHEN pm.meta_key = '_price' and p.ID = pm.post_id THEN pm.meta_value END ) as price,
	round(max( CASE WHEN pm.meta_key = '_price' and p.ID = pm.post_id THEN pm.meta_value END )) as max_price,
	round(min( CASE WHEN pm.meta_key = '_price' and p.ID = pm.post_id THEN pm.meta_value END )) as min_price,
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
WHERE libre > 0 AND cons = 'ps4' #ahora también agrupo por consola y titulo para evitar los duplicados de productos de la web (los que genero para WS)
GROUP BY consola, titulo");
$rsStock = mysql_query($query_rsStock, $Conexion) or die(mysql_error());
$row_rsStock = mysql_fetch_assoc($rsStock);
$totalRows_rsStock = mysql_num_rows($rsStock);
/***     STOCK     ***/

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
    <title><?php $titulo = 'Publicaciones para ML'; echo $titulo; ?></title>
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
    
<div class="row">
<!-- si existe stock continuo -->
<?php if($row_rsStock):?>
<!-- vamos a imprimir lo siguiente -->
  <?php do{ ?>
  <!-- siempre y cuando la imagen para el titulo y consola exista en el servidor. (eso me ayuda en el caso que la tabla creada dinamicamente tenga un error como por ejemplo la combinacion del titulo "ps plus" con la consola "steam", logicamente no existe ese COVER y por ende mejor ni lo muestro) -->
  <?php if(getimagesize('https://dixgamer.com/img/productos/'.$row_rsStock['consola'].'/'.$row_rsStock['titulo'].'.jpg') !== false):?>

  <div class="col-xs-6 col-sm-1b">
  <div class="thumbnail">
	  <div style="position:relative; overflow:hidden; padding-bottom:100%;">
		  <span style="position:absolute; z-index:100; bottom: 0px; background-color: rgba(0, 0, 0, 0.8); color:#FFF; padding:3px; font-size:0.9em;"><?php echo str_replace('-', ' ', $row_rsStock['titulo']);?></span>
		  <img class="img img-responsive full-width" src="https://dixgamer.com/img/productos/<?php echo $row_rsStock['consola'];?>/<?php echo $row_rsStock['titulo'];?>.jpg" border="0" alt="<?php echo $row_rsStock['titulo'];?> - <?php echo $row_rsStock['consola'];?>" style="border-radius:5px; position:absolute;">
		  <span class="badge badge-danger pull-right" style="position: relative; top: 8px; left: -8px;"><?php echo $row_rsStock['libre']; ?></span><br />
	  </div>
            <div class="caption text-center">
		    <?php # Escondo el boton - 15/02/2018
			/* <a href="#" class="btn btn-copiador <?php if($x == '0'): echo 'btn-info'; endif;?>" data-clipboard-target="#resultado<?php echo $row_rsStock['consola'];?>-<?php echo $row_rsStock['titulo'];?>-<?php echo $x;?>" style="color:#FFF;"><i aria-hidden="true" class="fa fa-clone fa-fw"></i>copiar <?php if($x == '1'): echo '(2)'; endif;?></a><br />
            <span class="badge badge-success">
			*/ ?>
			<span class="badge badge-success">
			<?php if($row_rsStock['max_price'] < $row_rsStock['min_price']):?>
				<?php echo $row_rsStock['max_price'];?>
			<?php else:?>
				<?php echo $row_rsStock['min_price'];?>
			<?php endif;?>
            </span>
            </div>
	</div>
	</div>
	<?php endif;?>
	<?php }while($row_rsStock = mysql_fetch_assoc($rsStock)); ?>
  <?php endif; ?>
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

mysql_free_result($rsStock);
?>
