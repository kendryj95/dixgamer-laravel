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

$colname_rsCliente = "-1";
if (isset($_GET['id'])) {
  $colname_rsCliente = (get_magic_quotes_gpc()) ? $_GET['id'] : addslashes($_GET['id']);
}
$colname_rsVenta = "-1";
if (isset($_GET['v_id'])) {
  $colname_rsVenta = (get_magic_quotes_gpc()) ? $_GET['v_id'] : addslashes($_GET['v_id']);
}


if ((isset($_POST["MM_update"])) && ($_POST["MM_update"] == "form1")) {
	
  $updateSQL = sprintf("UPDATE ventas SET order_item_id=%s WHERE ID=%s", 
					   GetSQLValueString($_POST['oii'], "int"),
					   GetSQLValueString($_POST['ID'], "int"));

  mysql_select_db($database_Conexion, $Conexion);
  $Result1 = mysql_query($updateSQL, $Conexion) or die(mysql_error());
	
	// Script para redirigir el top 
	echo "<script>window.top.location.href = \"clientes_detalles.php?id=$colname_rsCliente\";</script>";
	exit;
//  $updateGoTo = "clientes_detalles.php";
  //if (isset($_SERVER['QUERY_STRING'])) {
    //$updateGoTo .= (strpos($updateGoTo, '?')) ? "&" : "?";
    //$updateGoTo .= $_SERVER['QUERY_STRING'];
  //}
  //header(sprintf("Location: %s", $updateGoTo));
}

$nombresito = $_SESSION['MM_Username'];
mysql_select_db($database_Conexion, $Conexion);
$query_rsUsuarios = sprintf("SELECT * FROM usuarios WHERE Nombre = '$nombresito' ORDER BY Nombre ASC",$nombresito);
$rsUsuarios = mysql_query($query_rsUsuarios, $Conexion) or die(mysql_error());
$row_rsUsuarios = mysql_fetch_assoc($rsUsuarios);
$totalRows_rsUsuarios = mysql_num_rows($rsUsuarios);

?>
<!DOCTYPE html>
<html lang="es">
  <head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <!-- The above 3 meta tags *must* come first in the head; any other head content must come *after* these tags -->
    <meta name="description" content="base de datos">
    <meta name="author" content="vic">
    <link rel="icon" href="favicon.ico">
	<title><?php $titulo = 'Agregar <span class="label label-normal">order_item_id</span>'; echo $titulo; ?></title>
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
  <!-- antes que termine head-->
</head>

  <body>

    <div class="container">
	<h1><?php echo $titulo; ?></h1>
		<br /><br />
	<div class="row">
    <form method="post" name="form1" action="<?php echo $editFormAction; ?>">
    
            <div class="input-group form-group">
              <span class="input-group-addon"><i class="fa fa-shopping-cart fa-fw"></i></span>
              <input value="" class="form-control" type="text" name="oii" placeholder="order_item_id" autofocus>
            </div>

            <button class="btn btn-primary" type="submit">Agregar</button>
        <input type="hidden" name="MM_update" value="form1">
        <input type="hidden" name="ID" value="<?php echo $colname_rsVenta; ?>">
    </form>
    </div>
		<br /><br />
     <!--/row-->
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
	</script> <!-- extras de script y demás yerbas -->
</body>
</html>
<?php
mysql_free_result($rsUsuarios);

mysql_free_result($rsClientes);
?>