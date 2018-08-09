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

mysql_select_db($database_Conexion, $Conexion);
$query_rsClientes = "SELECT * FROM stock";
if (($_SESSION['MM_UserGroup'] !=  'Adm') && ($_SESSION['MM_Username'] !=  'Francisco')): $query_rsClientes .= " WHERE titulo NOT LIKE '%gift-card%' AND (titulo NOT LIKE '%plus%' AND titulo NOT LIKE '%slot')"; endif;
$query_rsClientes .= " ORDER BY ID DESC";
$query_limit_rsClientes = sprintf("%s LIMIT %d, %d", $query_rsClientes, $startRow_rsClientes, $maxRows_rsClientes);
$rsClientes = mysql_query($query_limit_rsClientes, $Conexion) or die(mysql_error());
$row_rsClientes = mysql_fetch_assoc($rsClientes);

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
    <title><?php $titulo = 'Listar stock'; echo $titulo; ?></title>
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
    <div class="table-responsive">
    <table border="0" align="center" cellpadding="0" cellspacing="5" class="table table-striped">
    <thead>
              <tr>
                <th>#</th>
                <th>Producto</th>
				<?php if ($_SESSION['MM_UserGroup'] ==  'Adm'):?>
                <th>Code Prov</th>
                <?php endif;?>
                <th>Cuenta</th>
				<th>Costo USD</th>
                <?php if (($_SESSION['MM_UserGroup'] ==  'Adm') or ($_SESSION['MM_Username'] ==  'Francisco')):?>
                <th>Costo</th>
				<?php endif;?>
				<?php if ($_SESSION['MM_UserGroup'] ==  'Adm'):?>
				<th>Pago por</th>
                <?php endif;?>
              </tr>
            </thead>
		  <tbody>
          <?php do { ?>
          
          <tr>
           	<td><?php echo $row_rsClientes['ID']; ?><?php if ($row_rsClientes['Notas']):?><a href="#" data-toggle="popover" data-placement="bottom" data-trigger="focus" title="Notas" data-content="<?php echo $row_rsClientes['Notas']; ?>" class="h6" style="color: #555555;"><i class="fa fa-comment fa-fw"></i></a><?php endif; ?></td>
            <td><?php echo $row_rsClientes['titulo']; ?> <span class="label label-default <?php echo $row_rsClientes['consola']; ?>"><?php echo $row_rsClientes['consola']; ?></span></td>
			<?php if ($_SESSION['MM_UserGroup'] ==  'Adm'):?>
			<td>
			<?php if ($row_rsClientes['code']):?>
			<small class="label label-default"><?php echo $row_rsClientes['code']; ?></small><span class="text-muted" style="font-size:0.8em;"> <?php echo '('.substr($row_rsClientes['code_prov'], 0 , 3) . ') ' . $row_rsClientes['n_order'];?> </span>
			<?php endif;?>
			</td>
			<?php endif;?>
            
			<td><?php if ($row_rsClientes['cuentas_id']): ?><a title="Ir a Cuenta" href="cuentas_detalles.php?id=<?php echo $row_rsClientes['cuentas_id']; ?>"><?php echo $row_rsClientes['cuentas_id']; ?></a><?php endif; ?></td>
			<td><?php echo $row_rsClientes['costo_usd']; ?></td>
            <?php if (($_SESSION['MM_UserGroup'] ==  'Adm') or ($_SESSION['MM_Username'] ==  'Francisco')):?>
            <td><?php echo $row_rsClientes['costo']; ?></td>
			<?php endif;?>
			<?php if ($_SESSION['MM_UserGroup'] ==  'Adm'):?>
			<td><?php echo $row_rsClientes['medio_pago']; ?></td>
            <?php endif;?>
            
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
