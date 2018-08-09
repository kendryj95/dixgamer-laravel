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
if ((isset($_POST["MM_insert"])) && ($_POST["MM_insert"] == "form1")) {
  $insertSQL = sprintf("INSERT INTO clientes (apellido, nombre, pais, provincia, ciudad, cp, direc, carac, tel, cel, email, ml_user, usuario) VALUES (%s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s,'$vendedor')", 
                       GetSQLValueString($_POST['apellido'], "text"),
                       GetSQLValueString($_POST['nombre'], "text"),
					   GetSQLValueString($_POST['pais'], "text"),
                       GetSQLValueString($_POST['provincia'], "text"),
                       GetSQLValueString($_POST['ciudad'], "text"),
                       GetSQLValueString($_POST['cp'], "int"),
                       GetSQLValueString($_POST['direc'], "text"),
                       GetSQLValueString($_POST['carac'], "text"),
                       GetSQLValueString($_POST['tel'], "text"),
                       GetSQLValueString($_POST['cel'], "text"),
                       GetSQLValueString($_POST['email'], "text"),
					   GetSQLValueString($_POST['ml_user'], "text"));

  mysql_select_db($database_Conexion, $Conexion);
  $Result1 = mysql_query($insertSQL, $Conexion) or die(mysql_error());

  $insertGoTo = "clientes_detalles.php?id=";
  $insertGoTo .= mysql_insert_id();
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
<link rel="stylesheet" href="//code.jquery.com/ui/1.11.4/themes/smoothness/jquery-ui.css">
<script src="//code.jquery.com/jquery-1.10.2.js"></script>
<script src="//code.jquery.com/ui/1.11.4/jquery-ui.js"></script>
<script src="http://ajax.aspnetcdn.com/ajax/jquery.validate/1.11.0/jquery.validate.min.js"></script>
<!-- FORM VALIDATION -->
<script type="text/javascript">
$(document).ready(function() {
    var x_timer;    
    $("#email").keyup(function (e){
        clearTimeout(x_timer);
        var user_name = $(this).val();
        x_timer = setTimeout(function(){
            check_email_ajax(user_name);
        }, 1000);
    });

function check_email_ajax(email){
	document.getElementById("user-result").className = "fa fa-spinner fa-pulse fa-fw";
    $.post('clientes_insertar_control_email.php', {'email':email}, function(data) {
	document.getElementById("user-result").className = (data);
	var test = document.getElementById("user-result");
	var testClass = test.className;
	switch(testClass){
    case "fa fa-ban": document.getElementById("user-result-div").className = "input-group form-group has-error"; break;
	case "fa fa-check": document.getElementById("user-result-div").className = "input-group form-group has-success"; break;
}
  	});
}
});
</script>

<script type="text/javascript">
$(document).ready(function() {
    var x_timer;    
    $("#ml_user").keyup(function (e){
        clearTimeout(x_timer);
        var user_name = $(this).val();
        x_timer = setTimeout(function(){
            check_ml_user_ajax(user_name);
        }, 1000);
    });

function check_ml_user_ajax(ml_user){
	document.getElementById("ml-user-result").className = "fa fa-spinner fa-pulse fa-fw";
    $.post('clientes_insertar_control_ml_user.php', {'ml_user':ml_user}, function(data) {
	document.getElementById("ml-user-result").className = (data);
	var test = document.getElementById("ml-user-result");
	var testClass = test.className;
	switch(testClass){
    case "fa fa-ban": document.getElementById("ml-user-result-div").className = "input-group form-group has-error"; break;
	case "fa fa-check": document.getElementById("ml-user-result-div").className = "input-group form-group has-success"; break;
}
  	});
}
});
</script>
    <title><?php $titulo = 'Insertar cliente'; echo $titulo; ?></title>
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
            <div id="user-result-div" class="input-group form-group">
              <span class="input-group-addon"><i class="fa fa-envelope-o fa-fw"></i></span>
              <input class="form-control" type="text" name="email" id="email" autocomplete="off" spellcheck="false" placeholder="Email" autofocus>
              <span class="input-group-addon"><i id="user-result" class="fa fa-pencil" aria-hidden="true"></i></span>
            </div>
            <div class="input-group form-group">
              <span class="input-group-addon"><i class="fa fa-user fa-fw"></i></span>
              <input class="form-control" type="text" name="apellido" autocomplete="off" placeholder="Apellido">
            </div>
            <div class="input-group form-group">
              <span class="input-group-addon"><i class="fa fa-user fa-fw"></i></span>
              <input class="form-control" type="text" name="nombre" autocomplete="off" placeholder="Nombre">
            </div>
            <div id="ml-user-result-div" class="input-group form-group">
              <span class="input-group-addon"><i class="fa fa-snapchat-ghost fa-fw"></i></span>
              <input class="form-control" type="text" name="ml_user" id="ml_user" autocomplete="off" placeholder="Usuario ML">
              <span class="input-group-addon"><i id="ml-user-result" class="fa fa-pencil" aria-hidden="true"></i></span>
            </div>
            <div class="input-group form-group">
              <span class="input-group-addon"><i class="fa fa-globe fa-fw"></i></span>
              <input class="form-control" type="text" name="pais" value="Argentina">
            </div>
            <div class="input-group form-group">
              <span class="input-group-addon"><i class="fa fa-map-marker fa-fw"></i></span>
              <select name="provincia" class="form-control">
					<option value="Buenos Aires" selected="selected">Buenos Aires</option>
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
              <input class="form-control" type="text" name="ciudad" placeholder="Ciudad">
            </div>
            <div class="input-group form-group">
              <span class="input-group-addon"><i class="fa fa-phone-square fa-fw"></i></span>
              <input class="form-control" type="text" name="carac" placeholder="Carac">
            </div>
            <div class="input-group form-group">
              <span class="input-group-addon"><i class="fa fa-phone fa-fw"></i></span>
              <input class="form-control" type="text" name="tel" placeholder="Tel">
            </div>
            <div class="input-group form-group">
              <span class="input-group-addon"><i class="fa fa-mobile fa-fw"></i></span>
              <input class="form-control" type="text" name="cel" placeholder="Cel">
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
    <!-- extras de script y demás yerbas -->
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/2.1.1/jquery.min.js"></script>
    <script type="text/javascript">
    $(function(){
    $('input[type="text"]').change(function(){
        this.value = $.trim(this.value);
    });
	});
	</script>
<!-- InstanceEndEditable -->
  </body>
  
<!-- InstanceEnd --></html>
<?php
mysql_free_result($rsUsuarios);

mysql_free_result($rsClientes);
?>