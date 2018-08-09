<?php require_once('../Connections/Conexion.php'); ?>
<?php
// DEFINIR FECHA EN ARGENTINA
date_default_timezone_set('America/Argentina/Buenos_Aires');
?>
<?php
//initialize the session
if (!isset($_SESSION)) {
  session_start();
}

// ** Logout the current user. **
$logoutAction = $_SERVER['PHP_SELF']."?doLogout=true";
if ((isset($_SERVER['QUERY_STRING'])) && ($_SERVER['QUERY_STRING'] != "")){
  $logoutAction .="&". htmlentities($_SERVER['QUERY_STRING']);
}

if ((isset($_GET['doLogout'])) &&($_GET['doLogout']=="true")){
  //to fully log out a visitor we need to clear the session varialbles
  $_SESSION['MM_Username'] = NULL;
  $_SESSION['MM_UserGroup'] = NULL;
  $_SESSION['PrevUrl'] = NULL;
  unset($_SESSION['MM_Username']);
  unset($_SESSION['MM_UserGroup']);
  unset($_SESSION['PrevUrl']);
	
  $logoutGoTo = "salida.php";
  if ($logoutGoTo) {
    header("Location: $logoutGoTo");
    exit;
  }
}
?>
<?php
if (!isset($_SESSION)) {
  session_start();
}
$MM_authorizedUsers = "Adm";
$MM_donotCheckaccess = "false";

// *** Restrict Access To Page: Grant or deny access to this page
function isAuthorized($strUsers, $strGroups, $UserName, $UserGroup) { 
  // For security, start by assuming the visitor is NOT authorized. 
  $isValid = False; 

  // When a visitor has logged into this site, the Session variable MM_Username set equal to their username. 
  // Therefore, we know that a user is NOT logged in if that Session variable is blank. 
  if (!empty($UserName)) { 
    // Besides being logged in, you may restrict access to only certain users based on an ID established when they login. 
    // Parse the strings into arrays. 
    $arrUsers = Explode(",", $strUsers); 
    $arrGroups = Explode(",", $strGroups); 
    if (in_array($UserName, $arrUsers)) { 
      $isValid = true; 
    } 
    // Or, you may restrict access to only certain users based on their username. 
    if (in_array($UserGroup, $arrGroups)) { 
      $isValid = true; 
    } 
    if (($strUsers == "") && false) { 
      $isValid = true; 
    } 
  } 
  return $isValid; 
}

$MM_restrictGoTo = "index.php";
if (!((isset($_SESSION['MM_Username'])) && (isAuthorized("",$MM_authorizedUsers, $_SESSION['MM_Username'], $_SESSION['MM_UserGroup'])))) {   
  $MM_qsChar = "?";
  $MM_referrer = $_SERVER['PHP_SELF'];
  if (strpos($MM_restrictGoTo, "?")) $MM_qsChar = "&";
  if (isset($QUERY_STRING) && strlen($QUERY_STRING) > 0) 
  $MM_referrer .= "?" . $QUERY_STRING;
  $MM_restrictGoTo = $MM_restrictGoTo. $MM_qsChar . "accesscheck=" . urlencode($MM_referrer);
  header("Location: ". $MM_restrictGoTo); 
  exit;
}
?>
<?php 
/*** SUPER QUERY DE STOCK ***/
$maxRows_rsCXP = 9999;
$pageNum_rsCXP = 0;
if (isset($_GET['pageNum_rsCXP'])) {
  $pageNum_rsCXP = $_GET['pageNum_rsCXP'];
}
$startRow_rsCXP = $pageNum_rsCXP * $maxRows_rsCXP;

