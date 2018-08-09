<?php require_once('../../Connections/Conexion.php'); ?>
<?php
$MM_authorizedUsers = "Adm,Vendedor";
$MM_donotCheckaccess = "false";
require_once('../_autentificacion.php');
?>
<?php /*** YA NO OCUPO ASÍ, AHORA AGRUPO LOS MOV Y SUS ANULACIONES MANUALES DE MERCADOPAGO_BAJA */
/*** QUERY PARA BORRAR TODOS LOS MOVIMIENTOS DE TABLA MERCADOPAGO QUE YA FUERON REVISADOS Y DADOS DE BAJA A TABLA MERCADOPAGO_BAJA 
$borrarMovControlados = "DELETE FROM mercadopago WHERE nro_mov IN (SELECT nro_mov FROM mercadopago_baja)";
mysql_select_db($database_Conexion, $Conexion);
$Rs_borrarMovControlados = mysql_query($borrarMovControlados, $Conexion) or die(mysql_error());
*/

/*** QUERY PARA CONTROLAR LOS CONCEPTOS DE MERCADOPAGO.... SI HAY UNO NUEVO DEBO AGREGARLO A ALGUN QUERY DE CONTROL EN .. WHERE = "concepto" ***/
// con tabulación los nuevos conceptos incorporados desde diciembre 2017
$words = array( "Anulación de comisión por venta de MercadoLibre",
"Anulación de cargo MercadoPago",
			   "Anulación de cargo Mercado Pago",
"Anulación de costo de envío por MercadoEnvíos",
			   "Anulación de cargo por envío",
"Anulación de costo de MercadoPago",
			   "Anulación de costo de Mercado Pago",
			   "Anulación de comisión por venta de Mercado Libre",
"Anulación de dinero retenido por contracargo",
"Anulación de retiro de dinero a cuenta bancaria",
"Anulación de retención de ingresos brutos de Córdoba",
			   "Anulación de retención de ingresos brutos de Catamarca",
"Anulación parcial de costo de MercadoPago",
			   "Anulación parcial de costo de Mercado Pago",
"Cargo MercadoPago",
			   "Cargo Mercado Pago",
			   "Cargo por envío",
"Cobro",
"Cobro Adicional",
"Cobro por descuento a tu contraparte",
"Comisión por venta de MercadoLibre",
			   "Comisión por venta de Mercado Libre",
"Costo de envío por MercadoEnvíos",
"Costo de MercadoPago",
			   "Costo de Mercado Pago",
"Devolución de cobro",
"Devolución de cobro Adicional",
"Devolución de cobro por descuento a tu contraparte",
"Devolución de dinero recibido",
"Devolución parcial de cobro",
			   "Devolución parcial de ingreso de dinero",
			   "Devolución parcial de pago",
"Devolución por Compra Protegida",
"Dinero recibido",
"Dinero retenido por contracargo",
			   "Ingreso de dinero",
"Pago",
"Pago adicional",
"Percepción Ing. Brutos CAP. FED.",
"Percepción Ing. Brutos Pcia. Bs. As.",
"Recarga de celular",
"Retención de ingresos brutos de Catamarca",
"Retención de ingresos brutos de Entre Ríos",
"Retención de ingresos brutos de La Pampa",
"Retención de ingresos brutos de Santa Fe",
"Retención de ingresos brutos de Córdoba",
"Retiro de dinero a cuenta bancaria" );
/*** SI AGREGO CONCEPTO QUE NO ES VENTA O COBRO (serían percepciones, retiros, pagos de mis compras, etc) AGREGARLO TMB EN "WHERE NOT LIKE" query_rsGRAL y query_rsGRAL2   */ 
$whereClause = "";
foreach( $words as $word) {
   $whereClause .= " concepto != '" . $word . "' AND";
}

