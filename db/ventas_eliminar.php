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
if (isset($_GET['c_id'])) {
  $colname_rsClientes = (get_magic_quotes_gpc()) ? $_GET['c_id'] : addslashes($_GET['c_id']);
}

$vendedor = $_SESSION['MM_Username'];
if ($vendedor === "Victor") { $verificado = 'si';} else { $verificado = 'no';}

$date = date('Y-m-d H:i:s', time());
	if ((isset($_POST["MM_insert"])) && ($_POST["MM_insert"] == "form1")) {
	$modificaciones2 = sprintf("INSERT INTO ventas_baja(ventas_id, clientes_id, stock_id, order_item_id, cons, slot, medio_venta, Day, Notas, Day_baja, Notas_baja, verificado, usuario ) SELECT ID, clientes_id, stock_id, order_item_id, cons, slot, medio_venta, Day, Notas, '$date', %s, '$verificado', '$vendedor' FROM ventas WHERE ID=%s",GetSQLValueString($_POST['Notas_baja'], "text"),GetSQLValueString($_POST['ID'], "int"));
	mysql_select_db($database_Conexion, $Conexion);
	$ResultadoModif2 = mysql_query($modificaciones2, $Conexion) or die(mysql_error());
	
	$updateSQL2 = sprintf("DELETE FROM ventas WHERE ID=%s",GetSQLValueString($_POST['ID'], "int"));
	mysql_select_db($database_Conexion, $Conexion);
	$Result12 = mysql_query($updateSQL2, $Conexion) or die(mysql_error());
	
	$cambioprecio = sprintf("UPDATE ventas_cobro SET precio='0', comision='0' WHERE ventas_id=%s", GetSQLValueString($_POST['ID'], "int"));
	mysql_select_db($database_Conexion, $Conexion);
	$ResultadoCambio = mysql_query($cambioprecio, $Conexion) or die(mysql_error());
	
	// Script para redirigir el top 
	echo "<script>window.top.location.href = \"clientes_detalles.php?id=$colname_rsClientes\";</script>";
	exit;
}

	elseif ((isset($_POST["MM_insert"])) && ($_POST["MM_insert"] == "form2")) {
	$updateSQL = sprintf("INSERT INTO ventas_modif(ventas_id, clientes_id, stock_id, order_item_id, cons, slot, medio_venta, Day, Notas, verificado, usuario ) SELECT ID, clientes_id, stock_id, order_item_id, cons, slot, medio_venta, '$date', Notas, '$verificado', '$vendedor' FROM ventas WHERE ID=%s",GetSQLValueString($_POST['ID'], "int"));
	mysql_select_db($database_Conexion, $Conexion);
	$Result1 = mysql_query($updateSQL, $Conexion) or die(mysql_error());
	
	$modificaciones = sprintf("INSERT INTO ventas_baja(ventas_id, clientes_id, stock_id, order_item_id, cons, slot, medio_venta, Day, Notas, Day_baja, Notas_baja, verificado, usuario ) SELECT ID, clientes_id, stock_id, order_item_id, cons, slot, medio_venta, Day, Notas, '$date', %s, '$verificado', '$vendedor' FROM ventas WHERE ID=%s",GetSQLValueString($_POST['Notas_baja'], "text"),GetSQLValueString($_POST['ID'], "int"));
	mysql_select_db($database_Conexion, $Conexion);
	$ResultadoModif = mysql_query($modificaciones, $Conexion) or die(mysql_error());
		
	$cambioprecio = sprintf("UPDATE ventas_cobro SET precio='0', comision='0' WHERE ventas_id=%s", GetSQLValueString($_POST['ID'], "int"));
	mysql_select_db($database_Conexion, $Conexion);
	$ResultadoCambio = mysql_query($cambioprecio, $Conexion) or die(mysql_error());
	
	// Script para redirigir el top 
	echo "<script>window.top.location.href = \"clientes_detalles.php?id=$colname_rsClientes\";</script>";
	exit;
}

