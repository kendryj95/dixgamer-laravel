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
$MM_authorizedUsers = "Adm,Vendedor";
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
function GetSQLValueString($theValue, $theType, $theDefinedValue = "", $theNotDefinedValue = "") 
{
  $theValue = (!get_magic_quotes_gpc()) ? addslashes($theValue) : $theValue;

  switch ($theType) {
    case "text":
      $theValue = ($theValue != "") ? "'" . $theValue . "'" : "NULL";
      break;    
    case "long":
    case "int":
      $theValue = ($theValue != "") ? intval($theValue) : "NULL";
      break;
    case "double":
      $theValue = ($theValue != "") ? "'" . doubleval($theValue) . "'" : "NULL";
      break;
    case "date":
      $theValue = ($theValue != "") ? "'" . $theValue . "'" : "NULL";
      break;
    case "defined":
      $theValue = ($theValue != "") ? $theDefinedValue : $theNotDefinedValue;
      break;
  }
  return $theValue;
}

$editFormAction = $_SERVER['PHP_SELF'];
if (isset($_SERVER['QUERY_STRING'])) {
  $editFormAction .= "?" . htmlentities($_SERVER['QUERY_STRING']);
}

if ((isset($_POST["MM_insert"])) && ($_POST["MM_insert"] == "form1")) {
  $insertSQL = sprintf("INSERT INTO publicaciones (titulo, consola, slot, url) VALUES (%s, %s, %s, %s)", 
                       GetSQLValueString($_POST['titulo'], "text"),
                       GetSQLValueString($_POST['consola'], "text"),
					   GetSQLValueString($_POST['slot'], "text"),
                       GetSQLValueString($_POST['url'], "text"));

  mysql_select_db($database_Conexion, $Conexion);
  $Result1 = mysql_query($insertSQL, $Conexion) or die(mysql_error());

  $insertGoTo = "publicaciones_insertar.php";
  if (isset($_SERVER['QUERY_STRING'])) {
    $insertGoTo .= (strpos($insertGoTo, '?')) ? "&" : "?";
    $insertGoTo .= $_SERVER['QUERY_STRING'];
  }
  header(sprintf("Location: %s", $insertGoTo));
}

$nombresito = $_SESSION['MM_Username'];
mysql_select_db($database_Conexion, $Conexion);
$query_rsUsuarios = sprintf("SELECT * FROM usuarios WHERE Nombre = '$nombresito' ORDER BY Nombre ASC",$nombresito);
$rsUsuarios = mysql_query($query_rsUsuarios, $Conexion) or die(mysql_error());
$row_rsUsuarios = mysql_fetch_assoc($rsUsuarios);
$totalRows_rsUsuarios = mysql_num_rows($rsUsuarios);

