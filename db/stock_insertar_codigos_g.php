<?php require_once('../Connections/Conexion.php'); ?>
<?php
$MM_authorizedUsers = "Adm,Vendedor";
$MM_donotCheckaccess = "false";
require_once('_autentificacion.php');
?>
<?php 

$editFormAction = $_SERVER['PHP_SELF'];
if (isset($_SERVER['QUERY_STRING'])) {
  $editFormAction .= "?" . htmlentities($_SERVER['QUERY_STRING']);
}

$TZ = "SET SESSION time_zone = '-3:00'";
mysql_select_db($database_Conexion, $Conexion);
$ResultTZ = mysql_query($TZ, $Conexion) or die(mysql_error());

$vendedor = $_SESSION['MM_Username'];
$vendedor .= "-GC";

if ((isset($_POST["MM_insert"])) && ($_POST["MM_insert"] == "form1")) {
	
	$sql = "INSERT INTO stock (titulo, consola, medio_pago, costo_usd, costo, code, code_prov, n_order, Notas, usuario) VALUES";
	
	$titulo = GetSQLValueString($_POST['clientes_id1'], "text");
	$consola = GetSQLValueString($_POST['clientes_id2'], "text");
	$medio_pago = GetSQLValueString($_POST['medio_pago'], "text");
	$costo_usd = GetSQLValueString($_POST['costo_usd'], "double");
	$costo = GetSQLValueString($_POST['costo'], "double");
	$code_prov = GetSQLValueString($_POST['code_prov'], "text");
	$n_order = GetSQLValueString($_POST['n_order'], "text");
	$Notas = GetSQLValueString($_POST['Notas'], "text");
	$usuario = GetSQLValueString($_POST['usuario'], "text");
	
	$code1 = GetSQLValueString($_POST['code1'], "text");
	$code2 = GetSQLValueString($_POST['code2'], "text");
	$code3 = GetSQLValueString($_POST['code3'], "text");
	$code4 = GetSQLValueString($_POST['code4'], "text");
	$code5 = GetSQLValueString($_POST['code5'], "text");
	$code6 = GetSQLValueString($_POST['code6'], "text");
	$code7 = GetSQLValueString($_POST['code7'], "text");
	$code8 = GetSQLValueString($_POST['code8'], "text");
	$code9 = GetSQLValueString($_POST['code9'], "text");
	$code10 = GetSQLValueString($_POST['code10'], "text");
	$code11 = GetSQLValueString($_POST['code11'], "text");
	$code12 = GetSQLValueString($_POST['code12'], "text");
	$code13 = GetSQLValueString($_POST['code13'], "text");
	$code14 = GetSQLValueString($_POST['code14'], "text");
	$code15 = GetSQLValueString($_POST['code15'], "text");
	$code16 = GetSQLValueString($_POST['code16'], "text");
	$code17 = GetSQLValueString($_POST['code17'], "text");
	$code18 = GetSQLValueString($_POST['code18'], "text");
	$code19 = GetSQLValueString($_POST['code19'], "text");
	$code20 = GetSQLValueString($_POST['code20'], "text");
	$code21 = GetSQLValueString($_POST['code21'], "text");
	$code22 = GetSQLValueString($_POST['code22'], "text");
	$code23 = GetSQLValueString($_POST['code23'], "text");
	$code24 = GetSQLValueString($_POST['code24'], "text");
	$code25 = GetSQLValueString($_POST['code25'], "text");
	$code26 = GetSQLValueString($_POST['code26'], "text");
	$code27 = GetSQLValueString($_POST['code27'], "text");
	$code28 = GetSQLValueString($_POST['code28'], "text");
	$code29 = GetSQLValueString($_POST['code29'], "text");
	$code30 = GetSQLValueString($_POST['code30'], "text");
	$code31 = GetSQLValueString($_POST['code31'], "text");
	$code32 = GetSQLValueString($_POST['code32'], "text");
	$code33 = GetSQLValueString($_POST['code33'], "text");
	$code34 = GetSQLValueString($_POST['code34'], "text");
	$code35 = GetSQLValueString($_POST['code35'], "text");
	$code36 = GetSQLValueString($_POST['code36'], "text");
	$code37 = GetSQLValueString($_POST['code37'], "text");
	$code38 = GetSQLValueString($_POST['code38'], "text");
	$code39 = GetSQLValueString($_POST['code39'], "text");
	$code40 = GetSQLValueString($_POST['code40'], "text");
	
	
	if("" !== trim($_POST['code1'])){$sql .= " ($titulo, $consola, $medio_pago, $costo_usd, $costo, $code1, $code_prov, $n_order, $Notas, $usuario) ";}
	if("" !== trim($_POST['code2'])){$sql .= " , ($titulo, $consola, $medio_pago, $costo_usd, $costo, $code2, $code_prov, $n_order, $Notas, $usuario) ";}
	if("" !== trim($_POST['code3'])){$sql .= " , ($titulo, $consola, $medio_pago, $costo_usd, $costo, $code3, $code_prov, $n_order, $Notas, $usuario) ";}
	if("" !== trim($_POST['code4'])){$sql .= " , ($titulo, $consola, $medio_pago, $costo_usd, $costo, $code4, $code_prov, $n_order, $Notas, $usuario) ";}
	if("" !== trim($_POST['code5'])){$sql .= " , ($titulo, $consola, $medio_pago, $costo_usd, $costo, $code5, $code_prov, $n_order, $Notas, $usuario) ";}
	if("" !== trim($_POST['code6'])){$sql .= " , ($titulo, $consola, $medio_pago, $costo_usd, $costo, $code6, $code_prov, $n_order, $Notas, $usuario) ";}
	if("" !== trim($_POST['code7'])){$sql .= " , ($titulo, $consola, $medio_pago, $costo_usd, $costo, $code7, $code_prov, $n_order, $Notas, $usuario) ";}
	if("" !== trim($_POST['code8'])){$sql .= " , ($titulo, $consola, $medio_pago, $costo_usd, $costo, $code8, $code_prov, $n_order, $Notas, $usuario) ";}
	if("" !== trim($_POST['code9'])){$sql .= " , ($titulo, $consola, $medio_pago, $costo_usd, $costo, $code9, $code_prov, $n_order, $Notas, $usuario) ";}
	if("" !== trim($_POST['code10'])){$sql .= " , ($titulo, $consola, $medio_pago, $costo_usd, $costo, $code10, $code_prov, $n_order, $Notas, $usuario) ";}
	if("" !== trim($_POST['code11'])){$sql .= " , ($titulo, $consola, $medio_pago, $costo_usd, $costo, $code11, $code_prov, $n_order, $Notas, $usuario) ";}
	if("" !== trim($_POST['code12'])){$sql .= " , ($titulo, $consola, $medio_pago, $costo_usd, $costo, $code12, $code_prov, $n_order, $Notas, $usuario) ";}
	if("" !== trim($_POST['code13'])){$sql .= " , ($titulo, $consola, $medio_pago, $costo_usd, $costo, $code13, $code_prov, $n_order, $Notas, $usuario) ";}
	if("" !== trim($_POST['code14'])){$sql .= " , ($titulo, $consola, $medio_pago, $costo_usd, $costo, $code14, $code_prov, $n_order, $Notas, $usuario) ";}
	if("" !== trim($_POST['code15'])){$sql .= " , ($titulo, $consola, $medio_pago, $costo_usd, $costo, $code15, $code_prov, $n_order, $Notas, $usuario) ";}
	if("" !== trim($_POST['code16'])){$sql .= " , ($titulo, $consola, $medio_pago, $costo_usd, $costo, $code16, $code_prov, $n_order, $Notas, $usuario) ";}
	if("" !== trim($_POST['code17'])){$sql .= " , ($titulo, $consola, $medio_pago, $costo_usd, $costo, $code17, $code_prov, $n_order, $Notas, $usuario) ";}
	if("" !== trim($_POST['code18'])){$sql .= " , ($titulo, $consola, $medio_pago, $costo_usd, $costo, $code18, $code_prov, $n_order, $Notas, $usuario) ";}
	if("" !== trim($_POST['code19'])){$sql .= " , ($titulo, $consola, $medio_pago, $costo_usd, $costo, $code19, $code_prov, $n_order, $Notas, $usuario) ";}
	if("" !== trim($_POST['code20'])){$sql .= " , ($titulo, $consola, $medio_pago, $costo_usd, $costo, $code20, $code_prov, $n_order, $Notas, $usuario) ";}
	if("" !== trim($_POST['code21'])){$sql .= " , ($titulo, $consola, $medio_pago, $costo_usd, $costo, $code21, $code_prov, $n_order, $Notas, $usuario) ";}
	if("" !== trim($_POST['code22'])){$sql .= " , ($titulo, $consola, $medio_pago, $costo_usd, $costo, $code22, $code_prov, $n_order, $Notas, $usuario) ";}
	if("" !== trim($_POST['code23'])){$sql .= " , ($titulo, $consola, $medio_pago, $costo_usd, $costo, $code23, $code_prov, $n_order, $Notas, $usuario) ";}
	if("" !== trim($_POST['code24'])){$sql .= " , ($titulo, $consola, $medio_pago, $costo_usd, $costo, $code24, $code_prov, $n_order, $Notas, $usuario) ";}
	if("" !== trim($_POST['code25'])){$sql .= " , ($titulo, $consola, $medio_pago, $costo_usd, $costo, $code25, $code_prov, $n_order, $Notas, $usuario) ";}
	if("" !== trim($_POST['code26'])){$sql .= " , ($titulo, $consola, $medio_pago, $costo_usd, $costo, $code26, $code_prov, $n_order, $Notas, $usuario) ";}
	if("" !== trim($_POST['code27'])){$sql .= " , ($titulo, $consola, $medio_pago, $costo_usd, $costo, $code27, $code_prov, $n_order, $Notas, $usuario) ";}
	if("" !== trim($_POST['code28'])){$sql .= " , ($titulo, $consola, $medio_pago, $costo_usd, $costo, $code28, $code_prov, $n_order, $Notas, $usuario) ";}
	if("" !== trim($_POST['code29'])){$sql .= " , ($titulo, $consola, $medio_pago, $costo_usd, $costo, $code29, $code_prov, $n_order, $Notas, $usuario) ";}
	if("" !== trim($_POST['code30'])){$sql .= " , ($titulo, $consola, $medio_pago, $costo_usd, $costo, $code30, $code_prov, $n_order, $Notas, $usuario) ";}
	if("" !== trim($_POST['code31'])){$sql .= " , ($titulo, $consola, $medio_pago, $costo_usd, $costo, $code31, $code_prov, $n_order, $Notas, $usuario) ";}
	if("" !== trim($_POST['code32'])){$sql .= " , ($titulo, $consola, $medio_pago, $costo_usd, $costo, $code32, $code_prov, $n_order, $Notas, $usuario) ";}
	if("" !== trim($_POST['code33'])){$sql .= " , ($titulo, $consola, $medio_pago, $costo_usd, $costo, $code33, $code_prov, $n_order, $Notas, $usuario) ";}
	if("" !== trim($_POST['code34'])){$sql .= " , ($titulo, $consola, $medio_pago, $costo_usd, $costo, $code34, $code_prov, $n_order, $Notas, $usuario) ";}
	if("" !== trim($_POST['code35'])){$sql .= " , ($titulo, $consola, $medio_pago, $costo_usd, $costo, $code35, $code_prov, $n_order, $Notas, $usuario) ";}
	if("" !== trim($_POST['code36'])){$sql .= " , ($titulo, $consola, $medio_pago, $costo_usd, $costo, $code36, $code_prov, $n_order, $Notas, $usuario) ";}
	if("" !== trim($_POST['code37'])){$sql .= " , ($titulo, $consola, $medio_pago, $costo_usd, $costo, $code37, $code_prov, $n_order, $Notas, $usuario) ";}
	if("" !== trim($_POST['code38'])){$sql .= " , ($titulo, $consola, $medio_pago, $costo_usd, $costo, $code38, $code_prov, $n_order, $Notas, $usuario) ";}
	if("" !== trim($_POST['code39'])){$sql .= " , ($titulo, $consola, $medio_pago, $costo_usd, $costo, $code39, $code_prov, $n_order, $Notas, $usuario) ";}
	if("" !== trim($_POST['code40'])){$sql .= " , ($titulo, $consola, $medio_pago, $costo_usd, $costo, $code40, $code_prov, $n_order, $Notas, $usuario) ";}
		
	mysql_select_db($database_Conexion, $Conexion);
  	$Result1 = mysql_query($sql, $Conexion) or die(mysql_error());
	
  $insertGoTo = "stock_insertar_codigos_g.php";
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
$sqlAA = "SELECT CONCAT('< ',titulo,' > (',consola,') [',costo,']') as nombre FROM (SELECT * FROM (select
    p.ID,
    p.post_title as titulo,
    max( CASE WHEN pm.meta_key = 'consola' and  p.ID = pm.post_id THEN pm.meta_value END ) as consola,
	max( CASE WHEN pm.meta_key = 'costo' and  p.ID = pm.post_id THEN pm.meta_value END ) as costo,
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
group by
    p.ID
ORDER BY `consola` DESC, `titulo` ASC) as resultado WHERE consola IN ('amazon','facebook','google-play','ps','xbox','nintendo','fifa-points','steam','fortnite') and titulo != 'plus-12-meses-slot') AS rdo";
$resultAA = mysql_query($sqlAA, $Conexion) or die(mysql_error());
$rowsAA = array();
while($r = mysql_fetch_assoc($resultAA)) {
    $rowsAA[]=$r['nombre'];
  }

