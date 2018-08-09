<?php require_once('../Connections/Conexion.php'); ?>
<?php
$MM_restrictGoTo = "index.php";
$MM_authorizedUsers = "Adm,Vendedor";
$MM_donotCheckaccess = "false";
require_once('_autentificacion.php');
?>
<?php 
$nombresito = $_SESSION['MM_Username'];

mysql_select_db($database_Conexion, $Conexion);
$query_rsUsuarios = sprintf("SELECT * FROM usuarios WHERE Nombre = '$nombresito' ORDER BY Nombre ASC",$nombresito);
$rsUsuarios = mysql_query($query_rsUsuarios, $Conexion) or die(mysql_error());
$row_rsUsuarios = mysql_fetch_assoc($rsUsuarios);
$totalRows_rsUsuarios = mysql_num_rows($rsUsuarios);

	//obtengo el OII
	if (isset($_GET['order_item_id'])) {
	  $colname_rsOII = (get_magic_quotes_gpc()) ? $_GET['order_item_id'] : addslashes($_GET['order_item_id']);
		//armo link con order item id
		$OII = "&order_item_id=" . $colname_rsOII;}

	//controlo que ya no exista una venta para ese OII
	mysql_select_db($database_Conexion, $Conexion);
	$query_rsExiste_OII = sprintf("SELECT order_item_id, clientes_id, usuario FROM ventas where order_item_id='%s'",$colname_rsOII); 
	$rsExiste_OII = mysql_query($query_rsExiste_OII, $Conexion) or die(mysql_error());
	$row_rsExiste_OII = mysql_fetch_assoc($rsExiste_OII);
	$totalRows_rsExiste_OII = mysql_num_rows($rsExiste_OII);

	$asignador = $row_rsExiste_OII['usuario'];
	$linkCte = $row_rsExiste_OII['clientes_id'];

	//Si existe venta aviso al gestor que ya se asignó antes y le muestro el link para verlo
	if($totalRows_rsExiste_OII > 0){exit("Este pedido ya fue asignado por $asignador <br><a href='clientes_detalles.php?id=$linkCte' target='_blank'>Ver Venta</a>");}

/***     Stock Nuevo   ***/    
mysql_select_db($database_Conexion, $Conexion);
$query_rsStockNuevo = "SELECT ID AS ID_stk, titulo, consola, cuentas_id AS stk_ctas_id, round(AVG(costo),0) as costo, ID_vtas, Q_vta, dayvta, COUNT(*) AS Q_Stock
FROM stock
LEFT JOIN
(SELECT ventas.ID as ID_vtas, stock_id, slot, COUNT(*) AS Q_vta, Day AS dayvta
FROM ventas
GROUP BY stock_id
ORDER BY ID DESC) AS vendido
ON ID = stock_id
WHERE (consola != 'ps4') AND (consola != 'ps3') AND (Q_vta IS NULL) AND (titulo != 'plus-12-meses-slot')
GROUP BY consola, titulo
ORDER BY consola, titulo DESC";
$rsStockNuevo = mysql_query($query_rsStockNuevo, $Conexion) or die(mysql_error());
$row_rsStockNuevo = mysql_fetch_assoc($rsStockNuevo);
$totalRows_rsStockNuevo = mysql_num_rows($rsStockNuevo);
/***     stock nuevo     ***/

/***     PS4 PRIMARIO     ***/
mysql_select_db($database_Conexion, $Conexion);
$query_rsPrimario = "SELECT ID AS ID_stk, titulo, consola, cuentas_id AS stk_ctas_id, round(AVG(costo),0) as costo, ID_vta, Q_vta, dayvta, Q_vta_pri, Q_vta_sec, COUNT(*) AS Q_Stock
FROM stock
### 2018-05-17 de acá quité el left join con reseteo sin sentido
LEFT JOIN
(SELECT ventas.ID as ID_vta, stock_id, SUM(case when slot = 'Primario' then 1 else null end) AS Q_vta_pri, SUM(case when slot = 'Secundario' then 1 else null end) AS Q_vta_sec, COUNT(*) AS Q_vta, Day AS dayvta
FROM ventas
GROUP BY stock_id) AS vendido
ON ID = stock_id
WHERE (consola = 'ps4' OR titulo = 'plus-12-meses-slot') AND (Q_vta_pri IS NULL)
GROUP BY  consola, titulo
ORDER BY consola, titulo, ID DESC";
$rsPrimario = mysql_query($query_rsPrimario, $Conexion) or die(mysql_error());
$row_rsPrimario = mysql_fetch_assoc($rsPrimario);
$totalRows_rsPrimario = mysql_num_rows($rsPrimario);
/***     PS4 PRIMARIO     ***/

