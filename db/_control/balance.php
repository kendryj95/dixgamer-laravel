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

mysql_select_db($database_Conexion, $Conexion);
$query_rsVentas = "SELECT SUM(precio) AS Ingresos, SUM(comision) AS Comisiones, COUNT(*) AS Cantidad FROM ventas_cobro WHERE precio > '0'";
$rsVentas = mysql_query($query_rsVentas, $Conexion) or die(mysql_error());
$row_rsVentas = mysql_fetch_assoc($rsVentas);
$totalRows_rsVentas = mysql_num_rows($rsVentas);

mysql_select_db($database_Conexion, $Conexion);
$query_rsClientes = "SELECT COUNT(*) AS Total FROM clientes";
$rsClientes = mysql_query($query_rsClientes, $Conexion) or die(mysql_error());
$row_rsClientes = mysql_fetch_assoc($rsClientes);
$totalRows_rsClientes = mysql_num_rows($rsClientes);

mysql_select_db($database_Conexion, $Conexion);
$query_rsCuentas = "SELECT COUNT(*) AS Total FROM cuentas";
$rsCuentas = mysql_query($query_rsCuentas, $Conexion) or die(mysql_error());
$row_rsCuentas = mysql_fetch_assoc($rsCuentas);
$totalRows_rsCuentas = mysql_num_rows($rsCuentas);

mysql_select_db($database_Conexion, $Conexion);
$query_rsStock = "SELECT COUNT(*) AS TotalC, SUM(costo) AS TotalP FROM stock WHERE costo > '0'";
$rsStock = mysql_query($query_rsStock, $Conexion) or die(mysql_error());
$row_rsStock = mysql_fetch_assoc($rsStock);
$totalRows_rsStock = mysql_num_rows($rsStock);

mysql_select_db($database_Conexion, $Conexion);
$query_rsStockXconsola = "SELECT consola, COUNT(*) AS Cantidad FROM stock WHERE costo > '0' GROUP BY consola";
$rsStockXconsola = mysql_query($query_rsStockXconsola, $Conexion) or die(mysql_error());
$row_rsStockXconsola = mysql_fetch_assoc($rsStockXconsola);
$totalRows_rsStockXconsola = mysql_num_rows($rsStockXconsola);

mysql_select_db($database_Conexion, $Conexion);
$query_rsGastos = "SELECT COUNT(*) AS TotalC, SUM(importe) AS gastos FROM gastos WHERE importe > '0'";
$rsGastos = mysql_query($query_rsGastos, $Conexion) or die(mysql_error());
$row_rsGastos = mysql_fetch_assoc($rsGastos);
$totalRows_rsGastos = mysql_num_rows($rsGastos);

mysql_select_db($database_Conexion, $Conexion);
$query_rsVentasXconsola = "SELECT consola, COUNT(*) AS Cantidad FROM ventas
LEFT JOIN
stock
ON ventas.stock_id = stock.ID
WHERE costo > '0'
GROUP BY consola";
$rsVentasXconsola = mysql_query($query_rsVentasXconsola, $Conexion) or die(mysql_error());
$row_rsVentasXconsola = mysql_fetch_assoc($rsVentasXconsola);
$totalRows_rsVentasXconsola = mysql_num_rows($rsVentasXconsola);

mysql_select_db($database_Conexion, $Conexion);
$query_rsCostoConsumido = "SELECT COUNT(*) as Q_Vta, SUM(cost) as Costo FROM (SELECT ventas.ID AS ID_vta, stock.ID AS ID_stk, titulo, consola, slot, precio, comision, costo, CASE WHEN consola = 'ps3' THEN (costo * 0.25) WHEN (consola = 'ps4' or consola = 'ps') and slot = 'Primario' THEN (costo * 0.6) WHEN (consola = 'ps4' or consola = 'ps') and slot = 'Secundario' THEN (costo * 0.4) ELSE costo END as cost, medio_venta FROM `ventas` left join (select ventas_id, sum(precio) as precio, sum(comision) as comision FROM ventas_cobro GROUP BY ventas_id) as ventas_cobro ON ventas.ID = ventas_cobro.ventas_id left join stock on ventas.stock_id = stock.ID) as listado";
$rsCostoConsumido = mysql_query($query_rsCostoConsumido, $Conexion) or die(mysql_error());
$row_rsCostoConsumido = mysql_fetch_assoc($rsCostoConsumido);
$totalRows_rsCostoConsumido = mysql_num_rows($rsCostoConsumido);

