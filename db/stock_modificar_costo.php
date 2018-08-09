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

$StockID = "-1";
if (isset($_GET['s_id'])) {
  $StockID = (get_magic_quotes_gpc()) ? $_GET['s_id'] : addslashes($_GET['s_id']);
}
$CuentaID = "-1";
if (isset($_GET['c_id'])) {
  $CuentaID = (get_magic_quotes_gpc()) ? $_GET['c_id'] : addslashes($_GET['c_id']);
}

$vendedor = $_SESSION['MM_Username'];
$date = date('Y-m-d H:i:s', time());

mysql_select_db($database_Conexion, $Conexion);
$query_rsSaldo = sprintf("SELECT cuentas_id, SUM(costo_usd) as costo_usd, SUM(costo) as costo, medio_pago
FROM saldo 
WHERE cuentas_id = %s
GROUP BY cuentas_id
ORDER BY ID DESC", $CuentaID);
$rsSaldo = mysql_query($query_rsSaldo, $Conexion) or die(mysql_error());
$row_rsSaldo = mysql_fetch_assoc($rsSaldo);
$totalRows_rsSaldo = mysql_num_rows($rsSaldo);


if ((isset($_POST["MM_update"])) && ($_POST["MM_update"] == "form1")) {
	
	
	/// SI EL COSTO EN USD ES 9.99, 19.99, etc... LE SUMO UN CENTAVO
	$costo_usd = GetSQLValueString($_POST['costo_usd'], "float");

	//// CALCULO EL SALDO LIBRE DE LA CUENTA EN USD Y EN ARS
	$saldo_libre_usd = $row_rsSaldo['costo_usd'];
	$saldo_libre_ars = $row_rsSaldo['costo'];

	/// SI EL SALDO A QUEDAR LUEGO DE INSERTAR UN PRODUCTO ES MAYOR O IGUAL A 9.99, CARGO COSTO ARS PROPORCIONAL
	if (($saldo_libre_usd - $costo_usd) >= 9.99) {
		$costo_ars = ($saldo_libre_ars * ($costo_usd/$saldo_libre_usd));
	} else {
	/// SI EL SALDO A QUEDAR ES MENOR A 9.99 LE ASIGNO EL TOTAL EN PESOS LIBRES > ABSORBO TODO EL COSTO EN PESOS
		$costo_ars = $saldo_libre_ars;
	}
	
	$modificaciones = sprintf("INSERT INTO cuentas_notas(cuentas_id, Notas, Day, usuario) SELECT cuentas_id, CONCAT('Modificacion de juego #', ID, ', antes ', costo_usd,' usd'), '$date', '$vendedor' FROM stock WHERE ID=%s",
					GetSQLValueString($_POST['ID'], "int"));
	mysql_select_db($database_Conexion, $Conexion);
    $ResultadoModif = mysql_query($modificaciones, $Conexion) or die(mysql_error());
	
  	$updateSQL = sprintf("UPDATE stock SET costo_usd=$costo_usd, costo=$costo_ars WHERE ID=%s", 
					   GetSQLValueString($_POST['ID'], "int"));

  mysql_select_db($database_Conexion, $Conexion);
  $Result1 = mysql_query($updateSQL, $Conexion) or die(mysql_error());

	// Script para redirigir el top 
	echo "<script>window.top.location.href = \"cuentas_detalles.php?id=$CuentaID\";</script>";
	exit;
}

$nombresito = $_SESSION['MM_Username'];
mysql_select_db($database_Conexion, $Conexion);
$query_rsUsuarios = sprintf("SELECT * FROM usuarios WHERE Nombre = '$nombresito' ORDER BY Nombre ASC",$nombresito);
$rsUsuarios = mysql_query($query_rsUsuarios, $Conexion) or die(mysql_error());
$row_rsUsuarios = mysql_fetch_assoc($rsUsuarios);
$totalRows_rsUsuarios = mysql_num_rows($rsUsuarios);

$StockID = "-1";
if (isset($_GET['s_id'])) {
  $StockID = (get_magic_quotes_gpc()) ? $_GET['s_id'] : addslashes($_GET['s_id']);
}

mysql_select_db($database_Conexion, $Conexion);
$query_rsClientes = sprintf("SELECT * FROM stock WHERE ID=%s", $StockID);
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
    <title><?php $titulo = 'Modificar Producto #'; $titulo .= $StockID; echo $titulo; ?></title>
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
	<div class="col-sm-2" style="text-align:right;">
    	<span id="alerta" class="label label-danger"></span>
		<img class="img-rounded pull-right" width="100" id="image-swap" src="" alt="" />
    </div>
		
    <div class="col-sm-6">
		<form method="post" name="form1" action="<?php echo $editFormAction; ?>">
                
			<div class="input-group form-group">
             	<span class="input-group-addon"><em>Costo en USD</em></span>
                
                <input id="proporcion_usd" class="form-control" type="number" step="0.01" name="costo_usd" value="<?php echo $row_rsClientes['costo_usd']; ?>" max="<?php echo $row_rsSaldo['costo_usd']; ?>">
                 <span class="input-group-addon"> <em style="opacity:0.7" class="text-muted">Actual: <?php echo $row_rsClientes['costo_usd']; ?> <img src="https://exur-exur.netdna-ssl.com/realskin/images/usa.png" style="opacity:0.7"></em></span>
            </div>
            
            <button class="btn btn-primary" type="submit">Modificar</button>
        <input type="hidden" name="MM_update" value="form1">
        <input type="hidden" name="ID" value="<?php echo $row_rsClientes['ID']; ?>">
    </form>
    </div>
	<div class="col-sm-4"></div>
	</div>
		
		<br /><br /><br /><br /><br /><br /><br /><br />
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
<!-- extras de script y demás yerbas -->
	<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-select/1.11.2/css/bootstrap-select.min.css">
    <!-- Latest compiled and minified JavaScript -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-select/1.11.2/js/bootstrap-select.min.js"></script>
	<script type="text/javascript">
	 jQuery(function($) {
            $("form").on('change', function() {
                var titulo = document.getElementById('titulo-selec').value;
				var consola = document.getElementById('consola').value;
				$("#image-swap").attr("src", (titulo !== "" &&  + consola !== "") ? "/img/productos/" + consola + "/" + titulo + ".jpg" : "");
				$('#image-swap').load(function() {
                document.getElementById("alerta").innerHTML = "";    
                });
				$('#image-swap').error(function() {
  				document.getElementById("alerta").innerHTML = "no se encuentra";
				});
            }).trigger('change');
        })
	</script>
</body>
</html>
<?php
mysql_free_result($rsUsuarios);

mysql_free_result($rsClientes);
?>