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

$vendedor = $_SESSION['MM_Username'];
if ($vendedor === "Victor") { $verificado = 'si';} else { $verificado = 'no';}
$date = date('Y-m-d H:i:s', time());
if ((isset($_POST["MM_update"])) && ($_POST["MM_update"] == "form1")) {
		$modificaciones = sprintf("INSERT INTO cuentas_modif(cuentas_id, mail_fake, mail, pass, name, surname, country, state, city, pc, address, Notas, Day, verificado, usuario ) SELECT ID,  mail_fake, mail, pass, name, surname, country, state, city, pc, address, Notas, '$date', '$verificado', '$vendedor' FROM cuentas WHERE ID=%s",GetSQLValueString($_POST['ID'], "int"));
		mysql_select_db($database_Conexion, $Conexion);
  		$ResultadoModif = mysql_query($modificaciones, $Conexion) or die(mysql_error());
  
  $updateSQL = sprintf("UPDATE cuentas SET country=%s, state=%s, city=%s, pc=%s, address=%s WHERE ID=%s", 
                       GetSQLValueString($_POST['country'], "text"),
                       GetSQLValueString($_POST['state'], "text"),
                       GetSQLValueString($_POST['city'], "text"),
                       GetSQLValueString($_POST['pc'], "text"),
                       GetSQLValueString($_POST['address'], "text"),
					   GetSQLValueString($_POST['ID'], "int"));

  mysql_select_db($database_Conexion, $Conexion);
  $Result1 = mysql_query($updateSQL, $Conexion) or die(mysql_error());

  $updateGoTo = "cuentas_detalles.php";
  if (isset($_SERVER['QUERY_STRING'])) {
    $updateGoTo .= (strpos($updateGoTo, '?')) ? "&" : "?";
    $updateGoTo .= $_SERVER['QUERY_STRING'];
  }
  header(sprintf("Location: %s", $updateGoTo));
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
$query_rsClientes = sprintf("SELECT * FROM cuentas WHERE ID = %s", $colname_rsClientes);
$rsClientes = mysql_query($query_rsClientes, $Conexion) or die(mysql_error());
$row_rsClientes = mysql_fetch_assoc($rsClientes);
$totalRows_rsClientes = mysql_num_rows($rsClientes);
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
<link rel="stylesheet" href="//code.jquery.com/ui/1.11.4/themes/smoothness/jquery-ui.css">
<script src="//code.jquery.com/jquery-1.10.2.js"></script>
<script src="//code.jquery.com/ui/1.11.4/jquery-ui.js"></script>
<script src="http://ajax.aspnetcdn.com/ajax/jquery.validate/1.11.0/jquery.validate.min.js"></script>
    <title><?php $titulo = 'Modificar Cuenta #' . $colname_rsClientes; echo $titulo; ?></title>
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
    <div class="col-sm-4">
    </div>
    <div class="col-sm-4">
    <form method="post" name="form1" action="<?php echo $editFormAction; ?>">
    <em class="text-muted"><?php echo $row_rsClientes['mail']; ?></em>
            
            <em class="text-muted"><?php echo $row_rsClientes['country']; ?></em>
            <div class="input-group form-group">
              <span class="input-group-addon"><i class="fa fa-globe fa-fw"></i></span>
              <input class="form-control" value="<?php echo $row_rsClientes['country']; ?>" type="text" name="country">
            </div>
            <em class="text-muted"><?php echo $row_rsClientes['state']; ?></em>
            <div class="input-group form-group">
              <span class="input-group-addon"><i class="fa fa-map-marker fa-fw"></i></span>
              <input class="form-control" value="<?php echo $row_rsClientes['state']; ?>" type="text" name="state">
            </div>
            <em class="text-muted"><?php echo $row_rsClientes['city']; ?></em>
            <div class="input-group form-group">
              <span class="input-group-addon"><i class="fa fa-street-view fa-fw"></i></span>
              <input class="form-control" value="<?php echo $row_rsClientes['city']; ?>" type="text" name="city">
            </div>
            <em class="text-muted"><?php echo $row_rsClientes['pc']; ?></em>
            <div class="input-group form-group">
              <span class="input-group-addon"><i class="fa fa-globe fa-fw"></i></span>
              <input class="form-control" value="<?php echo $row_rsClientes['pc']; ?>" type="text" name="pc">
            </div>
            <em class="text-muted"><?php echo $row_rsClientes['address']; ?></em>
            <div class="input-group form-group">
              <span class="input-group-addon"><i class="fa fa-location-arrow fa-fw"></i></span>
              <input class="form-control" type="text" value="<?php echo $row_rsClientes['address']; ?>" name="address">
            </div>
            
            <button class="btn btn-primary" type="submit">Modificar</button>
        <input type="hidden" name="MM_update" value="form1">
        <input type="hidden" name="ID" value="<?php echo $row_rsClientes['ID']; ?>">
    </form>
    </div>
    <div class="col-sm-4">
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