// Remove last 'and'
$whereClause = substr($whereClause, 0, -3);
/**** query para descubrir si hay nuevo concepto de operación en mercadopago cargado a la base de datos y por ende no tenido en cuenta en los querys al momento de incorporarlo */
mysql_select_db($database_Conexion, $Conexion);
$query_rsNewConcept = "SELECT concepto FROM mercadopago WHERE" . $whereClause;
$query_rsNewConcept .= "GROUP BY concepto";
$rsNewConcept = mysql_query($query_rsNewConcept, $Conexion) or die(mysql_error());
$row_rsNewConcept = mysql_fetch_assoc($rsNewConcept);
$totalRows_rsNewConcept = mysql_num_rows($rsNewConcept);


mysql_select_db($database_Conexion, $Conexion);
$query_rsGRAL = "SELECT mp.*, cobro.*, (imp_mp - imp_db) as dif # SACO LA DIFERENCIA ENTRE MP Y LA DB
FROM 
	(SELECT ref_op, GROUP_CONCAT(nro_mov SEPARATOR ', ') as nro_mov, # agrupo los mov de una misma operacion
		 GROUP_CONCAT(concepto SEPARATOR ', ') AS concepto, # y agrupo los conceptos de esos movimientos
		 SUM(importe) AS imp_mp # sumo el total final (saldo) de esa operacion
		 FROM (SELECT * FROM mercadopago UNION ALL SELECT ID,nro_mov,concepto,ref_op,importe,saldo FROM mercadopago_baja) as mercadopago
		 	 
		 WHERE concepto NOT LIKE '%Percepción Ing. Brutos%' AND concepto NOT LIKE '%Retención de ingresos brutos de%' AND concepto != 'Recarga de celular' AND concepto != 'Pago' AND concepto != 'Pago adicional' AND concepto != 'Anulación de retención de ingresos brutos de Córdoba' AND concepto != 'Retiro de dinero a cuenta bancaria' AND concepto != 'Anulación de retiro de dinero a cuenta bancaria' AND concepto != 'Anulación de retención de ingresos brutos de Catamarca' # quito los movimientos que no tienen que ver con ventas y cobros (serian pagos, retiros y reten o percep), 
		 GROUP BY ref_op # los agrupo por operacion
	  ) as mp
LEFT JOIN
		(SELECT ref_cobro, 
		IFNULL(SUM(ventas_cobro.precio - ventas_cobro.comision), 0) as imp_db, # si no hay cobro con esa referencia le coloco valor 0
		GROUP_CONCAT(ventas_id SEPARATOR ',') AS ventas_id, # agrupo todos las ventas que tienen esa ref de cobro
		clientes_id
		FROM ventas_cobro
			LEFT JOIN (SELECT ventas.ID as ID, clientes_id FROM ventas UNION ALL SELECT ventas_baja.ventas_id as ID, clientes_id FROM ventas_baja ) as vtas
			ON ventas_cobro.ventas_id = vtas.ID
			GROUP BY ref_cobro
		) as cobro
ON mp.ref_op = cobro.ref_cobro # No necesito mas esto -> COLLATE utf8_spanish_ci uno la tabla de mercadopago a la table de cobros";
$query_rsCXP = $query_rsGRAL;
$query_rsCXP .= "
WHERE ((imp_mp >= (imp_db + 0.50)) OR (imp_mp<= (imp_db - 0.50))) # filtro las que tengan diferencia entre importes > a 50 centavos
ORDER BY `dif` ASC";
$rsCXP = mysql_query($query_rsCXP, $Conexion) or die(mysql_error());
$row_rsCXP= mysql_fetch_assoc($rsCXP);
$totalRows_rsCXP = mysql_num_rows($rsCXP);


