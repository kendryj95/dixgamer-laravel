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

/***     Stock Nuevo   ***/  
mysql_select_db($database_Conexion, $Conexion);
$query_rsStockNuevo = "SELECT ID AS ID_stk, titulo, consola, cuentas_id AS stk_ctas_id, medio_pago, costo, ID_vtas, Q_vta, dayvta, COUNT(*) AS Q_Stock
FROM stock
LEFT JOIN
(SELECT ventas.ID as ID_vtas, stock_id, slot, COUNT(*) AS Q_vta, Day AS dayvta
FROM ventas
GROUP BY stock_id
ORDER BY ID DESC) AS vendido
ON ID = stock_id
WHERE (consola != 'ps4') AND (consola != 'ps3') AND (consola != 'xbox') AND (consola != 'nintendo') AND (consola != 'facebook') AND (consola != 'steam') AND (Q_vta IS NULL) AND (titulo != 'plus-12-meses-slot') AND (titulo != 'plus-1-mes') AND (titulo != 'plus-3-meses') AND (titulo != 'gift-card-75-usd') AND (titulo != 'gift-card-100-usd') AND (titulo NOT LIKE '%points-fifa%') 
GROUP BY consola, titulo
ORDER BY consola DESC, titulo ASC, ID ASC";
$rsStockNuevo = mysql_query($query_rsStockNuevo, $Conexion) or die(mysql_error());
$row_rsStockNuevo = mysql_fetch_assoc($rsStockNuevo);
$totalRows_rsStockNuevo = mysql_num_rows($rsStockNuevo);
/***     stock nuevo     ***/

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
    <title><?php $titulo = 'Cargar Saldo - Cuenta #'; $titulo .= $colname_rsCuentas; echo $titulo; ?></title>
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
              <div class="col-xs-6 col-sm-3">
              <div class="thumbnail" >
                 <a title="cargar saldo" href="cuentas_saldo_insertar.php?cta_id=<?php echo $colname_rsCuentas;?>&titulo=<?php echo $row_rsStockNuevo['titulo']; ?>&consola=<?php echo $row_rsStockNuevo['consola']; ?>">
                 <div>
                 <img src="/img/productos/<?php echo $row_rsStockNuevo['consola']."/".$row_rsStockNuevo['titulo'].".jpg"; ?>" alt="<?php echo $row_rsStockNuevo['consola']." - ".$row_rsStockNuevo['titulo'].".jpg"; ?>" class="img img-responsive full-width" />
                 <span class="badge badge-<?php if ($row_rsStockNuevo['Q_Stock'] > 4): echo 'success'; else: echo 'danger'; endif;?> pull-right" style="position: relative; top: 8px; left: -8px;">
					<?php if ($_SESSION['MM_UserGroup'] ==  'Adm'):?> 
						<?php echo $row_rsStockNuevo['Q_Stock']; ?>
                    <?php else:?>
							<?php if ($row_rsStockNuevo['Q_Stock'] > 4): echo '+4'; else: echo $row_rsStockNuevo['Q_Stock'];?>
							<?php endif;?>
                    <?php endif;?>
                    </span>
                 </div>
                 </a>
                
                <div class="caption text-center">
                <small style="color:#CFCFCF;"><?php if ($_SESSION['MM_UserGroup'] ==  'Adm'):?><i class="fa fa-gamepad fa-fw" aria-hidden="true"></i> <?php echo $row_rsStockNuevo['ID_stk']; ?><?php endif;?></small>
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
