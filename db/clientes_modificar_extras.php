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
$verificado = 'si';

$date = date('Y-m-d H:i:s', time());
if ((isset($_POST["MM_update"])) && ($_POST["MM_update"] == "form1")) {
	$modificaciones = sprintf("INSERT INTO clientes_modif(clientes_id, pais, provincia, ciudad, cp, direc, carac, tel, cel, Level, Day, verificado, usuario ) SELECT ID, pais, provincia, ciudad, cp, direc, carac, tel, cel, Level, '$date', '$verificado', 'Auto' FROM clientes WHERE ID=%s",GetSQLValueString($_POST['ID'], "int"));
	mysql_select_db($database_Conexion, $Conexion);
  $ResultadoModif = mysql_query($modificaciones, $Conexion) or die(mysql_error());
	
  $updateSQL = sprintf("UPDATE clientes SET pais=%s, provincia=%s, ciudad=%s, direc=%s, carac=%s, tel=%s, cel=%s, WHERE ID=%s", 
					   GetSQLValueString($_POST['pais'], "text"),
                       GetSQLValueString($_POST['provincia'], "text"),
                       GetSQLValueString($_POST['ciudad'], "text"),
                       GetSQLValueString($_POST['direc'], "text"),
                       GetSQLValueString($_POST['carac'], "text"),
                       GetSQLValueString($_POST['tel'], "text"),
                       GetSQLValueString($_POST['cel'], "text"),
					   GetSQLValueString($_POST['ID'], "int"));

  mysql_select_db($database_Conexion, $Conexion);
  $Result1 = mysql_query($updateSQL, $Conexion) or die(mysql_error());

	// Script para redirigir el top 
	echo "<script>window.top.location.href = \"clientes_detalles.php?id=$colname_rsClientes\";</script>";
	exit;
}

$nombresito = $_SESSION['MM_Username'];
mysql_select_db($database_Conexion, $Conexion);
$query_rsUsuarios = sprintf("SELECT * FROM usuarios WHERE Nombre = '$nombresito' ORDER BY Nombre ASC",$nombresito);
$rsUsuarios = mysql_query($query_rsUsuarios, $Conexion) or die(mysql_error());
$row_rsUsuarios = mysql_fetch_assoc($rsUsuarios);
$totalRows_rsUsuarios = mysql_num_rows($rsUsuarios);

$colname_rsClientes = "-1";
if (isset($_GET['id'])) {
  $colname_rsClientes = (get_magic_quotes_gpc()) ? $_GET['id'] : addslashes($_GET['id']);
}
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
  <link rel="stylesheet" href="//code.jquery.com/ui/1.11.4/themes/smoothness/jquery-ui.css">
<script src="//code.jquery.com/jquery-1.10.2.js"></script>
<script src="//code.jquery.com/ui/1.11.4/jquery-ui.js"></script>
<script src="http://ajax.aspnetcdn.com/ajax/jquery.validate/1.11.0/jquery.validate.min.js"></script>
    <title><?php $titulo = 'Modificar Datos'; echo $titulo; ?></title>
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
              <span class="input-group-addon"><i class="fa fa-globe fa-fw"></i></span>
              <input value="<?php echo $row_rsClientes['pais']; ?>" class="form-control" type="text" name="pais">
              <span class="input-group-addon"><em class="text-muted"><?php echo $row_rsClientes['pais']; ?></em></span>
            </div>

            <div class="input-group form-group">
              <span class="input-group-addon"><i class="fa fa-map-marker fa-fw"></i></span>
              <select name="provincia" class="form-control">
              		<option value="<?php echo $row_rsClientes['provincia']; ?>" selected="selected"><?php echo $row_rsClientes['provincia']; ?>- Actual</option>
					<option value="Buenos Aires">Buenos Aires</option>
					<option value="Catamarca" >Catamarca</option>
					<option value="Chaco" >Chaco</option>
					<option value="Chubut" >Chubut</option>
					<option value="Cordoba" >Cordoba</option>
                    <option value="Corrientes" >Corrientes</option>
					<option value="Entre Rios" >Entre Rios</option>
					<option value="Formosa" >Formosa</option>
					<option value="Jujuy" >Jujuy</option>
					<option value="La Pampa" >La Pampa</option>
					<option value="La Rioja" >La Rioja</option>
					<option value="Mendoza" >Mendoza</option>
					<option value="Misiones" >Misiones</option>
					<option value="Neuquen" >Neuquen</option>
					<option value="Rio Negro" >Rio Negro</option>
					<option value="San Juan" >San Juan</option>
					<option value="San Luis" >San Luis</option>
					<option value="Santa Cruz" >Santa Cruz</option>
					<option value="Santa Fe" >Santa Fe</option>
					<option value="Santiago del Estero" >Santiago del Estero</option>
					<option value="Salta" >Salta</option>
					<option value="Tierra del Fuego" >Tierra del Fuego</option>
					<option value="Tucuman" >Tucuman</option>
                    </select>
            </div>
            
            <div class="input-group form-group">
              <span class="input-group-addon"><i class="fa fa-street-view fa-fw"></i></span>
              <input value="<?php echo $row_rsClientes['ciudad']; ?>" class="form-control" type="text" name="ciudad" placeholder="Ciudad">
              <span class="input-group-addon"><em class="text-muted"><?php echo $row_rsClientes['ciudad']; ?></em></span>
            </div>
            
            <div class="input-group form-group">
              <span class="input-group-addon"><i class="fa fa-phone-square fa-fw"></i></span>
              <input value="<?php echo $row_rsClientes['carac']; ?>" class="form-control" type="text" name="carac" placeholder="Carac">
              <span class="input-group-addon"><em class="text-muted"><?php echo $row_rsClientes['carac']; ?></em></span>
            </div>
            
            <div class="input-group form-group">
              <span class="input-group-addon"><i class="fa fa-phone fa-fw"></i></span>
              <input value="<?php echo $row_rsClientes['tel']; ?>" class="form-control" type="text" name="tel" placeholder="Tel">
              <span class="input-group-addon"><em class="text-muted"><?php echo $row_rsClientes['tel']; ?></em></span>
            </div>
            
            <div class="input-group form-group">
              <span class="input-group-addon"><i class="fa fa-mobile fa-fw"></i></span>
              <input value="<?php echo $row_rsClientes['cel']; ?>" class="form-control" type="text" name="cel" placeholder="Cel">
              <span class="input-group-addon"><em class="text-muted"><?php echo $row_rsClientes['cel']; ?></em></span>
            </div>
            
            <button class="btn btn-primary" type="submit">Modificar</button>
        <input type="hidden" name="MM_update" value="form1">
        <input type="hidden" name="ID" value="<?php echo $row_rsClientes['ID']; ?>">
    </form>
    </div>
		<br><br>
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