/***     PS4 SECUNDARIO     ***/
mysql_select_db($database_Conexion, $Conexion);
$query_rsSecundario = "SELECT ID AS ID_stk, titulo, consola, cuentas_id AS stk_ctas_id, round(AVG(costo),0) as costo, ID_vta, Q_vta, dayvta, Q_vta_pri, Q_vta_sec, COUNT(*) AS Q_Stock
FROM stock
### 2018-05-17 de acá quité el left join con reseteo sin sentido
LEFT JOIN
(SELECT ventas.ID as ID_vta, stock_id, SUM(case when slot = 'Primario' then 1 else null end) AS Q_vta_pri, SUM(case when slot = 'Secundario' then 1 else null end) AS Q_vta_sec, COUNT(*) AS Q_vta, Day AS dayvta
FROM ventas
GROUP BY stock_id) AS vendido
ON ID = stock_id
WHERE (consola = 'ps4' OR titulo = 'plus-12-meses-slot') AND (Q_vta_sec IS NULL)
GROUP BY  consola, titulo
ORDER BY consola, titulo, ID DESC";
$rsSecundario = mysql_query($query_rsSecundario, $Conexion) or die(mysql_error());
$row_rsSecundario = mysql_fetch_assoc($rsSecundario);
$totalRows_rsSecundario = mysql_num_rows($rsSecundario);
/***     PS4 SECUNDARIO     ***/

/***     PS3     ***/
mysql_select_db($database_Conexion, $Conexion);
$query_rsPS3 = "SELECT ID_stk, titulo, consola, stk_ctas_id, dayreset, Q_reset, days_from_reset, Q_vta, round(AVG(costo),0) as costo, SUM(Q_Stock) AS Q_Stock FROM (SELECT ID AS ID_stk, titulo, consola, round(AVG(costo),0) as costo, cuentas_id AS stk_ctas_id, ID_reseteo AS ID_reset, r_cuentas_id AS reset_ctas_id, dayreseteo AS dayreset, reset.Q_reseteado AS Q_reset, DATEDIFF(NOW(), dayreseteo) AS days_from_reset, ID_vta, Q_vta, dayvta, ((2 + (IFNULL(Q_reseteado, 0) * 2)) - IFNULL(Q_vta, 0)) AS Q_Stock
FROM stock 
LEFT JOIN
(SELECT ID AS ID_reseteo, cuentas_id AS r_cuentas_id, COUNT(*) AS Q_reseteado, MAX(Day) AS dayreseteo
FROM reseteo
GROUP BY cuentas_id
ORDER BY ID DESC) AS reset
ON cuentas_id = r_cuentas_id
LEFT JOIN
(SELECT ventas.ID as ID_vta, stock_id, COUNT(*) AS Q_vta, Day AS dayvta
FROM ventas
GROUP BY stock_id) AS vendido
ON ID = stock_id
WHERE consola = 'ps3' AND (((Q_vta IS NULL) OR (Q_vta < '2')) OR (((Q_vta >= '2') AND (Q_reseteado = FLOOR(Q_vta/2)))))
GROUP BY ID
ORDER BY Q_reset, consola, titulo, ID DESC) AS consulta
GROUP BY consola, titulo
ORDER BY consola, titulo, ID_stk"; 
$rsPS3 = mysql_query($query_rsPS3, $Conexion) or die(mysql_error());
$row_rsPS3 = mysql_fetch_assoc($rsPS3);
$totalRows_rsPS3 = mysql_num_rows($rsPS3);
/***     PS3        ***/