mysql_select_db($database_Conexion, $Conexion);
$query_rsCXP2 = $query_rsGRAL; /*** es la misma tabla QUE ARRIBA pero le hago distinto filtro */
$query_rsCXP2 .= "
WHERE ref_cobro IS NULL and imp_mp != '0.00' # filtro las op. de mp que no tienen pareja en bd y que tienen importe distinto a 0
ORDER BY imp_mp ASC";
$rsCXP2 = mysql_query($query_rsCXP2, $Conexion) or die(mysql_error());
$row_rsCXP2 = mysql_fetch_assoc($rsCXP2);
$totalRows_rsCXP2 = mysql_num_rows($rsCXP2);

mysql_select_db($database_Conexion, $Conexion);
$query_rsGRAL2 = "SELECT db.*, mp.*, (imp_mp - imp_db) as dif
FROM 
(SELECT ventas_cobro.Day,
		ref_cobro, 
		IFNULL(SUM(ventas_cobro.precio - ventas_cobro.comision), 0) as imp_db, # si no hay cobro con esa referencia le coloco valor 0
		GROUP_CONCAT(ventas_id SEPARATOR ',') AS ventas_id, # agrupo todos los ID de ventas que tienen esa ref de cobro
		ventas_cobro.usuario,
		clientes_id
	FROM ventas_cobro
		LEFT JOIN 
		(SELECT ventas.ID as ID, clientes_id FROM ventas UNION ALL SELECT ventas_baja.ventas_id as ID, clientes_id FROM ventas_baja ) as vtas
	ON ventas_cobro.ventas_id = vtas.ID 
	WHERE ventas_cobro.Day > '2017-04-01' AND medio_cobro LIKE '%mercado%'
	GROUP BY ref_cobro) as db
LEFT JOIN
	(SELECT ref_op, GROUP_CONCAT(nro_mov SEPARATOR ', ') as nro_mov,
	GROUP_CONCAT(concepto SEPARATOR ', ') AS concepto,
	SUM(importe) AS imp_mp
	FROM (SELECT * FROM mercadopago UNION ALL SELECT ID,nro_mov,concepto,ref_op,importe,saldo FROM mercadopago_baja) as mercadopago
	WHERE concepto NOT LIKE '%Percepción Ing. Brutos%' AND concepto NOT LIKE '%Retención de ingresos brutos de%' AND concepto != 'Recarga de celular' AND concepto != 'Pago' AND concepto != 'Pago adicional' AND concepto != 'Anulación de retención de ingresos brutos de Córdoba' AND concepto != 'Retiro de dinero a cuenta bancaria' AND concepto != 'Anulación de retiro de dinero a cuenta bancaria' AND concepto != 'Anulación de retención de ingresos brutos de Catamarca' GROUP BY ref_op) as mp
ON db.ref_cobro = mp.ref_op # No necesito mas esto -> COLLATE utf8_spanish_ci";
$query_rsCobrosDB = $query_rsGRAL2;
$query_rsCobrosDB .= "
WHERE ((imp_mp >= (imp_db + 0.50)) OR (imp_mp<= (imp_db - 0.50))) # filtro las que tengan diferencia entre importes > a 50 centavos
ORDER BY `dif` ASC";
$rsCobrosDB = mysql_query($query_rsCobrosDB, $Conexion) or die(mysql_error());
$row_rsCobrosDB = mysql_fetch_assoc($rsCobrosDB);
$totalRows_rsCobrosDB = mysql_num_rows($rsCobrosDB);

