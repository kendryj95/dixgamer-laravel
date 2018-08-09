<?php require_once('../Connections/Conexion.php'); ?>
<?php
$MM_authorizedUsers = "Adm,Vendedor";
$MM_donotCheckaccess = "false";
require_once('_autentificacion.php');
?>
<?php 
$editFormAction = $_SERVER['PHP_SELF'];
if (isset($_SERVER['QUERY_STRING'])) {
  $editFormAction .= "?" . htmlentities($_SERVER['QUERY_STRING']);
}

$colname_rsCuentas = "-1";
if (isset($_GET['id'])) {
  $colname_rsCuentas = (get_magic_quotes_gpc()) ? $_GET['id'] : addslashes($_GET['id']);
}

$vendedor = $_SESSION['MM_Username'];
$date = date('Y-m-d H:i:s', time());
if ((isset($_POST["MM_insert"])) && ($_POST["MM_insert"] == "form1")) {
  $insertSQL = sprintf("INSERT INTO clientes_notas(clientes_id, Notas, usuario, Day) VALUES ('$colname_rsCuentas', %s, '$vendedor', '$date')", 
					   GetSQLValueString($_POST['Notas'], "text"));

  mysql_select_db($database_Conexion, $Conexion);
  $Result1 = mysql_query($insertSQL, $Conexion) or die(mysql_error());

	echo "<script>window.top.location.href = \"clientes_detalles.php?id=$colname_rsCuentas\";</script>";
	exit;
}

$nombresito = $_SESSION['MM_Username'];
mysql_select_db($database_Conexion, $Conexion);
$query_rsUsuarios = sprintf("SELECT * FROM usuarios WHERE Nombre = '$nombresito' ORDER BY Nombre ASC",$nombresito);
$rsUsuarios = mysql_query($query_rsUsuarios, $Conexion) or die(mysql_error());
$row_rsUsuarios = mysql_fetch_assoc($rsUsuarios);
$totalRows_rsUsuarios = mysql_num_rows($rsUsuarios);

mysql_select_db($database_Conexion, $Conexion);
$query_rsCuentas = sprintf("SELECT * FROM clientes WHERE ID=%s", $colname_rsCuentas);
$rsCuentas = mysql_query($query_rsCuentas, $Conexion) or die(mysql_error());
$row_rsCuentas = mysql_fetch_assoc($rsCuentas);
$totalRows_rsCuentas = mysql_num_rows($rsCuentas);
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
    <title><?php $titulo = 'Agregar Nota - Cliente #' . $colname_rsCuentas; echo $titulo; ?></title>
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
<div class="row">

    <form method="post" name="form1" action="<?php echo $editFormAction; ?>">

            <div class="input-group form-group">
              <span class="input-group-addon"><i class="fa fa-comment fa-fw"></i></span>
				<textarea class="form-control" rows="4" name="Notas" id="Notas" style="font-size: 22px;"></textarea>
              
            </div>
            <button class="btn btn-warning btn-lg" type="submit">Insertar</button>
        <input type="hidden" name="MM_insert" value="form1">
		<input type="hidden" name="ID" id="ID" value="<?php echo $row_rsCuentas['ID']; ?>">
    </form>

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

mysql_free_result($rsCuentas);
?>