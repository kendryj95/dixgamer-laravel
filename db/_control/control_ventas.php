<?php require_once('../../Connections/Conexion.php'); ?>
<?php
$MM_authorizedUsers = "Adm";
$MM_donotCheckaccess = "false";
require_once('../_autentificacion.php');
?>
<?php 
$currentPage = $_SERVER["PHP_SELF"];
mysql_select_db($database_Conexion, $Conexion);
$query_rsUsuarios = "SELECT * FROM usuarios";
$rsUsuarios = mysql_query($query_rsUsuarios, $Conexion) or die(mysql_error());
$row_rsUsuarios = mysql_fetch_assoc($rsUsuarios);
$totalRows_rsUsuarios = mysql_num_rows($rsUsuarios);

$maxRows_rsClientes = 100;
$pageNum_rsClientes = 0;
if (isset($_GET['pageNum_rsClientes'])) {
  $pageNum_rsClientes = $_GET['pageNum_rsClientes'];
}
$startRow_rsClientes = $pageNum_rsClientes * $maxRows_rsClientes;

mysql_select_db($database_Conexion, $Conexion);
$query_rsClientes = "SELECT ventas.ID AS ID_ventas, clientes_id, stock_id, slot, medio_venta, medio_cobro, precio, comision, ventas.Notas AS ventas_Notas, ventas.Day as ventas_Day, ventas.usuario as ventas_usuario, apellido, nombre, titulo, consola, cuentas_id, costo, q_vta FROM ventas
LEFT JOIN (select ventas_id, medio_cobro, sum(precio) as precio, sum(comision) as comision FROM ventas_cobro GROUP BY ventas_id) as ventas_cobro ON ventas.ID = ventas_cobro.ventas_id
LEFT JOIN clientes ON ventas.clientes_id = clientes.ID
LEFT JOIN (select ID, titulo, consola, cuentas_id, costo, q_vta FROM stock LEFT JOIN (select count(*) as q_vta, stock_id from ventas group by stock_id) as vendido ON stock.ID = vendido.stock_id) as stock ON ventas.stock_id = stock.ID ORDER BY ventas.ID DESC";
$query_limit_rsClientes = sprintf("%s LIMIT %d, %d", $query_rsClientes, $startRow_rsClientes, $maxRows_rsClientes);
$rsClientes = mysql_query($query_limit_rsClientes, $Conexion) or die(mysql_error());
$row_rsClientes = mysql_fetch_assoc($rsClientes);

if (isset($_GET['totalRows_rsClientes'])) {
  $totalRows_rsClientes = $_GET['totalRows_rsClientes'];
} else {
  $all_rsClientes = mysql_query($query_rsClientes);
  $totalRows_rsClientes = mysql_num_rows($all_rsClientes);
}
$totalPages_rsClientes = ceil($totalRows_rsClientes/$maxRows_rsClientes)-1;

$queryString_rsClientes = "";
if (!empty($_SERVER['QUERY_STRING'])) {
  $params = explode("&", $_SERVER['QUERY_STRING']);
  $newParams = array();
  foreach ($params as $param) {
    if (stristr($param, "pageNum_rsClientes") == false && 
        stristr($param, "totalRows_rsClientes") == false) {
      array_push($newParams, $param);
    }
  }
  if (count($newParams) != 0) {
    $queryString_rsClientes = "&" . htmlentities(implode("&", $newParams));
  }
}
$queryString_rsClientes = sprintf("&totalRows_rsClientes=%d%s", $totalRows_rsClientes, $queryString_rsClientes);

