<?php require_once('../Connections/Conexion.php'); ?>
<?php
$MM_authorizedUsers = "Adm,Vendedor";
$MM_donotCheckaccess = "false";
require_once('_autentificacion.php');
?>
<?php 
mysql_select_db($database_Conexion, $Conexion);
$query_rsUsuarios = "SELECT * FROM usuarios";
$rsUsuarios = mysql_query($query_rsUsuarios, $Conexion) or die(mysql_error());
$row_rsUsuarios = mysql_fetch_assoc($rsUsuarios);
$totalRows_rsUsuarios = mysql_num_rows($rsUsuarios);

$colname_rsCuentas = "-1";
if (isset($_GET['id'])) {
  $colname_rsCuentas = (get_magic_quotes_gpc()) ? $_GET['id'] : addslashes($_GET['id']);
}
mysql_select_db($database_Conexion, $Conexion);
$query_rsCuentas = sprintf("SELECT cuentas.*, reset.*, DATEDIFF(NOW(), dayreseteo) AS days_from_reset
FROM cuentas
LEFT JOIN
(SELECT ID AS ID_reseteo, cuentas_id AS r_cuentas_id, COUNT(*) AS Q_reseteado, MAX(Day) AS dayreseteo
FROM reseteo
GROUP BY cuentas_id
ORDER BY ID DESC) AS reset
ON ID = r_cuentas_id
WHERE ID = %s
ORDER BY ID DESC", $colname_rsCuentas);
$rsCuentas = mysql_query($query_rsCuentas, $Conexion) or die(mysql_error());
$row_rsCuentas = mysql_fetch_assoc($rsCuentas);
$totalRows_rsCuentas = mysql_num_rows($rsCuentas);

/*** NUEVA EDICION DE STOCK SOLUCIONADO EL PROBLEMA (antes si la cuenta tenia dos juegos y ninguna venta solo mostraba 1 juego) */
if (isset($_GET['id'])) {
  $colname_rsStock = (get_magic_quotes_gpc()) ? $_GET['id'] : addslashes($_GET['id']);
}
mysql_select_db($database_Conexion, $Conexion);
$query_rsStock = sprintf("SELECT ID AS ID_stock, titulo, consola, cuentas_id AS stock_cuentas_id, medio_pago, costo_usd, costo, stock.Notas AS stock_Notas, Day AS daystock, reset.ID_reseteo AS ID_reset, reset.r_cuentas_id AS reset_cuentas_id, reset.dayreseteo AS dayreset, reset.Q_reseteado AS Q_reset, vtas.*
FROM stock
LEFT JOIN
(SELECT ID AS ID_reseteo, cuentas_id AS r_cuentas_id, COUNT(*) AS Q_reseteado, MAX(Day) AS dayreseteo
FROM reseteo
GROUP BY cuentas_id
ORDER BY ID DESC) AS reset
ON cuentas_id = r_cuentas_id
LEFT JOIN
(SELECT ventas.ID as ID_ventas, stock_id AS v_stock_id, slot, medio_cobro, SUM(precio) AS total_ing, SUM(comision) AS total_com, COUNT(ventas.ID) AS Q_venta, ventas.Day AS dayventa FROM ventas LEFT JOIN (select ventas_id, medio_cobro, ref_cobro, sum(precio) as precio, sum(comision) as comision FROM ventas_cobro GROUP BY ventas_id) as ventas_cobro ON ventas.ID = ventas_cobro.ventas_id GROUP BY stock_id) AS vtas
ON stock.ID = vtas.v_stock_id
WHERE cuentas_id = %s
GROUP BY stock.ID
ORDER BY ID DESC", $colname_rsStock);
$rsStock = mysql_query($query_rsStock, $Conexion) or die(mysql_error());
$row_rsStock = mysql_fetch_assoc($rsStock);
$totalRows_rsStock = mysql_num_rows($rsStock);

/*** EDICION ANTERIOR DEL STOCK QUE ME ARROJABA PROBLEMAS (si una cuenta tenia dos juegos y ninguna venta solo mostraba 1 juego) */
/*** $colname_rsStock = "-1";
if (isset($_GET['id'])) {
  $colname_rsStock = (get_magic_quotes_gpc()) ? $_GET['id'] : addslashes($_GET['id']);
}
mysql_select_db($database_Conexion, $Conexion);
$query_rsStock = sprintf("SELECT resumen.*, ventas.ID as ID_ventas, stock_id, slot, medio_cobro, SUM(precio) AS total_ing, SUM(comision) AS total_com, COUNT(ventas.ID) AS Q_venta, Day AS dayventa
FROM ventas
RIGHT JOIN
(SELECT ID AS ID_stock, titulo, consola, cuentas_id AS stock_cuentas_id, medio_pago, costo, stock.Notas AS stock_Notas, Day AS daystock, reset.ID_reseteo AS ID_reset, reset.r_cuentas_id AS reset_cuentas_id, reset.dayreseteo AS dayreset, reset.Q_reseteado AS Q_reset
FROM stock
LEFT JOIN
(SELECT ID AS ID_reseteo, cuentas_id AS r_cuentas_id, COUNT(*) AS Q_reseteado, MAX(Day) AS dayreseteo
FROM reseteo
GROUP BY cuentas_id
ORDER BY ID DESC) AS reset
ON cuentas_id = r_cuentas_id ) AS resumen
ON stock_id = resumen.ID_stock
WHERE stock_cuentas_id = %s 
GROUP BY stock_id
ORDER BY ID DESC", $colname_rsStock);
$rsStock = mysql_query($query_rsStock, $Conexion) or die(mysql_error());
$row_rsStock = mysql_fetch_assoc($rsStock);
$totalRows_rsStock = mysql_num_rows($rsStock);
*/

$colname_rsCtaPass = "-1";
if (isset($_GET['id'])) {
  $colname_rsCtaPass = (get_magic_quotes_gpc()) ? $_GET['id'] : addslashes($_GET['id']);
}
mysql_select_db($database_Conexion, $Conexion);
$query_rsCtaPass = sprintf("SELECT * FROM cta_pass WHERE cuentas_id = %s ORDER BY ID DESC LIMIT 1, 1", $colname_rsCtaPass);
$rsCtaPass = mysql_query($query_rsCtaPass, $Conexion) or die(mysql_error());
$row_rsCtaPass = mysql_fetch_assoc($rsCtaPass);
$totalRows_rsCtaPass = mysql_num_rows($rsCtaPass);

$colname_rsClient = "-1";
if (isset($_GET['id'])) {
  $colname_rsClient = (get_magic_quotes_gpc()) ? $_GET['id'] : addslashes($_GET['id']);
}
mysql_select_db($database_Conexion, $Conexion);
$query_rsClient = sprintf("SELECT *
FROM
(SELECT 'venta' as concepto, client.Day, ID AS ID_stock, titulo, consola, cuentas_id, clientes_id, slot, ventas_Notas, apellido, nombre, email, clientes_Notas, NULL as new_pass, NULL as usuario
FROM stock
RIGHT JOIN
(SELECT ventas.ID AS ID_ventas, clientes_id, stock_id, slot, medio_cobro, precio, comision, estado, ventas.Notas AS ventas_Notas, ventas.Day, clientes.ID AS ID_clientes, apellido, nombre, email, clientes.Notas AS clientes_Notas
FROM ventas
LEFT JOIN (select ventas_id, medio_cobro, ref_cobro, sum(precio) as precio, sum(comision) as comision FROM ventas_cobro GROUP BY ventas_id) as ventas_cobro ON ventas.ID = ventas_cobro.ventas_id
LEFT JOIN
clientes
ON ventas.clientes_id = clientes.ID) AS client
ON stock.ID = client.stock_id
WHERE cuentas_id = %s
UNION ALL
SELECT 'contra' as concepto, Day, NULL as ID_stock, NULL as titulo, NULL as consola, cuentas_id, NULL as clientes_id, NULL as slot, NULL as ventas_Notas, NULL as apellido, NULL as nombre, NULL as email, NULL as clientes_Notas, new_pass, usuario FROM cta_pass WHERE cuentas_id =%s
UNION ALL
SELECT 'reset' as concepto, Day, NULL as ID_stock, NULL as titulo, NULL as consola, cuentas_id, NULL as clientes_id, NULL as slot, NULL as ventas_Notas, NULL as apellido, NULL as nombre, NULL as email, NULL as clientes_Notas, NULL as new_pass, usuario FROM reseteo WHERE cuentas_id = %s
UNION ALL
SELECT 'resetear' as concepto, Day, NULL as ID_stock, NULL as titulo, NULL as consola, cuentas_id, NULL as clientes_id, NULL as slot, NULL as ventas_Notas, NULL as apellido, NULL as nombre, NULL as email, NULL as clientes_Notas, NULL as new_pass, usuario FROM resetear WHERE cuentas_id = %s
UNION ALL
SELECT 'notas' as concepto, Day, NULL as ID_stock, NULL as titulo, NULL as consola, cuentas_id, NULL as clientes_id, NULL as slot, NULL as ventas_Notas, NULL as apellido, NULL as nombre, NULL as email, NULL as clientes_Notas, Notas as new_pass, usuario FROM cuentas_notas WHERE cuentas_id = %s
) AS listado
ORDER BY Day DESC", $colname_rsClient, $colname_rsClient, $colname_rsClient, $colname_rsClient, $colname_rsClient);
$rsClient = mysql_query($query_rsClient, $Conexion) or die(mysql_error());
$row_rsClient = mysql_fetch_assoc($rsClient);
$totalRows_rsClient = mysql_num_rows($rsClient);

mysql_select_db($database_Conexion, $Conexion);
$query_rsGtoEst = "SELECT (gasto/ingreso) as gto_x_ing FROM (SELECT (SELECT SUM(importe) as Gto_Tot FROM gastos WHERE concepto NOT LIKE '%IIBB%') as gasto, (SELECT SUM(precio) as Ing_Tot FROM ventas_cobro) as ingreso) as resultado";
$rsGtoEst = mysql_query($query_rsGtoEst, $Conexion) or die(mysql_error());
$row_rsGtoEst = mysql_fetch_assoc($rsGtoEst);
$totalRows_rsGtoEst = mysql_num_rows($rsGtoEst);

mysql_select_db($database_Conexion, $Conexion);
$query_rsFlag = sprintf("SELECT (SELECT MAX(Day) FROM reseteo WHERE cuentas_id = %s) as Max_Day_Reseteado, (SELECT MAX(Day) FROM resetear WHERE cuentas_id = %s) as Max_Day_Solicitado
", $colname_rsClient, $colname_rsClient);
$rsFlag = mysql_query($query_rsFlag, $Conexion) or die(mysql_error());
$row_rsFlag = mysql_fetch_assoc($rsFlag);
$totalRows_rsFlag = mysql_num_rows($rsFlag);

mysql_select_db($database_Conexion, $Conexion);
$query_rsSaldo = sprintf("
SELECT ex_stock_id as stk_id, 'carga' as concepto, cuentas_id, costo, costo_usd, code, code_prov, n_order, usuario FROM saldo WHERE cuentas_id = %s
UNION ALL
SELECT ID as stk_id, 'descarga' as concepto, cuentas_id, (-1 * SUM(costo)) as costo, (-1 * SUM(costo_usd)) as costo_usd, '' as code, '' as code_prov, '' as n_order, usuario FROM stock WHERE cuentas_id = %s", $colname_rsClient, $colname_rsClient);
$rsSaldo = mysql_query($query_rsSaldo, $Conexion) or die(mysql_error());
$row_rsSaldo = mysql_fetch_assoc($rsSaldo);
$totalRows_rsSaldo = mysql_num_rows($rsSaldo);

mysql_select_db($database_Conexion, $Conexion);
$query_rsTieneSaldo = sprintf("SELECT cuentas_id, costo_usd, code, code_prov, usuario FROM saldo WHERE cuentas_id = %s", $colname_rsClient);
$rsTieneSaldo = mysql_query($query_rsTieneSaldo, $Conexion) or die(mysql_error());
$row_rsTieneSaldo = mysql_fetch_assoc($rsTieneSaldo);
$totalRows_rsTieneSaldo = mysql_num_rows($rsTieneSaldo);
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
	  
<script language="javascript">
//Su explorador no soporta java o lo tiene deshabilitado; esta pagina necesita javascript para funcionar correctamente<!--
//Copyright © McAnam.com
function abrir(direccion, pantallacompleta, herramientas, direcciones, estado, barramenu, barrascroll, cambiatamano, ancho, alto, izquierda, arriba, sustituir){
     var opciones = "fullscreen=" + pantallacompleta +
                 ",toolbar=" + herramientas +
                 ",location=" + direcciones +
                 ",status=" + estado +
                 ",menubar=" + barramenu +
                 ",scrollbars=" + barrascroll +
                 ",resizable=" + cambiatamano +
                 ",width=" + ancho +
                 ",height=" + alto +
                 ",left=" + izquierda +
                 ",top=" + arriba;
     var ventana = window.open(direccion,"venta",opciones,sustituir);

}                    
//-->    
</script> 
	  <script>
	  function resizeIframe(obj) {
		obj.style.height = obj.contentWindow.document.body.scrollHeight + 'px';
	  }
	</script>
	    <script>
		  $( function() {
			$( "#dialog" ).dialog({
			  autoOpen: false,
			  show: {
				effect: "blind",
				duration: 1000
			  },
			  hide: {
				effect: "explode",
				duration: 1000
			  }
			});

			$( "#opener" ).on( "click", function() {
			  $( "#dialog" ).dialog( "open" );
			});
		  } );
	  </script>
    <title><?php $titulo = 'Cuenta #'; $titulo .= $row_rsCuentas['ID']; echo $titulo; ?></title>
    <?php $titulo .= '<a title="Cuenta anterior" style="color:#ccc;" href="cuentas_detalles.php?id=' . ($row_rsCuentas['ID'] - 1) . '" target="_self"> < </a> <a title="Cuenta siguiente" style="color:#ccc;" href="cuentas_detalles.php?id=' . ($row_rsCuentas['ID'] + 1) . '" target="_self"> > </a>';?>
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
    
    <!-- termina el head -->
<!-- InstanceEndEditable -->
  </head>

  <body>
  <?php include('_barra_nav.php'); ?>

    <div class="container">
	<h1><?php echo $titulo; ?></h1>
    <!-- InstanceBeginEditable name="body" -->
    <div class="row">
    	<div class="col-xs-12 col-sm-6 col-md-4">
        <div class="panel panel-warning">
            <div class="panel-heading clearfix">
              <h4 style="margin:0px;">
              		<a href="#" class="btn-copiador" data-clipboard-target="#email-copy" style="color:#FFF;"><span id="email-copy"><?php echo $row_rsCuentas['mail_fake']; ?></span> <i aria-hidden="true" class="fa fa-clone"></i></a>
                  <span class="btn-group pull-right">
                    <a class="btn btn-xs btn-default" type="button" href="cuentas_modificar.php?id=<?php echo $row_rsCuentas['ID']; ?>"><i aria-hidden="true" class="fa fa-pencil"></i></a>
                    
                  </span>
				</h4>
            </div>
          <div class="panel-body" style="background-color: #efefef;">
          	
            	<div class="dropdown pull-right"><button class="btn btn-default dropdown-toggle btn-xs" type="button" id="dropdownMenu1" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false"><i class="fa fa-fw fa-refresh"></i> cambiar pass<span class="caret"></span></button>
              <ul class="dropdown-menu bg-info" aria-labelledby="dropdownMenu1">
                <li class="dropdown-header">¿Seguro deseas</li>
                <li class="dropdown-header">cambiar el pass?</li>
                <li role="separator" class="divider"></li>
                <li><a class="btn-xs text-center" title="cambiar pass" href="cuentas_modificar_pass.php?id=<?php echo $row_rsCuentas['ID']; ?>">Si, seguro!</a></li>
              </ul>
            </div>
            <p>
            	<i class="fa fa-key fa-fw"></i> <a href="#" class="btn-copiador" data-clipboard-target="#pass-copy"><span id="pass-copy"><?php echo $row_rsCuentas['pass']; ?></span> <i aria-hidden="true" class="fa fa-clone"></i></a>
            </p>
            
            <p><i class="fa fa-user fa-fw"></i><span id="name"><?php echo $row_rsCuentas['name']; ?></span> <span id="surname"><?php echo $row_rsCuentas['surname']; ?></span></p>
            	
            <?php
			$persona = $row_rsCuentas['usuario'];
			include '_colores.php';
			?>
			
            <?php if ($row_rsCuentas['mail']):?><p class="text-muted"><i class="fa fa-user-secret fa-fw"></i><span id="email-real"><?php echo $row_rsCuentas['mail']; ?></span> <span class="badge badge-<?php echo $color;?> pull-right" style="opacity:0.5; font-weight:400;" title="Registrado por <?php echo $row_rsCuentas['usuario'];?>"><?php echo substr($row_rsCuentas['usuario'],0 , 1); ?></span></p><?php endif; ?>
            <em class="text-muted" style="opacity:0.7; font-size:0.8em;"><i class="fa fa-map-marker fa-fw"></i><span id="address"><?php echo $row_rsCuentas['address']; ?></span>, <?php echo $row_rsCuentas['city']; ?>, <?php echo $row_rsCuentas['state']; ?>, <?php echo $row_rsCuentas['pc']; ?></em><a style="opacity:0.7; font-size:0.8em;" class="text-muted btn-xs" href="cuentas_modificar_direc.php?id=<?php echo $row_rsCuentas['ID']; ?>"><i aria-hidden="true" class="fa fa-pencil"></i></a><br />
            <p>
			<em class="text-muted" style="opacity:0.7;font-size:0.8em;"><i class="fa fa-question-circle fa-fw"></i>
            <?php if ($row_rsCuentas['ID'] > "5338"): ?>
            	<?php echo '<span id="days">' .$row_rsCuentas['days'] . '</span>-<span id="months">' . $row_rsCuentas['months'] . '</span>-<span id="years">' . $row_rsCuentas['years'] . '</span>; Nac: <span id="nacimiento">' . $row_rsCuentas['nacimiento'] . '</span>'; ?>
			<?php else:?>
				<?php if (($row_rsCuentas['ID'] < "4115") OR ($row_rsCuentas['ID'] === "4158")): echo '25 Dic 1990; Naciste: Corrientes'; else:?>
                    <?php if (strpos($row_rsCuentas['usuario'], 'Victor') !== false): echo '03 Oct 1987; Visitar: Andorra';
                          elseif (strpos($row_rsCuentas['usuario'], 'Manuel') !== false): echo '25 Dic 1990; Naciste: Corrientes';
                          elseif (strpos($row_rsCuentas['usuario'], 'Leo') !== false): echo '02 Feb 1992; Visitar: Inglaterra';
						  elseif (strpos($row_rsCuentas['usuario'], 'Hernan') !== false): echo '14 Nov 1986; Visitar: Italia';
						  elseif (strpos($row_rsCuentas['usuario'], 'Mariano') !== false): echo '05 Jul 1993; Visitar: Polonia';
						  elseif (strpos($row_rsCuentas['usuario'], 'Enri') !== false): echo '30 Nov 1987; Visitar: Egipto';
                    ?>
                    <?php endif;?>
                <?php endif;?>
            <?php endif;?>
            </em>
			</p>
			  <p><button class="btn btn-warning btn-xs" style="color: #8a6d3b; background-color:#FFDD87; opacity: 0.7" type="button" data-toggle="modal" data-target=".bs-example-modal-lg" onClick='document.getElementById("ifr").src="cuentas_notas_insertar.php?c_id=<?php echo $row_rsCuentas['ID'];?>";'><i class="fa fa-fw fa-comment"></i> Agregar Nota</button></p>
           
          </div>
          
          <?php if ($row_rsTieneSaldo['costo_usd']):?>
            
          <ul class="list-group">
          	<li class="list-group-item" style="background-color: #efefef;">
          	<?php $saldo = 0.00; $saldoARS = 0.00; do { ?>
			<?php if ((!($row_rsSaldo['code'] == NULL)) && ($_SESSION['MM_UserGroup'] ==  'Adm')):?>	
			<div class="dropdown" style="display:inline;"><button class="btn btn-default dropdown-toggle btn-xs" type="button" id="dropdownMenu2" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false"><i class="fa fa-fw fa-trash-o"></i></button>
              <ul class="dropdown-menu bg-info" aria-labelledby="dropdownMenu1">
                <li class="dropdown-header">¿Devolver GC?</li>
                <li role="separator" class="divider"></li>
                <li><a class="btn-xs text-center" title="cambiar pass" href="cuentas_saldo_devolver.php?id=<?php echo $row_rsSaldo['stk_id']; ?>&c_id=<?php echo $row_rsSaldo['cuentas_id']; ?>">Si, seguro!</a></li>
              </ul>
            </div>
			<?php endif;?>
			<?php
			$persona = $row_rsSaldo['usuario'];
			include '_colores.php';
			?>
              <em>
				<?php if (!($row_rsSaldo['code'] == NULL)):?>
                  <small class="label label-default"><?php echo $row_rsSaldo['code']; ?></small> <?php if ($_SESSION['MM_UserGroup'] ==  'Adm'): echo '<span class="text-mued" style="font-size:0.6em;"> ('. substr($row_rsSaldo['code_prov'], 0 , 3) . ') ' .$row_rsSaldo['n_order']. '</span>'; endif;?> <span class="badge badge-<?php echo $color;?>" style="opacity:0.5; font-weight:400;" title="Fondeado por <?php echo $row_rsSaldo['usuario'];?>"> <?php echo substr($row_rsSaldo['usuario'],0 , 1); ?></span>
                <?php endif;?>
                 	<small class="pull-right text-muted"><?php echo $row_rsSaldo['costo_usd'];?> <?php if ($_SESSION['MM_UserGroup'] ==  'Adm'): ?><span style="font-size:0.8em;">(<?php echo $row_rsSaldo['costo'];?>)</span><?php endif;?></small><br />
              </em>
          	<?php $saldo = $saldo + $row_rsSaldo['costo_usd']; $saldoARS = $saldoARS + $row_rsSaldo['costo'];
			} while ($row_rsSaldo = mysql_fetch_assoc($rsSaldo)); ?>
				
          	<small class="pull-right" style="font-weight:bold;">saldo <img src="https://exur-exur.netdna-ssl.com/realskin/images/usa.png" style="opacity:0.7"> <em style="border-top: 2px solid #cccccc; padding: 0 10px;"><?php echo number_format($saldo , 2, '.', '');?> <?php if ($_SESSION['MM_UserGroup'] ==  'Adm'): ?><span style="font-size:0.8em;">(<?php echo number_format($saldoARS , 2, '.', '');?>)</span><?php endif;?></em></small><br /> 
			</li> 
	  	
          </ul>
          <?php endif;?>
          
          <ul class="list-group">
              <li class="list-group-item" style="background-color: #efefef;">
              		<?php 
					$solicitud = strtotime($row_rsFlag['Max_Day_Solicitado']);
					$reseteo = strtotime($row_rsFlag['Max_Day_Reseteado']);
					if ( ($row_rsFlag['Max_Day_Solicitado'] === NULL) or (($solicitud < $reseteo) && ($row_rsFlag['Max_Day_Reseteado'] != NULL))):?>
				 	<button class="btn btn-normal btn-xs pull-right" style="opacity: 0.5;" type="button" data-toggle="modal" data-target=".bs-example-modal-lg" onClick='document.getElementById("ifr").src="cuentas_reseteo_solicitar.php?id=<?php echo $row_rsCuentas['ID'];?>";'><i class="fa fa-fw fa-power-off"></i> Pedir Reseteo</button>
				  
				  	<?php else:?>
				 	<a class="btn btn-danger btn-xs pull-right" style="opacity: 0.9;" href=""><i class="fa fa-fw fa-check"></i> Reset Pendiente</a>
				 	<?php endif;?>
                 
			  		<?php if (($row_rsCuentas['days_from_reset'] == NULL) || ($row_rsCuentas['days_from_reset'] > 180)):?>
				  	<button class="btn btn-default btn-xs" type="button" data-toggle="modal" data-target=".bs-example-modal-lg" onClick='document.getElementById("ifr").src="cuentas_reseteo_insertar.php?id=<?php echo $row_rsCuentas['ID'];?>";'><i class="fa fa-fw fa-power-off"></i> Resetear</button>
				  	<?php endif;?>
				  
                    <?php if ($row_rsCuentas['Q_reseteado']):?>
                    <em class="small" style="color:#BBB;"> (<?php echo $row_rsCuentas['Q_reseteado']; ?> reset) hace <?php echo $row_rsCuentas['days_from_reset']; ?> días</em>
					<?php endif; ?>
                   
               </li>
            </ul>
        </div>
     </div>
     
    <?php ///SI HAY JUEGOS LAS MUESTRO
    if($row_rsStock): ?>
		<?php do { ?>
        <div class="col-xs-12 col-sm-4 col-md-3">
            <div class="thumbnail">
            <span class="pull-right" style="width: 45%;"> 
            <p>
                <span class="btn-group pull-right">
                    <a class="btn btn-xs btn-default" type="button" href="stock_modificar.php?id=<?php echo $row_rsStock['ID_stock']; ?>"><i aria-hidden="true" class="fa fa-pencil"></i></a>
                    <a class="btn btn-xs btn-default" type="button" href="stock_modificar.php?id=<?php echo $row_rsStock['ID_stock']; ?>"><i aria-hidden="true" class="fa fa-trash-o"></i></a>
                </span>
                
                <small style="color:#CFCFCF;" title="<?php echo $row_rsStock['daystock']; ?>"><i class="fa fa-calendar-o fa-xs fa-fw" aria-hidden="true"></i><?php echo date("d M 'y", strtotime($row_rsStock['daystock'])); ?></small>

            </p>
            <p><small style="color:#CFCFCF;"><i class="fa fa-gamepad fa-fw" aria-hidden="true"></i> <?php echo $row_rsStock['ID_stock']; ?> 
					<?php if ($row_rsStock['stock_Notas']):?>
                    	<a href="#" data-toggle="popover" data-placement="bottom" data-trigger="focus" title="Notas de Stock" data-content="<?php echo $row_rsStock['stock_Notas']; ?>" style="color: #555555;"><i class="fa fa-comment fa-fw"></i></a>
				<?php endif; ?>
			</small>
            <small style="color:#CFCFCF;"> <i class="fa fa-shopping-cart fa-fw" aria-hidden="true"></i> <?php echo $row_rsStock['Q_venta']; ?>x</small>
            </p>
            
            <p><small class="text-success"><i class="fa fa-dollar fa-xs fa-fw" aria-hidden="true"></i><?php echo round($row_rsStock['total_ing']); ?></small>
            <br /><small class="text-danger"><i class="fa fa-dollar fa-xs fa-fw" aria-hidden="true"></i><?php echo round($row_rsStock['total_com']); ?></small><br />
            
            <?php if ($_SESSION['MM_UserGroup'] ==  'Adm'):?>
            <?php $gtoestimado = round($row_rsGtoEst['gto_x_ing'] * $row_rsStock['total_ing']); ?>
            <?php $iibbestimado = round($row_rsStock['total_ing'] * 0.035); ?>
            <?php $resultado = round($row_rsStock['total_ing'] - $row_rsStock['total_com'] - $row_rsStock['costo'] - $iibbestimado - $gtoestimado);?><small class="text-danger"><i class="fa fa-dollar fa-xs fa-fw" aria-hidden="true"></i><?php echo $iibbestimado; ?>, <?php echo $gtoestimado; ?>, <?php echo round($row_rsStock['costo']); ?></small>
            <hr style="margin:0px">
            <small class="<?php if ($resultado < '0'):?>text-danger<?php else:?>text-success<?php endif;?>"><i class="fa fa-dollar fa-xs fa-fw" aria-hidden="true"></i><?php echo $resultado; ?></small>
            <?php endif;?>
            </p>
            <?php if (($row_rsTieneSaldo['costo_usd']) or ($_SESSION['MM_UserGroup'] ==  'Adm')):?>
            <p style="opacity:0.7"><img src="https://exur-exur.netdna-ssl.com/realskin/images/usa.png" style="opacity:0.7"> <small><strong><?php echo $row_rsStock['costo_usd'];?></strong></small></p>
            <?php endif;?>
            </span>
            
            <img class="img img-responsive img-rounded full-width" style="width:54%; margin:0;" alt="<?php echo $row_rsStock['titulo'];?>" src="../img/productos/<?php echo $row_rsStock['consola']."/".$row_rsStock['titulo'].".jpg"; ?>">
            <span class="label label-default <?php echo $row_rsStock['consola']; ?>" style="position: absolute; bottom: 35px; left: 13px;"><?php echo $row_rsStock['consola']; ?></span>
            <div class="caption text-center">
            </div>
            </div>
            </div>
			<?php } while ($row_rsStock = mysql_fetch_assoc($rsStock)); ?>   
    <?php endif; ?>
    
    <div class="col-md-2 pull-right">
		<p>
		 <button class="btn btn-primary btn-lg" type="button" data-toggle="modal" data-target=".bs-example-modal-lg" onClick='document.getElementById("ifr").src="cuentas_saldo_pre_insertar.php?cta_id=<?php echo $row_rsCuentas['ID'];?>";'><i class="fa fa-fw fa-dollar"></i> Cargar Saldo</button>
	</p>
     <?php if ($saldo > 0.00):?>
     <p>
     <a class="btn btn-primary btn-lg" type="button" href="cuentas_stock_insertar.php?cta_id=<?php echo $row_rsCuentas['ID'];?>"><i class="fa fa-fw fa-gamepad "></i> Cargar Juego</a></p>
     <?php endif;?>
     </div>
     
    </div>
     
    <?php if ($row_rsClient): ?>
    <div class="table-responsive">
    <table border="0" align="center" cellpadding="0" cellspacing="5" class="table table-striped">
    <thead>
              <tr>
                <th>Fecha</th>
                <th>Cliente</th>
                <th>Email</th>
                <th>Juego</th>
                <th style="text-align:right;"></th>
              </tr>
            </thead>
		  <tbody>
          <?php do { ?><tr>
          
            <td><?php echo date("d M 'y", strtotime($row_rsClient['Day'])); ?></td>
            <?php if ($row_rsClient['concepto'] == 'contra'):?>
            <td colspan="4"><em class="badge badge-default" style="font-weight:normal; opacity:0.8;"><i class="fa fa-key fa-fw"></i> Nueva contra: <?php echo $row_rsClient['new_pass'];?> (<?php echo $row_rsClient['usuario'];?>)</em></td>
			<?php elseif ($row_rsClient['concepto'] == 'notas'):?>
			<td colspan="4"><div class="alert alert-warning" style="color: #8a6d3b; background-color:#FFDD87; padding: 4px 7px; font-size: 12px; font-style:italic; margin:0px; opacity: 0.9;"><i class="fa fa-comment fa-fw"></i> <?php echo $row_rsClient['new_pass'];?> (<?php echo $row_rsClient['usuario'];?>)</em></td> 
            <?php elseif ($row_rsClient['concepto'] == 'reset'):?>
            <td colspan="4"><em class="badge badge-default" style="font-weight:normal; opacity:0.8;"><i class="fa fa-power-off fa-fw" aria-hidden="true"></i> Reseteado por <?php echo $row_rsClient['usuario'];?></em></td>
            <?php elseif ($row_rsClient['concepto'] == 'resetear'):?>
            <td colspan="4"><em class="badge badge-danger" style="font-weight:normal; opacity:0.8;"><i class="fa fa-power-off fa-fw" aria-hidden="true"></i> Solicitud de Reseteo por <?php echo $row_rsClient['usuario'];?></em></td>
            <?php else:?>
            <td><span class="text-muted small">#<?php echo $row_rsClient['clientes_id']; ?></span> <span class="label label-info"><?php echo $row_rsClient['nombre']; ?> <?php echo $row_rsClient['apellido']; ?></span> 
            <?php if ($row_rsClient['clientes_Notas']):?><a href="#" data-toggle="popover" data-placement="bottom" data-trigger="focus" title="Notas de Cliente" data-content="<?php echo $row_rsClient['clientes_Notas']; ?>" style="color: #555555;"><i class="fa fa-comment fa-fw"></i></a><?php endif; ?></td>
			
            <td><a title="Ir a Cliente" href="clientes_detalles.php?id=<?php echo $row_rsClient['clientes_id']; ?>"><?php echo $row_rsClient['email']; ?></a> <a class="btn btn-xs btn-default" style="opacity:0.6;" href="https://mail.google.com/a/dixgamer.com/#search/<?php echo substr($row_rsClient['email'], 0, strpos($row_rsClient['email'], '@')) . '+' . substr($row_rsCuentas['mail_fake'], 0, strpos($row_rsCuentas['mail_fake'], '@')); ?>" title="filtrar guia de descarga en gmail" target="_blank"><i aria-hidden="true" class="fa fa-google"></i>mail</a></td>
            <td><span class="label <?php if ($row_rsClient['slot'] == 'Primario'):?>label-default<?php endif;?>"><?php echo $row_rsClient['titulo']; ?></span> <?php if ($row_rsClient['slot'] == 'Secundario'): ?><span class="label label-danger" style="opacity:0.7">2°</span><?php endif; ?> <span class="label label-default <?php echo $row_rsClient['consola']; ?>"><?php echo $row_rsClient['consola']; ?></span> <?php echo $row_rsClient['ID_ventas']; ?> <?php if ($row_rsClient['ventas_Notas']):?><a href="#" data-toggle="popover" data-placement="bottom" data-trigger="focus" title="Notas de Venta" data-content="<?php echo $row_rsClient['ventas_Notas']; ?>" style="color: #555555;"><i class="fa fa-comment fa-fw"></i></a><?php endif; ?></td>
           
            <td style="text-align:right;"><a class="btn btn-xs btn-default" type="button" title="Modificar venta" href="ventas_modificar.php?id=<?php echo $row_rsClient['ID_ventas']; ?>"><i aria-hidden="true" class="fa fa-pencil"></i></a></td>
            <?php endif;?>
          </tr>
        <?php } while ($row_rsClient = mysql_fetch_assoc($rsClient)); ?>
        </tbody>
        </table>
        </div>
        <?php endif; ?> 
	  <div class="container">
	    <div class="row">
        <!-- Large modal -->
		<div class="modal fade bs-example-modal-lg" tabindex="-1" role="dialog" aria-labelledby="myLargeModalLabel">
		  <div class="modal-dialog modal-lg" style="top:40px;">
			<div class="modal-content">
			<div class="modal-body">
			  <iframe id="ifr" src="" onload="resizeIframe(this)" style="width:860px;border:0px;"></iframe>
			  </div>
			</div>
		  </div>
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
    
	<!-- InstanceEndEditable -->
  </body>
  
<!-- InstanceEnd --></html>
<?php
mysql_free_result($rsUsuarios);

mysql_free_result($rsCuentas);

mysql_free_result($rsStock);

mysql_free_result($rsCtaPasss);

mysql_free_result($rsClient);

mysql_free_result($rsGtoEst);

mysql_free_result($rsFlag);

mysql_free_result($rsSaldo);

mysql_free_result($rsTieneSaldo);
?>