$query_rsTitulos = sprintf("SELECT * FROM (select
    p.ID,
    REPLACE(REPLACE(REPLACE(REPLACE(TRIM(LCASE(p.post_title)), ' ', '-'), '''', ''), '’', ''), '.', '') AS titulo,
    max( CASE WHEN pm.meta_key = 'consola' and  p.ID = pm.post_id THEN pm.meta_value END ) as consola,
	max( CASE WHEN pm.meta_key = 'costo' and  p.ID = pm.post_id THEN pm.meta_value END ) as costo,
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
group by
    p.ID
ORDER BY `consola` DESC, `titulo` ASC) as resultado WHERE consola IN ('amazon','facebook','google-play','ps','xbox','nintendo','fifa-points','steam') and titulo != 'plus-12-meses-slot'", $colname_rsTitulos);
$rsTitulos = mysql_query($query_rsTitulos, $Conexion) or die(mysql_error());
$row_rsTitulos = mysql_fetch_assoc($rsTitulos);
$totalRows_rsTitulos = mysql_num_rows($rsTitulos);
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
    <title><?php $titulo = 'Gift Cards G'; echo $titulo; ?></title>
	  <style>
		  .corregircodigo {height: 20px !important; padding: 0px !important; font-size:12px !important;}
	  </style>
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
    
    <form method="post" name="form1" action="<?php echo $editFormAction; ?>">
	<div class="row">
    <div class="col-sm-2" style="text-align:right;">
    <span id="alerta" class="label label-danger"></span>
	<img class="img-rounded pull-right" width="100" id="image-swap" src="" alt="" />
    </div>
    <div class="col-sm-5">
         <input id="clientes" type="text" class="form-control typeahead" autocomplete="off" spellcheck="false" value=""><br /><br />

            <input type="text" id="clientes_id1" name="clientes_id1" value="" hidden="">
			<input type="text" id="clientes_id2" name="clientes_id2" value="" hidden="">
            
            <div class="input-group form-group">
            <span class="input-group-addon"><i class="fa fa-dollar fa-fw"></i></span>
            <select id="medio_pago" name="medio_pago" class="selectpicker form-control">
				<option selected value="BiP" data-content="<span class='label label-primary'>BiP</span>">BiP</option>
				<option value="MC Uala" data-content="<span class='label label-info'>MC U</span>">MC U</option>
                <option value="MC MercadoPago" data-content="<span class='label label-info'>MC MP</span>">MC MP</option>
				<option value="MC Galicia" data-content="<span class='label label-default'>MC G</span>">MC G</option>
				<option value="VISA Galicia" data-content="<span class='label label-default'>VISA G</span>">VISA G</option>
				<option value="Debito Galicia" data-content="<span class='label label-success'>Deb G</span>">Deb G</option>
            	<option value="Efectivo" data-content="<span class='label label-success'>Efectivo</span>">Efectivo</option>
            </select>
            </div>
            
              <div class="col-md-4">
                <div class="input-group form-group" id="div_costo_usd">
                <span class="input-group-addon">usd</span>
                  <input class="form-control" type="text" name="costo_usd" id="multiplicando" value="">
                </div><!-- /input-group -->
              </div><!-- /.col-lg-6 -->
              
              <?php $amount = urlencode($amount);
				  $get = file_get_contents("https://finance.google.co.uk/bctzjpnsun/converter?a=1&from=USD&to=ARS");
				  $get = explode("<span class=bld>",$get);
				  $get = explode("</span>",$get[1]);  
				  $converted_amount = preg_replace("/[^0-9\.]/", null, $get[0]);
				  $cotiz =  round(($converted_amount + 0.2), 1, PHP_ROUND_HALF_UP);
				?>
              <div class="col-md-4">
                <div class="input-group form-group">
                <span class="input-group-addon">ctz</span>
                  <input class="form-control" type="text" id="multiplicador" value="<?php echo $cotiz; ?>">
                </div><!-- /input-group -->
              </div><!-- /.col-lg-6 -->
            
            <div class="col-md-4">
            <div class="input-group form-group">
              <span class="input-group-addon"><i class="fa fa-dollar fa-fw"></i></span>
              <input class="form-control" type="text" name="costo" id="resultado" value="" style="text-align:right; color: #777" readonly>
			</div>
            </div>
        	
		    <p>.</p>
        	
		    <br /><br /> 
            <div class="input-group form-group" id="div_order">
              <span class="input-group-addon"><i class="fa fa-pencil fa-fw"></i></span>
              <input class="form-control" id="n_order" type="text" name="n_order" placeholder="n° de compra">
			</div>    
		
			<p>
			<textarea rows="5" id="excel_data" name="excel_data"></textarea><br>
			<input class="btn btn-xs btn-warning" type="button" onClick="javascript:generateTable()" value="Trasladar Codes" />
			</p>
            
			<button class="btn btn-primary" type="submit">Insertar</button>
            <input type="hidden" name="MM_insert" value="form1">
		
			<input type="hidden" name="code_prov" value="P2">
			<input type="hidden" name="usuario" value="<?php echo $vendedor; ?>">
            <!-- <a href="" id="vinculo" class="btn btn-primary">insertar bulk</a> -->
	</div>
    <div class="col-sm-1">
	</div>
		
    <div class="col-sm-4">
		<div class="col-sm-6">
		<div class="input-group form-group" style="margin:0px">
               <input class="form-control corregircodigo" id="code1" type="text" name="code1" placeholder=" 1" >
			</div>
            
            <div class="input-group form-group" style="margin:0px">
               <input class="form-control corregircodigo" id="code2" type="text" name="code2" placeholder=" 2">
			</div>
            
            <div class="input-group form-group" style="margin:0px">
               <input class="form-control corregircodigo" id="code3" type="text" name="code3" placeholder=" 3">
			</div>
            
            <div class="input-group form-group" style="margin:0px">
               <input class="form-control corregircodigo" id="code4" type="text" name="code4" placeholder=" 4">
			</div>
            
            <div class="input-group form-group" style="margin:0px">
               <input class="form-control corregircodigo" id="code5" type="text" name="code5" placeholder=" 5">
			</div>
            
            <div class="input-group form-group" style="margin:0px">
               <input class="form-control corregircodigo" id="code6" type="text" name="code6" placeholder=" 6">
			</div>
            
            <div class="input-group form-group" style="margin:0px">
               <input class="form-control corregircodigo" id="code7" type="text" name="code7" placeholder=" 7">
			</div>
            
            <div class="input-group form-group" style="margin:0px">
               <input class="form-control corregircodigo" id="code8" type="text" name="code8" placeholder=" 8">
			</div>
            
            <div class="input-group form-group" style="margin:0px">
               <input class="form-control corregircodigo" id="code9" type="text" name="code9" placeholder=" 9">
			</div>
            
            <div class="input-group form-group" style="margin:0px">
               <input class="form-control corregircodigo" id="code10" type="text" name="code10" placeholder=" 10">
			</div>
		
			<div class="input-group form-group" style="margin:0px">
               <input class="form-control corregircodigo" id="code11" type="text" name="code11" placeholder=" 11">
			</div>
		
		<div class="input-group form-group" style="margin:0px">
               <input class="form-control corregircodigo" id="code12" type="text" name="code12" placeholder=" 12">
			</div>
		
		<div class="input-group form-group" style="margin:0px">
               <input class="form-control corregircodigo" id="code13" type="text" name="code13" placeholder=" 13">
			</div>
		
		<div class="input-group form-group" style="margin:0px">
               <input class="form-control corregircodigo" id="code14" type="text" name="code14" placeholder=" 14">
			</div>
		
		<div class="input-group form-group" style="margin:0px">
               <input class="form-control corregircodigo" id="code15" type="text" name="code15" placeholder=" 15">
			</div>
		
		<div class="input-group form-group" style="margin:0px">
               <input class="form-control corregircodigo" id="code16" type="text" name="code16" placeholder=" 16">
			</div>
		
		<div class="input-group form-group" style="margin:0px">
               <input class="form-control corregircodigo" id="code17" type="text" name="code17" placeholder=" 17">
			</div>
		
		<div class="input-group form-group" style="margin:0px">
               <input class="form-control corregircodigo" id="code18" type="text" name="code18" placeholder=" 18">
			</div>
		
		<div class="input-group form-group" style="margin:0px">
               <input class="form-control corregircodigo" id="code19" type="text" name="code19" placeholder=" 19">
			</div>
		
		<div class="input-group form-group" style="margin:0px">
               <input class="form-control corregircodigo" id="code20" type="text" name="code20" placeholder=" 20">
			</div>
		</div>
		
		<div class="col-sm-6">
		<div class="input-group form-group" style="margin:0px">
               <input class="form-control corregircodigo" id="code21" type="text" name="code21" placeholder=" 21" >
			</div>
            
            <div class="input-group form-group" style="margin:0px">
               <input class="form-control corregircodigo" id="code22" type="text" name="code22" placeholder=" 22">
			</div>
            
            <div class="input-group form-group" style="margin:0px">
               <input class="form-control corregircodigo" id="code23" type="text" name="code23" placeholder=" 23">
			</div>
            
            <div class="input-group form-group" style="margin:0px">
               <input class="form-control corregircodigo" id="code24" type="text" name="code24" placeholder=" 24">
			</div>
            
            <div class="input-group form-group" style="margin:0px">
               <input class="form-control corregircodigo" id="code25" type="text" name="code25" placeholder=" 25">
			</div>
            
            <div class="input-group form-group" style="margin:0px">
               <input class="form-control corregircodigo" id="code26" type="text" name="code26" placeholder=" 26">
			</div>
            
            <div class="input-group form-group" style="margin:0px">
               <input class="form-control corregircodigo" id="code27" type="text" name="code27" placeholder=" 27">
			</div>
            
            <div class="input-group form-group" style="margin:0px">
               <input class="form-control corregircodigo" id="code28" type="text" name="code28" placeholder=" 28">
			</div>
            
            <div class="input-group form-group" style="margin:0px">
               <input class="form-control corregircodigo" id="code29" type="text" name="code29" placeholder=" 29">
			</div>
            
            <div class="input-group form-group" style="margin:0px">
               <input class="form-control corregircodigo" id="code30" type="text" name="code30" placeholder=" 30">
			</div>
		
			<div class="input-group form-group" style="margin:0px">
               <input class="form-control corregircodigo" id="code31" type="text" name="code31" placeholder=" 31">
			</div>
		
		<div class="input-group form-group" style="margin:0px">
               <input class="form-control corregircodigo" id="code32" type="text" name="code32" placeholder=" 32">
			</div>
		
		<div class="input-group form-group" style="margin:0px">
               <input class="form-control corregircodigo" id="code33" type="text" name="code33" placeholder=" 33">
			</div>
		
		<div class="input-group form-group" style="margin:0px">
               <input class="form-control corregircodigo" id="code34" type="text" name="code34" placeholder=" 34">
			</div>
		
		<div class="input-group form-group" style="margin:0px">
               <input class="form-control corregircodigo" id="code35" type="text" name="code35" placeholder=" 35">
			</div>
		
		<div class="input-group form-group" style="margin:0px">
               <input class="form-control corregircodigo" id="code36" type="text" name="code36" placeholder=" 36">
			</div>
		
		<div class="input-group form-group" style="margin:0px">
               <input class="form-control corregircodigo" id="code37" type="text" name="code37" placeholder=" 37">
			</div>
		
		<div class="input-group form-group" style="margin:0px">
               <input class="form-control corregircodigo" id="code38" type="text" name="code38" placeholder=" 38">
			</div>
		
		<div class="input-group form-group" style="margin:0px">
               <input class="form-control corregircodigo" id="code39" type="text" name="code39" placeholder=" 39">
			</div>
		
		<div class="input-group form-group" style="margin:0px">
               <input class="form-control corregircodigo" id="code40" type="text" name="code40" placeholder=" 40">
			</div>
		</div>
		</div>
    </div>
    </form>
    
    
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
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/2.1.1/jquery.min.js"></script>
	
	<script type="text/javascript" src="js/typeahead.bundle.js"></script>
	<script type="text/javascript">
    $(document).ready(function(){
        // Defining the local dataset
        var cars = <?php echo json_encode($rowsAA); ?>;
        //console.log(cars, "Hello, world!");
        
        // Constructing the suggestion engine
        var cars = new Bloodhound({
            datumTokenizer: Bloodhound.tokenizers.whitespace,
            queryTokenizer: Bloodhound.tokenizers.whitespace,
            local: cars
        });
        
        // Initializing the typeahead
        $('.typeahead').typeahead({
            hint: true,
            highlight: true, /* Enable substring highlighting */
            minLength: 2 /* Specify minimum characters required for showing result */
            
        },
        {
            name: 'cars',
            source: cars
        });
    });  
    </script>

    <script type="text/javascript">
	.on('typeahead:render', (e,firstOption) => {
                    if (!!firstOption) {
                        enterSelection = firstOption
                    } else {
                        enterSelection = undefined;
                    }
                }).on('keypress', (e) => {
                    if (e.which == 13 && enterSelection) {
                        $('#typeahead').typeahead('val', enterSelection.value);
                        (this.onDataSelect || _.noop)({ selection: enterSelection });
                        $('#typeahead').typeahead('close');
                    }
                });
	</script>
	  
	  
	<!--- invento para mejorar la carga de clientes y consulta de la lista de clientes -->
	<script type="text/javascript">
		$("#clientes").blur(function(){
        if(this.value==""){
         this.value = arr[0];   
        }
     });
		
	// selecciono los datos dl campo input cliente y quito los espacios en blanco y coloco guion medio, paso todo a minuscula	
	$("#clientes").blur(function(){
	cliente = document.getElementById("clientes").value;
    client = cliente.replace(/\s+/g, '-').toLowerCase();
		
	// selecciono la informacion titulo, consola e ID del campo y actualizo los inputs con los datos del select
	var Strin = client.substring(client.lastIndexOf('<-')+2,client.lastIndexOf('->'));
	var Stri = client.substring(client.lastIndexOf('(')+1,client.lastIndexOf(')'));
	var String = client.substring(client.lastIndexOf('[')+1,client.lastIndexOf(']'));
	document.getElementById("clientes_id1").value = Strin;
	document.getElementById("clientes_id2").value = Stri;
	document.getElementById("multiplicando").value = String;
    });
	</script>
	
	<!-- Cargo la imagen del producto seleccionado cada 0.5 segundos -->
	<script type="text/javascript">
	window.setInterval(function() {
                var titulo = document.getElementById('clientes_id1').value;
				var consola = document.getElementById('clientes_id2').value;
				$("#image-swap").attr("src", (titulo !== "" &&  + consola !== "") ? "/img/productos/" + consola + "/" + titulo + ".jpg" : "");
				$('#image-swap').load(function() {
                document.getElementById("alerta").innerHTML = "";    
                });
				$('#image-swap').error(function() {
  				document.getElementById("alerta").innerHTML = "no se encuentra";
				});
            },500);
	</script>
	  
	<!--- Calculo el precio en pesos -->
	<script type="text/javascript">
	window.setInterval(function() {
      m1 = document.getElementById("multiplicando").value;
      m2 = document.getElementById("multiplicador").value;
      r = m1*m2;
      document.getElementById("resultado").value = r;
    },500);
	window.setInterval(function() {
      m1 = document.getElementById("multiplicando").value;
      m2 = document.getElementById("multiplicador").value;
      r = m1*m2;
      document.getElementById("resultado").value = r;
    },500);
	</script>
	  
	<!--- FUNCION PARA AGREGAR EL DASH " - " cada 4 caracteres -->
	<!--- Primero establezco el formato, luego quito los dash que tenga actualmente, para finalmente agregar el dash cada 4 caracteres -->	   
	<script>
		 function format(input, format, sep) {
			 var output = "";
    		var idx = 0;
    		for (var i = 0; i < format.length && idx < input.length; i++) {
        		output += input.substr(idx, format[i]);
        		if (idx + format[i] < input.length) output += sep;
        		idx += format[i];
    			}		
			 output += input.substr(idx);
			 return output;
    		}
		  
		$('#excel_data').keyup(function() {
		var foo = $(this).val().replace(/-/g, ""); // remove hyphens
		// You may want to remove all non-digits here
		// var foo = $(this).val().replace(/\D/g, "");
		//if (foo.length > 0) {
			//foo = format(foo, ;
			//}

		$(this).val(foo);
		});
	</script>
	  
	  <script type="text/javascript">
		  function generateTable(){
	  		var data = $('textarea[name=excel_data]').val();
		  var cells = data.split("\n");
		  $("#code1").val(format(cells[0], [4, 4, 4, 4, 4, 4, 4, 4, 4], "-")); // AGREGO CODIGO PARA SEPARAR DE A 4 CARACTERES
		  $("#code2").val(format(cells[1], [4, 4, 4, 4, 4, 4, 4, 4, 4], "-"));
		  $("#code3").val(format(cells[2], [4, 4, 4, 4, 4, 4, 4, 4, 4], "-"));
		  $("#code4").val(format(cells[3], [4, 4, 4, 4, 4, 4, 4, 4, 4], "-"));
		  $("#code5").val(format(cells[4], [4, 4, 4, 4, 4, 4, 4, 4, 4], "-"));
		  $("#code6").val(format(cells[5], [4, 4, 4, 4, 4, 4, 4, 4, 4], "-"));
		  $("#code7").val(format(cells[6], [4, 4, 4, 4, 4, 4, 4, 4, 4], "-"));
		  $("#code8").val(format(cells[7], [4, 4, 4, 4, 4, 4, 4, 4, 4], "-"));
		  $("#code9").val(format(cells[8], [4, 4, 4, 4, 4, 4, 4, 4, 4], "-"));
		  $("#code10").val(format(cells[9], [4, 4, 4, 4, 4, 4, 4, 4, 4], "-"));
		  $("#code11").val(format(cells[10], [4, 4, 4, 4, 4, 4, 4, 4, 4], "-"));
		  $("#code12").val(format(cells[11], [4, 4, 4, 4, 4, 4, 4, 4, 4], "-"));
		  $("#code13").val(format(cells[12], [4, 4, 4, 4, 4, 4, 4, 4, 4], "-"));
		  $("#code14").val(format(cells[13], [4, 4, 4, 4, 4, 4, 4, 4, 4], "-"));
		  $("#code15").val(format(cells[14], [4, 4, 4, 4, 4, 4, 4, 4, 4], "-"));
		  $("#code16").val(format(cells[15], [4, 4, 4, 4, 4, 4, 4, 4, 4], "-"));
		  $("#code17").val(format(cells[16], [4, 4, 4, 4, 4, 4, 4, 4, 4], "-"));
		  $("#code18").val(format(cells[17], [4, 4, 4, 4, 4, 4, 4, 4, 4], "-"));
		  $("#code19").val(format(cells[18], [4, 4, 4, 4, 4, 4, 4, 4, 4], "-"));
		  $("#code20").val(format(cells[19], [4, 4, 4, 4, 4, 4, 4, 4, 4], "-"));
		  $("#code21").val(format(cells[20], [4, 4, 4, 4, 4, 4, 4, 4, 4], "-"));
		  $("#code22").val(format(cells[21], [4, 4, 4, 4, 4, 4, 4, 4, 4], "-"));
		  $("#code23").val(format(cells[22], [4, 4, 4, 4, 4, 4, 4, 4, 4], "-"));
		  $("#code24").val(format(cells[23], [4, 4, 4, 4, 4, 4, 4, 4, 4], "-"));
		  $("#code25").val(format(cells[24], [4, 4, 4, 4, 4, 4, 4, 4, 4], "-"));
		  $("#code26").val(format(cells[25], [4, 4, 4, 4, 4, 4, 4, 4, 4], "-"));
		  $("#code27").val(format(cells[26], [4, 4, 4, 4, 4, 4, 4, 4, 4], "-"));
		  $("#code28").val(format(cells[27], [4, 4, 4, 4, 4, 4, 4, 4, 4], "-"));
		  $("#code29").val(format(cells[28], [4, 4, 4, 4, 4, 4, 4, 4, 4], "-"));
		  $("#code30").val(format(cells[29], [4, 4, 4, 4, 4, 4, 4, 4, 4], "-"));
		  $("#code31").val(format(cells[30], [4, 4, 4, 4, 4, 4, 4, 4, 4], "-"));
		  $("#code32").val(format(cells[31], [4, 4, 4, 4, 4, 4, 4, 4, 4], "-"));
		  $("#code33").val(format(cells[32], [4, 4, 4, 4, 4, 4, 4, 4, 4], "-"));
		  $("#code34").val(format(cells[33], [4, 4, 4, 4, 4, 4, 4, 4, 4], "-"));
		  $("#code35").val(format(cells[34], [4, 4, 4, 4, 4, 4, 4, 4, 4], "-"));
		  $("#code36").val(format(cells[35], [4, 4, 4, 4, 4, 4, 4, 4, 4], "-"));
		  $("#code37").val(format(cells[36], [4, 4, 4, 4, 4, 4, 4, 4, 4], "-"));
		  $("#code38").val(format(cells[37], [4, 4, 4, 4, 4, 4, 4, 4, 4], "-"));
		  $("#code39").val(format(cells[38], [4, 4, 4, 4, 4, 4, 4, 4, 4], "-"));
		  $("#code40").val(format(cells[39], [4, 4, 4, 4, 4, 4, 4, 4, 4], "-"));
			  }
	  </script>
	  
	  
    <script type="text/javascript">
	$('input').bind('paste', null, function(e){
		$this = $(this);

		setTimeout(function(){
			var columns = $this.val().split(/\s+/);
			var i;

			for(i=0; i < columns.length; i++){
				$('input').val(columns[i]);
			}
		}, 0);
	});'
	</script>
	  
    <script type="text/javascript">
    $(function(){
    $('input[type="text"]').change(function(){
        this.value = $.trim(this.value);
    });
	});
	</script>
	  
	<!--- Controlo que se ingrese un numero de orden y un valor para multiplicando -->
    <script type="text/javascript">
    var isFormValid = false;
	$('form').submit(function() {
	
	if (!isFormValid) {
    if($('#multiplicando').val() == ''){
		if($('#n_order').val() == ''){
			document.getElementById("div_order").className = "input-group form-group has-error";
		}
		document.getElementById("div_costo_usd").className = "input-group form-group has-error";
		isFormValid = true;
        return false;
	}
    }
	if (!isFormValid) {
    if($('#n_order').val() == ''){
		if($('#multiplicando').val() == ''){
			document.getElementById("div_costo_usd").className = "input-group form-group has-error";
		}
		document.getElementById("div_order").className = "input-group form-group has-error";
		isFormValid = true;
        return false;
	}
    }
	});
	</script>
    <script type="text/javascript">
		 function highlightDuplicates() {
			// loop over all input fields in table
			$('form').find('input').each(function() {
				// check if there is another one with the same value
				if ($('form').find('input[value="' + $(this).val() + '"]').size() > 1) {
					// highlight this
					if ($(this).val() != ''){
					$(this).parent().addClass('has-error');
					}
					else { $(this).parent().removeClass('has-error'); }
				} else {
					// otherwise remove
					$(this).parent().removeClass('has-error');
				}
			});
		}
		$().ready(function() {
			// initial test
			highlightDuplicates();
	
			// fix for newer jQuery versions!
			// since you can select by value, but not by current val
			$('form').find('input').bind('input',function() {
				$(this).attr('value',this.value)
			});
	
			// bind test on any change event
			$('form').find('input').on('input',highlightDuplicates);
		});
		</script>
	
	
    <!-- IMAGE PICKER  
    <link rel="stylesheet" href="css/image-picker.css">
    <script src="js/image-picker.min.js"></script>
    <script type="text/javascript">
	$("#consola").imagepicker()
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

mysql_free_result($rsTitulos);

mysql_free_result($rsCuentas);
?>