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

$colname_rsClientes = "-1";
if (isset($_GET['id'])) {
  $colname_rsClientes = (get_magic_quotes_gpc()) ? $_GET['id'] : addslashes($_GET['id']);
}

$vendedor = $_SESSION['MM_Username'];
$date = date('Y-m-d H:i:s', time());
if ((isset($_POST["MM_update"])) && ($_POST["MM_update"] == "form1")) {
	$modificaciones = sprintf("INSERT INTO clientes_notas(clientes_id, Notas, Day, usuario ) SELECT ID, CONCAT('Otro FB: ', face), '$date', '$vendedor' FROM clientes WHERE ID=%s AND face IS NOT NULL",GetSQLValueString($_POST['ID'], "int"));
	mysql_select_db($database_Conexion, $Conexion);
  $ResultadoModif = mysql_query($modificaciones, $Conexion) or die(mysql_error());
	
  $updateSQL = sprintf("UPDATE clientes SET face=%s WHERE ID=%s", 
					   GetSQLValueString($_POST['face'], "text"),
					   GetSQLValueString($_POST['ID'], "int"));

  mysql_select_db($database_Conexion, $Conexion);
  $Result1 = mysql_query($updateSQL, $Conexion) or die(mysql_error());
	
	// Script para redirigir el top 
	echo "<script>window.top.location.href = \"clientes_detalles.php?id=$colname_rsClientes\";</script>";
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

mysql_select_db($database_Conexion, $Conexion);
$query_rsClientes = sprintf("SELECT * FROM clientes WHERE ID = %s", $colname_rsClientes);
$rsClientes = mysql_query($query_rsClientes, $Conexion) or die(mysql_error());
$row_rsClientes = mysql_fetch_assoc($rsClientes);
$totalRows_rsClientes = mysql_num_rows($rsClientes);
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
	<title><?php $titulo = 'Facebook'; echo $titulo; ?></title>
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
	<div class="row">
    <form method="post" name="form1" action="<?php echo $editFormAction; ?>">
    
            <div class="input-group form-group">
              <span class="input-group-addon"><i class="fa fa-facebook fa-fw"></i></span>
              <input value="" class="form-control" type="text" name="face" placeholder="facebook" autofocus>
            </div>

            <button class="btn btn-primary" type="submit">Modificar</button>
        <input type="hidden" name="MM_update" value="form1">
        <input type="hidden" name="ID" value="<?php echo $row_rsClientes['ID']; ?>">
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
	</script> <!--- extras de script y demás yerbas -->
	  
</body>
</html>
<?php
mysql_free_result($rsUsuarios);

mysql_free_result($rsClientes);
?>