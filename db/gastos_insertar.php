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

$date = date('Y-m-d H:i:s', time());
if ((isset($_POST["MM_insert"])) && ($_POST["MM_insert"] == "form1")) {
  $insertSQL = sprintf("INSERT INTO gastos (concepto, importe, medio_pago, nro_transac, Notas, Day) VALUES (%s, %s, %s, %s, %s, '$date')", 
                       GetSQLValueString($_POST['concepto'], "text"),
                       GetSQLValueString($_POST['importe'], "double"),
					   GetSQLValueString($_POST['medio_pago'], "text"),
                       GetSQLValueString($_POST['nro_transac'], "text"),
                       GetSQLValueString($_POST['Notas'], "text"));

  mysql_select_db($database_Conexion, $Conexion);
  $Result1 = mysql_query($insertSQL, $Conexion) or die(mysql_error());

  $insertGoTo = "gastos.php";
  if (isset($_SERVER['QUERY_STRING'])) {
    $insertGoTo .= (strpos($insertGoTo, '?')) ? "&" : "?";
    $insertGoTo .= $_SERVER['QUERY_STRING'];
  }
  header(sprintf("Location: %s", $insertGoTo));
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
$query_rsClientes = sprintf("SELECT * FROM clientes", $colname_rsClientes);
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
<title><?php $titulo = 'Insertar gastos'; echo $titulo; ?></title>
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
            <div class="input-group form-group">
              <span class="input-group-addon"><i class="fa fa-compass fa-fw"></i></span>
              <select name="concepto" class="form-control">
					<option value="Autonomos">Autonomos</option>
					<option value="Publicidad Google" >Publicidad Google</option>
                    <option value="Publicidad Facebook" selected="selected">Publicidad Facebook</option>
                    <option value="Publicidad ML" >Publicidad ML</option>
                    <option value="Honorarios Enri" >Honorarios Enri</option>
				    <option value="Honorarios Euge" >Honorarios Euge</option>
                    <option value="Honorarios Leo" >Honorarios Leo</option>
                    <option value="Honorarios Betina" >Honorarios Betina</option>
				  	<option value="Honorarios Francisco" >Honorarios Francisco</option>
                    <option value="Honorarios Diseño" >Honorarios Diseño</option>
				    <option value="Honorarios Contador" >Honorarios Contador</option>
                    <option value="Facturante" >Facturante</option>
                    <option value="Real Trends" >Real Trends</option>
				  	<option value="MailChimp" >MailChimp</option>
				  	<option value="Kinsta" >Kinsta</option>
					<option value="Otros Gastos" >Otros Gastos</option>
				  <option value="IIBB">IIBB</option>
				  <option value="Otros Impuestos" >Otros Impuestos</option>
                    </select>
            </div>
            
            <div class="input-group form-group">
              <span class="input-group-addon"><i class="fa fa-dollar fa-fw"></i></span>
              <input class="form-control" type="text" name="importe" value="" autocomplete="off" placeholder="Importe">
			</div>
            
            <div class="input-group form-group">
              <span class="input-group-addon"><i class="fa fa-barcode fa-fw"></i></span>
              <input class="form-control" type="text" name="nro_transac" value="" autocomplete="off" placeholder="Nro Transac">
			</div>
            
            <div class="input-group form-group">
            <span class="input-group-addon"><i class="fa fa-bank fa-fw"></i></span> 
           <select name="medio_pago" class="selectpicker form-control">
           <option selected value="Transferencia Bancaria" data-content="<span class='label label-default'>Transferencia Bancaria</span>">Transferencia Bancaria</option>
            	<option  value="MercadoPago" data-content="<span class='label label-primary'>MercadoPago</span>">MercadoPago</option>
                <option value="Tarjeta" data-content="<span class='label label-info'>Tarjeta</span>">Tarjeta</option>
                <option value="Efectivo" data-content="<span class='label label-success'>Efectivo</span>">Efectivo</option>
                <option value="Saldo de Google" data-content="<span class='label label-normal'>Saldo de Google</span>">Saldo de Google</option>
                
            </select>                
            </div> 
            
            <div class="input-group form-group">
              <span class="input-group-addon"><i class="fa fa-comment fa-fw"></i></span>
              <input class="form-control" type="text" name="Notas" autocomplete="off" placeholder="Notas">
            </div>
            <button class="btn btn-primary" type="submit">Insertar</button>
        <input type="hidden" name="MM_insert" value="form1">
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
    <!-- Latest compiled and minified CSS -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-select/1.11.2/css/bootstrap-select.min.css">
    <!-- Latest compiled and minified JavaScript -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-select/1.11.2/js/bootstrap-select.min.js"></script>
<!-- InstanceEndEditable -->
  </body>
  
<!-- InstanceEnd --></html>
<?php
mysql_free_result($rsUsuarios);

mysql_free_result($rsClientes);
?>