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

$colname_rsClientes = "-1";
if (isset($_GET['id'])) {
  $colname_rsClientes = (get_magic_quotes_gpc()) ? $_GET['id'] : addslashes($_GET['id']);
}
mysql_select_db($database_Conexion, $Conexion);
$query_rsClientes = sprintf("SELECT * FROM clientes WHERE ID =%s", $colname_rsClientes);
$rsClientes = mysql_query($query_rsClientes, $Conexion) or die(mysql_error());
$row_rsClientes = mysql_fetch_assoc($rsClientes);
$totalRows_rsClientes = mysql_num_rows($rsClientes);

$colname_rsClient = "-1";
if (isset($_GET['id'])) {
  $colname_rsClient = (get_magic_quotes_gpc()) ? $_GET['id'] : addslashes($_GET['id']);
}
mysql_select_db($database_Conexion, $Conexion);
$query_rsClient = sprintf("SELECT ID AS ID_stock, ID_cobro, titulo, consola, costo, code, code_prov, n_order, Notas AS stock_Notas, stock.cuentas_id, q_reset, max_Day, client.*
FROM stock
LEFT JOIN
(SELECT cuentas_id, COUNT(*) AS q_reset, MAX(Day) as max_Day FROM reseteo GROUP BY cuentas_id) AS ctas_reseteadas
ON stock.cuentas_id = ctas_reseteadas.cuentas_id
RIGHT JOIN
(SELECT ventas.ID AS ID_ventas, ID_cobro, clientes_id, stock_id, slot, medio_venta, order_item_id, order_id_web, order_id_ml, medio_cobro, ref_cobro, precio, comision, estado, ventas.Notas AS ventas_Notas, ventas.Day, ventas.usuario as usuario, clientes.ID AS ID_clientes, apellido, nombre, email, mail.*
FROM ventas
LEFT JOIN (select GROUP_CONCAT(ID SEPARATOR ',') as ID_cobro, ventas_id, medio_cobro, GROUP_CONCAT(ref_cobro SEPARATOR ',') as ref_cobro, sum(precio) as precio, sum(comision) as comision FROM ventas_cobro GROUP BY ventas_id) as ventas_cobro ON ventas.ID = ventas_cobro.ventas_id
LEFT JOIN
(SELECT ventas_id, COUNT(case when concepto = 'datos1' then 1 else null end) AS datos1, COUNT(case when concepto = 'evitar_candado' then 1 else null end) AS evitar_candado FROM mailer GROUP BY ventas_id) AS mail
ON ventas.ID = mail.ventas_id
LEFT JOIN
clientes
ON ventas.clientes_id = clientes.ID) AS client
ON stock.ID = client.stock_id
WHERE clientes_id = %s
ORDER BY client.Day DESC", $colname_rsClient);
$rsClient = mysql_query($query_rsClient, $Conexion) or die(mysql_error());
$row_rsClient = mysql_fetch_assoc($rsClient);
$totalRows_rsClient = mysql_num_rows($rsClient);

mysql_select_db($database_Conexion, $Conexion);
$query_QVtas = sprintf("SELECT COUNT(*) as Q FROM ventas WHERE clientes_id = %s GROUP BY clientes_id", $colname_rsClient);
$QVtas = mysql_query($query_QVtas, $Conexion) or die(mysql_error());
$row_QVtas = mysql_fetch_assoc($QVtas);
$totalRows_QVtas = mysql_num_rows($QVtas);

mysql_select_db($database_Conexion, $Conexion);
$query_rsVentasBajas = sprintf("SELECT ID AS ID_stock, titulo, consola, costo, Notas AS stock_Notas, cuentas_id, client.*
FROM stock
RIGHT JOIN
(SELECT ventas_baja.ventas_id AS ID_ventas, clientes_id, stock_id, slot, medio_venta, medio_cobro, precio, comision, ventas_baja.Notas AS ventas_Notas, ventas_baja.Day, ventas_baja.Notas_baja AS Notas_baja, Day_baja, clientes.ID AS ID_clientes, apellido, nombre, email, mail.*
FROM ventas_baja
LEFT JOIN (select ventas_id, medio_cobro, ref_cobro, sum(precio) as precio, sum(comision) as comision FROM ventas_cobro GROUP BY ventas_id) as ventas_cobro ON ventas_baja.ventas_id = ventas_cobro.ventas_id
LEFT JOIN
(SELECT ventas_id, COUNT(case when concepto = 'datos1' then 1 else null end) AS datos1, COUNT(case when concepto = 'evitar_candado' then 1 else null end) AS evitar_candado FROM mailer GROUP BY ventas_id) AS mail
ON ventas_baja.ventas_id = mail.ventas_id
LEFT JOIN
clientes
ON ventas_baja.clientes_id = clientes.ID) AS client
ON stock.ID = client.stock_id
WHERE clientes_id = %s
ORDER BY client.Day DESC", $colname_rsClient);
$rsVentasBajas = mysql_query($query_rsVentasBajas, $Conexion) or die(mysql_error());
$row_rsVentasBajas = mysql_fetch_assoc($rsVentasBajas);
$totalRows_rsVentasBajas = mysql_num_rows($rsVentasBajas);

mysql_select_db($database_Conexion, $Conexion);
$query_rsGtoEst = "SELECT (gasto/ingreso) as gto_x_ing FROM (SELECT (SELECT SUM(importe) as Gto_Tot FROM gastos WHERE concepto NOT LIKE '%IIBB%') as gasto, (SELECT SUM(precio) as Ing_Tot FROM ventas_cobro) as ingreso) as resultado";
$rsGtoEst = mysql_query($query_rsGtoEst, $Conexion) or die(mysql_error());
$row_rsGtoEst = mysql_fetch_assoc($rsGtoEst);
$totalRows_rsGtoEst = mysql_num_rows($rsGtoEst);


mysql_select_db($database_Conexion, $Conexion);
$query_Notas = sprintf("SELECT * FROM clientes_notas WHERE clientes_id = %s ORDER BY ID DESC", $colname_rsClientes);
$Notas = mysql_query($query_Notas, $Conexion) or die(mysql_error());
$row_Notas = mysql_fetch_assoc($Notas);
$totalRows_Notas = mysql_num_rows($Notas);
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
    <title><?php $titulo = 'Cliente #'; $titulo .= $row_rsClientes['ID']; echo $titulo; ?></title>
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
    <div class="row clientes_detalles">
    	<div class="col-xs-12 col-sm-6 col-md-5">
        <div class="panel panel-info">
            <div class="panel-heading clearfix">
              <h4 style="margin:0px;">
				  <i class="fa fa-user fa-fw" aria-hidden="true"></i> <?php echo $row_rsClientes['nombre']; ?> <?php echo $row_rsClientes['apellido']; ?>
                    <button title="Modificar Nombre" class="btn btn-xs btn-default pull-right" type="button" data-toggle="modal" data-target=".bs-example-modal-lg" onClick='document.getElementById("ifr").src="clientes_modificar.php?id=<?php echo $row_rsClientes['ID']; ?>";'>
						<i aria-hidden="true" class="fa fa-pencil"></i>
					</button>
				</h4>
            </div>
          <div class="panel-body" style="background-color: #efefef;">
          
          	<p>
				<i class="fa fa-envelope-o fa-fw"></i> <a href="#" class="btn-copiador" data-clipboard-target="#email-copy"><span id="email-copy"><?php echo $row_rsClientes['email']; ?></span> <i aria-hidden="true" class="fa fa-clone"></i></a>
					<a class="btn btn-xs btn-default" href="https://mail.google.com/a/dixgamer.com/#search/<?php echo substr($row_rsClientes['email'], 0, strpos($row_rsClientes['email'], '@')); ?>" title="filtrar cliente en gmail" target="_blank"><i aria-hidden="true" class="fa fa-google"></i>mail</a>
			  	<button title="Modificar E-mail" class="btn btn-xs btn-default pull-right" style="opacity: 0.5;" type="button" data-toggle="modal" data-target=".bs-example-modal-lg" onClick='document.getElementById("ifr").src="clientes_modificar_email.php?id=<?php echo $row_rsClientes['ID']; ?>";'>
					<i aria-hidden="true" class="fa fa-pencil"></i>
				</button>
			</p>
			
			<?php if ($row_rsClientes['ml_user']):?>
            <p><i class="fa fa-snapchat-ghost fa-fw"></i>
				<?php echo $row_rsClientes['ml_user'];?>
					<a title="Modificar ML user" class="btn-xs text-muted" style="opacity: 0.7;" type="button" data-toggle="modal" data-target=".bs-example-modal-lg" onClick='document.getElementById("ifr").src="clientes_modificar_ml_user.php?id=<?php echo $row_rsClientes['ID']; ?>";'><i aria-hidden="true" class="fa fa-pencil"></i></a>
			</p>
			<?php endif; ?>
			  
            <p>
			  <em class="text-muted" style="opacity:0.7; font-size:0.8em;">
				<i class="fa fa-map-marker fa-fw"></i><?php if ($row_rsClientes['ciudad']): ?><?php echo $row_rsClientes['ciudad']; ?>, <?php endif; ?>
				<?php echo $row_rsClientes['provincia']; ?> <?php if ($row_rsClientes['cp']):?>, <?php echo $row_rsClientes['cp']; ?><?php endif; ?><a title="Modificar otros datos" class="btn-xs text-muted" style="opacity: 0.7;" type="button" data-toggle="modal" data-target=".bs-example-modal-lg" onClick='document.getElementById("ifr").src="clientes_modificar_extras.php?id=<?php echo $row_rsClientes['ID']; ?>";'><i aria-hidden="true" class="fa fa-pencil"></i></a></em>
				
				<?php if (($row_rsClientes['tel']) || ($row_rsClientes['cel'])):?><br ><em class="text-muted" style="opacity:0.7; font-size:0.8em;"><i class="fa fa-phone fa-fw"></i><?php if ($row_rsClientes['carac']):?><?php echo $row_rsClientes['carac']; ?><?php endif; ?> <?php if ($row_rsClientes['tel']):?><?php echo $row_rsClientes['tel']; ?><?php endif; ?><?php if (($row_rsClientes['tel']) && ($row_rsClientes['cel'])):?> / <?php endif; ?><?php if ($row_rsClientes['cel']):?><?php echo $row_rsClientes['cel']; ?><?php endif; ?></em><?php endif; ?>
			  </p>		  	
			 
			<?php if ($row_rsClientes['face']):?>
            <p><i class="fa fa-facebook fa-fw"></i>
				<?php echo $row_rsClientes['face'];?>
					<a title="Modificar FB" class="btn-xs text-muted" style="opacity: 0.7;" type="button" data-toggle="modal" data-target=".bs-example-modal-lg" onClick='document.getElementById("ifr").src="clientes_facebook.php?id=<?php echo $row_rsClientes['ID']; ?>";'><i aria-hidden="true" class="fa fa-pencil"></i></a>
			</p>
			<?php endif; ?>
			  
			<p>
				<button class="btn btn-warning btn-xs" style="color: #8a6d3b; background-color:#FFDD87; opacity: 0.7" type="button" data-toggle="modal" data-target=".bs-example-modal-lg" onClick='document.getElementById("ifr").src="clientes_notas_insertar.php?id=<?php echo $row_rsClientes['ID']; ?>";'><i class="fa fa-fw fa-comment"></i> Agregar Nota</button> 
				<?php if (!$row_rsClientes['face']):?>
					<button title="Agregar FB" class="btn btn-xs btn-info" type="button" data-toggle="modal" data-target=".bs-example-modal-lg" onClick='document.getElementById("ifr").src="clientes_facebook.php?id=<?php echo $row_rsClientes['ID']; ?>";'><i class="fa fa-facebook fa-fw"></i> FB</button>
				<?php endif;?>
				<?php if (!$row_rsClientes['ml_user']):?>
					<button title="Agregar ML User" class="btn btn-xs btn-normal" type="button" data-toggle="modal" data-target=".bs-example-modal-lg" onClick='document.getElementById("ifr").src="clientes_modificar_ml_user.php?id=<?php echo $row_rsClientes['ID']; ?>";'><i class="fa fa-snapchat-ghost fa-fw"></i> ML</button>
				<?php endif;?>
				
				<?php if (strpos($row_rsClientes['auto'], 're') !== false):?>
					<a type="button" <?php if ($_SESSION['MM_UserGroup'] ==  'Adm'):?>href="clientes_hacer_revendedor.php?id=<?php echo $row_rsClientes['ID']; ?>&a=no"<?php endif;?> class="btn btn-danger btn-xs pull-right"> Revendedor</a>
					<?php else:?>
					<?php if ($_SESSION['MM_UserGroup'] ==  'Adm'):?>
						<a type="button" href="clientes_hacer_revendedor.php?id=<?php echo $row_rsClientes['ID']; ?>&a=re" class="btn btn-default btn-xs pull-right">Revende</a>
					<?php endif;?>
				<?php endif;?>	
			</p>
			
			<?php if ($_SESSION['MM_UserGroup'] ==  'Adm'):?>
			<div style="display: none;">
			<p>
			  <a class="btn btn-primary" data-toggle="collapse" href="#collapseExample" role="button" aria-expanded="false" aria-controls="collapseExample">
				Link with href
			  </a>
			  <button class="btn btn-primary" type="button" data-toggle="collapse" data-target="#collapseExample" aria-expanded="false" aria-controls="collapseExample">
				Button with data-target
			  </button>
			</p>
			<div class="collapse" id="collapseExample">
			  <div class="card card-body">
				Anim pariatur cliche reprehenderit, enim eiusmod high life accusamus terry richardson ad squid. Nihil anim keffiyeh helvetica, craft beer labore wes anderson cred nesciunt sapiente ea proident.
			  </div>
			</div>
			</div>
			  <?php endif; ?>
		  </div>
			<?php if ($row_Notas): ?>
			<ul class="list-group">
				<li class="list-group-item" style="background-color: #efefef;">
				<?php do { ?>
					<div class="alert alert-warning" style="color: #8a6d3b; background-color:#FFDD87; padding: 4px 7px; margin:0px; opacity: 0.9;"><?php echo $row_Notas['Notas'];?></div>
					<?php if (($row_Notas['Day']) > "2018-03-02"): ?>
						<em class="small text-muted pull-right" style="opacity: 0.7"><?php echo date("d M 'y", strtotime($row_Notas['Day'])); ?> (<?php echo $row_Notas['usuario'];?>)</em><br>
					<?php endif;?>
				<?php } while ($row_Notas = mysql_fetch_assoc($Notas)); ?>
				</li>
			</ul>
			<?php endif;?>
		</div>
	 </div>

    <?php 
	// SI ES MI PERFIL DE CLIENTE SOLO ME PERMITO VERLO YO Y NADIE MAS
	if(($row_rsClientes['ID'] === "371") && (!($_SESSION['MM_UserGroup'] ==  'Adm'))) {
		echo '';
	} else {
	// SI HAY VENTAS LAS MUESTRO
    if($row_rsClient): ?>
		<?php $i = 3; do { ?>
		<?php  $abrirdiv = array("5", "9", "13", "17", "21", "25", "29", "33", "37", "41", "45", "49");
				if (in_array($i, $abrirdiv)) {echo "<div class='row " . $i . " ' style='display:-webkit-box;'>"; }?>
		<?php 
			if (strpos($row_rsClient['medio_venta'], 'Web') !== false): $text = 'W'; $color1 = 'info';
			elseif (strpos($row_rsClient['medio_venta'], 'Mail') !== false): $text = 'M'; $color1 = 'danger';
			elseif (strpos($row_rsClient['medio_venta'], 'Mercado') !== false): $text = 'ML'; $color1 = 'warning';
        	endif;
		?>
        <?php 
			if (strpos($row_rsClient['medio_cobro'], 'Transferencia') !== false): $text2 = 'Bco'; $color2 = 'default';
			elseif (strpos($row_rsClient['medio_cobro'], 'Ticket') !== false): $text2 = 'Cash'; $color2 = 'success';
			elseif (strpos($row_rsClient['medio_cobro'], 'Mercado') !== false): $text2 = 'MP'; $color2 = 'primary';
			elseif (strpos($row_rsClient['medio_cobro'], 'Fondo') !== false): $text2 = 'F'; $color2 = 'normal';
        	endif;
		?>
		
		<?php
			$persona = $row_rsClient['usuario'];
			include '_colores.php';
		?>
            
        <div class="col-xs-12 col-sm-6 col-md-3" style="display: inline-flex">
        <div class=" thumbnail" <?php if ($row_rsClient['slot'] == 'Secundario'): ?>style="background-color:#efefef;"<?php endif; ?>>
            <span class="pull-right" style="width: 45%;"> 
            
            <p>
                <span class="btn-group pull-right">
					<button title="Modificar Venta" class="btn btn-xs btn-default" type="button" data-toggle="modal" data-target=".bs-example-modal-lg" onClick='document.getElementById("ifr").src="ventas_modificar.php?id=<?php echo $row_rsClient['ID_ventas']; ?>&c_id=<?php echo $row_rsClientes['ID']; ?>";'>
                    <i aria-hidden="true" class="fa fa-pencil"></i>
					</button>
					<button title="Modificar Venta" class="btn btn-xs btn-default" type="button" data-toggle="modal" data-target=".bs-example-modal-lg" onClick='document.getElementById("ifr").src="ventas_eliminar.php?id=<?php echo $row_rsClient['ID_ventas']; ?>&c_id=<?php echo $row_rsClientes['ID']; ?>";'>
                    <i aria-hidden="true" class="fa fa-trash-o"></i>
					</button>
                </span>
                <small style="color:#CFCFCF;" title="<?php echo $row_rsClient['Day']; ?>"><em class="fa fa-calendar-o fa-xs fa-fw" aria-hidden="true"></em><?php echo date("d M 'y", strtotime($row_rsClient['Day'])); ?></small>
            </p>
            
            <p>
            	<span class="badge badge-<?php echo $color;?> pull-right" style="opacity:0.7; font-weight:400;" title="<?php echo $row_rsClient['usuario']; ?>"><?php echo substr($row_rsClient['usuario'],0 , 1); ?></span>
                
            	<small class="label label-<?php echo $color1;?>" style="opacity:0.7; font-weight:400;" title="<?php echo $row_rsClient['medio_venta']; ?>"><?php echo $text;?></small><small style="color:#CFCFCF;"> <?php echo $row_rsClient['ID_ventas']; ?></small>
                <?php if($row_rsClient['estado'] == 'listo'):?>
                	<small style="color:#E7E7E7;"><i class="fa fa-download fa-fw" aria-hidden="true"></i></small>
                <?php endif; ?>
                
            </p>
            
            <p>
            	<small class="label label-<?php echo $color2;?>" style="opacity:0.7; font-weight:400;" title="<?php echo $row_rsClient['medio_cobro']; ?>"><?php echo $text2;?></small> <small style="color:#CFCFCF;">
                <?php // si es el admin muestro los ID de cobros como links para editar los cobros
				if (($_SESSION['MM_UserGroup'] ==  'Adm') or ($_SESSION['MM_Username'] ==  'Leo')):?>
                <?php $array = (explode(',', $row_rsClient['ID_cobro'], 10)); foreach ($array as $valor) { echo "<a style='color:#CFCFCF; padding:0px 2px; font-size:0.8em;' title='Editar Cobro en DB' type='button' href='ventas_cobro_modificar.php?id=$valor'>$valor</a> <a class='btn-xs' title='Actualizar importes' type='button' href='control_mp_actualizar_comision.php?id=$valor'><i class='fa fa-refresh' aria-hidden='true'></i></a>"; }?>
                <?php else:?>
				<?php echo $row_rsClient['ID_cobro']; ?>
                <?php endif;?></small>
                <span class="btn-group pull-right">
					<button title="Agregar Cobro" class="btn btn-xs btn-default" type="button" data-toggle="modal" data-target=".bs-example-modal-lg" onClick='document.getElementById("ifr").src="ventas_cobro_insertar.php?vta_id=<?php echo $row_rsClient['ID_ventas']; ?>&c_id=<?php echo $row_rsClientes['ID']; ?>";'>
                    <i aria-hidden="true" class="fa fa-plus"></i>
					</button>
				</span>
                <br />
                
                <?php 
				// si hay un cobro nuevo de mp sin ref de cobro imprimir cartel de que "falta ref cobro"
				if (($row_rsClient['ID_cobro'] > '3300') && ($row_rsClient['ref_cobro'] == "") && (strpos($row_rsClient['medio_cobro'], 'Mercado') !== false)):?>
                	<?php echo '<a href="ventas_cobro_modificar.php?id=' . $row_rsClient['ID_cobro'] . '" class="label label-danger" style="font-size:0.7em;">Agregar Ref de Cobro</a>';?>
				                 
                <?php  // si hay referencia de cobro hacer el explode para mostrar todos
				elseif ($row_rsClient['ref_cobro'] != ""):?>
					<?php $array = (explode(',', $row_rsClient['ref_cobro'], 10)); foreach ($array as $valor)
						{ echo "<small style='color:#CFCFCF; font-size:0.7em;' class='caption text-center'>$valor <a title='ver cobro en MP' target='_blank' class='btn-xs' type='button' href='https://www.mercadopago.com.ar/activities?q=$valor'><i aria-hidden='true' class='fa fa-external-link'></i></a></small>";					  
						}?> 
                <?php endif;?>
				
				<?php  // si hay un solo cobro ID y mas de 1 ref de cobro para ese ID (caso de array importado con varias ref de cobros desde MP) habilito la modif
				if (($row_rsClient['ref_cobro'] != "") & ((count(explode(',', $row_rsClient['ID_cobro'], 10))) != (count(explode(',', $row_rsClient['ref_cobro'], 10))))):?>
					<?php echo '<a href="ventas_cobro_modificar.php?id=' . $row_rsClient['ID_cobro'] . '" class="label label-danger" style="font-size:0.7em;">Modificar Ref Cobro</a>';
				endif;?>
				<?php  // si es venta Web pero no tiene order item id se debe corregir
				if ((strpos($row_rsClient['medio_venta'], 'Web') !== false) & (is_null($row_rsClient['order_item_id']))):?>
					<?php //echo '<a href="ventas_modificar.php?id=' . $row_rsClient['ID_ventas'] . '" class="label label-danger" style="font-size:0.7em;">Falta order_item_id</a>';?>
					<button class="label label-danger" type="button" data-toggle="modal" data-target=".bs-example-modal-lg" onClick='document.getElementById("ifr").src="ventas_agregar_oii.php?id=<?php echo $row_rsClientes['ID']; ?>&v_id=<?php echo $row_rsClient['ID_ventas'];?>"'>falta order item id</button>
				<?php endif;?>
				
			</p>
            
			<?php 
				if (($row_rsClient['consola'] == 'ps4') && ($row_rsClient['slot'] == 'Primario')): $costo = round($row_rsClient['costo'] * 0.6); 
				elseif (($row_rsClient['consola'] == 'ps4') && ($row_rsClient['slot'] == 'Secundario')): $costo = round($row_rsClient['costo'] * 0.4);
				elseif ($row_rsClient['consola'] == 'ps3'): $costo = round($row_rsClient['costo'] * (1 / (2 + (2 * $row_rsClient['q_reset']))));
				elseif ($row_rsClient['titulo'] == 'plus-12-meses-slot'): $costo = round($row_rsClient['costo'] * 0.5);
				elseif (($row_rsClient['consola'] !== 'ps4') && ($row_rsClient['consola'] !== 'ps3') && ($row_rsClient['titulo'] !== 'plus-12-meses-slot')): $costo = round($row_rsClient['costo']);
            endif; ?>
            <?php if ($_SESSION['MM_UserGroup'] ==  'Adm'):
				$gtoestimado = round($row_rsGtoEst['gto_x_ing'] * $row_rsClient['precio']);
				$iibbestimado = round($row_rsClient['precio'] * 0.04);
				$ganancia = round($row_rsClient['precio'] - $row_rsClient['comision'] - $costo - $gtoestimado - $iibbestimado);
			endif;?>

            <p>
            <?php if ($row_rsClient['slot'] == 'Secundario'): ?><span class="label label-danger pull-right" style="opacity:0.7">2°</span><?php endif; ?>
            	<small class="text-success"><i class="fa fa-dollar fa-xs fa-fw" aria-hidden="true"></i><?php echo round($row_rsClient['precio']); ?> </small><?php if ($_SESSION['MM_UserGroup'] ==  'Adm'):?>/ <small class="<?php if ($ganancia < '0'):?>text-danger<?php else:?>text-success<?php endif;?>"><?php echo $ganancia; endif; ?></small>

            	<br />
            	<small class="text-danger"><i class="fa fa-dollar fa-xs fa-fw" aria-hidden="true"></i><?php echo round($row_rsClient['comision']); ?><?php if ($_SESSION['MM_UserGroup'] ==  'Adm'): echo ', ' . $gtoestimado . ', ' . $iibbestimado . ', ' . $costo; endif;?></small>
            </p>
            
            <?php  // si es un código lo muestro
			if (($row_rsClient['code']) && ($row_rsClient['slot'] == "No")) :?>
            <p>
            	<small class=""><?php echo $row_rsClient['code']; ?> </small><?php if ($_SESSION['MM_UserGroup'] ==  'Adm'):echo '<br><small style="color:#CFCFCF; font-size:0.6em;" class="caption text-center">' . $row_rsClient['code_prov']; echo '-' . $row_rsClient['n_order'] . '</small>'; endif; ?></small>
            </p>
            <?php endif;?>
            </span>
            
            <img class="img img-responsive img-rounded full-width" style="width:54%; margin:0;" alt="<?php echo $row_rsClient['titulo'];?>" src="../img/productos/<?php echo $row_rsClient['consola']."/".$row_rsClient['titulo'].".jpg"; ?>">
		<div class="clearfix"></div>
		
			<div style="opacity: 0.3; padding: 4px 2px;">
				<?php if ($row_rsClient['order_item_id']):?>
					<span class="badge badge-normal" style="font-weight:400; font-size: 0.8em; color:#000">	oii #<?php echo $row_rsClient['order_item_id'];?></span>
				<?php endif;?>
				<?php if ($row_rsClient['order_id_web']):?>
					<span class="badge badge-normal" style="font-weight:400; font-size: 0.8em; color:#000">Ped #<?php echo $row_rsClient['order_id_web'];?> <a target="_blank" href="https://dixgamer.com/wp-admin/post.php?post=<?php echo $row_rsClient['order_id_web'];?>&amp;action=edit" style="color:#000" title="Ver Pedido en la Adm del Sitio">- <i class="fa fa-external-link" aria-hidden="true"></i> -</a></span>
				<?php endif;?>
				<?php if ($row_rsClient['order_id_ml']):?>
					<span class="badge badge-normal" style="font-weight:400; font-size: 0.8em; color:#000"><a target="_blank" href="https://myaccount.mercadolibre.com.ar/sales/vop?orderId=<?php echo $row_rsClient['order_id_ml'];?>&amp;role=buyer" style="color:#000" title="Ver Venta en ML">ML <i class="fa fa-external-link" aria-hidden="true"> </i> </a></span>
					
				<?php endif;?>

			</div>	
				
            <div class="caption text-center">
            <small style="color:#CFCFCF; line-height: 2em;" class="pull-left"><i class="fa fa-gamepad fa-fw" aria-hidden="true"></i> <?php echo $row_rsClient['ID_stock']; ?> 		
				<?php if ($_SESSION['MM_UserGroup'] ==  'Adm'):?>
					<?php if ($row_rsClient['stock_Notas']):?>
                    	<a href="#" data-toggle="popover" data-placement="bottom" data-trigger="focus" title="Notas de Stock" data-content="<?php echo $row_rsClient['stock_Notas']; ?>" style="color: #555555;"><i class="fa fa-comment fa-fw"></i></a>
					<?php endif; ?>
	            <?php endif; ?>
				<a title="Cambiar Producto" class="btn-xs text-muted" style="opacity: 0.7;" type="button" data-toggle="modal" data-target=".bs-example-modal-lg" onClick='document.getElementById("ifr").src="ventas_modificar_producto.php?id=<?php echo $row_rsClient['ID_ventas']; ?>&c_id=<?php echo $row_rsClientes['ID']; ?>";'><i aria-hidden="true" class="fa fa-pencil"></i></a>
				<a title="Quita Producto (producto 0)" class="btn-xs text-muted" style="opacity: 0.7;" type="button" data-toggle="modal" data-target=".bs-example-modal-lg" onClick='document.getElementById("ifr").src="ventas_quitar_producto.php?id=<?php echo $row_rsClient['ID_ventas']; ?>&c_id=<?php echo $row_rsClientes['ID']; ?>";'><i aria-hidden="true" class="fa fa-remove"></i></a>
            </small>
				
            <?php if ($row_rsClient['cuentas_id']):?><a href="cuentas_detalles.php?id=<?php echo $row_rsClient['cuentas_id']; ?>" class="btn btn-xs" title="Ir a Cuenta"><i class="fa fa-link fa-xs fa-fw" aria-hidden="true"></i> <?php echo $row_rsClient['cuentas_id']; ?></a> <?php endif; ?>
            
            
            <!--- inicio del analisis si deben avisar a victor ->
            <?php if (($row_rsClient['ID_cobro'] > '3300') && ($row_rsClient['ref_cobro'] == "") && (strpos($row_rsClient['medio_cobro'], 'Mercado') !== false)): $colorcito = 'danger'; else: $colorcito = 'info'; endif; ?>
            
                <!--- aca entran los mails de gift cards --> 
                <?php if ( ($row_rsClient['consola'] === "ps") && ($row_rsClient['slot'] == "No") && ((strpos($row_rsClient['titulo'], 'gift-card-') !== false))): ?>
                
				<button class="btn btn-<?php echo $colorcito;?> btn-xs" type="button" data-toggle="modal" data-target=".bs-example-modal-lg" onClick='document.getElementById("ifr").src="_emails/mail_datos_gift.php?id=<?php echo $row_rsClient['ID_ventas']; ?>";'><i class="fa fa-paper-plane fa-xs fa-fw" aria-hidden="true"></i> <i class="fa fa-info fa-xs fa-fw" aria-hidden="true"></i> <?php if($row_rsClient['datos1'] > 0): echo '('.$row_rsClient['datos1'].')'; endif;?></button>
			
                <!--- aca entran los mails de PLUS no slot --> 
                <?php elseif ( ($row_rsClient['consola'] === "ps") && ($row_rsClient['slot'] == "No") && ((strpos($row_rsClient['titulo'], 'plus-') !== false))): ?>
                
				<button class="btn btn-<?php echo $colorcito;?> btn-xs" type="button" data-toggle="modal" data-target=".bs-example-modal-lg" onClick='document.getElementById("ifr").src="_emails/mail_datos_plus.php?id=<?php echo $row_rsClient['ID_ventas']; ?>";'><i class="fa fa-paper-plane fa-xs fa-fw" aria-hidden="true"></i> <i class="fa fa-info fa-xs fa-fw" aria-hidden="true"></i> <?php if($row_rsClient['datos1'] > 0): echo '('.$row_rsClient['datos1'].')'; endif;?></button>
				
				<!--- aca entran los mails de FIFA POINTS no slot --> 
                <?php elseif ( ($row_rsClient['consola'] === "fifa-points") && ($row_rsClient['slot'] == "No") && ((strpos($row_rsClient['titulo'], 'ps4') !== false))): ?>
                
				<button class="btn btn-<?php echo $colorcito;?> btn-xs" type="button" data-toggle="modal" data-target=".bs-example-modal-lg" onClick='document.getElementById("ifr").src="_emails/mail_datos_fifapoints_ps4.php?id=<?php echo $row_rsClient['ID_ventas']; ?>";'><i class="fa fa-paper-plane fa-xs fa-fw" aria-hidden="true"></i> <i class="fa fa-info fa-xs fa-fw" aria-hidden="true"></i> <?php if($row_rsClient['datos1'] > 0): echo '('.$row_rsClient['datos1'].')'; endif;?></button>
				
                <?php else:?>
                
                <!--- aca entran los mails de juegos y ps plus slot pri y secu -->
				<button class="btn btn-<?php echo $colorcito;?> btn-xs" type="button" data-toggle="modal" data-target=".bs-example-modal-lg" onClick='document.getElementById("ifr").src="_emails/mail_datos_<?php echo $row_rsClient['consola']; ?><?php echo $row_rsClient['slot']; ?>.php?id=<?php echo $row_rsClient['ID_ventas']; ?>&c_id=<?php echo $row_rsClient['cuentas_id']; ?>";'><i class="fa fa-paper-plane fa-xs fa-fw" aria-hidden="true"></i> <i class="fa fa-info fa-xs fa-fw" aria-hidden="true"></i> <?php if($row_rsClient['datos1'] > 0): echo '('.$row_rsClient['datos1'].')'; endif;?></button>
				
                <?php endif;?>
            
                
            
            <!--- filtrar en gmail -->
            <a class="btn btn-xs btn-default" href="https://mail.google.com/a/dixgamer.com/#search/<?php echo substr($row_rsClientes['email'], 0, strpos($row_rsClientes['email'], '@')) . '+' . $row_rsClient['titulo'] . '+(' . $row_rsClient['consola'] .')'; ?>" title="filtrar guia de descarga en gmail" target="_blank"><i aria-hidden="true" class="fa fa-google"></i>mail</a>
            </div>
            <?php if ($row_rsClient['ventas_Notas']):?><div class="alert alert-warning"><i class="fa fa-comment fa-fw"></i> <?php echo $row_rsClient['ventas_Notas']; ?></div><?php endif; ?>
            </div>
            </div>
			<?php  $cerrardiv = array("8", "12", "16", "20", "24", "28", "32", "36", "40", "44", "48", "52");
				if ((in_array($i, $cerrardiv)) or (($i-2) == $row_QVtas['Q'])) {echo "</div>";}?>
			<?php $i++;} while ($row_rsClient = mysql_fetch_assoc($rsClient)); ?>   
    <?php endif; ?>
    <div class="clear" style="clear:both;"></div>
    
    <?php ///SI HAY VENTAS ELIMINADAS LAS MUESTRO
    if($row_rsVentasBajas): ?>
    <h3>Ventas eliminadas</h3>
		<?php do { ?>
        <div class="col-xs-12 col-sm-6 col-md-3 thumbnail" <?php if ($row_rsVentasBajas['slot'] == 'Secundario'): ?>style="background-color:#efefef;"<?php endif; ?>>
            <span class="pull-right" style="width: 45%;"> 
            <p>
                <small style="color:#CFCFCF;" title="<?php echo $row_rsVentasBajas['Day']; ?>"><em class="fa fa-calendar-o fa-xs fa-fw" aria-hidden="true"></em><?php echo date("d-M", strtotime($row_rsVentasBajas['Day'])); ?></small>
                <small style="color:#CFCFCF;" title="<?php echo $row_rsVentasBajas['Day_baja']; ?>"><em class="fa fa-trash-o fa-xs fa-fw" aria-hidden="true"></em><?php echo date("d-M", strtotime($row_rsVentasBajas['Day_baja'])); ?></small>
            </p>
            <p><small style="color:#CFCFCF;"><i class="fa fa-gamepad fa-fw" aria-hidden="true"></i> <?php echo $row_rsVentasBajas['ID_stock']; ?> <?php if ($row_rsVentasBajas['stock_Notas']):?><a href="#" data-toggle="popover" data-placement="bottom" data-trigger="focus" title="Notas de Stock" data-content="<?php echo $row_rsVentasBajas['stock_Notas']; ?>" style="color: #555555;"><i class="fa fa-comment fa-fw"></i></a><?php endif; ?></small>
            <small style="color:#CFCFCF;"><i class="fa fa-shopping-bag fa-fw" aria-hidden="true"></i> <?php echo $row_rsVentasBajas['ID_ventas']; ?></small></p>
            <p>
            <?php 
			if (strpos($row_rsVentasBajas['medio_venta'], 'Web') !== false): $text3 = '<i class="fa fa-shopping-basket fa-fw" aria-hidden="true"></i>';
			elseif (strpos($row_rsVentasBajas['medio_venta'], 'Mail') !== false): $text3 = '<i class="fa fa-envelope fa-fw"  aria-hidden="true"></i>';
			elseif (strpos($row_rsVentasBajas['medio_venta'], 'Mercado') !== false): $text3 = 'ML';
			endif;?>
            <?php 
			if (strpos($row_rsVentasBajas['medio_cobro'], 'Transferencia') !== false): $text4 = '<i class="fa fa-bank fa-xs fa-fw" aria-hidden="true"></i>';
			elseif (strpos($row_rsVentasBajas['medio_cobro'], 'Ticket') !== false): $text4 = '<i class="fa fa-dollar fa-xs fa-fw" aria-hidden="true"></i>';
			elseif (strpos($row_rsVentasBajas['medio_cobro'], 'Mercado') !== false): $text4 = 'MP';
			endif;?>
			<small style="color:#CFCFCF;" title="<?php echo $row_rsVentasBajas['medio_venta']; ?>"><?php echo $text3;?></small> <small style="color:#CFCFCF;" title="<?php echo $row_rsVentasBajas['medio_cobro']; ?>"><?php echo $text4;?></small>
            <?php if($row_rsVentasBajas['estado'] == 'listo'):?>
            <small style="color:#CFCFCF;"><i class="fa fa-download fa-fw" aria-hidden="true"></i></small>
            <?php endif; ?>
            </p>
			<?php if (($row_rsVentasBajas['consola'] == 'ps4') && ($row_rsVentasBajas['slot'] == 'Primario')): $costo2 = round($row_rsClient['costo'] * 0.6) ?>
            <?php elseif (($row_rsVentasBajas['consola'] == 'ps4') && ($row_rsVentasBajas['slot'] == 'Secundario')): $costo2 = round($row_rsVentasBajas['costo'] * 0.4) ?>
            <?php elseif ($row_rsVentasBajas['consola'] == 'ps3'): $costo2 = round($row_rsVentasBajas['costo'] * 0.25) ?>
            <?php elseif (($row_rsVentasBajas['titulo'] == 'plus-12-meses-slot')&& ($row_rsVentasBajas['slot'] == 'Primario')): $costo2 = round($row_rsVentasBajas['costo'] * 0.6) ?>
            <?php elseif (($row_rsVentasBajas['titulo'] == 'plus-12-meses-slot')&& ($row_rsVentasBajas['slot'] == 'Secundario')): $costo2 = round($row_rsVentasBajas['costo'] * 0.4) ?>
            <?php elseif (($row_rsVentasBajas['consola'] !== 'ps4') && ($row_rsVentasBajas['consola'] !== 'ps3') && ($row_rsVentasBajas['titulo'] !== 'plus-12-meses-slot')): $costo2 = round($row_rsVentasBajas['costo']) ?>
            <?php endif; ?>
            <?php $ganancia2 = round($row_rsVentasBajas['precio'] - $row_rsVentasBajas['comision'] - $costo2); ?>
            <p><small class="text-success"><i class="fa fa-dollar fa-xs fa-fw" aria-hidden="true"></i><?php echo round($row_rsVentasBajas['precio']); ?></small><br /><small class="text-danger"><i class="fa fa-dollar fa-xs fa-fw" aria-hidden="true"></i><?php echo round($row_rsVentasBajas['comision']); ?></small>
			<?php if ($_SESSION['MM_UserGroup'] ==  'Adm'):?>
            <br /><small class="text-danger"><i class="fa fa-dollar fa-xs fa-fw" aria-hidden="true"></i><?php echo $costo2; ?></small><hr style="margin:0px"><small class="<?php if ($ganancia2 < '0'):?>text-danger<?php else:?>text-success<?php endif;?>"><i class="fa fa-dollar fa-xs fa-fw" aria-hidden="true"></i><?php echo $ganancia2; ?></small>
            <?php endif;?></p>
            
            </span>
            
            <img class="img img-responsive img-rounded full-width" style="width:54%; margin:0; opacity:0.8;" alt="<?php echo $row_rsClient['titulo'];?>" src="../img/productos/<?php echo $row_rsVentasBajas['consola']."/".$row_rsVentasBajas['titulo'].".jpg"; ?>">
            <span class="label label-default <?php echo $row_rsVentasBajas['consola']; ?>" style="position: relative; bottom: 22px; left: 5px; float:left;"><?php echo $row_rsVentasBajas['consola']; ?></span>
            <div class="caption text-center">
            <?php if ($row_rsVentasBajas['cuentas_id']):?><a href="cuentas_detalles.php?id=<?php echo $row_rsVentasBajas['cuentas_id']; ?>" class="btn btn-xs" title="Ir a Cuenta"><i class="fa fa-link fa-xs fa-fw" aria-hidden="true"></i> <?php echo $row_rsVentasBajas['cuentas_id']; ?></a> <?php endif; ?>          
            </div>
            <?php if ($row_rsVentasBajas['ventas_Notas']):?><div class="alert alert-warning"><i class="fa fa-comment fa-fw"></i> <?php echo $row_rsVentasBajas['ventas_Notas']; ?></div><?php endif; ?>
            <?php if ($row_rsVentasBajas['Notas_baja']):?><div class="alert alert-danger"><i class="fa fa-comment fa-fw"></i> <?php echo $row_rsVentasBajas['Notas_baja']; ?></div><?php endif; ?>
            </div>
			<?php } while ($row_rsVentasBajas = mysql_fetch_assoc($rsVentasBajas)); ?>   
    <?php endif; }; ?>
    </div>  
	<div class="container">
			<div class="row">
			<!-- Large modal -->
			<div class="modal fade bs-example-modal-lg" tabindex="-1" role="dialog" aria-labelledby="myLargeModalLabel">
			  <div class="modal-dialog modal-lg" style="top:40px;">
				<div class="modal-content">
				<div class="modal-body" style="text-align:center;">
				  <iframe id="ifr" src="" onload="resizeIframe(this)" style="width:860px;border:0px;" ></iframe>
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
    <!-- extras de script y demás yerbas -->
<!-- InstanceEndEditable -->
  </body>
  
<!-- InstanceEnd --></html>
<?php
mysql_free_result($rsUsuarios);

mysql_free_result($rsClientes);

mysql_free_result($rsClient);

mysql_free_result($rsVentasBajas);

mysql_free_result($rsGtoEst);

mysql_free_result($Notas);

?>