mysql_select_db($database_Conexion, $Conexion);
$query_rsMesFcro = "SELECT M_S, qty, precio, comision, costo, IFNULL(gasto,0) as gasto, (precio - comision - costo - IFNULL(gasto,0)) as ganancia FROM
(SELECT DATE_FORMAT(Day,'%Y-%m') AS M_S, round(SUM(costo)) AS costo FROM stock GROUP BY M_S) as costos
LEFT JOIN
(SELECT DATE_FORMAT(Day,'%Y-%m') AS M_V, COUNT(ID) AS qty FROM ventas GROUP BY M_V) as ventas
ON M_S = M_V
LEFT JOIN
(SELECT DATE_FORMAT(Day,'%Y-%m') AS M_C, round(SUM(precio)) AS precio, round(SUM(comision)) AS comision FROM ventas_cobro GROUP BY M_C) as cobros
ON M_S = M_C
LEFT JOIN
(SELECT DATE_FORMAT(Day,'%Y-%m') AS M_G, round(SUM(importe)) AS gasto FROM gastos GROUP BY M_G) as gastos
ON M_S = M_G
ORDER BY M_S ASC";
$rsMesFcro = mysql_query($query_rsMesFcro, $Conexion) or die(mysql_error());
$row_rsMesFcro = mysql_fetch_assoc($rsMesFcro);
$totalRows_rsMesFcro = mysql_num_rows($rsMesFcro);

mysql_select_db($database_Conexion, $Conexion);
$query_rsCicloVtaGRAL = "SELECT AVG(diasfromcompra) as diasfromcompra FROM
(SELECT t1.*, stock.Day, titulo, consola, cuentas_id, TIMESTAMPDIFF(DAY, stock.Day, prom_dia_venta) as diasfromcompra FROM
(Select ID, from_unixtime(AVG(unix_timestamp(Day))) AS prom_dia_venta, stock_id
FROM ventas 
GROUP BY stock_id) as t1
LEFT JOIN
stock
ON t1.stock_id=stock.ID  
ORDER BY `diasfromcompra` DESC) as t2
";
$rsCicloVtaGRAL = mysql_query($query_rsCicloVtaGRAL, $Conexion) or die(mysql_error());
$row_rsCicloVtaGRAL = mysql_fetch_assoc($rsCicloVtaGRAL);
$totalRows_rsCicloVtaGRAL = mysql_num_rows($rsCicloVtaGRAL);

mysql_select_db($database_Conexion, $Conexion);
$query_rsCicloVta = $query_rsCicloVtaGRAL;
$query_rsCicloVta .= "WHERE diasfromcompra > 0";
$rsCicloVta = mysql_query($query_rsCicloVta, $Conexion) or die(mysql_error());
$row_rsCicloVta = mysql_fetch_assoc($rsCicloVta);
$totalRows_rsCicloVta = mysql_num_rows($rsCicloVta);

mysql_select_db($database_Conexion, $Conexion);
$query_rsCicloVtaPS4 = $query_rsCicloVtaGRAL;
$query_rsCicloVtaPS4 .= "WHERE diasfromcompra >= 0 AND consola ='ps4'";
$rsCicloVtaPS4 = mysql_query($query_rsCicloVtaPS4, $Conexion) or die(mysql_error());
$row_rsCicloVtaPS4 = mysql_fetch_assoc($rsCicloVtaPS4);
$totalRows_rsCicloVtaPS4 = mysql_num_rows($rsCicloVtaPS4);

mysql_select_db($database_Conexion, $Conexion);
$query_rsCicloVtaPS3 = $query_rsCicloVtaGRAL;
$query_rsCicloVtaPS3 .= "WHERE diasfromcompra >= 0 AND consola ='ps3'";
$rsCicloVtaPS3 = mysql_query($query_rsCicloVtaPS3, $Conexion) or die(mysql_error());
$row_rsCicloVtaPS3 = mysql_fetch_assoc($rsCicloVtaPS3);
$totalRows_rsCicloVtaPS3 = mysql_num_rows($rsCicloVtaPS3);

