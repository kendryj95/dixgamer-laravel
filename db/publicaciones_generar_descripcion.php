<?php require_once('../Connections/Conexion.php'); ?>
<?php
$MM_authorizedUsers = "Adm,Vendedor";
$MM_donotCheckaccess = "false";
require_once('_autentificacion.php');
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
    REPLACE(REPLACE(REPLACE(REPLACE(TRIM(LCASE(p.post_title)), ' ', '-'), '''', ''), '’', ''), '.', '') AS titulo,
    max( CASE WHEN pm.meta_key = 'consola' and  p.ID = pm.post_id THEN pm.meta_value END ) as consola,
    '' as slot,
	max( CASE WHEN pm.meta_key = 'idioma' and p.ID = pm.post_id THEN pm.meta_value END ) as idioma,
    max( CASE WHEN pm.meta_key = 'peso' and p.ID = pm.post_id THEN pm.meta_value END ) as peso,
	max( CASE WHEN pm.meta_key = 'lanzamiento' and p.ID = pm.post_id THEN pm.meta_value END ) as lanzamiento,
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
SELECT post_parent as ID, titulo, consola, GROUP_CONCAT(slot) as slot, idioma, peso, lanzamiento, post_status FROM (select
    p.ID,
    p.post_parent,
    SUBSTRING_INDEX(SUBSTRING_INDEX(REPLACE(REPLACE(REPLACE(REPLACE(TRIM(LCASE(p.post_title)), ' ', '-'), '''', ''), '’', ''), '.', ''),'-&ndash;',1),'---',1) as titulo,
    max( CASE WHEN pm2.meta_key = 'consola' and  p.post_parent = pm2.post_id THEN pm2.meta_value END ) as consola,
    max( CASE WHEN pm.meta_key = 'attribute_pa_slot' and p.ID = pm.post_id THEN pm.meta_value END ) as slot,
	max( CASE WHEN pm2.meta_key = 'idioma' and p.post_parent = pm2.post_id THEN pm2.meta_value END ) as idioma,
    max( CASE WHEN pm2.meta_key = 'peso' and p.post_parent = pm2.post_id THEN pm2.meta_value END ) as peso,
	max( CASE WHEN pm2.meta_key = 'lanzamiento' and p.post_parent = pm2.post_id THEN pm2.meta_value END ) as lanzamiento,
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
ORDER BY consola ASC, titulo ASC, slot ASC) as conslot
GROUP BY post_parent) as listado";
$query_limit_rsCXP = sprintf("%s LIMIT %d, %d", $query_rsCXP, $startRow_rsCXP, $maxRows_rsCXP);
$rsCXP = mysql_query($query_limit_rsCXP, $Conexion) or die(mysql_error());
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
    <title><?php $titulo = 'Listado para Generar Descripcion en ML'; echo $titulo; ?></title>
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
	<!-- Automatizador de stock en website -->
	<h5>incluyo archivo generador que agrega los videos a WooSync desde WC para exportar a ML</h5>
    <?php require_once('productos_agregar_video_woosync.php'); ?>	
		<?php require_once('productos_actualizar_video_woosync.php'); ?>	
	<table class="table table-striped table-responsive" border="0" cellpadding="0" cellspacing="5">
      <tr>
        <th width="50">Cover</th>
        <th>Titulo</th>
        <th>Publicacion de ML</th>
      </tr>
      <?php $i = 0; do { ?>
      <?php $ID = $row_rsCXP['ID'];?>
      <tr>
        <td><img class="img-rounded" width="50" id="image-swap" src="/img/productos/<?php echo $row_rsCXP['consola']."/".$row_rsCXP['titulo'].".jpg";?>"alt="" /></td>
        <td title="<?php echo $row_rsCXP['titulo']; ?> (<?php echo $row_rsCXP['consola']; ?>)"><?php echo str_replace("-", " ", $row_rsCXP['titulo']); ?> <?php echo $row_rsCXP['consola']; if (strpos($row_rsCXP['slot'], 'primario') !== false): echo ' 1ro'; endif; echo ' DixGamer'; ?></td>
        <td>
        <div style="opacity: 0;position: absolute;pointer-events: none;z-index: -1;" id="resultado-<?php echo $i;?>">
        <?php $plain_text = strtoupper(str_replace('-', ' ', $row_rsCXP['titulo'])) . " " . strtoupper($row_rsCXP['consola']) . " - Digital (no físico)\r\n\r\n"; ?>
		<?php $plain_text = "Tenemos atención las 24 horas, te entregamos el producto al instante y no vas a tener que esperar para recibirlo.\r\nComprá con confianza y si tenés alguna consulta preguntanos, estamos para asesorarte.\r\n\r\n"; ?>
        <?php if (strpos($row_rsCXP['titulo'], 'nintendo') !== false) {
		$plain_text .= "DETALLES DEL PRODUCTO\r\n-Código de Nintendo eShop para canjear en perfil de EEUU, si no tenes un perfil podés crearlo desde la consola (es fácil y gratis), te enviamos tutorial explicativo muy sencillo.\r\n-Sirve para comprar juegos o contenidos extra. Compatible con Wii U, Nintendo 3DS y Nintendo Switch.\r\n-Con éste código/tarjeta podés comprar juegos, contenidos extras, DLC y mucho más de Nintendo eShop.\r\n\r\n";
		} 
		elseif (strpos($row_rsCXP['titulo'], 'xbox-live-cash') !== false) {
		$plain_text .= "DETALLES DEL PRODUCTO\r\n-Código de Xbox para canjear en tu cuenta de USA. Crear una cuenta de USA es fácil y gratis, si todavía no lo hiciste te enviamos un tutorial paso a paso para crearla.\r\n-En XBOX 360 su cuenta debe estar configurada en USA, en XBOX ONE lo puede cambiar desde la configuración de region de la consola y una vez reiniciada podrá canjear el código. No se recomienda volver a cambiar la región una vez realizado este paso.\r\n\r\n";
		} 
		elseif (strpos($row_rsCXP['titulo'], 'xbox-live-gold') !== false) {
		$plain_text .= "DETALLES DEL PRODUCTO\r\n-Código Digital para canjear en tu cuenta. Funciona para cualquier región/país, inclusive Argentina, España, EEUU, etc. Compatible con Xbox 360 y Xbox One.\r\n-Códigos originales y únicos que te permiten jugar en linea, descargar juegos gratis todos los meses, acceder a ofertas semanales y obtener beneficios exclusivos.\r\n\r\n";
		} 
		
		elseif (strpos($row_rsCXP['titulo'], 'points-fifa-18') !== false) {
		$plain_text .= "DETALLES DEL PRODUCTO\r\n-Código Digital para canjear en tu cuenta. Funciona para región/país Argentina o EEUU. Exclusivo para juego FIFA 18 DIGITAL, no funciona para el juego físico.\r\n-Códigos originales y únicos que te permiten acumular Points en tu cuenta y canjearlos para comprar Packs, Jugadores, Camisetas, Indumentaria y mas.\r\n\r\n";
		} 
						
		elseif (strpos($row_rsCXP['titulo'], 'fortnite-ps4') !== false) {
		$plain_text .= "DETALLES DEL PRODUCTO\r\n\r\n*** ATENCIÓN: NO FUNCIONA PARA CUENTA ARGENTINA ***\r\n\r\nCódigo Digital para canjear en tu cuenta de EEUU.\r\nFunciona para cuentas con región/país Estados Unidos únicamente. Crear una cuenta es gratis y fácil, nosotros te ayudamos.\r\n\r\n*** ATENCIÓN: NO FUNCIONA PARA CUENTA ARGENTINA ***\r\n\r\n";
		}
		elseif (strpos($row_rsCXP['titulo'], 'fortnite-pc') !== false) {
		$plain_text .= "DETALLES DEL PRODUCTO\r\n\r\n*** ATENCIÓN: REQUISITOS IMPORTANTES ***\r\n\r\nPara completar el proceso vamos a necesitar tu usuario y contraseña temporalmente. Realizamos la compra del paquete o de cualquier contenido directamente desde tu cuenta.\r\n\r\n*** ATENCIÓN: REQUISITOS IMPORTANTES ***\r\n\r\n";
		} 
		elseif (strpos($row_rsCXP['titulo'], 'facebook') !== false) {
		$plain_text .= "DETALLES DEL PRODUCTO\r\n-Código de Facebook Credits para canjear en tu cuenta. Esta tarjeta es para tus juegos favoritos como Candy Crush Saga, FarmVille 2, Doubledown Casino o Farm Heroes Saga, etc. Canjear el código es muy fácil, te explicamos cómo hacerlo al comprar.\r\n-Esta tarjeta sirve para Juegos y Aplicaciones de Facebook. NO sirve para pagar publicidad de Facebook Ads.\r\n\r\n";
		} 
		elseif (strpos($row_rsCXP['titulo'], 'gift-card-') !== false) {
		$plain_text .= "DETALLES DEL PRODUCTO\r\n-Código de PSN para canjear en perfil de EEUU, si no tenes un perfil podés crearlo desde la consola (es fácil y gratis), te enviamos video tutorial explicativo muy sencillo.\r\n-Cuando comprás un juego con gift cards podes usarlo desde cualquiera de tus cuentas (perfiles).\r\n-Con éste código/tarjeta podés comprar juegos, contenidos extras, DLC, videos, música y mucho más de PlayStation Store.\r\n\r\n";
		} 
		elseif ((strpos($row_rsCXP['titulo'], 'plus-12-meses-slot') !== false)) {
			if (strpos($row_rsCXP['slot'], 'primario') !== false):
				$plain_text .= "DETALLES DEL PRODUCTO\r\nSlot Primario de PS PLUS para jugar online a todos tus juegos de PS4 (físicos, primarios y secundarios) y obtener los beneficios de mejores descuentos en la tienda de PlayStation y también los 2 juegos gratis cada mes.\r\n-Te enviamos video tutorial explicativo muy sencillo.\r\n\r\n";
			else: $plain_text .= "DETALLES DEL PRODUCTO\r\nSlot Secundario de PS PLUS para jugar online a tus juegos de PS4 (físicos y primarios) y obtener los beneficios de mejores descuentos en la tienda de PlayStation y también los 2 juegos gratis cada mes.\r\n-Te enviamos video tutorial explicativo muy sencillo.\r\n\r\n"; endif;
		$plain_text .= "";
		}
		elseif (strpos($row_rsCXP['titulo'], 'plus') !== false) {
		$plain_text .= "DETALLES DEL PRODUCTO\r\n-Disponible para PS4, PS3 Y PS VITA.\r\n-Sirve para cuentas de USA, Argentina, España, México, etc...\r\n-Vas a poder jugar online desde cualquier perfil (cuenta) de tu consola, te enviamos video tutorial explicativo muy sencillo.\r\n-Código de PS PLUS para jugar online a tus juegos de PS4 y obtener otros beneficios cómo importantes descuentos en la tienda de PlayStation y también 2 juegos gratis cada mes, todos los meses.\r\n\r\n";
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
		$idioma = $row_rsCXP['idioma'];
		$peso = $row_rsCXP['peso'];
		$plain_text .= "DETALLES DEL PRODUCTO\r\n- Idioma: $idioma\r\n- Peso: $peso GB"; 
		
		if($row_rsCXP['jugadores'] != NULL): $plain_text .= "\r\n- Jugadores: " . $row_rsCXP['jugadores']; endif; 
		if(!(empty($external)) && $iso_datetime > date("c")): $plain_text .=  "\r\nSe entrega el " . $external; endif;
			
		$plain_text .= "\r\n- Trofeos: SI\r\n- Online: SI"; 
		if ($row_rsCXP['consola'] == "ps4"): if (strpos($row_rsCXP['slot'], 'primario') !== false): $plain_text .= "\r\n- Pri: jugas con tu usuario de siempre."; else: $plain_text .= "\r\n- Secu: jugas con perfil nuevo. Requiere conexión a internet."; endif; endif;
		$plain_text .= "\r\n\r\nPRODUCTO ORIGINAL\r\nIncluye todas las funciones y modos de juego.\r\n\r\n"; 
		
		$plain_text .= "REQUISITOS\r\n1) Consola original sin chipear/flashear.\r\n2) Espacio libre para descargar.\r\n3) Conexión a internet.\r\n\r\n";
		};
		
        $plain_text .= "BENEFICIO DE COMPRAR CON NOSOTROS\r\n1) Enviamos al instante en menos de 5 minutos.\r\n2) Asistencia post-venta 24hs por FB, WTS y Mail.\r\n3) Tu dinero está protegido, te acompañamos en toda la compra.\r\n\r\n";
		
		
        $plain_text .= "IMPORTANTE - NO PAGAR ENVIO\r\nEn opción de envío seleccionar ACUERDO CON EL VENDEDOR para evitar pagar gastos de envío.\r\n\r\n(!) NO vendemos a personas anónimas ni con identidad falsa.\r\n(!) Si compra con tarjeta solicitaremos verificación de identidad.\r\n\r\n";?>
	  
        </div>
			
		<?php 
		echo $plain_text;
		$updateSQL = sprintf("UPDATE cbgw_posts SET post_content='%s' WHERE ID=%s",$plain_text,$ID);
		mysql_select_db($database_Conexion, $Conexion);
		$Result2 = mysql_query($updateSQL, $Conexion) or die(mysql_error());?>
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