mysql_select_db($database_Conexion, $Conexion);
$query_rsGtoEst = "SELECT (gasto/ingreso) as gto_x_ing FROM (SELECT (SELECT SUM(importe) as Gto_Tot FROM gastos WHERE concepto NOT LIKE '%IIBB%') as gasto, (SELECT SUM(precio) as Ing_Tot FROM ventas_cobro) as ingreso) as resultado";
$rsGtoEst = mysql_query($query_rsGtoEst, $Conexion) or die(mysql_error());
$row_rsGtoEst = mysql_fetch_assoc($rsGtoEst);
$totalRows_rsGtoEst = mysql_num_rows($rsGtoEst);
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
    <title><?php $titulo = 'Listar ventas'; echo $titulo; ?></title>
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
    <div class="table-responsive">
    <table border="0" align="center" cellpadding="0" cellspacing="5" class="table table-striped">
    <thead>
              <tr>
				<th>#</th>
                <th>ID Vta</th>
                <th>Fecha</th>
                <th width="50">Cover</th>
                <th>Cliente</th>
                <th>Medios</th>
                <th>Precio</th>
                <th>Rdo</th>
              </tr>
            </thead>
		  <tbody>
          <?php $i = 1; do { ?><tr>
          
         <?php 
			if (strpos($row_rsClientes['medio_venta'], 'Web') !== false): $text = 'W'; $color1 = 'info';
			elseif (strpos($row_rsClientes['medio_venta'], 'Mail') !== false): $text = 'M'; $color1 = 'danger';
			elseif (strpos($row_rsClientes['medio_venta'], 'Mercado') !== false): $text = 'ML'; $color1 = 'warning';
        	endif;
		?>
        <?php 
			if (strpos($row_rsClientes['medio_cobro'], 'Transferencia') !== false): $text2 = 'Bco'; $color2 = 'default';
			elseif (strpos($row_rsClientes['medio_cobro'], 'Ticket') !== false): $text2 = 'Cash'; $color2 = 'success';
			elseif (strpos($row_rsClientes['medio_cobro'], 'Mercado') !== false): $text2 = 'MP'; $color2 = 'primary';
        	endif;
		?>
		<?php
			$persona = $row_rsClientes['ventas_usuario'];
			include '../_colores.php';
		?>
		
        <?php
		// Aplico un mejor criterio de "asignar" costo a las ventas de PS3, si el jugo tiene 4 o mas ventas le asigno costo proporcional, si tiene menos ventas le asigno solo el 25%
		if($row_rsClientes['q_vta'] > '3'): $proporcional = (1 / $row_rsClientes['q_vta']);
		else: $proporcional = 0.25;
		endif;
		?>
        <?php 
				if (($row_rsClientes['consola'] == 'ps4') && ($row_rsClientes['slot'] == 'Primario')): $costo = round($row_rsClientes['costo'] * 0.6); 
				elseif (($row_rsClientes['consola'] == 'ps4') && ($row_rsClientes['slot'] == 'Secundario')): $costo = round($row_rsClientes['costo'] * 0.4);
				elseif ($row_rsClientes['consola'] == 'ps3'): $costo = round($row_rsClientes['costo'] * $proporcional);
				elseif (($row_rsClientes['consola'] !== 'ps4') && ($row_rsClientes['consola'] !== 'ps3') && ($row_rsClientes['titulo'] !== 'plus-12-meses-slot')): $costo = round($row_rsClientes['costo']);
            endif;
		
				$gtoestimado = round($row_rsGtoEst['gto_x_ing'] * $row_rsClientes['precio']);
				$iibbestimado = round($row_rsClientes['precio'] * 0.04);
				$ganancia = round($row_rsClientes['precio'] - $row_rsClientes['comision'] - $costo - $gtoestimado - $iibbestimado);
			?>
          	<td><?php echo $i;?></td>
          	<td><?php echo $row_rsClientes['ID_ventas']; ?><?php if ($row_rsClientes['ventas_Notas']):?><a href="#" data-toggle="popover" data-placement="bottom" data-trigger="focus" title="Notas" data-content="<?php echo $row_rsClientes['ventas_Notas']; ?>" class="h6" style="color: #555555;"><i class="fa fa-comment fa-fw"></i></a><?php endif; ?></td>
            <td><?php echo date("d-M", strtotime($row_rsClientes['ventas_Day'])); ?></td>
            <td><img class="img-rounded" width="50" id="image-swap" src="/img/productos/<?php echo $row_rsClientes['consola']."/".$row_rsClientes['titulo'].".jpg";?>"alt="" /></td>
            <td><a title="Ir a Cliente" href="../clientes_detalles.php?id=<?php echo $row_rsClientes['clientes_id']; ?>"><?php echo $row_rsClientes['nombre']; ?> <?php echo $row_rsClientes['apellido']; ?></a><br /><br /><span style="opacity:0.5" class="text-muted btn-xs"><i class="fa fa-gamepad fa-fw" aria-hidden="true"></i> <?php echo $row_rsClientes['stock_id']; ?></span> <?php if ($row_rsClientes['cuentas_id']):?><a style="opacity:0.5" class="text-muted btn-xs" href="../cuentas_detalles.php?id=<?php echo $row_rsClientes['cuentas_id']; ?>" title="Ir a Cuenta"><i class="fa fa-link fa-xs fa-fw" aria-hidden="true"></i> <?php echo $row_rsClientes['cuentas_id']; ?></a> <?php endif; ?><?php if ($row_rsClientes['slot'] == 'Secundario'): ?><span class="label label-danger" style="opacity:0.5">2°</span><?php endif; ?></td>
            <td><small class="label label-<?php echo $color1;?>" style="opacity:0.7; font-weight:400;" title="<?php echo $row_rsClientes['medio_venta']; ?>"><?php echo $text;?></small> <small class="label label-<?php echo $color2;?>" style="opacity:0.7; font-weight:400;" title="<?php echo $row_rsClientes['medio_cobro']; ?>"><?php echo $text2;?></small></td>
            <td><span class="<?php if ($row_rsClientes['precio'] < '1'):?>badge badge-danger<?php endif;?>"><?php echo round($row_rsClientes['precio']); ?></span></td>
            <td><span class="<?php if ($ganancia < '0'):?>badge badge-danger<?php endif;?>"><?php echo $ganancia; ?></span></td>
            <td><span class="badge badge-<?php echo $color;?> pull-right" style="opacity:0.7; font-weight:400;"><?php echo substr($row_rsClientes['ventas_usuario'],0 , 1); ?></span></td>
            
          </tr>
        <?php $i++;} while ($row_rsClientes = mysql_fetch_assoc($rsClientes)); ?>
        </tbody>
        </table>
        <div>
        <div class="col-md-12">
                <ul class="pager">
                	<?php if ($pageNum_rsClientes > 0) { // Show if not first page ?>
                 	 <li class="previous"><a title="Anterior" href="<?php printf("%s?pageNum_rsClientes=%d%s", $currentPage, max(0, $pageNum_rsClientes - 1), $queryString_rsClientes); ?>">Anterior</a></li>
                     <?php } else { ?>
                    <li class="previous disabled"><a href="#">Anterior</a></li>
                     <?php } // Show if not first page ?>
                     <li class="disabled"><?php echo ($startRow_rsClientes + 1) ?>-<?php echo min($startRow_rsClientes + $maxRows_rsClientes, $totalRows_rsClientes) ?>&nbsp;</li>
                  <?php if ($pageNum_rsClientes < $totalPages_rsClientes) { // Show if not last page ?>
                  <li class="next"><a title="Siguiente" href="<?php printf("%s?pageNum_rsClientes=%d%s", $currentPage, min($totalPages_rsClientes, $pageNum_rsClientes + 1), $queryString_rsClientes); ?>">Siguiente</a></li>
                    <?php } else { ?>
                    <li class="next disabled"><a href="#">Siguiente</a></li>
                    <?php } // Show if not last page ?>
                </ul>
              </div>
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
    <!-- extras de script y demás yerbas -->
<!-- InstanceEndEditable -->
  </body>
  
<!-- InstanceEnd --></html>
<?php
mysql_free_result($rsUsuarios);

mysql_free_result($rsClientes);

mysql_free_result($rsGtoEst);
?>