mysql_select_db($database_Conexion, $Conexion);
$query_rsCobrosDB2 = $query_rsGRAL2; /*** es la misma tabla QUE ARRIBA pero le hago distinto filtro */
$query_rsCobrosDB2 .= "
WHERE ref_op IS NULL and imp_db != '0.00' # filtro las op. de DB que no tienen pareja en MP y que tienen importe distinto a 0
ORDER BY imp_db DESC";
$rsCobrosDB2 = mysql_query($query_rsCobrosDB2, $Conexion) or die(mysql_error());
$row_rsCobrosDB2 = mysql_fetch_assoc($rsCobrosDB2);
$totalRows_rsCobrosDB2 = mysql_num_rows($rsCobrosDB2);
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
    <title><?php $titulo = 'Control Mercado Pago'; echo $titulo; ?></title>
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
    <?php if ($row_rsNewConcept['concepto']): do { echo '<span class="label label-danger">el concepto <strong>"' . $row_rsNewConcept['concepto'] . '"</strong> no está filtrado para control</span><br />';} while ($row_rsNewConcept = mysql_fetch_assoc($rsNewConcept)); endif;?>
	
    <h4>Cobros MP con pareja en BD</h4> 
    <table class="table table-striped" border="0" cellpadding="0" cellspacing="5">
     
      <thead>
      <tr>
		  <th>#</th>
        <th title="número de movimiento">N° de Mov</th>
        <th title="concepto">Concepto</th>
        <th title="Importe MP">MP</th>
        <th title="Importe DB">DB</th>
        <th title="diferencia">Diferencia</th>
        <th title="referencia">Ref</th>
        <th><i class="fa fa-shopping-bag fa-fw" aria-hidden="true"></i></th>
        <th><i class="fa fa-user fa-fw" aria-hidden="true"></i></th>
        </tr>
		</thead>
      
      <?php $i = 1; do { ?>
      <tr>
		  <td><?php echo $i;?></td>
        <td><small><?php $array = (explode(',', $row_rsCXP['nro_mov'], 10)); foreach ($array as $valor) { echo "$valor<a class='btn-xs' type='button' href='control_mp_baja.php?nro_mov=$valor'><i aria-hidden='true' class='fa fa-trash-o'></i></a><br />"; }?> </small></td>
        <td><small><?php $array2 = (explode(',', $row_rsCXP['concepto'], 10)); foreach ($array2 as $valor2) { echo "$valor2<br />"; }?></small></td>
        <td><small><?php echo round($row_rsCXP['imp_mp']); ?></small></td>
        <td><small><?php echo round($row_rsCXP['imp_db']); ?></small></td>
        <td><span style="font-size: 0.9em;" class="badge badge-<?php if(round($row_rsCXP['dif']) > 0.01): echo 'success'; else: echo 'danger';endif;?>"><?php echo round($row_rsCXP['dif']); ?></span><br> <a class="btn-xs" title="Anular ingreso por envío" type="button" href="control_mp_baja_envio.php?dif=<?php echo $row_rsCXP['dif']; ?>&ref_cobro=<?php echo $row_rsCXP['ref_op'];?>"><i class="fa fa-truck" aria-hidden="true"></i></a><a class="btn-xs" title="Crear venta con producto 0 reflejando el ingreso sin costo" type="button" href="control_mp_crear_venta_cero.php?ref_cobro=<?php echo $row_rsCXP['ref_op'];?>&importe=<?php echo $row_rsCXP['imp_mp'];?>&c_id=<?php echo $row_rsCXP['clientes_id'];?>"><i class="fa fa-shopping-bag fa-fw" aria-hidden="true"></i></a></td>
        <td><a href="https://www.mercadopago.com.ar/activities?q=<?php echo $row_rsCXP['ref_op']; ?>" target="_blank"><?php echo $row_rsCXP['ref_op']; ?></a></td>
		  <td><a href="../clientes_detalles.php?id=<?php echo $row_rsCXP['clientes_id']; ?>" target="_blank"><?php echo $row_rsCXP['clientes_id']; ?></a></td>
        <td><small><?php echo $row_rsCXP['']; ?>
			<?php if (count($array) < 2):?> <a class="btn-xs" title="Actualizar importes" type="button" href="control_mp_actualizar_importes.php?ref_op=<?php echo $row_rsCXP['ref_op']; ?>"><i class="fa fa-refresh" aria-hidden="true"></i></a><?php endif;?>
			<?php $array = (explode(',', $row_rsCXP['ventas_id'], 10)); foreach ($array as $valor) { 
				echo "$valor<form action='/db/ventas_buscador.php' method='get' target='_blank' style='display:inline-block'>
				  <input type='text' name='campo' value='ID' hidden>
				  <input type='text' name='palabra' value='$valor' hidden>
				  <button type='submit' id='$valor' name='enviar' formmethod='post'><i class='fa fa-search' aria-hidden='true'></i></button>
				</form>"; }?>  
		</small></td>
        
      </tr>   
      <?php $i++;}while ($row_rsCXP = mysql_fetch_assoc($rsCXP)); ?>     

    </table>
    
    <h4>Cobros BD con pareja en MP</h4> 
    <table class="table table-striped" border="0" cellpadding="0" cellspacing="5">
     
      <thead>
      <tr>
		  <th>#</th>
        <th title="número de movimiento">N° de Mov</th>
        <th title="concepto">Concepto</th>
        <th title="Importe MP">MP</th>
        <th title="Importe DB">DB</th>
        <th title="diferencia">Diferencia</th>
        <th title="referencia">Ref</th>
        <th><i class="fa fa-shopping-bag fa-fw" aria-hidden="true"></i></th>
        <th><i class="fa fa-user fa-fw" aria-hidden="true"></i></th>
        </tr>
		</thead>
      
      <?php $i = 1; do { ?>
      <tr>
		  <td><?php echo $i;?></td>
        <td><small><?php $array = (explode(',', $row_rsCobrosDB['nro_mov'], 10)); foreach ($array as $valor) { echo "$valor<a class='btn-xs' type='button' href='control_mp_baja.php?nro_mov=$valor'><i aria-hidden='true' class='fa fa-trash-o'></i></a><br />"; }?> </small></td>
        <td><small><?php $array2 = (explode(',', $row_rsCobrosDB['concepto'], 10)); foreach ($array2 as $valor2) { echo "$valor2<br />"; }?></small></td>
        <td><small><?php echo round($row_rsCobrosDB['imp_mp']); ?></small></td>
        <td><small><?php echo round($row_rsCobrosDB['imp_db']); ?></small></td>
        <td><span style="font-size: 0.9em;" class="badge badge-<?php if(round($row_rsCobrosDB['dif']) > 0.01): echo 'success'; else: echo 'danger';endif;?>"><?php echo round($row_rsCobrosDB['dif']); ?></span><br> <a class="btn-xs" title="Anular ingreso por envío" type="button" href="control_mp_baja_envio.php?dif=<?php echo $row_rsCobrosDB['dif']; ?>&ref_cobro=<?php echo $row_rsCobrosDB['ref_op'];?>"><i class="fa fa-truck" aria-hidden="true"></i></a> <a class="btn-xs" title="Anular ingreso por no activación de slot" type="button" href="control_mp_baja_slot_libre.php?dif=<?php echo $row_rsCobrosDB['dif']; ?>&ref_cobro=<?php echo $row_rsCobrosDB['ref_op'];?>"><i class="fa fa-money" aria-hidden="true"></i></a> <a class="btn-xs" title="Crear venta con producto 0 reflejando el ingreso sin costo" type="button" href="control_mp_crear_venta_cero.php?ref_cobro=<?php echo $row_rsCobrosDB['ref_op'];?>&importe=<?php echo $row_rsCobrosDB['imp_mp'];?>&c_id=<?php echo $row_rsCobrosDB['clientes_id'];?>"><i class="fa fa-shopping-bag fa-fw" aria-hidden="true"></i></a></td>
        <td><a href="https://www.mercadopago.com.ar/activities?q=<?php echo $row_rsCobrosDB['ref_op']; ?>" target="_blank"><?php echo $row_rsCobrosDB['ref_op']; ?></a></td>
        <td><small><?php echo $row_rsCobrosDB['ventas_id']; ?></small></td>
        <td><a href="../clientes_detalles.php?id=<?php echo $row_rsCobrosDB['clientes_id']; ?>" target="_blank"><?php echo $row_rsCobrosDB['clientes_id']; ?></a></td>
        
      </tr>   
      <?php $i++;} while ($row_rsCobrosDB = mysql_fetch_assoc($rsCobrosDB)); ?>     
</table>

	        
    <h4>Cobros BD sin pareja en MP</h4> 
    <table class="table table-striped" border="0" cellpadding="0" cellspacing="5">
     
      <thead>
      <tr>
		  <th>#</th>
      	<th title="número de movimiento">db</th>
        <th title="referencia">Ref</th>
        <th><i class="fa fa-shopping-bag fa-fw" aria-hidden="true"></i></th>
        <th><i class="fa fa-user fa-fw" aria-hidden="true"></i></th>
        </tr>
		</thead>
      
      <?php $i = 1; do { ?>
      <tr>
        
        <td><?php echo $i;?></td>
        <td><span style="font-size: 0.9em;" class="badge badge-<?php if(round($row_rsCobrosDB2['imp_db']) > 0.01): echo 'danger'; else: echo 'success';endif;?>"><?php echo round($row_rsCobrosDB2['imp_db']); ?></span></td>
        <td><a href="https://www.mercadopago.com.ar/activities?q=<?php echo $row_rsCobrosDB2['ref_cobro']; ?>" target="_blank"><?php echo $row_rsCobrosDB2['ref_cobro']; ?></a></td>
        <td><?php echo $row_rsCobrosDB2['ventas_id']; ?></td>
        <td><a href="../clientes_detalles.php?id=<?php echo $row_rsCobrosDB2['clientes_id']; ?>" target="_blank"><?php echo $row_rsCobrosDB2['clientes_id']; ?></a></td>
                
      </tr>   
      <?php $i++;} while ($row_rsCobrosDB2 = mysql_fetch_assoc($rsCobrosDB2)); ?>     

    </table>
    
    
    <h4>Cobros MP sin pareja en BD</h4> 
    <table class="table table-striped" border="0" cellpadding="0" cellspacing="5">
     
      <thead>
      <tr>
		  <th>#</th>
        <th title="número de movimiento">N° de Mov</th>
        <th title="concepto">Concepto</th>
        <th title="Importe MP">MP</th>
        <th title="referencia">Ref</th>
        </tr>
		</thead>
      
      <?php $i = 1; do { ?>
      <tr>
		  <td><?php echo $i;?></td>
        <td><small><?php $array = (explode(',', $row_rsCXP2['nro_mov'], 10)); foreach ($array as $valor) { echo "$valor<a class='btn-xs' type='button' href='control_mp_baja.php?nro_mov=$valor'><i aria-hidden='true' class='fa fa-trash-o'></i></a><br />"; }?> </small></td>
        <td><small><?php $array2 = (explode(',', $row_rsCXP2['concepto'], 10)); foreach ($array2 as $valor2) { echo "$valor2<br />"; }?></small></td>
        <td><span style="font-size: 0.9em;" class="badge badge-<?php if(round($row_rsCXP2['imp_mp']) > 0.01): echo 'success'; else: echo 'danger';endif;?>"><?php echo round($row_rsCXP2['imp_mp']); ?></span></td>
        <td><a href="https://www.mercadopago.com.ar/activities?q=<?php echo $row_rsCXP2['ref_op']; ?>" target="_blank"><?php echo $row_rsCXP2['ref_op']; ?></a></td>
      </tr>   
      <?php $i++;} while ($row_rsCXP2 = mysql_fetch_assoc($rsCXP2)); ?>     

    </table>

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

mysql_free_result($rsCXP);

mysql_free_result($rsCXP2);

mysql_free_result($rsCobrosDB);

mysql_free_result($rsCobrosDB2);
?>
