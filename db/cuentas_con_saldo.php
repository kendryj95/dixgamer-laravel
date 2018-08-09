<?php require_once('../Connections/Conexion.php'); ?>
<?php
$MM_authorizedUsers = "Adm,Vendedor";
$MM_donotCheckaccess = "false";
require_once('_autentificacion.php');
?>
<?php 
$currentPage = $_SERVER["PHP_SELF"];
mysql_select_db($database_Conexion, $Conexion);
$query_rsUsuarios = "SELECT * FROM usuarios";
$rsUsuarios = mysql_query($query_rsUsuarios, $Conexion) or die(mysql_error());
$row_rsUsuarios = mysql_fetch_assoc($rsUsuarios);
$totalRows_rsUsuarios = mysql_num_rows($rsUsuarios);

$maxRows_rsClientes = 100;
$pageNum_rsClientes = 0;
if (isset($_GET['pageNum_rsClientes'])) {
  $pageNum_rsClientes = $_GET['pageNum_rsClientes'];
}
$startRow_rsClientes = $pageNum_rsClientes * $maxRows_rsClientes;

$colname_rsGastito = "-1";
if (isset($_GET['consola'])) {
  $colname_rsGastito = (get_magic_quotes_gpc()) ? $_GET['consola'] : addslashes($_GET['consola']);
	mysql_select_db($database_Conexion, $Conexion);
	$query_rsClientes = sprintf("SELECT sa_cta_id as cuentas_id, (sa_costo_usd - COALESCE(st_costo_usd,0)) libre_usd, (sa_costo - COALESCE(st_costo,0)) libre_ars, consola FROM
	(SELECT cuentas_id as sa_cta_id, SUM(costo_usd) as sa_costo_usd, SUM(costo) as sa_costo FROM saldo GROUP BY cuentas_id) as saldo
	LEFT JOIN 
	(SELECT cuentas_id as st_cta_id, SUM(costo_usd) as st_costo_usd, SUM(costo) as st_costo, GROUP_CONCAT(consola) as consola FROM stock GROUP BY cuentas_id) as stock
	ON saldo.sa_cta_id = stock.st_cta_id
	WHERE (sa_costo_usd - COALESCE(st_costo_usd,0)) != 0.00 AND consola LIKE '%s'
	ORDER BY (sa_costo_usd - COALESCE(st_costo_usd,0)) DESC", $colname_rsGastito);
	$query_limit_rsClientes = sprintf("%s LIMIT %d, %d", $query_rsClientes, $startRow_rsClientes, $maxRows_rsClientes);
	$rsClientes = mysql_query($query_limit_rsClientes, $Conexion) or die(mysql_error());
	$row_rsClientes = mysql_fetch_assoc($rsClientes);
}


if (!(isset($_GET['consola']))) {
mysql_select_db($database_Conexion, $Conexion);
$query_rsClientes = "SELECT sa_cta_id as cuentas_id, (sa_costo_usd - COALESCE(st_costo_usd,0)) libre_usd, (sa_costo - COALESCE(st_costo,0)) libre_ars, consola FROM
	(SELECT cuentas_id as sa_cta_id, SUM(costo_usd) as sa_costo_usd, SUM(costo) as sa_costo FROM saldo GROUP BY cuentas_id) as saldo
	LEFT JOIN 
	(SELECT cuentas_id as st_cta_id, SUM(costo_usd) as st_costo_usd, SUM(costo) as st_costo, GROUP_CONCAT(consola) as consola FROM stock GROUP BY cuentas_id) as stock
	ON saldo.sa_cta_id = stock.st_cta_id
	WHERE (sa_costo_usd - COALESCE(st_costo_usd,0)) != 0.00
	ORDER BY (sa_costo_usd - COALESCE(st_costo_usd,0)) DESC";
$query_limit_rsClientes = sprintf("%s LIMIT %d, %d", $query_rsClientes, $startRow_rsClientes, $maxRows_rsClientes);
$rsClientes = mysql_query($query_limit_rsClientes, $Conexion) or die(mysql_error());
$row_rsClientes = mysql_fetch_assoc($rsClientes);
}



if (isset($_GET['totalRows_rsClientes'])) {
  $totalRows_rsClientes = $_GET['totalRows_rsClientes'];
} else {
  $all_rsClientes = mysql_query($query_rsClientes);
  $totalRows_rsClientes = mysql_num_rows($all_rsClientes);
}
$totalPages_rsClientes = ceil($totalRows_rsClientes/$maxRows_rsClientes)-1;

$queryString_rsClientes = "";
if (!empty($_SERVER['QUERY_STRING'])) {
  $params = explode("&", $_SERVER['QUERY_STRING']);
  $newParams = array();
  foreach ($params as $param) {
    if (stristr($param, "pageNum_rsClientes") == false && 
        stristr($param, "totalRows_rsClientes") == false) {
      array_push($newParams, $param);
    }
  }
  if (count($newParams) != 0) {
    $queryString_rsClientes = "&" . htmlentities(implode("&", $newParams));
  }
}
$queryString_rsClientes = sprintf("&totalRows_rsClientes=%d%s", $totalRows_rsClientes, $queryString_rsClientes);
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
    <title><?php $titulo = 'Cuentas con Saldo libre'; echo $titulo; ?></title>
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
    <a class="btn btn-default btn-sm" href="cuentas_con_saldo.php" title="Todos" style="margin:5px 0 0 0;">Todos</a>
    <a class="btn btn-default btn-sm" href="cuentas_con_saldo.php?consola=ps3" title="Libres para PS4" style="margin:5px 0 0 0;">Libres para PS4</a>
    <a class="btn btn-default btn-sm" href="cuentas_con_saldo.php?consola=ps4" title="Libres para PS3" style="margin:5px 0 0 0;">Libres para PS3</a>
    <div class="table-responsive">
    <table border="0" align="center" cellpadding="0" cellspacing="5" class="table table-striped">
    <thead>
              <tr>
                <th>#</th>
                <th>Libre en USD</th>
                <?php if ($_SESSION['MM_UserGroup'] ==  'Adm'):?>
                <th>Libre en Pesos</th>
				<?php endif;?>
                <th>Para Consola</th>
              </tr>
            </thead>
		  <tbody>
          <?php do { ?><tr>
          <!--- Si ya se cargó juego de ps4 la consola libre es ps3, y viceversa -->
          	<?php if ($row_rsClientes['consola'] == "ps4"): $consol = "ps3";
				elseif ($row_rsClientes['consola'] == "ps3"): $consol = "ps4";
					else : $consol = ""; endif;?>
          	<td><a title="ir a cuenta" href="cuentas_detalles.php?id=<?php echo $row_rsClientes['cuentas_id']; ?>"><?php echo $row_rsClientes['cuentas_id']; ?></a></td>
            <td><?php echo $row_rsClientes['libre_usd']; ?></td>
            <?php if ($_SESSION['MM_UserGroup'] ==  'Adm'):?> 
            <td><?php echo $row_rsClientes['libre_ars']; ?></td>
            <?php endif;?>
            <td><span class="label label-default <?php echo $consol; ?>"><?php echo $consol; ?></span></td>
            
          </tr>
        <?php } while ($row_rsClientes = mysql_fetch_assoc($rsClientes)); ?>
        </tbody>
        </table>
        <div>
        <div class="col-md-12">
                <ul class="pager">
                	<?php if ($pageNum_rsClientes > 0) { // Show if not first page ?>
                 	 <li class="previous"><a title="Anterior" href="<?php printf("%s?pageNum_rsClientes=%d%s", $currentPage, max(0, $pageNum_rsClientes - 1), $queryString_rsClientes); ?>">Anterior</a></li>
                     <?php } else { ?>
                    <li class="previous disabled"><a href="#">Anterior</a></li>
                     <?php } // Show if not first page ?>
                     <li class="disabled"><?php echo ($startRow_rsClientes + 1) ?>-<?php echo min($startRow_rsClientes + $maxRows_rsClientes, $totalRows_rsClientes) ?>&nbsp;</li>
                  <?php if ($pageNum_rsClientes < $totalPages_rsClientes) { // Show if not last page ?>
                  <li class="next"><a title="Siguiente" href="<?php printf("%s?pageNum_rsClientes=%d%s", $currentPage, min($totalPages_rsClientes, $pageNum_rsClientes + 1), $queryString_rsClientes); ?>">Siguiente</a></li>
                    <?php } else { ?>
                    <li class="next disabled"><a href="#">Siguiente</a></li>
                    <?php } // Show if not last page ?>
                </ul>
              </div>
              </div>
              
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

mysql_free_result($rsClientes);
?>