mysql_select_db($database_Conexion, $Conexion);
$query_rsCicloVtaPS = $query_rsCicloVtaGRAL;
$query_rsCicloVtaPS .= "WHERE diasfromcompra >= 0 AND consola ='ps'";
$rsCicloVtaPS = mysql_query($query_rsCicloVtaPS, $Conexion) or die(mysql_error());
$row_rsCicloVtaPS = mysql_fetch_assoc($rsCicloVtaPS);
$totalRows_rsCicloVtaPS = mysql_num_rows($rsCicloVtaPS);
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
    <title><?php $titulo = 'Balance'; echo $titulo; ?></title>
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
    
    <div class="pricing">
        <ul>
            <li class="unit price-success" style="min-width:200px;">
                <div class="price-title">
                    <h3>$<?php echo round($row_rsVentas['Ingresos']); ?></h3>
                    <p>ingresos</p>
                </div>
            </li>
            <li class="unit price-primary" style="min-width:200px;">
                <div class="price-title">
                    <h3>$<?php echo round($row_rsStock['TotalP']); ?></h3>
                    <p>costos</p>
                </div>
            </li>
            <li class="unit price-warning" style="min-width:200px;">
                <div class="price-title">
                    <h3>$<?php echo round($row_rsVentas['Comisiones']); ?></h3>
                    <p>comisiones</p>
                </div>
            </li>
            <li class="unit price-warning" style="min-width:200px;">
                <div class="price-title">
                    <h3>$<?php echo round($row_rsGastos['gastos']); ?></h3>
                    <p>gastos</p>
                </div>
            </li>
            <li class="unit" style="background-color:#efefef;min-width:200px;" >
                <div class="price-title" style="color:#000;">
                    <h3>$<?php echo round(($row_rsVentas['Ingresos'] - $row_rsStock['TotalP'] - $row_rsVentas['Comisiones'] - $row_rsGastos['gastos'])); ?></h3>
                    <p>ganancia</p>
                </div>
            </li>
        </ul>
    </div>
    <div class="pricing">
        <ul>
            <li class="unit" style="min-width:200px; max-height:1px;">
                <div class="price-title">
                </div>
            </li>
            <li class="unit price-primary" style="min-width:200px;">
                <div class="price-title">
                    <h3>$<?php echo round(($row_rsStock['TotalP'] - $row_rsCostoConsumido['Costo'])); ?></h3>
                    <p>costo no consumido</p>
                </div>
            </li>
           
        </ul>
    </div>
    <h3>Balance Mensual Fcro</h3>
    <table class="table table-striped" border="0" cellpadding="0" cellspacing="5">
      <tr>
        <th width="100">Mes</th>
        <th title="Cantidad">Qty</th>
        <th title="Precio">Ingreso</th>
        <th title="Costo">Costo</th>
        <th title="Comision">Comision</th>
        
        <th title="Gasto">Gasto</th>
        <th title="Ganancia">Ganancia</th>
      </tr>
      <?php $Qty = 0; $Ingreso = 0; $Comision = 0; $Costo = 0; $Gasto = 0; $Ganancia = 0;  do { ?>
      <tr>
        
        <td><?php echo $row_rsMesFcro['M_S']; ?></td>
        <td><?php echo $row_rsMesFcro['qty']; ?></td>
        <td><?php echo $row_rsMesFcro['precio']; ?></td>
        
        <td><?php echo $row_rsMesFcro['costo']; ?></td>
        <td><?php echo $row_rsMesFcro['comision']; ?></td>
        <td><?php echo $row_rsMesFcro['gasto']; ?></td>
        <td><?php echo $row_rsMesFcro['ganancia']; ?></td>
        <td>
        
        </td>
      </tr>   
      <?php 
      $Qty = $Qty + $row_rsMesFcro['qty'];
      $Ingreso = $Ingreso + $row_rsMesFcro['precio'];
      $Comision = $Comision + $row_rsMesFcro['comision'];
      $Costo = $Costo + $row_rsMesFcro['costo'];
      $Gasto = $Gasto + $row_rsMesFcro['gasto'];
      $Ganancia = $Ganancia + $row_rsMesFcro['ganancia'];
      } while ($row_rsMesFcro = mysql_fetch_assoc($rsMesFcro)); ?>     
      <tr>
        <th></th>
        <th><?=$Qty?></th>
        <th><?=$Ingreso?></th>
        <th><?=$Comision?></th>
        <th><?=$Costo?></th>
        <th><?=$Gasto?></th>
        <th><?=$Ganancia?></th>
      </tr> 
    </table>
    <h3>Ciclo de Ventas</h3>
    <div class="pricing">
        <ul>
            <li class="unit price-success" style="min-width:200px;">
                <div class="price-title">
                    <h3><?php echo round($row_rsCicloVtaGRAL['diasfromcompra']); ?> días</h3>
                    <p>Ciclo de Ventta</p>
                </div>
            </li>
            <li class="unit price-primary" style="min-width:200px;">
                <div class="price-title">
                    <h3><?php echo round($row_rsCicloVta['diasfromcompra']); ?> días</h3>
                    <p>Solo ciclos >= 1 día</p>
                </div>
            </li>
            <li class="unit price-primary" style="min-width:200px;">
                <div class="price-title">
                    <h3><?php echo round($row_rsCicloVtaPS4['diasfromcompra']); ?> días</h3>
                    <p>Solo PS4</p>
                </div>
            </li>
            <li class="unit price-primary" style="min-width:200px;">
                <div class="price-title">
                    <h3><?php echo round($row_rsCicloVtaPS3['diasfromcompra']); ?> días</h3>
                    <p>Solo PS3</p>
                </div>
            </li>
            <li class="unit price-primary" style="min-width:200px;">
                <div class="price-title">
                    <h3><?php echo round($row_rsCicloVtaPS['diasfromcompra']); ?> días</h3>
                    <p>Solo PSN</p>
                </div>
            </li>
        </ul>
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
    <script type="text/javascript" src="http://static.fusioncharts.com/code/latest/fusioncharts.js"></script>
<!-- InstanceEndEditable -->
  </body>
  
<!-- InstanceEnd --></html>
<?php
mysql_free_result($rsUsuarios);

mysql_free_result($rsClientes);

mysql_free_result($rsMesFcro);

?>
