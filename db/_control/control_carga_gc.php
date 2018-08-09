<?php require_once('../../Connections/Conexion.php'); ?>
<?php
$MM_authorizedUsers = "Adm,Vendedor";
$MM_donotCheckaccess = "false";
require_once('../_autentificacion.php');
?>
<?php 
$editFormAction = $_SERVER['PHP_SELF'];
if (isset($_SERVER['QUERY_STRING'])) {
  $editFormAction .= "?" . htmlentities($_SERVER['QUERY_STRING']);
}

$date = date('Y-m-d H:i:s', time());

if ((isset($_POST["MM_insert"])) && ($_POST["MM_insert"] == "form1")) {
  $insertSQL = sprintf("INSERT INTO saldo_prov(usd, cotiz, ars, usuario, Day) VALUES (%s, %s, %s, %s, '$date')",
                      GetSQLValueString($_POST['carga_usd'], "double"),
					  GetSQLValueString($_POST['carga_cotiz'], "double"),
					  GetSQLValueString($_POST['carga_ars'], "double"),
					  GetSQLValueString($_POST['usuario'], "text"));

  mysql_select_db($database_Conexion, $Conexion);
  $Result1 = mysql_query($insertSQL, $Conexion) or die(mysql_error());

	echo "<script>window.top.location.href = \"control_carga_gc.php\";</script>";
	exit;

}

// solo si soy Administrador puedo mandar el parametro GET con el usuario que quiero controlar
if ($_SESSION['MM_UserGroup'] ==  'Adm'):
	if (isset($_GET['carga'])) {
  		$vendedor = (get_magic_quotes_gpc()) ? $_GET['carga'] : addslashes($_GET['carga']); 
	}
	else {$vendedor = $_SESSION['MM_Username'];}
else: $vendedor = $_SESSION['MM_Username'];
endif;
$vendedor .= "-GC";

	
mysql_select_db($database_Conexion, $Conexion);
$query_Diario = "SELECT COUNT(*) as Q, titulo, consola, round(AVG(costo_usd),0) as costo_usd, usuario FROM 
(SELECT titulo, consola, costo_usd, Day as D, usuario FROM `stock` where usuario='$vendedor' and DATE_FORMAT(Day, '%Y-%m-%d') >= DATE_FORMAT(NOW(), '%Y-%m-%d')
UNION ALL
SELECT titulo, consola, costo_usd, ex_Day_stock as D, ex_usuario as usuario FROM `saldo` where ex_usuario='$vendedor' and DATE_FORMAT(ex_Day_stock, '%Y-%m-%d') >= DATE_FORMAT(NOW(), '%Y-%m-%d')) AS resultado
GROUP BY consola, titulo
ORDER BY consola, titulo ASC";
$Diario = mysql_query($query_Diario, $Conexion) or die(mysql_error());
$row_Diario = mysql_fetch_assoc($Diario);
$totalRows_Diario = mysql_num_rows($Diario);

mysql_select_db($database_Conexion, $Conexion);
$query_Mensual = "SELECT COUNT(*) as Q, titulo, consola, round(AVG(costo_usd),0) as costo_usd, usuario FROM 
(SELECT titulo, consola, costo_usd, Day as D, usuario FROM `stock` where usuario='$vendedor' and DATE_FORMAT(Day, '%Y-%m') >= DATE_FORMAT(NOW(), '%Y-%m')
UNION ALL
SELECT titulo, consola, costo_usd, ex_Day_stock as D, ex_usuario as usuario FROM `saldo` where ex_usuario='$vendedor' and DATE_FORMAT(ex_Day_stock, '%Y-%m') >= DATE_FORMAT(NOW(), '%Y-%m')) AS resultado
GROUP BY consola, titulo
ORDER BY consola, titulo ASC";
$Mensual = mysql_query($query_Mensual, $Conexion) or die(mysql_error());
$row_Mensual = mysql_fetch_assoc($Mensual);
$totalRows_Mensual = mysql_num_rows($Mensual);

mysql_select_db($database_Conexion, $Conexion);
$query_Total = "SELECT COUNT(*) as Q, titulo, consola, round(AVG(costo_usd),0) as costo_usd, usuario FROM 
(SELECT titulo, consola, costo_usd, Day as D, usuario FROM `stock` where usuario='$vendedor'
UNION ALL
SELECT titulo, consola, costo_usd, ex_Day_stock as D, ex_usuario as usuario FROM `saldo` where ex_usuario='$vendedor') AS resultado
GROUP BY consola, titulo
ORDER BY consola, titulo ASC";
$Total = mysql_query($query_Total, $Conexion) or die(mysql_error());
$row_Total = mysql_fetch_assoc($Total);
$totalRows_Total = mysql_num_rows($Total);


