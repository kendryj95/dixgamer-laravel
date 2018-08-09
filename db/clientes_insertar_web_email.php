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


if ((isset($_POST["MM_insert"])) && ($_POST["MM_insert"] == "form1")) {
  $insertSQL = sprintf("UPDATE cbgw_postmeta SET meta_value=%s WHERE meta_key ='_billing_email' and post_id=%s",
                       GetSQLValueString($_POST['email'], "text"),
					   GetSQLValueString($_POST['post_id'], "int"));

  mysql_select_db($database_Conexion, $Conexion);
  $Result1 = mysql_query($insertSQL, $Conexion) or die(mysql_error());
	
$pedido = GetSQLValueString($_POST['post_id'], "int");
  $insertGoTo = "ventas_web.php?pedido=" . $pedido;
  header(sprintf("Location: %s", $insertGoTo));
}

$nombresito = $_SESSION['MM_Username'];
mysql_select_db($database_Conexion, $Conexion);
$query_rsUsuarios = sprintf("SELECT * FROM usuarios WHERE Nombre = '$nombresito' ORDER BY Nombre ASC",$nombresito);
$rsUsuarios = mysql_query($query_rsUsuarios, $Conexion) or die(mysql_error());
$row_rsUsuarios = mysql_fetch_assoc($rsUsuarios);
$totalRows_rsUsuarios = mysql_num_rows($rsUsuarios);

$colname_rsCuentas = "-1";
if (isset($_GET['order_id'])) {
  $colname_rsCuentas = (get_magic_quotes_gpc()) ? $_GET['order_id'] : addslashes($_GET['order_id']);
}
mysql_select_db($database_Conexion, $Conexion);
$query_rsCuentas = sprintf("select
ID as post_id,
max( CASE WHEN pm.meta_key = '_billing_last_name' and p.ID = pm.post_id THEN pm.meta_value END ) as apellido,
max( CASE WHEN pm.meta_key = '_billing_first_name' and p.ID = pm.post_id THEN pm.meta_value END ) as nombre,
max( CASE WHEN pm.meta_key = '_billing_email' and p.ID = pm.post_id THEN pm.meta_value END ) as email,
max( CASE WHEN pm.meta_key = 'order_id_ml' and p.ID = pm.post_id THEN pm.meta_value END ) as order_id_ml,
max( CASE WHEN pm.meta_key = 'user_id_ml' and p.ID = pm.post_id THEN pm.meta_value END ) as user_id_ml
from
cbgw_posts as p
LEFT JOIN cbgw_postmeta as pm ON p.ID = pm.post_id
where ID = %s", $colname_rsCuentas);
$rsCuentas = mysql_query($query_rsCuentas, $Conexion) or die(mysql_error());
$row_rsCuentas = mysql_fetch_assoc($rsCuentas);
$totalRows_rsCuentas = mysql_num_rows($rsCuentas);
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
    <title><?php $titulo = 'Modificar Email'; echo $titulo; ?></title>
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
              <span class="input-group-addon"><i class="fa fa-user fa-fw"></i></span>
                <select name="cuentas_id" id="cuentas_id" class="form-control">
                <option value=""><?php echo $row_rsCuentas['nombre']?> <?php echo $row_rsCuentas['apellido']?></option>
                </select>
            </div>
            <p>
            <a target="_blank" href="https://myaccount.mercadolibre.com.ar/messaging/orders/<?php echo $row_rsCuentas['order_id_ml'];?>" class="btn btn-warning btn-xs" type="submit"> <i class="fa fa-comments" aria-hidden="true"></i> Mensajes en ML</a>
            <a target="_blank" href="https://perfil.mercadolibre.com.ar/profile/showProfile?id=<?php echo $row_rsCuentas['user_id_ml'];?>&role=buyer" class="btn btn-primary btn-xs" type="submit"> <i class="fa fa-user" aria-hidden="true"></i> Perfil en ML</a>
            <a target="_blank" href="https://myaccount.mercadolibre.com.ar/sales/vop?orderId=<?php echo $row_rsCuentas['order_id_ml'];?>&role=buyer" class="btn btn-success btn-xs" type="submit"> <i class="fa fa-shopping-bag" aria-hidden="true"></i> Venta en ML</a>
            </p>
            <div class="input-group form-group">
              <span class="input-group-addon"><i class="fa fa-envelope-o fa-fw"></i></span>
              <input class="form-control" type="text" name="email" id="email" placeholder="completar email" value="" autofocus>
            </div>
            
            <button class="btn btn-normal" type="submit">Modificar</button>
        <input type="hidden" name="MM_insert" value="form1">
        <input type="hidden" name="post_id" id="post_id" value="<?php echo $row_rsCuentas['post_id']; ?>">
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
	    <script type="text/javascript">
    $(function(){
    $('input[type="text"]').change(function(){
        this.value = $.trim(this.value);
    });
	});
	</script>
	<script type="text/javascript">
	var isFormValid = false;
	$('#form1').submit(function() {
	
	if (!isFormValid) {
		isFormValid = true;
        return false;
    }
	});
	</script><!-- InstanceEndEditable -->
  </body>
  
<!-- InstanceEnd --></html>
<?php
mysql_free_result($rsUsuarios);

mysql_free_result($rsCuentas);
?>