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

$colname_rsVta = "-1";
if (isset($_GET['vta_id'])) {
  $colname_rsVta = (get_magic_quotes_gpc()) ? $_GET['vta_id'] : addslashes($_GET['vta_id']);
}

$colname_rsClientes = "-1";
if (isset($_GET['c_id'])) {
  $colname_rsClientes = (get_magic_quotes_gpc()) ? $_GET['c_id'] : addslashes($_GET['c_id']);
}

$date = date('Y-m-d H:i:s', time());
$vendedor = $_SESSION['MM_Username'];
if ((isset($_POST["MM_insert"])) && ($_POST["MM_insert"] == "form1")) {
	$insertSQL2 = sprintf("INSERT INTO ventas_cobro (ventas_id, medio_cobro, ref_cobro, precio, comision, Day, Notas, usuario) VALUES ('$colname_rsVta', %s, %s, %s, %s, '$date', %s, '$vendedor')",
                       GetSQLValueString($_POST['medio_cobro'], "text"),
					   GetSQLValueString($_POST['ref_cobro'], "text"),
					   GetSQLValueString($_POST['precio'], "double"),
                       GetSQLValueString($_POST['comision'], "double"),
                       GetSQLValueString($_POST['Notas_cobro'], "text"));
					   
	mysql_select_db($database_Conexion, $Conexion);
  	$Result2 = mysql_query($insertSQL2, $Conexion) or die(mysql_error());

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


mysql_select_db($database_Conexion, $Conexion);
$query_rsStock = sprintf("SELECT ventas.ID, ventas.Day, ventas.clientes_id, ventas.stock_id, titulo, consola FROM ventas LEFT JOIN stock ON ventas.stock_id = stock.ID WHERE ventas.ID = %s", $colname_rsVta);
$rsStock = mysql_query($query_rsStock, $Conexion) or die(mysql_error());
$row_rsStock = mysql_fetch_assoc($rsStock);
$totalRows_rsStock = mysql_num_rows($rsStock);

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
    <title><?php $titulo = 'Agregar Cobro (vta #'; $titulo .= $colname_rsVta . ')'; echo $titulo; ?></title>
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
	<img class="img-rounded pull-right" width="100" src="/img/productos/<?php echo $row_rsStock['consola']."/".$row_rsStock['titulo'];?>.jpg" alt="<?php echo $row_rsStock['titulo']." - ".$row_rsStock['consola'];?>" />
    </div>
    <div class="col-sm-8">
    <form method="post" name="form1" id="form1" action="<?php echo $editFormAction; ?>">
    	<input type="text" id="clientes_id" name="clientes_id" value="<?php echo $row_rsClientes['ID']; ?>" hidden>
            
           <div class="input-group form-group">
           <span class="input-group-addon"><i class="fa fa-shopping-bag fa-fw"></i></span> 
           <select id="medio_cobro" name="medio_cobro" class="selectpicker form-control">
            	<option selected value="MercadoPago" data-content="<span class='label label-primary'>MercadoPago</span>">MercadoPago</option>
                <option value="Deposito/Transferencia" data-content="<span class='label label-info'>Deposito/Transferencia</span>">Deposito/Transferencia</option>
            </select>                
            </div> 
            
			<div class="input-group form-group" id="n_cobro">
              <span class="input-group-addon"><i class="fa fa-hashtag fa-fw"></i></span>
              <input class="form-control" type="text" name="ref_cobro" id="ref_cobro" placeholder="N° de Cobro">             
            </div>
            <span id="faltacobro" style="color:#777;display:none;"><i id="user-result" class="fa fa-pencil" aria-hidden="true"></i> completar</span>
                 
			<br />

			<div class="col-sm-5">
            <div class="input-group form-group">
              <span class="input-group-addon">precio</span>
              <input class="form-control" type="text" id="precio" name="precio" value="">
			</div>
          	</div>
            
            <div class="col-sm-3" style="opacity:0.7">
            <div class="input-group form-group">
            <select id="porcentaje" class="form-control">
            	<option value="0.12">12 %</option>
                <option selected value="0.0538">6 %</option>
                <option value="0.00">0 %</option>
            </select>  
            </div>
            </div>
            
			<div class="col-sm-4">
            <div class="input-group form-group">
                <span class="input-group-addon">comision</span>
                <input class="form-control" type="text" id="comision" name="comision" value="">
            </div>
            </div>
                        
            <div class="input-group form-group">
              <span class="input-group-addon"><i class="fa fa-comment fa-fw"></i></span>
              <input class="form-control" type="text" name="Notas_cobro" placeholder="Notas del cobro">
            </div>

            <button class="btn btn-primary botonero" id="submiter" type="submit">Insertar</button>
        <input type="hidden" name="MM_insert" value="form1">
    </form>
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
	</script> <script type="text/javascript">
	var isFormValid = false;
	$('#form1').submit(function() {
	
	if (!isFormValid) {
    if($('#ref_cobro').val() == ''){
		document.getElementById("n_cobro").className = "input-group form-group has-error";
		$("#faltacobro").show(300);
		isFormValid = true;
        return false;
	}
    }
	});
	</script>
  <script type="text/javascript">
	$(document).ready(function () {
		$("form :input").change(function() {
			var val = $('#medio_cobro').val();
			//alert(val2); 
			if (val == "Deposito/Transferencia") {
				$("#porcentaje").html("<option value='0.00'>0%</option>");
			} 
		});
	});
	</script>
    
  <script type="text/javascript">
	//$(document).ready(function() {
      //m1 = document.getElementById("precio").value;
      //m2 = document.getElementById("porcentaje").value;
      //r = m1*m2;
      //document.getElementById("comision").value = r;
	//});
	window.setInterval(function() {
      m1 = document.getElementById("precio").value;
      m2 = document.getElementById("porcentaje").value;
      r = m1*m2;
      document.getElementById("comision").value = r;
    },500);
	//$("#porcentaje").change(function() {
     // m1 = document.getElementById("precio").value;
      //m2 = document.getElementById("porcentaje").value;
      //r = m1*m2;
      //document.getElementById("comision").value = r;
    //});
	</script>
  <!-- IMAGE PICKER  
    <link rel="stylesheet" href="css/image-picker.css">
    <script src="js/image-picker.min.js"></script>
    <script type="text/javascript">
	$("#consola").imagepicker()
	</script>
    <!-- Latest compiled and minified CSS -->
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-select/1.11.2/css/bootstrap-select.min.css">
  <!-- Latest compiled and minified JavaScript -->
  <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-select/1.11.2/js/bootstrap-select.min.js"></script>
</body>
</html>
<?php
mysql_free_result($rsUsuarios);

mysql_free_result($rsStock);

mysql_free_result($rsClientes);
?>