mysql_select_db($database_Conexion, $Conexion);
$query_rsCXP = "SELECT * FROM (select
    p.ID,
    p.post_parent,
    REPLACE(REPLACE(REPLACE(REPLACE(TRIM(LCASE(p.post_title)), ' ', '-'), '''', ''), '’', ''), '.', '') AS titulo,
    max( CASE WHEN pm.meta_key = 'consola' and  p.ID = pm.post_id THEN pm.meta_value END ) as consola,
    '' as slot,
	max( CASE WHEN pm.meta_key = '_regular_price' and p.ID = pm.post_id THEN pm.meta_value END ) as reg_price,
    max( CASE WHEN pm.meta_key = '_sale_price' and p.ID = pm.post_id THEN pm.meta_value END ) as sale_price,
	max( CASE WHEN pm.meta_key = 'idioma' and p.ID = pm.post_id THEN pm.meta_value END ) as idioma,
    max( CASE WHEN pm.meta_key = 'peso' and p.ID = pm.post_id THEN pm.meta_value END ) as peso,
	max( CASE WHEN pm.meta_key = 'lanzamiento' and p.ID = pm.post_id THEN pm.meta_value END ) as lanzamiento,
	max( CASE WHEN pm.meta_key = 'jugadores' and p.ID = pm.post_id THEN pm.meta_value END ) as jugadores,
    post_status
from
    cbgw_posts as p
LEFT JOIN
    cbgw_postmeta as pm
ON
   p.ID = pm.post_id
where
    post_type = 'product' and
    post_status = 'publish'
group BY
	p.ID
UNION ALL
select
    p.ID,
    p.post_parent,
    SUBSTRING_INDEX(SUBSTRING_INDEX(REPLACE(REPLACE(REPLACE(REPLACE(TRIM(LCASE(p.post_title)), ' ', '-'), '''', ''), '’', ''), '.', ''),'-&ndash;',1),'---',1) as titulo,
    max( CASE WHEN pm2.meta_key = 'consola' and  p.post_parent = pm2.post_id THEN pm2.meta_value END ) as consola,
    max( CASE WHEN pm.meta_key = 'attribute_pa_slot' and p.ID = pm.post_id THEN pm.meta_value END ) as slot,
	max( CASE WHEN pm.meta_key = '_regular_price' and p.ID = pm.post_id THEN pm.meta_value END ) as reg_price,
    max( CASE WHEN pm.meta_key = '_sale_price' and p.ID = pm.post_id THEN pm.meta_value END ) as sale_price,
	max( CASE WHEN pm2.meta_key = 'idioma' and p.post_parent = pm2.post_id THEN pm2.meta_value END ) as idioma,
    max( CASE WHEN pm2.meta_key = 'peso' and p.post_parent = pm2.post_id THEN pm2.meta_value END ) as peso,
	max( CASE WHEN pm2.meta_key = 'lanzamiento' and p.post_parent = pm2.post_id THEN pm2.meta_value END ) as lanzamiento,
	max( CASE WHEN pm2.meta_key = 'jugadores' and p.post_parent = pm2.post_id THEN pm2.meta_value END ) as jugadores,
    post_status
from
    cbgw_posts as p
LEFT JOIN
    cbgw_postmeta as pm
ON
   p.ID = pm.post_id
LEFT JOIN
	cbgw_postmeta as pm2
ON
	p.post_parent = pm2.post_id
where
    post_type = 'product_variation' and
    post_status = 'publish'
GROUP BY
	p.ID
ORDER BY consola ASC, titulo ASC, slot ASC) as listado
WHERE reg_price != ''";
$query_limit_rsCXP = sprintf("%s LIMIT %d, %d", $query_rsCXP, $startRow_rsCXP, $maxRows_rsCXP);
$rsCXP = mysql_query($query_limit_rsCXP, 
$Conexion) or die(mysql_error());
$row_rsCXP = mysql_fetch_assoc($rsCXP);
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
    <title><?php $titulo = 'Listado para Publicar en ML'; echo $titulo; ?></title>
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
	<table class="table table-striped table-responsive" border="0" cellpadding="0" cellspacing="5">
      <tr>
        <th width="50">Cover</th>
        <th>Titulo</th>
        <th>Slot</th>
        <th title="Precio Regular">Regular</th>
        <th title="Precio Oferta">Oferta</th>
        <th>Publicacion de ML</th>
      </tr>
      <?php $i = 0; do { ?>
      <tr>
      <?php if ($row_rsCXP['slot'] === "primario"):$color='primary'; else: $color='normal';endif; ?>
        <td><img class="img-rounded" width="50" id="image-swap" src="/img/productos/<?php echo $row_rsCXP['consola']."/".$row_rsCXP['titulo'].".jpg";?>"alt="" /></td>
        <td title="<?php echo $row_rsCXP['titulo']; ?> (<?php echo $row_rsCXP['consola']; ?>)"><?php echo str_replace("-", " ", $row_rsCXP['titulo']); ?> <?php echo $row_rsCXP['consola']; if (strpos($row_rsCXP['slot'], 'primario') !== false): echo ' 1ro'; endif; echo ' DixGamer'; ?></td>
        <td><?php echo '<span class="label label-' . $color . '">' . $row_rsCXP['slot'] . '</span>'; ?></td>
        <td><?php echo $row_rsCXP['reg_price']; ?></td>
        <td><?php echo $row_rsCXP['sale_price']; ?></td>
        <td>
        <div style="opacity: 0;position: absolute;pointer-events: none;z-index: -1;" id="resultado-<?php echo $i;?>">
        <p><?php echo strtoupper(str_replace('-', ' ', $row_rsCXP['titulo'])); ?> (<?php echo strtoupper($row_rsCXP['consola']); ?>)</p>
        <?php if (strpos($row_rsCXP['titulo'], 'gift-card-') !== false) {
		echo '<p>Código de PSN para canjear en perfil de U.S.A., si no tenes un perfil podés crearlo desde la consola de forma fácil y gratis, te enviamos video tutorial explicativo muy sencillo.</p>
		<p>Todo el contenido que compras podes usar desde cualquiera de tus cuentas (Perfiles) de tu consola.<br />Con éste código/tarjeta podés comprar juegos, contenidos extras, DLC, y mucho más de PlayStation Store.</p>';
		} 
		elseif ((strpos($row_rsCXP['titulo'], 'plus') !== false) && (strpos($row_rsCXP['slot'], 'secundario') !== false)) {
		echo '<p>Slot Secundario de PS PLUS para jugar online a tus juegos de PS4 (físicos y primarios) y obtener los beneficios de mejores descuentos en la tienda de PlayStation y también los 2 juegos gratis cada mes.</p>
		<p>Te enviamos video tutorial explicativo muy sencillo.</p>';
		}
		elseif (strpos($row_rsCXP['titulo'], 'plus') !== false) {
		echo '<p>Código de PS PLUS para jugar online a tus juegos de PS4 y obtener los beneficios de mejores descuentos en la tienda de PlayStation y también los 2 juegos gratis cada mes.</p>
		<p>Vas a poder jugar online desde cualquier perfil (cuenta) de tu consola, te enviamos video tutorial explicativo muy sencillo.</p>
		<p>Sirve para cuentas de USA, Argentina, España, México, etc...</p>
		<p>Disponible para PS4, PS3 Y PS VITA.</p>';
		}
		else {
			
			// SI ES UN JUEGO CON PRE VENTA VOY A ACLARARLO EN EL CONTENIDO
			$external = $row_rsCXP['lanzamiento'];
            if(!(empty($external))) : 
            // HOWEVER WE CAN INJECT THE FORMATTING WHEN WE DECODE THE DATE
            $format = "d-m-Y";
            $dateobj = DateTime::createFromFormat($format, $external);
            $iso_datetime = $dateobj->format(Datetime::ATOM);
		  	endif;

		echo '<p>DETALLES DEL PRODUCTO<br />- Idioma: ' . $row_rsCXP['idioma'] . '<br />- Peso: ' . $row_rsCXP['peso'] . ' GB'; 
		
		if($row_rsCXP['jugadores'] != NULL): echo '<br />- Jugadores: ' . $row_rsCXP['jugadores']; endif; 
		if(!(empty($external)) && $iso_datetime > date("c")): echo  '<br />Se entrega el ' . $external; endif;
			
		echo '<br />- Trofeos: SI<br />- Online: SI'; 
			if (strpos($row_rsCXP['slot'], 'primario') !== false): echo '<br />- Pri: jugas con tu usuario de siempre.'; elseif (strpos($row_rsCXP['slot'], 'secundario') !== false): echo '<br />- Secu: jugas con ID nuevo. Requiere conexión a internet permanente.'; endif;
		echo '</p><p>PRODUCTO ORIGINAL<br />Incluye todas las funciones y modos de juego.</p>'; 
		}
		
		?>
        <p>BENEFICIO DE COMPRAR CON NOSOTROS<br />1) Enviamos al instante en menos de 5 minutos.<br />2) Asistencia post-venta 24hs por FB, WTS y Mail.<br />3) Tu dinero está protegido, te acompañamos en toda la compra.</p>
        <p>IMPORTANTE<br />Seleccionar "Retiro en domicilio del vendedor" o "Acuerdo con el vendedor" para evitar pagar gastos de envío.</p><p>NO vendemos a personas anónimas ni con identidad falsa. (!)</p>
        </div>
			
		<button class="button" id="copy-button" data-clipboard-target="#resultado-<?php echo $i;?>">Copy</button>
        </td>
       </tr>   
      <?php ++$i; } while ( $row_rsCXP = mysql_fetch_assoc($rsCXP)); ?>     
    </table>
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
		(function(){
			new Clipboard('#copy-button');
		})();
	</script>
<!-- InstanceEndEditable -->
  </body>
  
<!-- InstanceEnd --></html>
<?php
mysql_free_result($rsUsuarios);

mysql_free_result($rsCXP);
?>