mysql_select_db($database_Conexion, $Conexion);
$query_SaldoP = "SELECT Q, (costo_usd - (Q*0.01)) as costo_usd, costo_ars, SUM(usd) as carga_usd, SUM(ars) as carga_ars, saldo_prov.usuario FROM saldo_prov
LEFT JOIN 
(SELECT COUNT(*) as Q, SUM(costo_usd) as costo_usd, SUM(costo) as costo_ars, usuario FROM 
(SELECT costo_usd, costo, usuario FROM `stock` where usuario='$vendedor'
UNION ALL
SELECT costo_usd, costo, ex_usuario as usuario FROM `saldo` where ex_usuario='$vendedor') AS resultado GROUP by usuario) as gastado
ON saldo_prov.usuario = gastado.usuario
where saldo_prov.usuario='$vendedor'";
$SaldoP = mysql_query($query_SaldoP, $Conexion) or die(mysql_error());
$row_SaldoP = mysql_fetch_assoc($SaldoP);
$totalRows_SaldoP = mysql_num_rows($SaldoP);
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
    <link rel="icon" href="../favicon.ico">
	
<!-- InstanceBeginEditable name="doctitle" -->
    <title><?php $titulo = 'Cargas de '; $titulo .= $vendedor; echo $titulo; ?></title>
<!-- InstanceEndEditable -->

	  
    <!-- Font Awesome style desde mi servidor -->
    <link href="../css/font-awesome.min.css" rel="stylesheet">
    
    <!-- link a mi css -->
    <link href="../css/bootstrap.css" rel="stylesheet">
    
    <!-- Bootstrap SITE CSS -->
    <link href="../css/site.css" rel="stylesheet">

    <!-- Custom styles for this template -->
    <link href="../css/offcanvas.css" rel="stylesheet">
    
	<!-- 2017-12-30 Agrego nuevo css de BootFLAT --> 
    <link href="../css/bootflat.css" rel="stylesheet">
	  
	<!-- Estilo personalizado por mi -->
	<link href="../css/personalizado.css" rel="stylesheet">
	 	
    <!--- BootFLAT core CSS 
    <link href="../db/css/site.min.css" rel="stylesheet">
	-->

    <!-- InstanceBeginEditable name="head" -->
    <!-- antes que termine head-->