/***     PS3  RESETEAR   ***/
mysql_select_db($database_Conexion, $Conexion);
$query_rsPS3Resetear = "SELECT ID AS ID_stk, titulo, consola, cuentas_id AS stk_ctas_id, costo, ID_reseteo AS ID_reset, r_cuentas_id AS reset_ctas_id, dayreseteo AS dayreset, reset.Q_reseteado AS Q_reset, DATEDIFF(NOW(), dayreseteo) AS days_from_reset, ID_vta, Q_vta, dayvta, (DATEDIFF(NOW(), dayvta) - 1) AS days_from_vta
FROM stock
LEFT JOIN
(SELECT ID AS ID_reseteo, cuentas_id AS r_cuentas_id, COUNT(*) AS Q_reseteado, MAX(Day) AS dayreseteo
FROM reseteo
GROUP BY cuentas_id
ORDER BY ID DESC) AS reset
ON cuentas_id = r_cuentas_id
LEFT JOIN
(SELECT ventas.ID as ID_vta, stock_id, COUNT(*) AS Q_vta, MAX(Day) AS dayvta
FROM ventas
GROUP BY stock_id) AS vendido
ON ID = stock_id
WHERE (consola = 'ps3') AND ((Q_vta >= '2' AND ID_reseteo IS NULL) OR (((Q_vta >= '2') AND ((Q_reseteado + 1) = FLOOR(Q_vta/2))) AND DATEDIFF(NOW(), dayreseteo) > '180')) AND cuentas_id NOT IN (6, 361)
ORDER BY consola, titulo, ID DESC"; // el ultimo condition es para filtrar las cuentas que me "robaron" y ya no se puede resetear
// 2018-03-01 Reemplazo la linea de WHERE CLAUSE
// para evitar mostrar cuentas reseteadas que todavía no vendieron los dos slots luego del último reseteo
//
// Antes ->  WHERE (consola = 'ps3') AND ((Q_vta >= '2' AND ID_reseteo IS NULL) OR (Q_vta >= '4' AND DATEDIFF(NOW(), dayreseteo) > '180')) AND cuentas_id NOT IN (6, 361)
//
$rsPS3Resetear = mysql_query($query_rsPS3Resetear, $Conexion) or die(mysql_error());
$row_rsPS3Resetear = mysql_fetch_assoc($rsPS3Resetear);
$totalRows_rsPS3Resetear = mysql_num_rows($rsPS3Resetear);
/***     PS3  RESETEAR      ***/

$vendedor = $_SESSION['MM_Username'];
$TZ = "SET SESSION time_zone = '-3:00'";
mysql_select_db($database_Conexion, $Conexion);
$ResultTZ = mysql_query($TZ, $Conexion) or die(mysql_error());

mysql_select_db($database_Conexion, $Conexion);
$query_rsHoy = sprintf("SELECT * FROM horario WHERE usuario = '$vendedor' ORDER BY ID DESC LIMIT 1", $vendedor);
$rsHoy = mysql_query($query_rsHoy, $Conexion) or die(mysql_error());
$row_rsHoy = mysql_fetch_assoc($rsHoy);
$totalRows_rsHoy = mysql_num_rows($rsHoy);
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
    <title><?php $titulo = 'Inicio'; echo $titulo; ?></title>
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

  <body style="padding: 20px 0px;">	
	  
