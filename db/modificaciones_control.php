<?php require_once('../Connections/Conexion.php'); ?>
<?php
$MM_authorizedUsers = "Adm,Vendedor";
$MM_donotCheckaccess = "false";
require_once('_autentificacion.php');
?>
<?php 
$currentPage = $_SERVER["PHP_SELF"];
mysql_select_db($database_Conexion, $Conexion);
$query_rsUsuarios = "SELECT * FROM usuarios";
$rsUsuarios = mysql_query($query_rsUsuarios, $Conexion) or die(mysql_error());
$row_rsUsuarios = mysql_fetch_assoc($rsUsuarios);
$totalRows_rsUsuarios = mysql_num_rows($rsUsuarios);

mysql_select_db($database_Conexion, $Conexion);
$query_rsClientes = "SELECT * FROM 
(SELECT 
GROUP_CONCAT(ID SEPARATOR ', ') as ID_M, 
clientes_id as clientes_id_M,
GROUP_CONCAT(DISTINCT apellido SEPARATOR ', ') as apellido_M,
GROUP_CONCAT(DISTINCT nombre SEPARATOR ', ') as nombre_M, 
GROUP_CONCAT(DISTINCT pais SEPARATOR ', ') as pais_M,
GROUP_CONCAT(DISTINCT provincia SEPARATOR ', ') as provincia_M, 
GROUP_CONCAT(DISTINCT ciudad SEPARATOR ', ') as ciudad_M, 
GROUP_CONCAT(DISTINCT cp SEPARATOR ', ') as cp_M, 
GROUP_CONCAT(DISTINCT direc SEPARATOR ', ') as direc_M, 
GROUP_CONCAT(DISTINCT carac SEPARATOR ', ') as carac_M, 
GROUP_CONCAT(DISTINCT tel SEPARATOR ', ') as tel_M, 
GROUP_CONCAT(DISTINCT cel SEPARATOR ', ') as cel_M, 
GROUP_CONCAT(DISTINCT email SEPARATOR ', ') as email_M, 
GROUP_CONCAT(DISTINCT ml_user SEPARATOR ', ') as ml_user_M, 
GROUP_CONCAT(DISTINCT face SEPARATOR ', ') as face_M, 
GROUP_CONCAT(DATE_FORMAT(Day,'%m-%d %k') SEPARATOR ', ') as Day_M,
GROUP_CONCAT(DISTINCT usuario SEPARATOR ', ') as usuario_M
FROM `clientes_modif` WHERE verificado = 'no' group by clientes_id) as modif
LEFT JOIN
(SELECT 
ID, 
apellido,
nombre, 
pais,
provincia, 
ciudad, 
cp, 
direc, 
carac, 
tel, 
cel, 
email, 
ml_user, 
face, 
usuario
FROM clientes) as clien
ON modif.clientes_id_M = clien.ID
ORDER BY ID DESC";
$rsClientes = mysql_query($query_rsClientes, $Conexion) or die(mysql_error());
$row_rsClientes = mysql_fetch_assoc($rsClientes);
$totalRows_rsClientes = mysql_num_rows($rsClientes);

mysql_select_db($database_Conexion, $Conexion);
$query_rsCuentas = "SELECT * FROM 
(SELECT 
GROUP_CONCAT(ID SEPARATOR ', ') as ID_M, 
cuentas_id as cuentas_id_M,
GROUP_CONCAT(DISTINCT mail_fake SEPARATOR ', ') as mail_fake_M,
GROUP_CONCAT(DISTINCT mail SEPARATOR ', ') as mail_M, 
GROUP_CONCAT(DISTINCT pass SEPARATOR ', ') as pass_M,
GROUP_CONCAT(DISTINCT name SEPARATOR ', ') as name_M, 
GROUP_CONCAT(DISTINCT surname SEPARATOR ', ') as surname_M, 
GROUP_CONCAT(DISTINCT country SEPARATOR ', ') country_M, 
GROUP_CONCAT(DISTINCT state SEPARATOR ', ') as state_M, 
GROUP_CONCAT(DISTINCT city SEPARATOR ', ') as city_M, 
GROUP_CONCAT(DISTINCT pc SEPARATOR ', ') as pc_M, 
GROUP_CONCAT(DISTINCT address SEPARATOR ', ') as address_M, 
GROUP_CONCAT(DATE_FORMAT(Day,'%m-%d %k') SEPARATOR ', ') as Day_M,
GROUP_CONCAT(DISTINCT usuario SEPARATOR ', ') as usuario_M
FROM `cuentas_modif` WHERE verificado = 'no' group by cuentas_id) as modif
LEFT JOIN
(SELECT 
ID,
mail_fake,
mail,
pass,
name,
surname,
country,
state,
city,
pc,
address,
usuario
FROM cuentas) as cuent
ON modif.cuentas_id_M = cuent.ID
ORDER BY ID DESC";
$rsCuentas = mysql_query($query_rsCuentas, $Conexion) or die(mysql_error());
$row_rsCuentas = mysql_fetch_assoc($rsCuentas);
$totalRows_rsCuentas = mysql_num_rows($rsCuentas);