<!-- InstanceEndEditable -->
  </head>

  <body>
  <?php include('../_barra_nav.php'); ?>

    <div class="container">
	<h1><?php echo $titulo; ?></h1>
    <!-- InstanceBeginEditable name="body" -->
		<p><?php if ($_SESSION['MM_UserGroup'] ==  'Adm'):?> <a class="btn btn-info" href="../?carga=Francisco">ver Francisco</a><?php endif;?></p>
		<?php if ($_SESSION['MM_UserGroup'] ==  'Adm'):?>
		
			<form method="post" name="form1" action="<?php echo $editFormAction; ?>">
				<div class="col-md-4">
                <div class="input-group form-group" id="div_costo_usd">
                <span class="input-group-addon">usd</span>
                  <input class="form-control" type="text" name="carga_usd" id="multiplicando" value="">
                </div><!-- /input-group -->
              </div><!-- /.col-lg-6 -->
              
              <?php $amount = urlencode($amount);
				  $get = file_get_contents("https://finance.google.com/finance/converter?a=1&from=USD&to=ARS");
				  $get = explode("<span class=bld>",$get);
				  $get = explode("</span>",$get[1]);  
				  $converted_amount = preg_replace("/[^0-9\.]/", null, $get[0]);
				  $cotiz =  round(($converted_amount + 0.27), 1, PHP_ROUND_HALF_UP);
				?>
              <div class="col-md-4">
                <div class="input-group form-group">
                <span class="input-group-addon">ctz</span>
                  <input class="form-control" type="text" name="carga_cotiz" id="multiplicador" value="<?php echo $cotiz; ?>">
                </div><!-- /input-group -->
              </div><!-- /.col-lg-6 -->
            
            <div class="col-md-4">
            <div class="input-group form-group" id="caja3">
              <span class="input-group-addon"><i class="fa fa-dollar fa-fw"></i></span>
              <input class="form-control" type="text" name="carga_ars" id="resultado" value="" style="text-align:right;">
			</div>
            </div>

				<button class="btn btn-success" type="submit">Carga a <?php echo $vendedor; ?></button>
				<input type="hidden" name="MM_insert" value="form1">
				<input type="hidden" name="usuario" value="<?php echo $vendedor; ?>">
			</form>
		<?php endif;?>
    <div class="row">
    <div class="col-md-4">
	<h4>Saldo Proveedor <span class="label label-normal"><?php if ($row_SaldoP): ?><?php echo ($row_SaldoP['carga_usd'] - $row_SaldoP['costo_usd']); ?><?php endif;?></span></h4>
	</div>
		
	<div class="col-md-4">
    <?php if ($row_Diario): ?>
	<h4>Listado del Día</h4>
    <div class="table-responsive">
    <table border="0" align="center" cellpadding="0" cellspacing="5" class="table table-striped">
    <thead>
              <tr>
				<th>Q</th>
				<th>Cover</th>
				<th>Producto</th>
                
                <th>Costo USD</th>
                <th>Total USD</th>
              </tr>
            </thead>
		  <tbody>
          <?php $q = 0; $ct = 0; do { ?><tr>
          	<td><?php echo $row_Diario['Q']; ?> x </td>
            <td><img class="img-rounded" width="50" id="image-swap" src="/img/productos/<?php echo $row_Diario['consola']."/".$row_Diario['titulo'].".jpg";?>"alt="" /></td>
            <td><?php echo str_replace('-', ' ', $row_Diario['titulo']);?></td>
            
			<td><?php echo $row_Diario['costo_usd']; ?></td>
            <td><?php echo ($row_Diario['costo_usd'] * $row_Diario['Q']); ?></td>
          </tr>
        <?php $q = $q + $row_Diario['Q']; $ct = $ct + ($row_Diario['costo_usd'] * $row_Diario['Q']); } while ($row_Diario = mysql_fetch_assoc($Diario)); ?>
		<tr>
			<th><?=$q?></th>
			<th></th>
			<th></th>
			<th></th>
			<th><?=$ct?></th>
      	</tr> 
        </tbody>
        </table>
        </div>
        <?php endif; ?> 
		<?php if ($row_Mensual): ?>
	<h4>Listado del Mes</h4>
    <div class="table-responsive">
        <table border="0" align="center" cellpadding="0" cellspacing="5" class="table table-striped">
    <thead>
              <tr>
				<th>Q</th>
				<th>Cover</th>
				<th>Producto</th>
                
                <th>Costo USD</th>
                <th>Total USD</th>
              </tr>
            </thead>
		  <tbody>
          <?php $q = 0; $ct = 0; do { ?><tr>
          	<td><?php echo $row_Mensual['Q']; ?> x </td>
            <td><img class="img-rounded" width="50" id="image-swap" src="/img/productos/<?php echo $row_Mensual['consola']."/".$row_Mensual['titulo'].".jpg";?>"alt="" /></td>
            <td><?php echo str_replace('-', ' ', $row_Mensual['titulo']);?></td>
            
			<td><?php echo $row_Mensual['costo_usd']; ?></td>
            <td><?php echo ($row_Mensual['costo_usd'] * $row_Mensual['Q']); ?></td>
          </tr>
        <?php $q = $q + $row_Mensual['Q']; $ct = $ct + ($row_Mensual['costo_usd'] * $row_Mensual['Q']); } while ($row_Mensual = mysql_fetch_assoc($Mensual)); ?>
		<tr>
			<th><?=$q?></th>
			<th></th>
			<th></th>
			<th></th>
			<th><?=$ct?></th>
      	</tr> 
        </tbody>
        </table>
        </div>
        <?php endif; ?>
    </div>
	
		
	<div class="col-md-2">
    </div>
    <div class="col-md-4 pull-right">
    
    <?php if ($row_Total): ?>
	<h4>Listado Total</h4>
    <div class="table-responsive">
        <table border="0" align="center" cellpadding="0" cellspacing="5" class="table table-striped">
    <thead>
              <tr>
				<th>Q</th>
				<th>Cover</th>
				<th>Producto</th>
                
                <th>Costo USD</th>
                <th>Total USD</th>
              </tr>
            </thead>
		  <tbody>
          <?php $q = 0; $ct = 0; do { ?><tr>
          	<td><?php echo $row_Total['Q']; ?> x </td>
            <td><img class="img-rounded" width="50" id="image-swap" src="/img/productos/<?php echo $row_Total['consola']."/".$row_Total['titulo'].".jpg";?>"alt="" /></td>
            <td><?php echo str_replace('-', ' ', $row_Total['titulo']);?></td>
            
			<td><?php echo $row_Total['costo_usd']; ?></td>
            <td><?php echo ($row_Total['costo_usd'] * $row_Total['Q']); ?></td>
          </tr>
        <?php $q = $q + $row_Total['Q']; $ct = $ct + ($row_Total['costo_usd'] * $row_Total['Q']); } while ($row_Total = mysql_fetch_assoc($Total)); ?>
		<tr>
			<th><?=$q?></th>
			<th></th>
			<th></th>
			<th></th>
			<th><?=$ct?></th>
      	</tr> 
        </tbody>
        </table>
        </div>
        <?php endif; ?> 
    </div>
    </div>
    
     <!--/row-->
     <!-- InstanceEndEditable -->
    </div><!--/.container-->
	<!-- Bootstrap core JavaScript
    ================================================== -->
	<!-- Placed at the end of the document so the pages load faster -->
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/1.12.4/jquery.min.js"></script>
	<script src="../assets/js/docs.min.js"></script>
    <script src="../js/bootstrap.min.js"></script>
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
	<script type="text/javascript">
	$("#multiplicando").keyup(function() {
      m1 = document.getElementById("multiplicando").value;
      m2 = document.getElementById("multiplicador").value;
      r = m1*m2;
      document.getElementById("resultado").value = r;
    });
	$("#multiplicador").keyup(function() {
      m1 = document.getElementById("multiplicando").value;
      m2 = document.getElementById("multiplicador").value;
      r = m1*m2;
      document.getElementById("resultado").value = r;
    });
	</script>
<!-- InstanceEndEditable -->
  </body>
  
<!-- InstanceEnd --></html>
<?php
mysql_free_result($Diario);

mysql_free_result($Mensual);

mysql_free_result($Total);

mysql_free_result($SaldoP);
?>