<?php 
// Si hay Order Item Id definido, oculto lo siguiente
if (!(isset($_GET['order_item_id']))):?>
	  
  <?php include('_barra_nav.php'); ?>

    <div class="container">
	<h1><?php echo $titulo; ?></h1>
	<?php if ($_SESSION['MM_UserGroup'] ==  'Adm'):?>
    <!-- Alertar falta de stock en productos con descuento -->
			<div class="col-md-12">
    <?php include('stock_alertar_precio_ps3.php'); ?>
    <?php include('stock_alertar_precio_ps4.php'); ?>
    </div>
    <div class="row">
    </div>
    <?php endif;?>
	<!-- Actualizar estado de los pedidos -->
	<?php $lastRunLog = 'lastrun.log';
		if (file_exists($lastRunLog)) {
    		$lastRun = file_get_contents($lastRunLog);
    			if (time() - $lastRun >= 15100) {
         			//its been more than a day so run our external file
					$cron3 = include('_pedidos_actualizar_estado_wc.php');
					//update lastrun.log with current time
         			file_put_contents($lastRunLog, time());
    			}
		};
	?>	
    <!-- Automatizador de stock en website -->
	<?php $lastRunLog2 = 'lastrun2.log';
		if (file_exists($lastRunLog2)) {
    		$lastRun = file_get_contents($lastRunLog2);
    			if (time() - $lastRun >= 1800) {
         			//its been more than a day so run our external file
					$cron = require_once('stock_automatizar_web.php');
					//update lastrun.log with current time
         			file_put_contents($lastRunLog2, time());
    			}
		};
	?>
	<!--- Actualizar clientes y ventas ID -->
	<?php $lastRunLog3 = 'lastrun3.log';
		if (file_exists($lastRunLog3)) {
    		$lastRun = file_get_contents($lastRunLog3);
    			if (time() - $lastRun >= 43000) {
         			//its been more than a day so run our external file
					$cron = include('_clientes_automatizar.php');
					$cron2 = include('_ventas_actualizar_ids.php');
					//update lastrun.log with current time
         			file_put_contents($lastRunLog3, time());
    			}
		};
	?>	

    <?php //require_once('_emails/mail_promo_2016_12.php'); ?>
    <div class="col-md-6">
	<?php /// require_once('_emails/mail_control_estado.php'); ?>
    </div><!-- no pongo div primera col (p estado) -->
    <div class="col-md-6">
    <?php /// require_once('_emails/mail_control_estado_cali.php'); ?>
    </div><!-- end div segunda col (p estado cali) -->
	
	
	<?php if ($_SESSION['MM_UserGroup'] ==  'Vendedor'):?>
    <div class="row">
    <div class="col-md-12">
		<?php if (!(is_null($row_rsHoy['fin']))):?>
        <a class="text-center btn btn-success btn-xs pull-right" title="Iniciar día" href="horario_iniciar.php">¡Iniciar día!</a>
        <?php endif;?>
        <?php if (($row_rsHoy['ID']) && (is_null($row_rsHoy['fin']))):?>
        <a class="text-center btn btn-danger btn-xs pull-right" title="Iniciar día" href="horario_finalizar.php">¡Finalizar día!</a>
        <?php endif; ?>
        </div>
    </div>
    <?php endif; ?>
		
<?php // fin de Order item Id
		endif;?>
<?php 
// Si hay Order Item Id definido, oculto lo siguiente
if (isset($_GET['order_item_id'])):?>
		<div class="container" style="width:97%">