mysql_select_db($database_Conexion, $Conexion);
$query_rsTitulos = sprintf("SELECT web.*, stk.*
FROM
(SELECT REPLACE(REPLACE(REPLACE(REPLACE(TRIM(LCASE(post_title)), ' ', '-'), '''', ''), '’', ''), '.', '') AS producto FROM cbgw_posts WHERE post_type = 'product' and post_status = 'publish' group by producto) as web
LEFT JOIN
(SELECT titulo, COUNT(*) AS Q_Stk FROM stock GROUP BY titulo) AS stk
ON producto = titulo 
ORDER BY Q_Stk DESC", $colname_rsTitulos);
$rsTitulos = mysql_query($query_rsTitulos, $Conexion) or die(mysql_error());
$row_rsTitulos = mysql_fetch_assoc($rsTitulos);
$totalRows_rsTitulos = mysql_num_rows($rsTitulos);

mysql_select_db($database_Conexion, $Conexion);
$query_rsPublicaciones = sprintf("SELECT publicaciones.*, publicaciones_ads.ID AS ID_ads, publicaciones_ads.public_id, publicaciones_ads.concepto FROM publicaciones LEFT JOIN publicaciones_ads ON publicaciones.ID = publicaciones_ads.public_id ORDER BY consola,titulo,slot");
$rsPublicaciones = mysql_query($query_rsPublicaciones, $Conexion) or die(mysql_error());
$row_rsPublicaciones = mysql_fetch_assoc($rsPublicaciones);
$totalRows_rsPublicaciones = mysql_num_rows($rsPublicaciones);

$colname_rsURL = "-1";
$colname_rsProducto = "-1";
$colname_rsTit = "-1";
$colname_rsCon = "-1";
$colname_rsSlo = "-1";
if (isset($_GET['l'],$_GET['t'],$_GET['t'],$_GET['c'],$_GET['s'])) {
$colname_rsURL = (get_magic_quotes_gpc()) ? $_GET['l'] : addslashes($_GET['l']);
  $colname_rsProducto = (get_magic_quotes_gpc()) ? $_GET['p'] : addslashes($_GET['p']);
  $colname_rsTit = (get_magic_quotes_gpc()) ? $_GET['t'] : addslashes($_GET['t']);
  $colname_rsCon = (get_magic_quotes_gpc()) ? $_GET['c'] : addslashes($_GET['c']);
  $colname_rsSlo = (get_magic_quotes_gpc()) ? $_GET['s'] : addslashes($_GET['s']);

mysql_select_db($database_Conexion, $Conexion);
$query_rsClient = sprintf("SELECT * FROM
(SELECT url, group_concat(concat(titulo, ':', consola, ':', slot)) as producto FROM publicaciones GROUP BY ID) as publicacion
WHERE url = '%s' OR producto = '%s'", $colname_rsURL, $colname_rsProducto);
$rsClient = mysql_query($query_rsClient, $Conexion) or die(mysql_error());
$row_rsClient = mysql_fetch_assoc($rsClient);
$totalRows_rsClient = mysql_num_rows($rsClient);

if ($row_rsClient){
$resulta2 = '<div class="alert alert-warning alert-dismissable"><button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button><h4>Ya existe!</h4><p>'.$row_rsClient['producto'].' <a target="_blank" href="'.$row_rsClient['url'].'" title="publicacion">link a publicacion</a></p></div>';
} else
{
$insertSQL = sprintf("INSERT INTO publicaciones (titulo,consola,slot,url) VALUES ('$colname_rsTit', '$colname_rsCon', '$colname_rsSlo', '$colname_rsURL')");
mysql_select_db($database_Conexion, $Conexion);
$Result1 = mysql_query($insertSQL, $Conexion) or die(mysql_error());
$insertGoTo = "publicaciones_insertar.php";
$insertGoTo .= "?agregar_publicacion=listo";
header(sprintf("Location: %s", $insertGoTo));
}
}

$colname_rsPosicion = "-1";
$colname_rsP_ID = "-1";
if (isset($_GET['posicion'],$_GET['p_id'])) {
$colname_rsPosicion = (get_magic_quotes_gpc()) ? $_GET['posicion'] : addslashes($_GET['posicion']);
 $colname_rsP_ID = (get_magic_quotes_gpc()) ? $_GET['p_id'] : addslashes($_GET['p_id']);

mysql_select_db($database_Conexion, $Conexion);
$query_rsADS = sprintf("SELECT * FROM publicaciones_ads
WHERE concepto = '$colname_rsPosicion'");
$rsADS = mysql_query($query_rsADS, $Conexion) or die(mysql_error());
$row_rsADS = mysql_fetch_assoc($rsADS);
$totalRows_rsADS = mysql_num_rows($rsADS);

if ($row_rsADS){
  $deleteSQL = sprintf("UPDATE publicaciones_ads SET public_id = '$colname_rsP_ID' WHERE concepto = '$colname_rsPosicion'");
  mysql_select_db($database_Conexion, $Conexion);
  $Result2 = mysql_query($deleteSQL, $Conexion) or die(mysql_error());
  $insertGoTo = "publicaciones_insertar.php";
  $insertGoTo .= "?cambio_ads=listo";
  header(sprintf("Location: %s", $insertGoTo));
} else
{
$resulta2 = '<div class="alert alert-warning alert-dismissable"><button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button><h4>Hubo algun error, revisar!</h4><p>ID: '.$colname_rsP_ID.' concepto: '.$colname_rsPosicion.'</p></div>';
}
}


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
<link rel="stylesheet" href="//code.jquery.com/ui/1.11.4/themes/smoothness/jquery-ui.css">
<script src="//code.jquery.com/jquery-1.10.2.js"></script>
<script src="//code.jquery.com/ui/1.11.4/jquery-ui.js"></script>
<script src="http://ajax.aspnetcdn.com/ajax/jquery.validate/1.11.0/jquery.validate.min.js"></script>
<!-- FORM VALIDATION -->
<script type="text/javascript">
$(document).ready(function() {
    var x_timer;    
    $("#link").keyup(function (e){
        clearTimeout(x_timer);
        var user_name = $(this).val();
        x_timer = setTimeout(function(){
            check_link_ajax(user_name);
        }, 1000);
    });

function check_link_ajax(link){
	document.getElementById("user-result").className = "fa fa-spinner fa-pulse fa-fw";
    $.post('publicaciones_insertar_control_link.php', {'link':link}, function(data) {
	document.getElementById("user-result").className = (data);
	var test = document.getElementById("user-result");
	var testClass = test.className;
	switch(testClass){
    case "fa fa-ban": document.getElementById("user-result-div").className = "input-group form-group has-error"; break;
	case "fa fa-check": document.getElementById("user-result-div").className = "input-group form-group has-success"; break;
}
  	});
}
});
</script>

    <title><?php $titulo = 'Insertar publicaciones'; echo $titulo; ?></title>
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
    <div class="col-sm-3" style="text-align:right;">
    <span id="alerta" class="label label-danger"></span>
	<img class="img-rounded pull-right" width="100" id="image-swap" src="" alt="" />
    </div>
    <div class="col-sm-6" style="text-align: center; ">
    <form method="post" name="form1" action="<?php echo $editFormAction; ?>">
            <div id="user-result-div" class="input-group form-group">
              <span class="input-group-addon"><i class="fa fa-link fa-fw"></i></span>
              <input class="form-control" type="text" name="url" id="link" autocomplete="off" spellcheck="false" placeholder="Url" autofocus>
              <span class="input-group-addon"><i id="user-result" class="fa fa-pencil" aria-hidden="true"></i></span>
            </div>
            
            <div class="input-group form-group">
            <span class="input-group-addon"><a title="agregar" href="stock_insertar_agregar_titulo.php"><i class="fa fa-plus fa-fw"></i></a></span>
              <select id="titulo-selec" name="titulo" class="selectpicker form-control" data-live-search="true" data-size="5">
			  <?php do {  ?>
            	<option value="<?php echo $row_rsTitulos['producto']?>"><?php echo $row_rsTitulos['producto']?></option>
              <?php } while ($row_rsTitulos = mysql_fetch_assoc($rsTitulos));
				$rows = mysql_num_rows($rsTitulos);
							  if($rows > 0) {
								  mysql_data_seek($rsTitulos, 0);
								  $row_rsTitulos = mysql_fetch_assoc($rsTitulos);
								  }
								?>
				</select>
              <!--- <input type="text" id="titulo-selec" name="titulo" class="titulo tt-query form-control" autocomplete="off" spellcheck="false" placeholder="Buscar título"> -->
            </div>
            
  			<div class="input-group form-group">
            <span class="input-group-addon"><i class="fa fa-gamepad fa-fw"></i></span>
            <select id="consola" name="consola" class="selectpicker form-control">
            	<option selected value="ps4" data-content="<span class='label label-primary'>ps4</span>">ps4</option>
                <option value="ps3" data-content="<span class='label label-warning' style='background-color:#000;'>ps3</span>">ps3</option>
                <option value="ps" data-content="<span class='label label-danger'>psn</span>">psn</option>
                <option value="steam" data-content="<span class='label label-default'>steam</span>">steam</option>
                <option value="psvita" data-content="<span class='label label-info'>psvita</span>">psvita</option>
            </select>
            </div>
            
            <div class="input-group form-group">
            <span class="input-group-addon"><i class="fa fa-gamepad fa-fw"></i></span>
            <select name="slot" id="slot" class="selectpicker form-control">
            	<option selected="selected" value="Primario" data-content="<span class='label label-primary'>Primario</span>">Primario</option>
                <option value="Secundario" data-content="<span class='label label-normal'>Secundario</span>">Secundario</option>
                <option value="No" data-content="<span class='label label-danger'>No</span>">No</option>
            </select>
            </div>
            
           	<a href="" id="vinculo" class="btn btn-primary">agregar publicacion</a>
    </form>
    </div>
    <div class="col-sm-3">
        <?php echo $resulta2; ?>
    </div>
    </div>
    <hr>
    <div class="row">
        <blockquote>
        <p>Recordar</p>
          <small>Si cambio el título de la publicación en ML tengo que actualizar el link en mi DB. Aunque si el código númerico y el JM final figuran no hay problema, redirige solo.</small>
        </blockquote>
    </div>
    <hr>
    <div class="table-responsive table-striped">
    <table border="0" align="center" cellpadding="0" cellspacing="5" class="table table-striped">
    <thead>
              <tr>
              	<th>Cover</th>
                <th>#</th>
                <th>Titulo</th>
                <th>Consola</th>
                <th>Slot</th>
                <th>url</th>
                <th width="100" align="right">Ads</th>
              </tr>
            </thead>
		  <tbody>
          <?php do { ?>
          <!-- siempre y cuando la imagen para el titulo y consola exista en el servidor creo la fila (eso me ayuda en el caso que la tabla tenga un error logicamente si no existe ese COVER mejor ni lo muestro) -->
  		<?php if(getimagesize('https://dixgamer.com/img/productos/'.$row_rsPublicaciones['consola'].'/'.$row_rsPublicaciones['titulo'].'.jpg') !== false):?>
          <tr>
          	<td><img class="img-rounded" width="50" id="image-swap" src="/img/productos/<?php echo $row_rsPublicaciones['consola']."/".$row_rsPublicaciones['titulo'].".jpg";?>" alt="" /></td>
          	<td><?php echo $row_rsPublicaciones['ID']; ?> <a class="btn btn-xs btn-default" type="button" title="Modificar publicacion" href="publicaciones_modificar.php?id=<?php echo $row_rsPublicaciones['ID']; ?>"><i aria-hidden="true" class="fa fa-pencil"></i></a></td>
            <td><?php echo $row_rsPublicaciones['titulo']; ?></td>
            <td><span class="label label-default <?php echo $row_rsPublicaciones['consola']; ?>"><?php echo $row_rsPublicaciones['consola']; ?></span></td>
            <td>
			<?php if($row_rsPublicaciones['slot'] == 'Primario'): $span = 'primary'; elseif($row_rsPublicaciones['slot'] == 'Secundario'): $span = 'normal'; else: $span = 'danger'; endif;?>
            <span class="label label-<?php echo $span;?>"><?php echo $row_rsPublicaciones['slot'];?></span>
            </td>
            <td><?php echo $row_rsPublicaciones['url']; ?> <a href="<?php echo $row_rsPublicaciones['url']; ?>" target="_blank">link a publicacion</a></td>
            <td align="right">
            <span class="btn-group pull-right">
                <a class="btn btn-xs btn-default" type="button" href="publicaciones_insertar.php?posicion=<?php echo $row_rsPublicaciones['consola']; ?>-1&p_id=<?php echo $row_rsPublicaciones['ID']; ?>"><i aria-hidden="true" class="fa fa-star<?php if($row_rsPublicaciones['concepto'] !== ($row_rsPublicaciones['consola'].'-1')):?>-o<?php endif;?>"></i></a>
                <a class="btn btn-xs btn-default" type="button" href="publicaciones_insertar.php?posicion=<?php echo $row_rsPublicaciones['consola']; ?>-2&p_id=<?php echo $row_rsPublicaciones['ID']; ?>"><i aria-hidden="true" class="fa fa-star<?php if($row_rsPublicaciones['concepto'] !== ($row_rsPublicaciones['consola'].'-2')):?>-o<?php endif;?>"></i></a>
                <a class="btn btn-xs btn-default" type="button" href="publicaciones_insertar.php?posicion=<?php echo $row_rsPublicaciones['consola']; ?>-3&p_id=<?php echo $row_rsPublicaciones['ID']; ?>"><i aria-hidden="true" class="fa fa-star<?php if($row_rsPublicaciones['concepto'] !== ($row_rsPublicaciones['consola'].'-3')):?>-o<?php endif;?>"></i></a>
              </span>
            </td>
            
          </tr>
          <?php endif; ?>
        <?php } while ($row_rsPublicaciones = mysql_fetch_assoc($rsPublicaciones)); ?>
        </tbody>
        </table>
        
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
     <script type="text/javascript">
	 $(document).ready(function() {
            $("form").change(function() {
				var lin = document.getElementById('link').value;
                var tit = document.getElementById('titulo-selec').value;
				var con = document.getElementById('consola').value;
				var slo = document.getElementById('slot').value;
				$("#vinculo").attr("href", (lin !== "" &&  + tit !== "" &&  + con !== "" &&  + slo !== "") ? "publicaciones_insertar.php?l=" + lin + "&p=" + tit + ":" + con + ":" + slo + "&t=" + tit + "&c=" + con + "&s=" + slo : "");
				});
            });
	 </script>
     <script type="text/javascript">
	 $(document).ready(function() {
            $("form").change(function() {
                var titulo = document.getElementById('titulo-selec').value;
				var consola = document.getElementById('consola').value;
				$("#image-swap").attr("src", (titulo !== "" &&  + consola !== "") ? "/img/productos/" + consola + "/" + titulo + ".jpg" : "");
				$('#image-swap').load(function() {
                document.getElementById("alerta").innerHTML = "";    
                });
				$('#image-swap').error(function() {
  				document.getElementById("alerta").innerHTML = "no se encuentra";
				});
            });
        });
	</script>
    
    <!-- Latest compiled and minified CSS -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-select/1.11.2/css/bootstrap-select.min.css">
    <!-- Latest compiled and minified JavaScript -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-select/1.11.2/js/bootstrap-select.min.js"></script>
<!-- InstanceEndEditable -->
  </body>
  
<!-- InstanceEnd --></html>
<?php
mysql_free_result($rsUsuarios);

mysql_free_result($rsClient);

mysql_free_result($rsADS);
?>