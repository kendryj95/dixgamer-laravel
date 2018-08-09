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
$date = date('Y-m-d H:i:s', time());
if ((isset($_POST["MM_update"])) && ($_POST["MM_update"] == "form1")) {  
  $updateSQL = sprintf("UPDATE ventas_cobro SET medio_cobro=%s, ref_cobro=%s, precio=%s, comision=%s, Notas=%s WHERE ID=%s", 
                       GetSQLValueString($_POST['medio_cobro'], "text"),
                       GetSQLValueString($_POST['ref_cobro'], "text"),
					   GetSQLValueString($_POST['precio'], "double"),
                       GetSQLValueString($_POST['comision'], "double"),
                       GetSQLValueString($_POST['Notas'], "text"),
					   GetSQLValueString($_POST['ID'], "int"));

  mysql_select_db($database_Conexion, $Conexion);
  $Result1 = mysql_query($updateSQL, $Conexion) or die(mysql_error());

  $updateGoTo = "clientes_detalles.php?id=";
  $updateGoTo .= GetSQLValueString($_POST['clientes_id'], "int");
  //if (isset($_SERVER['QUERY_STRING'])) {
    //$updateGoTo .= (strpos($updateGoTo, '?')) ? "&" : "?";
    //$updateGoTo .= $_SERVER['QUERY_STRING'];
  //}
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
$query_rsClientes = sprintf("SELECT ventas_cobro.ID, medio_cobro, ref_cobro, precio, comision, ventas_cobro.Notas, ventas_cobro.ventas_id, ventas.clientes_id FROM ventas_cobro LEFT JOIN ventas ON ventas_cobro.ventas_id = ventas.ID WHERE ventas_cobro.ID = %s", $colname_rsClientes);
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
    <title><?php $titulo = 'Modificar Cobro #' . $colname_rsClientes; echo $titulo; ?></title>
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
    <div class="col-sm-3">
    </div>
    <div class="col-sm-6">
    <form method="post" name="form1" action="<?php echo $editFormAction; ?>">
    		<?php 
            if (strpos($row_rsClientes['medio_cobro'], 'Ticket') !== false): $colorcons = 'success';
            elseif (strpos($row_rsClientes['medio_cobro'], 'Mercado') !== false): $colorcons = 'primary';
            elseif (strpos($row_rsClientes['medio_cobro'], 'Transferencia') !== false): $colorcons = 'info';
            endif;?>
            <div class="input-group form-group">
              <span class="input-group-addon"><i class="fa fa-shopping-bag fa-fw"></i></span>
              <select id="medio_cobro" name="medio_cobro" class="selectpicker form-control">
              		<option value="<?php echo $row_rsClientes['medio_cobro']; ?>" selected="selected" data-content="<span class='label label-<?php echo $colorcons; ?>'><?php echo $row_rsClientes['medio_cobro']; ?></span> - <span class='label label-success'>Actual</span>"><?php echo $row_rsClientes['medio_cobro']; ?> - Actual</option>
                    <option value="MercadoPago" data-content="<span class='label label-primary'>MercadoPago</span>">MercadoPago</option>
                    <option value="MercadoPago - Tarjeta" data-content="<span class='label label-primary'>MercadoPago - Tarjeta</span>">MercadoPago - Tarjeta</option>
                    <option value="MercadoPago - Ticket" data-content="<span class='label label-success'>MercadoPago - Ticket</span>">MercadoPago - Ticket</option>
                    <option value="Transferencia" data-content="<span class='label label-info'>Transferencia</span>">Transferencia</option>
                    </select>
            </div>
            
			<div class="input-group form-group" id="n_cobro">
              <span class="input-group-addon"><i class="fa fa-hashtag fa-fw"></i></span>
              <input class="form-control" type="text" name="ref_cobro" id="ref_cobro" placeholder="N° de Cobro" value="<?php echo $row_rsClientes['ref_cobro']; ?>">             
            </div>
            <span id="faltacobro" style="color:#777;display:none;"><i id="user-result" class="fa fa-pencil" aria-hidden="true"></i> completar</span>
                 
			<br />
			
			
			<?php // si es admin permito modificar precio y comision
			if (($_SESSION['MM_UserGroup'] ==  'Adm') or ($_SESSION['MM_Username'] ==  'Leo')):?>
			<div class="col-md-5">
            <div class="input-group form-group">
              <span class="input-group-addon">precio</span>
              <input class="form-control" type="text" id="precio" name="precio" value="<?php echo $row_rsClientes['precio']; ?>">
			</div>
          	</div>
            
            <div class="col-md-3" style="opacity:0.7">
            <div class="input-group form-group">
            <select id="porcentaje" class="form-control">
            	<option value="0.12">12 %</option>
                <option selected value="0.0538">6 %</option>
                <option value="0.00">0 %</option>
            </select>  
            </div>
            </div>
            
			<div class="col-md-4">
            <div class="input-group form-group">
                <span class="input-group-addon">com</span>
                <input class="form-control" type="text" id="comision" name="comision" value="<?php echo $row_rsClientes['comision']; ?>">
            </div>
            </div>
			<?php // Si no es Admin oculto los campos de precio y comision para que no se puedan modificar
				else: ?>
				<input class="form-control" type="hidden" id="precio" name="precio" value="<?php echo $row_rsClientes['precio']; ?>">
				<input class="form-control" type="hidden" id="comision" name="comision" value="<?php echo $row_rsClientes['comision']; ?>">
			<?php endif;?>
                        
            <div class="input-group form-group">
              <span class="input-group-addon"><i class="fa fa-comment fa-fw"></i></span>
				
              <input class="form-control" type="text" name="Notas_cobro" placeholder="Notas del cobro" value="<?php echo $row_rsClientes['Notas']; ?>">
            </div>

            <button class="btn btn-primary" type="submit">Modificar</button>
        <input type="hidden" name="MM_update" value="form1">
        <input type="hidden" name="ID" value="<?php echo $row_rsClientes['ID']; ?>">
        <input type="hidden" name="clientes_id" value="<?php echo $row_rsClientes['clientes_id']; ?>">
    </form>
    </div>
    <div class="col-sm-3">
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
        <script type="text/javascript">
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
	$("#porcentaje").change(function() {
      m1 = document.getElementById("precio").value;
      m2 = document.getElementById("porcentaje").value;
      r = m1*m2;
      document.getElementById("comision").value = r;
    });
	</script>
    <!-- Latest compiled and minified CSS -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-select/1.11.2/css/bootstrap-select.min.css">
    <!-- Latest compiled and minified JavaScript -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-select/1.11.2/js/bootstrap-select.min.js"></script>
    <!-- extras de script y demás yerbas -->
<!-- InstanceEndEditable -->
  </body>
  
<!-- InstanceEnd --></html>
<?php
mysql_free_result($rsUsuarios);

mysql_free_result($rsClientes);
?>