<?php // segundo fin de Order item Id
		endif;?>
		
    <div class="row text-center"><a href="#pri" class="btn btn-lg btn-primary">Ir a Primarios</a> <a href="#secu" class="btn btn-lg btn-info">Ir a Secundarios</a> <a href="#ps3" class="btn btn-lg btn-default" style="background-color:#000; color:#FFF;">Ir a PS3</a> <a href="#reset" class="btn btn-lg btn-warning">Ir a Resetear</a></div>
    
    <div class="row">
          <?php if($row_rsStockNuevo):?>
            <div class="page-header"><h3>Stock</h3> </div>
			  <?php do { ?>
              <div class="col-sm-1b col-xs-6" style="width: 12.5%">
              <div class="thumbnail" >
                 <div>
					<div style="position:relative; overflow:hidden; padding-bottom:100%;">
					 <span style="position:absolute; z-index:100; bottom: 0px; background-color: rgba(0, 0, 0, 0.8); color:#FFF; padding:3px; font-size:0.9em;"><?php echo str_replace('-', ' ', $row_rsStockNuevo['titulo']);?></span>
					 <a title="vender <?php echo $row_rsStockNuevo['titulo']; ?>" href="ventas_insertar.php?consola=<?php echo $row_rsStockNuevo['consola']; ?>&titulo=<?php echo $row_rsStockNuevo['titulo']; ?>&slot=No<?php echo $OII;?>">
						 <img src="/img/productos/<?php echo $row_rsStockNuevo['consola']."/".$row_rsStockNuevo['titulo'].".jpg"; ?>" alt="<?php echo $row_rsStockNuevo['consola']." - ".$row_rsStockNuevo['titulo'].".jpg"; ?>" class="img img-responsive full-width" style="border-radius:5px; position:absolute;">
					  </a>
					</div>
					<span class="badge badge-<?php if ($row_rsStockNuevo['Q_Stock'] > 4): echo 'success'; else: echo 'danger'; endif;?> pull-right" style="position: relative; top: 8px; left: -8px;">
						<?php if (($_SESSION['MM_UserGroup'] ==  'Adm') or ($_SESSION['MM_Username'] ==  'Francisco')):?>
							<?php echo $row_rsStockNuevo['Q_Stock']; ?>
						<?php else:?>
								<?php if ($row_rsStockNuevo['Q_Stock'] > 10): echo '+10'; else: echo $row_rsStockNuevo['Q_Stock'];?>
								<?php endif;?>
						<?php endif;?>
						</span>
				</div>
                 </a>
                <div class="caption text-center">
                <small style="color:#CFCFCF;"><i class="fa fa-gamepad fa-fw" aria-hidden="true"></i> <?php echo $row_rsStockNuevo['ID_stk']; ?></small>
					<br /><?php if ($_SESSION['MM_UserGroup'] ==  'Adm'):?> <span class="badge badge-default">$<?php echo $row_rsStockNuevo['costo']; ?></span><?php endif;?>
                </div>
			</div>
            </div>
			<?php }while($row_rsStockNuevo = mysql_fetch_assoc($rsStockNuevo)); ?>
          <?php endif; ?>
          </div>
          <div class="row">
          <?php if($row_rsPrimario):?>
			<div class="page-header"><h3 id="pri">PS4 Primario</h3></div>
			  <?php do { ?>
              <div class="col-xs-6 col-sm-1b" style="width: 12.5%;">
              <div class="thumbnail">
              	<div>
					<div style="position:relative; overflow:hidden; padding-bottom:100%;">
					 <span style="position:absolute; z-index:100; bottom: 0px; background-color: rgba(0, 0, 0, 0.8); color:#FFF; padding:3px; font-size:0.9em;"><?php echo str_replace('-', ' ', $row_rsPrimario['titulo']);?></span>
               		  <a title="vender <?php echo $row_rsPrimario['titulo']; ?>" href="ventas_insertar.php?consola=<?php echo $row_rsPrimario['consola']; ?>&titulo=<?php echo $row_rsPrimario['titulo']; ?>&slot=Primario<?php echo $OII;?>">
							<img src="/img/productos/<?php echo $row_rsPrimario['consola']."/".$row_rsPrimario['titulo'].".jpg"; ?>" alt="<?php echo $row_rsPrimario['consola']." - ".$row_rsPrimario['titulo'].".jpg"; ?>" class="img img-responsive full-width" style="border-radius:5px; position:absolute;">
						</a>
                 		<?php if ($_SESSION['MM_UserGroup'] ==  'Adm'):?>
                 		<span class="badge badge-danger pull-right" style="position: relative; top: 8px; left: -8px;"><?php echo $row_rsPrimario['Q_Stock']; ?></span><?php endif;?>
					</div>
                </div>
                 </a>
                <div class="caption text-center">
                 <a href="cuentas_detalles.php?id=<?php echo $row_rsPrimario['stk_ctas_id']; ?>" title="Ir a Cuenta" role="button" class="btn btn-xs"><i class="fa fa-link fa-fw" aria-hidden="true"></i> <?php echo $row_rsPrimario['stk_ctas_id']; ?></a> <small style="color:#CFCFCF;"><i class="fa fa-gamepad fa-fw" aria-hidden="true"></i> <?php echo $row_rsPrimario['ID_stk']; ?></small>
					<br /><?php if ($_SESSION['MM_UserGroup'] ==  'Adm'):?> <span class="badge badge-default">$<?php echo $row_rsPrimario['costo']; ?></span><?php endif;?>
                </div>
              </div>
            </div>
                <?php }while($row_rsPrimario = mysql_fetch_assoc($rsPrimario)); ?>
          <?php endif; ?>
          </div>
          
		<div class="row">
          <?php if($row_rsSecundario):?>
			<div class="page-header"><h3 id="secu">PS4 Secundario</h3></div>
			  <?php do { ?>
              <div class="col-xs-6 col-sm-1b" style="width: 12.5%; background-color: #CCC;">
              <div class="thumbnail">
             	<div>
					<div style="position:relative; overflow:hidden; padding-bottom:100%;">
					 <span style="position:absolute; z-index:100; bottom: 0px; background-color: rgba(0, 0, 0, 0.8); color:#FFF; padding:3px; font-size:0.9em;"><?php echo str_replace('-', ' ', $row_rsSecundario['titulo']);?></span>
                 		 <a title="vender <?php echo $row_rsSecundario['titulo']; ?>" href="ventas_insertar.php?consola=<?php echo $row_rsSecundario['consola']; ?>&titulo=<?php echo $row_rsSecundario['titulo']; ?>&slot=Secundario<?php echo $OII;?>">
							 <img src="/img/productos/<?php echo $row_rsSecundario['consola']."/".$row_rsSecundario['titulo'].".jpg"; ?>" alt="<?php echo $row_rsSecundario['consola']." - ".$row_rsSecundario['titulo'].".jpg"; ?>" class="img img-responsive full-width" style="border-radius:5px; position:absolute;">
						</a>
                 <?php if ($_SESSION['MM_UserGroup'] ==  'Adm'):?>  
                 	<span class="badge badge-danger pull-right" style="position: relative; top: 8px; left: -8px;"><?php echo $row_rsSecundario['Q_Stock']; ?></span><?php endif;?>
                 	</div>
				  </div>
                 </a>
                <div class="caption text-center">
                 <a href="cuentas_detalles.php?id=<?php echo $row_rsSecundario['stk_ctas_id']; ?>" title="Ir a Cuenta" role="button" class="btn btn-xs"><i class="fa fa-link fa-fw" aria-hidden="true"></i> <?php echo $row_rsSecundario['stk_ctas_id']; ?></a> <small style="color:#CFCFCF;"><i class="fa fa-gamepad fa-fw" aria-hidden="true"></i> <?php echo $row_rsSecundario['ID_stk']; ?></small>
					<br /><?php if ($_SESSION['MM_UserGroup'] ==  'Adm'):?> <span class="badge badge-default">$<?php echo $row_rsSecundario['costo']; ?></span><?php endif;?>
                </div>
              </div>
            </div>
            <?php }while($row_rsSecundario = mysql_fetch_assoc($rsSecundario)); ?>
          <?php endif; ?>
		</div>	
          <div class="row">
          <?php if($row_rsPS3):?>
			<div class="page-header"><h3 id="ps3">PS3</h3></div>
            <?php do { ?>
            <div class="col-xs-6 col-sm-1b" style="width: 12.5%">
              <div class="thumbnail">
              	<div>
					<div style="position:relative; overflow:hidden; padding-bottom:100%;">
					 <span style="position:absolute; z-index:100; bottom: 0px; background-color: rgba(0, 0, 0, 0.8); color:#FFF; padding:3px; font-size:0.9em;"><?php echo str_replace('-', ' ', $row_rsPS3['titulo']);?></span>
                 		<a title="vender <?php echo $row_rsPS3['titulo']; ?>" href="ventas_insertar.php?consola=<?php echo $row_rsPS3['consola']; ?>&titulo=<?php echo $row_rsPS3['titulo']; ?>&slot=Primario<?php echo $OII;?>">
							<img src="/img/productos/<?php echo $row_rsPS3['consola']."/".$row_rsPS3['titulo'].".jpg"; ?>" alt="<?php echo $row_rsPS3['consola']." - ".$row_rsPS3['titulo'].".jpg"; ?>" class="img img-responsive full-width" style="border-radius:5px; position:absolute;">
						</a>
                 		<?php if ($_SESSION['MM_UserGroup'] ==  'Adm'):?>
                 	<span class="badge badge-danger pull-right" style="position: relative; top: 8px; left: -8px;"><?php echo $row_rsPS3['Q_Stock']; ?></span><?php endif; ?>
                	 </div>
				  </div>
                 </a>
                <div class="caption text-center">
                 <a href="cuentas_detalles.php?id=<?php echo $row_rsPS3['stk_ctas_id']; ?>" title="Ir a Cuenta" role="button" class="btn btn-xs"><i class="fa fa-link fa-fw" aria-hidden="true"></i> <?php echo $row_rsPS3['stk_ctas_id']; ?></a> <small style="color:#CFCFCF;"><i class="fa fa-gamepad fa-fw" aria-hidden="true"></i> <?php echo $row_rsPS3['ID_stk']; ?></small>
					<br /><?php if ($_SESSION['MM_UserGroup'] ==  'Adm'):?> <span class="badge badge-default">$<?php echo $row_rsPS3['costo']; ?></span><?php endif;?>
                </div>
              </div>
            </div>
			<?php }while($row_rsPS3 = mysql_fetch_assoc($rsPS3)); ?>
          <?php endif; ?>
          </div>
          <div class="row">
          <?php if($row_rsPS3Resetear):?>
			<div class="page-header"><h3 id="reset">PS3 <span class="label label-warning">resetear</span></h3></div>
            <?php do { ?>
            <div class="col-xs-6 col-sm-1b" style="width: 12.5%">
              <div class="thumbnail">
                 <div style="position:relative; overflow:hidden; padding-bottom:100%;">
                 	<span style="position:absolute; z-index:100; bottom: 0px; BACKGROUND-COLOR: rgba(0, 0, 0, 0.8); font-size: 0.8em; opacity:0.8; color:#FFF; padding:5px;">(<?php if($row_rsPS3Resetear['days_from_vta'] > 6) {echo '+ 7';} else{echo $row_rsPS3Resetear['days_from_vta'];}?> días) <?php echo str_replace('-', ' ', $row_rsPS3Resetear['titulo']);?></span>

                 	<img src="/img/productos/<?php echo $row_rsPS3Resetear['consola']."/".$row_rsPS3Resetear['titulo'].".jpg"; ?>" alt="<?php echo $row_rsPS3Resetear['consola']." - ".$row_rsPS3Resetear['titulo'].".jpg"; ?>" class="img img-responsive full-width" style="position:absolute;" />
                 </div>
                <div class="caption text-center">
                 <a href="cuentas_detalles.php?id=<?php echo $row_rsPS3Resetear['stk_ctas_id']; ?>" title="Ir a Cuenta" role="button" class="btn btn-xs btn-<?php if($row_rsPS3Resetear['days_from_vta'] > 6){echo 'success';} else {echo 'normal';}?>"><i class="fa fa-link fa-fw" aria-hidden="true"></i> <?php echo $row_rsPS3Resetear['stk_ctas_id']; ?></a> <small style="color:#CFCFCF"><i class="fa fa-gamepad fa-fw" aria-hidden="true"></i> <?php echo $row_rsPS3Resetear['ID_stk']; ?></small>
					
                </div>
              </div>
            </div>
			<?php }while($row_rsPS3Resetear = mysql_fetch_assoc($rsPS3Resetear)); ?>
          <?php endif; ?>
          </div>
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
	</script> <!-- extras de script y demás yerbas -->
</body>
</html>
<?php
mysql_free_result($rsUsuarios);

mysql_free_result($rsStockNuevo);

mysql_free_result($rsPrimario);

mysql_free_result($rsSecundario);

mysql_free_result($rsPS3);

mysql_free_result($rsPS3Resetear);

mysql_free_result($rsHoy);
?>