mysql_select_db($database_Conexion, $Conexion);
$query_rsVentas = "SELECT * FROM 
(SELECT 
GROUP_CONCAT(ID SEPARATOR ', ') as ID_M, 
ventas_id as ventas_id_M,
GROUP_CONCAT(DISTINCT clientes_id SEPARATOR ', ') as clientes_id_M,
GROUP_CONCAT(DISTINCT stock_id SEPARATOR ', ') as stock_id_M, 
GROUP_CONCAT(DISTINCT order_item_id SEPARATOR ', ') as order_item_id_M,
GROUP_CONCAT(DISTINCT cons SEPARATOR ', ') as cons_M, 
GROUP_CONCAT(DISTINCT slot SEPARATOR ', ') as slot_M, 
GROUP_CONCAT(DISTINCT medio_venta SEPARATOR ', ') medio_venta_M, 
GROUP_CONCAT(DISTINCT Notas SEPARATOR ', ') as Notas_M, 
GROUP_CONCAT(DATE_FORMAT(Day,'%m-%d %k') SEPARATOR ', ') as Day_M,
GROUP_CONCAT(DISTINCT usuario SEPARATOR ', ') as usuario_M
FROM `ventas_modif` WHERE verificado = 'no' group by ventas_id) as modif
LEFT JOIN
(SELECT 
ID,
clientes_id,
stock_id,
order_item_id,
cons,
slot,
medio_venta,
Notas,
usuario
FROM ventas) as vent
ON modif.ventas_id_M = vent.ID
ORDER BY ID DESC";
$rsVentas = mysql_query($query_rsVentas, $Conexion) or die(mysql_error());
$row_rsVentas = mysql_fetch_assoc($rsVentas);
$totalRows_rsVentas = mysql_num_rows($rsVentas);

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
    <title><?php $titulo = 'Control Modificaciones'; echo $titulo; ?></title>
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
	  <?php 
      $sp = '<em class="badge badge-normal">';
      $esp = '</em>';
      $gg = '<span class="text-muted"> ';
      $gg2 = ' a </span> <b>';
      $hf = '</b>';
  		?>
    <?php if($row_rsClientes):?>
    <h4>Modif Clientes</h4>
    <div class="table-responsive">
    <table border="0" align="center" cellpadding="0" cellspacing="5" style="table-layout:fixed;" class="table table-striped">
    <thead>
              <tr>
                <th>ID</th>
                <th>ID Modif</th>
                <th width="85%">Cambios</th>
                <th><i class="fa fa-check fa-fw" aria-hidden="true"></i></th>
              </tr>
            </thead>
		  <tbody>

          <?php do { ?><tr>
          	<td><a title="Ir a Cliente." href="clientes_detalles.php?id=<?php echo $row_rsClientes['clientes_id_M']; ?>"><?php echo $row_rsClientes['clientes_id_M']; ?></a></td>
            <td><?php echo $row_rsClientes['ID_M']; ?></td>
            <td>
			<?php if (!($row_rsClientes['apellido'] === $row_rsClientes['apellido_M'])) {echo $sp . 'apellido ' . $esp . $gg . $row_rsClientes['apellido_M'] . $gg2 . $row_rsClientes['apellido'] . $hf;}?>
            <?php if (!($row_rsClientes['nombre'] === $row_rsClientes['nombre_M'])) {echo $sp . 'nombre ' . $esp . $gg . $row_rsClientes['nombre_M'] . $gg2 . $row_rsClientes['nombre'] . $hf;}?>
            <?php if (!($row_rsClientes['pais'] === $row_rsClientes['pais_M'])) {echo $sp . 'pais ' . $esp . $gg . $row_rsClientes['pais_M'] . $gg2 . $row_rsClientes['pais'] . $hf;}?>
            <?php if (!($row_rsClientes['provincia'] === $row_rsClientes['provincia_M'])) {echo $sp . 'provincia ' . $esp . $gg . $row_rsClientes['provincia_M'] . $gg2 . $row_rsClientes['provincia'] . $hf;}?>
            <?php if (!($row_rsClientes['ciudad'] === $row_rsClientes['ciudad_M'])) {echo $sp . 'ciudad ' . $esp . $gg . $row_rsClientes['ciudad_M'] . $gg2 . $row_rsClientes['ciudad'] . $hf;}?>
            <?php if (!($row_rsClientes['cp'] === $row_rsClientes['cp_M'])) {echo $sp . 'cp ' . $esp . $gg . $row_rsClientes['cp_M'] . $gg2 . $row_rsClientes['cp'] . $hf;}?>
            <?php if (!($row_rsClientes['direc'] === $row_rsClientes['direc_M'])) {echo $sp . 'direc ' . $esp . $gg . $row_rsClientes['direc_M'] . $gg2 . $row_rsClientes['direc'] . $hf;}?>
            <?php if (!($row_rsClientes['carac'] === $row_rsClientes['carac_M'])) {echo $sp . 'carac ' . $esp . $gg . $row_rsClientes['carac_M'] . $gg2 . $row_rsClientes['carac'] . $hf;}?>
            <?php if (!($row_rsClientes['tel'] === $row_rsClientes['tel_M'])) {echo $sp . 'tel ' . $esp . $gg . $row_rsClientes['tel_M'] . $gg2 . $row_rsClientes['tel'] . $hf;}?>
            <?php if (!($row_rsClientes['cel'] === $row_rsClientes['cel_M'])) {echo $sp . 'cel ' . $esp . $gg . $row_rsClientes['cel_M'] . $gg2 . $row_rsClientes['cel'] . $hf;}?>
            <?php if (!($row_rsClientes['email'] === $row_rsClientes['email_M'])) {echo $sp . 'email ' . $esp . $gg . $row_rsClientes['email_M'] . $gg2 . $row_rsClientes['email'] . $hf;}?>
            <?php if (!($row_rsClientes['ml_user'] === $row_rsClientes['ml_user_M'])) {echo $sp . 'ml_user ' . $esp . $gg . $row_rsClientes['ml_user_M'] . $gg2 . $row_rsClientes['ml_user'] . $hf;}?>
            <?php if (!($row_rsClientes['face'] === $row_rsClientes['face_M'])) {echo $sp . 'face ' . $esp . $gg . $row_rsClientes['face_M'] . $gg2 . $row_rsClientes['face'] . $hf;}?>
            <?php if (!($row_rsClientes['Notas'] === $row_rsClientes['Notas_M'])) {echo $sp . 'Notas ' . $esp . $gg . $row_rsClientes['Notas_M'] . $gg2 . $row_rsClientes['Notas'] . $hf;}?>
            <?php if (($row_rsClientes['usuario'] === $row_rsClientes['usuario_M'])) {echo ' <span class="badge badge-info">' . $row_rsClientes['usuario'] . '</span>';} else {echo '<span class="badge badge-info">' . $row_rsClientes['usuario'] . ' a ' . $row_rsClientes['usuario_M'] . '</span>';}?>
            <?php echo '<em class="badge badge-normal">' . $row_rsClientes['Day_M'] . '</em>';?>
             
            </td>
            <td><a class="text-center btn btn-success btn-xs" title="verificar" href="modificaciones_clientes_verificar.php?ID=<?php echo $row_rsClientes['clientes_id_M'];?>"><i class="fa fa-check fa-fw"  aria-hidden="true"></i></a></td>
            
          </tr>
        <?php } while ($row_rsClientes = mysql_fetch_assoc($rsClientes)); ?>
        </tbody>
        </table>
        </div>
        <?php endif;?>
          
    <?php if($row_rsCuentas):?>
    <h4>Modif Cuentas</h4>
    <div class="table-responsive">
    <table border="0" align="center" cellpadding="0" cellspacing="5" style="table-layout:fixed;" class="table table-striped">
    <thead>
              <tr>
                <th>ID</th>
                <th>ID Modif</th>
                <th width="85%">Cambios</th>
                <th><i class="fa fa-check fa-fw" aria-hidden="true"></i></th>
              </tr>
            </thead>
		  <tbody>

          <?php do { ?><tr>
          	<td><a title="Ir a Cuenta" href="cuentas_detalles.php?id=<?php echo $row_rsCuentas['cuentas_id_M']; ?>"><?php echo $row_rsCuentas['cuentas_id_M']; ?></a></td>
            <td><?php echo $row_rsCuentas['ID_M']; ?></td>
            <td>
			<?php if (!($row_rsCuentas['mail_fake'] === $row_rsCuentas['mail_fake_M'])) {echo $sp . 'mail_fake ' . $esp . $gg . $row_rsCuentas['mail_fake_M'] . $gg2 . $row_rsCuentas['mail_fake'] . $hf;}?>
            <?php if (!($row_rsCuentas['mail'] === $row_rsCuentas['mail_M'])) {echo $sp . 'mail ' . $esp . $gg . $row_rsCuentas['mail_M'] . $gg2 . $row_rsCuentas['mail'] . $hf;}?>
            <?php if (!($row_rsCuentas['pass'] === $row_rsCuentas['pass_M'])) {echo $sp . 'pass ' . $esp . $gg . $row_rsCuentas['pass_M'] . $gg2 . $row_rsCuentas['pass'] . $hf;}?>
            <?php if (!($row_rsCuentas['name'] === $row_rsCuentas['name_M'])) {echo $sp . 'name ' . $esp . $gg . $row_rsCuentas['name_M'] . $gg2 . $row_rsCuentas['name'] . $v;}?>
            <?php if (!($row_rsCuentas['surname'] === $row_rsCuentas['surname_M'])) {echo $sp . 'surname ' . $esp . $gg . $row_rsCuentas['surname_M'] . $gg2 . $row_rsCuentas['surname'] . $hf;}?>
            <?php if (!($row_rsCuentas['country'] === $row_rsCuentas['country_M'])) {echo $sp . 'country ' . $esp . $gg . $row_rsCuentas['country_M'] . $gg2 . $row_rsCuentas['country'] . $hf;}?>
            <?php if (!($row_rsCuentas['state'] === $row_rsCuentas['state_M'])) {echo $sp . 'state ' . $esp . $gg . $row_rsCuentas['state_M'] . $gg2 . $row_rsCuentas['state'] . $hf;}?>
            <?php if (!($row_rsCuentas['city'] === $row_rsCuentas['city_M'])) {echo $sp . 'city ' . $esp . $gg . $row_rsCuentas['city_M'] . $gg2 . $row_rsCuentas['city'] . $hf;}?>
            <?php if (!($row_rsCuentas['pc'] === $row_rsCuentas['pc_M'])) {echo $sp . 'pc ' . $esp . $gg . $row_rsCuentas['pc_M'] . $gg2 .$row_rsCuentas['pc'] . $hf;}?>
            <?php if (!($row_rsCuentas['address'] === $row_rsCuentas['address_M'])) {echo $sp . 'address ' . $esp . $gg . $row_rsCuentas['address_M'] . $gg2 . $row_rsCuentas['address'] . $hf;}?>
            <?php if (!($row_rsCuentas['Notas'] === $row_rsCuentas['Notas_M'])) {echo $sp . 'Notas ' . $esp . $gg . $row_rsCuentas['Notas_M'] . $gg2 . $row_rsCuentas['Notas'] . $hf;}?>
            <?php if (($row_rsCuentas['usuario'] === $row_rsCuentas['usuario_M'])) {echo ' <span class="badge badge-info">' . $row_rsCuentas['usuario'] . '</span>';} else {echo '<span class="badge badge-info">' . $row_rsCuentas['usuario'] . ' a ' . $row_rsCuentas['usuario_M'] . '</span>';}?>
            <?php echo '<em class="badge badge-normal">' . $row_rsCuentas['Day_M'] . '</em>';?>
             
            </td>
            <td><a class="text-center btn btn-success btn-xs" title="verificar" href="modificaciones_cuentas_verificar.php?ID=<?php echo $row_rsCuentas['cuentas_id_M'];?>"><i class="fa fa-check fa-fw"  aria-hidden="true"></i></a></td>
            
          </tr>
        <?php } while ($row_rsCuentas = mysql_fetch_assoc($rsCuentas)); ?>
        </tbody>
        </table>
        </div>
        <?php endif;?>
        
        <?php if($row_rsVentas):?>
    <h4>Modif Ventas</h4>
    <div class="table-responsive">
    <table border="0" align="center" cellpadding="0" cellspacing="5" style="table-layout:fixed;" class="table table-striped">
    <thead>
              <tr>
                <th>Cliente ID</th>
                <th>ID Modif</th>
                <th width="85%">Cambios</th>
                <th><i class="fa fa-check fa-fw" aria-hidden="true"></i></th>
              </tr>
            </thead>
		  <tbody>

          <?php do { ?><tr>
          	<td><a title="Ir a Cuenta" href="clientes_detalles.php?id=<?php echo $row_rsVentas['clientes_id_M']; ?>"><?php echo $row_rsVentas['clientes_id_M']; ?></a></td>
            <td><?php echo $row_rsVentas['ID_M']; ?></td>
            <td>
            <?php if (!($row_rsVentas['clientes_id'] === $row_rsVentas['clientes_id_M'])) {echo $sp . 'clientes_id ' . $esp . $gg . $row_rsVentas['clientes_id_M'] . $gg2 . $row_rsVentas['clientes_id'] . $hf;}?>
			<?php if (!($row_rsVentas['stock_id'] === $row_rsVentas['stock_id_M'])) {echo $sp . 'stock_id ' . $esp . $gg . $row_rsVentas['stock_id_M'] . $gg2 . $row_rsVentas['stock_id'] . $hf;}?>
            <?php if (!($row_rsVentas['order_item_id'] === $row_rsVentas['order_item_id_M'])) {echo $sp . 'order_item_id ' . $esp . $gg . $row_rsVentas['order_item_id_M'] . $gg2 . $row_rsVentas['order_item_id'] . $hf;}?>
            <?php if (!($row_rsVentas['cons'] === $row_rsVentas['cons_M'])) {echo $sp . 'cons ' . $esp . $gg . $row_rsVentas['cons_M'] . $gg2 . $row_rsVentas['cons'] . $hf;}?>
            <?php if (!($row_rsVentas['slot'] === $row_rsVentas['slot_M'])) {echo $sp . 'slot ' . $esp . $gg . $row_rsVentas['slot_M'] . $gg2 . $row_rsVentas['slot'] . $hf;}?>
            <?php if (!($row_rsVentas['medio_venta'] === $row_rsVentas['medio_venta_M'])) {echo $sp . 'medio_venta ' . $esp . $gg . $row_rsVentas['medio_venta_M'] . $gg2 . $row_rsVentas['medio_venta'] . $hf;}?>
            <?php if (!($row_rsVentas['Notas'] === $row_rsVentas['Notas_M'])) {echo $sp . 'Notas ' . $esp . $gg . $row_rsVentas['Notas_M'] . $gg2 . $row_rsVentas['Notas'] . $hf;}?>
            <?php if (($row_rsVentas['usuario'] === $row_rsVentas['usuario_M'])) {echo ' <span class="badge badge-info">' . $row_rsVentas['usuario'] . '</span>';} else {echo '<span class="badge badge-info">' . $row_rsVentas['usuario'] . ' a ' . $row_rsVentas['usuario_M'] . '</span>';}?>
            <?php echo '<em class="badge badge-normal">' . $row_rsVentas['Day_M'] . '</em>';?>
            </td>
            <td><a class="text-center btn btn-success btn-xs" title="verificar" href="modificaciones_ventas_verificar.php?ID=<?php echo $row_rsVentas['ventas_id_M'];?>"><i class="fa fa-check fa-fw"  aria-hidden="true"></i></a></td>
            
          </tr>
        <?php } while ($row_rsVentas = mysql_fetch_assoc($rsVentas)); ?>
        </tbody>
        </table>
        </div>
        <?php endif;?>
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

mysql_free_result($rsCuentas);

mysql_free_result($rsVentas);


?>
