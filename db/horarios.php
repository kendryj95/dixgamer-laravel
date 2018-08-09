<?php require_once('../Connections/Conexion.php'); ?>
<?php
$MM_authorizedUsers = "Adm";
$MM_donotCheckaccess = "false";
require_once('_autentificacion.php');
?>
<?php 

$day = date('Y-m-d', time());
$date = date('Y-m-d H:i:s', time());

mysql_select_db($database_Conexion, $Conexion);
$query_rsUsuarios = "SELECT * FROM usuarios";
$rsUsuarios = mysql_query($query_rsUsuarios, $Conexion) or die(mysql_error());
$row_rsUsuarios = mysql_fetch_assoc($rsUsuarios);
$totalRows_rsUsuarios = mysql_num_rows($rsUsuarios);


mysql_select_db($database_Conexion, $Conexion);
$query_rsDias = sprintf("SELECT *, (time_to_sec(timediff(fin, inicio)) / 3600) as Q_horas FROM horario WHERE (date_format(Day, '%%Y-%%m')=date_format(NOW(), '%%Y-%%m') or (date_format(Day, '%%Y-%%m') = date_format((DATE_SUB(curdate(), INTERVAL 1 MONTH)), '%%Y-%%m'))) AND fin IS NOT NULL ORDER BY Day desc, inicio DESC", $vendedor);
$rsDias = mysql_query($query_rsDias, $Conexion) or die(mysql_error());
$row_rsDias = mysql_fetch_assoc($rsDias);
$totalRows_rsDias = mysql_num_rows($rsDias);

mysql_select_db($database_Conexion, $Conexion);
$query_rsMeses = sprintf("SELECT date_format(Day, '%%Y-%%m') as mes, COUNT(*) as Q_dias, SUM(time_to_sec(timediff(fin, inicio)) / 3600) as Q_horas , usuario FROM horario GROUP BY date_format(Day, '%%Y-%%m'), usuario DESC ORDER BY mes DESC", $vendedor);
$rsMeses = mysql_query($query_rsMeses, $Conexion) or die(mysql_error());
$row_rsMeses = mysql_fetch_assoc($rsMeses);
$totalRows_rsMeses = mysql_num_rows($rsMeses);
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
    <title><?php $titulo = 'Horarios ';  echo $titulo; ?></title>
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
    <div class="col-md-6">
    <h4>Listado por día</h4>
    <?php if ($row_rsDias): ?>
    <div class="table-responsive">
    <table border="0" align="center" cellpadding="0" cellspacing="5" class="table table-striped">
    <thead>
              <tr>
                <th>Día</th>
                <th>Inicio</th>
                <th>Fin</th>
                <th><i class="fa fa-user fa-fw" aria-hidden="true"></i></th>
                <th>Total</th>
                <th><i class="fa fa-check fa-fw" aria-hidden="true"></i></th>
              </tr>
            </thead>
		  <tbody>
          <?php do { ?><tr>
		
          <?php
			$persona = $row_rsDias['usuario'];
			include '_colores.php';
			?>
            <td><?php echo date("j", strtotime($row_rsDias['Day'])); ?></td>
            <td class="text-muted"><?php echo date("H:i", strtotime($row_rsDias['inicio'])); ?></td>
            <td class="text-muted"><?php echo date("H:i", strtotime($row_rsDias['fin'])); ?></td>
            <td><span class="label label-<?php echo $color;?>"><strong><?php echo substr($row_rsDias['usuario'],0 , 1); ?></strong></span></td>
            <td><?php echo number_format((float)$row_rsDias['Q_horas'], 2, ',', ''); ?></td>
            <td><?php if ($row_rsDias['verificado'] == "si"): ?><i class="fa fa-check fa-fw" style="color:#ddd;" aria-hidden="true"></i><?php else: ?><a class="text-center btn btn-success btn-xs" title="verificar" href="horarios_verificados.php?ID=<?php echo $row_rsDias['ID'];?>"><i class="fa fa-check fa-fw"  aria-hidden="true"></i></a><?php endif;?></td>
          </tr>
        <?php } while ($row_rsDias = mysql_fetch_assoc($rsDias)); ?>
        </tbody>
        </table>
        </div>
        <?php endif; ?>  
    </div>
     <div class="col-md-2">
    </div>
    <div class="col-md-4 pull-right">
    <h4 style="text-align:right">Listado por mes</h4>
       <?php if ($row_rsMeses): ?>
    <div class="table-responsive">
    <table border="0" align="center" cellpadding="0" cellspacing="5" class="table table-striped">
    <thead>
          <?php do { ?><tr>
		
          <?php
			$persona = $row_rsMeses['usuario'];
			include '_colores.php';
			?>
          	<td style="text-align:right"><?php echo $row_rsMeses['mes']; ?></td>
            <td style="text-align:right"><span class="label label-<?php echo $color;?>"><strong><?php echo substr($row_rsMeses['usuario'],0 , 1); ?></strong></span></td>
            <td style="text-align:right"><?php echo $row_rsMeses['Q_dias']; ?></td>
            <td style="text-align:right"><strong><?php echo number_format((float)$row_rsMeses['Q_horas'], 2, ',', ''); ?></strong></td>
          </tr>
        <?php } while ($row_rsMeses = mysql_fetch_assoc($rsMeses)); ?>
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

mysql_free_result($rsHoy);

mysql_free_result($rsHorario);

mysql_free_result($rsDias);

mysql_free_result($rsMeses);
?>