elseif ((isset($_POST["MM_insert"])) && ($_POST["MM_insert"] == "form3")) {
	$updateSQL3 = sprintf("DELETE FROM ventas WHERE ID=%s",GetSQLValueString($_POST['ID'], "int"));
	mysql_select_db($database_Conexion, $Conexion);
	$Result13 = mysql_query($updateSQL3, $Conexion) or die(mysql_error());
	
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

$colname_rsCuentas = "-1";
if (isset($_GET['id'])) {
  $colname_rsCuentas = (get_magic_quotes_gpc()) ? $_GET['id'] : addslashes($_GET['id']);
}
mysql_select_db($database_Conexion, $Conexion);
$query_rsCuentas = sprintf("SELECT * FROM ventas WHERE ID = %s", $colname_rsCuentas);
$rsCuentas = mysql_query($query_rsCuentas, $Conexion) or die(mysql_error());
$row_rsCuentas = mysql_fetch_assoc($rsCuentas);
$totalRows_rsCuentas = mysql_num_rows($rsCuentas);

mysql_select_db($database_Conexion, $Conexion);
$query_rsVentasBaja = sprintf("SELECT * FROM ventas_baja WHERE ventas_id = %s", $colname_rsCuentas);
$rsVentasBaja = mysql_query($query_rsVentasBaja, $Conexion) or die(mysql_error());
$row_rsVentasBaja = mysql_fetch_assoc($rsVentasBaja);
$totalRows_rsVentasBaja = mysql_num_rows($rsVentasBaja);
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
    <title><?php $titulo = 'Ateción... ¡eliminando venta!'; echo $titulo; ?></title>
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

  <body style="padding: 0px;">
    <div class="container">
	<h1><?php echo $titulo; ?></h1>
<div class="row">
    <div class="col-sm-2">
    </div>
    <div class="col-sm-8">
    <?php if (!($row_rsVentasBaja[ID])) :?>
    <form method="post" name="form1" action="<?php echo $editFormAction; ?>">
			<div class="input-group form-group">
				<span class="input-group-addon"><i class="fa fa-comment fa-fw"></i></span>
				<textarea class="form-control" rows="2" name="Notas_baja" id="Notas_baja" style="font-size: 18px;" placeholder="Nota"></textarea>
			</div>
            
            <button class="btn btn-normal" type="submit"><i class="fa fa-trash fa-fw"></i> Eliminar Venta y Cobro</button>
        <input type="hidden" name="MM_insert" value="form1">
        <input type="hidden" name="ID" value="<?php echo $row_rsCuentas['ID']; ?>">
        <input type="hidden" name="clientes_id" value="<?php echo $row_rsCuentas['clientes_id']; ?>">
    </form>
    <br /><br />
    
        <form method="post" name="form2" action="<?php echo $editFormAction; ?>">
			<div class="input-group form-group">
				<span class="input-group-addon"><i class="fa fa-comment fa-fw"></i></span>
				<textarea class="form-control" rows="2" name="Notas_baja" id="Notas_baja" style="font-size: 18px;" placeholder="Nota"></textarea>
			</div>
            
            <button class="btn btn-danger" type="submit" ><i class="fa fa-frown-o fa-fw"></i> Eliminar solo Cobro</button>
        <input type="hidden" name="MM_insert" value="form2">
        <input type="hidden" name="ID" value="<?php echo $row_rsCuentas['ID']; ?>">
        <input type="hidden" name="clientes_id" value="<?php echo $row_rsCuentas['clientes_id']; ?>">
    </form><br /><br />
    <?php else: ?>
        <form method="post" name="form3" action="<?php echo $editFormAction; ?>">
            
            <button class="btn btn-success" type="submit"><i class="fa fa-smile-o fa-fw"></i> Eliminar Venta</button>
        <input type="hidden" name="MM_insert" value="form3">
        <input type="hidden" name="ID" value="<?php echo $row_rsCuentas['ID']; ?>">
        <input type="hidden" name="clientes_id" value="<?php echo $row_rsCuentas['clientes_id']; ?>">
    </form>
    <?php endif;?>
    </div>
    <div class="col-sm-2">
    </div>
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

mysql_free_result($rsCuentas);
mysql_free_result($rsVentasBaja);

?>