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

$colname_rsCuentas = "-1";
if (isset($_GET['cta_id'])) {
  $colname_rsCuentas = (get_magic_quotes_gpc()) ? $_GET['cta_id'] : addslashes($_GET['cta_id']);
}

$colname_rsSaldo = "-1";
if (isset($_GET['saldo'])) {
  $colname_rsSaldo = (get_magic_quotes_gpc()) ? $_GET['saldo'] : addslashes($_GET['saldo']);
}
/***     Stock mas cargado en los ultimos 3 días y en el día de hoy   ***/  
mysql_select_db($database_Conexion, $Conexion);
$query_rsStockNuevo = "SELECT * FROM
(SELECT * FROM (SELECT COUNT(*) as q, titulo, consola, costo_usd FROM stock where usuario='$nombresito' and Day >= DATE(NOW() - INTERVAL 2 DAY) GROUP BY consola, titulo ORDER BY q DESC LIMIT 4) AS t1
UNION ALL
SELECT * FROM (SELECT COUNT(*) as q, titulo, consola, costo_usd FROM stock where usuario='$nombresito' and Day >= DATE(NOW()) GROUP BY consola, titulo ORDER BY q DESC LIMIT 4) as t2
ORDER BY q DESC) as resultado
GROUP BY consola, titulo";
$rsStockNuevo = mysql_query($query_rsStockNuevo, $Conexion) or die(mysql_error());
$row_rsStockNuevo = mysql_fetch_assoc($rsStockNuevo);
$totalRows_rsStockNuevo = mysql_num_rows($rsStockNuevo);
/***     stock      ***/

?>
<!DOCTYPE html>
<html lang="es">
  <head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <!-- The above 3 meta tags *must* come first in the head; any other head content must come *after* these tags -->
    <meta name="description" content="tienda de productos digitales">
    <meta name="author" content="game plaza argentina">
    <link rel="icon" href="favicon.ico">
    <title><?php $titulo = 'Cargar Juego'; echo $titulo; ?></title>
<!-- Estilo personalizado por mi -->
	<link href="css/personalizado.css" rel="stylesheet">
    
    <!-- Font Awesome style desde mi servidor -->
    <link href="css/font-awesome.min.css" rel="stylesheet">
    
    <!-- Bootstrap core CSS -->
    <link href="css/bootstrap.min.css" rel="stylesheet">
    
    <!-- Bootstrap SITE  CSS -->
    <link href="css/site.css" rel="stylesheet">

    <!-- Custom styles for this template -->
    <link href="css/offcanvas.css" rel="stylesheet">
    
    <!-- BootFLAT core CSS -->
    <link href="css/site.min.css" rel="stylesheet">
  <!-- antes que termine head-->
</head>

  <body style="margin:0px; padding: 5px 0px;">

    <div class="container">
	<h1><?php echo $titulo; ?></h1>
<div class="row"></div>
    
    <div class="row">
          <?php if($row_rsStockNuevo):?>
			  <?php do { ?>
              <div class="col-xs-6 col-sm-2">
              <div class="thumbnail" >
				 <?php if ($colname_rsSaldo >= $row_rsStockNuevo['costo_usd']):?>
                 <a title="cargar stock" href="cuentas_stock_masivo_insertar.php?cta_id=<?php echo $colname_rsCuentas;?>&consola=<?php echo $row_rsStockNuevo['consola']; ?>&titulo=<?php echo $row_rsStockNuevo['titulo']; ?>&costo_usd=<?php echo $row_rsStockNuevo['costo_usd']; ?>"><div>
				<?php else:?>
					 <a><div style="filter: brightness(50%); opacity:0.5;">
				<?php endif;?>
                 
                 <img src="/img/productos/<?php echo $row_rsStockNuevo['consola']."/".$row_rsStockNuevo['titulo'].".jpg"; ?>" alt="<?php echo $row_rsStockNuevo['consola']." - ".$row_rsStockNuevo['titulo'].".jpg"; ?>" class="img img-responsive full-width" />
                 </div>
                 </a>
                
                <div class="caption text-center">
				<span class="badge badge-<?php if ($colname_rsSaldo >= $row_rsStockNuevo['costo_usd']): echo 'success'; else: echo 'danger'; endif;?>">USD <?php echo $row_rsStockNuevo['costo_usd']; ?></span>
				<?php if ($colname_rsSaldo < $row_rsStockNuevo['costo_usd']):?><p class="badge badge-danger">falta saldo</p><?php endif;?>
                </div>
              </div>
            </div>
			<?php }while($row_rsStockNuevo = mysql_fetch_assoc($rsStockNuevo)); ?>
          <?php endif; ?>
          </div>
     <!--/row-->
    </div><!--/.container-->
    <!-- Bootstrap core JavaScript
    ================================================== -->
    <!-- Placed at the end of the document so the pages load faster -->
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/1.12.4/jquery.min.js"></script>
    <script>window.jQuery || document.write('<script src="../db/assets/js/vendor/jquery.min.js"><\/script>')</script>
    <script src="https://cdn.jsdelivr.net/clipboard.js/1.5.12/clipboard.min.js"></script>
	<script>new Clipboard('.btn-copiador');</script>
    <script src="dist/js/bootstrap.min.js"></script>
    <script src="assets/js/docs.min.js"></script>
    <!-- Activar popover -->
    <script>
	$(document).ready(function(){
		$('[data-toggle="popover"]').popover();
	});
	</script>
    <!-- IE10 viewport hack for Surface/desktop Windows 8 bug -->
    <script src="assets/js/ie10-viewport-bug-workaround.js"></script> <!-- extras de script y demás yerbas -->
</body>
</html>
<?php
mysql_free_result($rsUsuarios);

mysql_free_result($rsStockNuevo);

?>
