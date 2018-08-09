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

if ((isset($_POST["MM_update"])) && ($_POST["MM_update"] == "form1")) {
	$modificaciones = sprintf("INSERT INTO cuentas_notas(cuentas_id, Notas, Day, usuario) SELECT cuentas_id, CONCAT('Modificacion de juego #', ID, ', antes ', titulo,' (', consola, ')'), '$date', '$vendedor' FROM stock WHERE ID=%s",
					GetSQLValueString($_POST['ID'], "int"));
	mysql_select_db($database_Conexion, $Conexion);
  $ResultadoModif = mysql_query($modificaciones, $Conexion) or die(mysql_error());
	
  $updateSQL = sprintf("UPDATE stock SET titulo=%s, consola=%s WHERE ID=%s", 
                       GetSQLValueString($_POST['titulo'], "text"),
                       GetSQLValueString($_POST['consola'], "text"),
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

$query_rsTitulos = "SELECT web.*, stk.*
FROM
(SELECT REPLACE(REPLACE(REPLACE(REPLACE(TRIM(LCASE(post_title)), ' ', '-'), '''', ''), '’', ''), '.', '') AS producto FROM cbgw_posts WHERE post_type = 'product' and post_status = 'publish' group by producto) as web
LEFT JOIN
(SELECT titulo, COUNT(*) AS Q_Stk, Day FROM stock WHERE Day >= (DATE_ADD(CURDATE(), INTERVAL -45 DAY)) and (titulo LIKE '%slot%' or consola != 'ps') GROUP BY titulo) AS stk
ON producto = titulo 
ORDER BY Q_Stk DESC";
$rsTitulos = mysql_query($query_rsTitulos, $Conexion) or die(mysql_error());
$row_rsTitulos = mysql_fetch_assoc($rsTitulos);
$totalRows_rsTitulos = mysql_num_rows($rsTitulos);
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
            <span class="input-group-addon"><i class="fa fa-gamepad fa-fw"></i></span>
              <select id="titulo-selec" name="titulo" class="selectpicker form-control" data-live-search="true" data-size="5">
			  <?php do {  ?>
            	<option value="<?php echo $row_rsTitulos['producto']?>"><?php echo str_replace('-', ' ', $row_rsTitulos['producto']);?></option>
              <?php } while ($row_rsTitulos = mysql_fetch_assoc($rsTitulos));
				$rows = mysql_num_rows($rsTitulos);
							  if($rows > 0) {
								  mysql_data_seek($rsTitulos, 0);
								  $row_rsTitulos = mysql_fetch_assoc($rsTitulos);
								  }
								?>
				</select>
              <!--- <input type="text" id="titulo-selec" name="titulo" class="titulo tt-query form-control" autocomplete="off" spellcheck="false" placeholder="Buscar título"> -->
            </div>
  
  			<div class="input-group form-group">
            <span class="input-group-addon"><i class="fa fa-cube fa-fw"></i></span>
            <select id="consola" name="consola" class="selectpicker form-control">
            	<option selected value="ps4" data-content="<span class='label label-primary'>ps4</span>">ps4</option>
                <option value="ps3" data-content="<span class='label label-warning' style='background-color:#000;'>ps3</span>">ps3</option>
                <option value="ps" data-content="<span class='label label-danger'>psn</span>">psn</option>
                <option value="steam" data-content="<span class='label label-default'>steam</span>">steam</option>
                <option value="psvita" data-content="<span class='label label-info'>psvita</span>">psvita</option>
            </select>
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

mysql_free_result($rsTitulos);
?>