<?php require_once('../../Connections/Conexion.php'); ?>
<?php
$MM_authorizedUsers = "Adm,Vendedor";
$MM_donotCheckaccess = "false";
require_once('../_autentificacion.php');
?>
<?php 
$colname_rsCuentas = "-1";
if (isset($_GET['cta_id'])) {
  $colname_rsCuentas = (get_magic_quotes_gpc()) ? $_GET['cta_id'] : addslashes($_GET['cta_id']);
}

mysql_select_db($database_Conexion, $Conexion);
$query_rsCuentas = sprintf("SELECT cuentas.ID, mail_fake, name, surname, stk.Q_Stk
FROM cuentas 
LEFT JOIN
(SELECT cuentas_id, COUNT(*) AS Q_Stk FROM stock GROUP BY cuentas_id) AS stk
ON cuentas.ID = stk.cuentas_id
WHERE (Q_Stk < 2 OR Q_Stk IS NULL) AND (ID = %s)
ORDER BY ID DESC", $colname_rsCuentas);
$rsCuentas = mysql_query($query_rsCuentas, $Conexion) or die(mysql_error());
$row_rsCuentas = mysql_fetch_assoc($rsCuentas);
$totalRows_rsCuentas = mysql_num_rows($rsCuentas);

mysql_select_db($database_Conexion, $Conexion);
$query_rsSaldo = sprintf("SELECT cuentas_id, SUM(costo_usd) as costo_usd, SUM(costo) as costo, medio_pago
FROM saldo 
WHERE cuentas_id = %s
GROUP BY cuentas_id
ORDER BY ID DESC", $colname_rsCuentas);
$rsSaldo = mysql_query($query_rsSaldo, $Conexion) or die(mysql_error());
$row_rsSaldo = mysql_fetch_assoc($rsSaldo);
$totalRows_rsSaldo = mysql_num_rows($rsSaldo);

mysql_select_db($database_Conexion, $Conexion);
$query_rsGastado = sprintf("SELECT cuentas_id, SUM(costo_usd) as costo_usd, SUM(costo) as costo
FROM stock 
WHERE cuentas_id = %s
GROUP BY cuentas_id
ORDER BY ID DESC", $colname_rsCuentas);
$rsGastado = mysql_query($query_rsGastado, $Conexion) or die(mysql_error());
$row_rsGastado = mysql_fetch_assoc($rsGastado);
$totalRows_rsGastado = mysql_num_rows($rsGastado);

$editFormAction = $_SERVER['PHP_SELF'];
if (isset($_SERVER['QUERY_STRING'])) {
  $editFormAction .= "?" . htmlentities($_SERVER['QUERY_STRING']);
}

$vendedor = $_SESSION['MM_Username'];
$date = date('Y-m-d H:i:s', time());

if ((isset($_POST["MM_insert"])) && ($_POST["MM_insert"] == "form1")) {
  $insertSQL = sprintf("INSERT INTO stock (titulo, consola, cuentas_id, medio_pago, costo_usd, costo, Day, Notas, usuario) VALUES (%s, %s, %s, 'Saldo', %s, %s, '$date', %s, '$vendedor')", 
                       GetSQLValueString($_POST['titulo'], "text"),
                       GetSQLValueString($_POST['consola'], "text"),
					   GetSQLValueString($_POST['cuentas_id'], "int"),
					   GetSQLValueString($_POST['costo_usd'], "double"),
                       GetSQLValueString($_POST['costo'], "double"),
                       GetSQLValueString($_POST['Notas'], "text"));

  mysql_select_db($database_Conexion, $Conexion);
  $Result1 = mysql_query($insertSQL, $Conexion) or die(mysql_error());

  $insertGoTo = "cuentas_detalles.php?id=";
  $insertGoTo .= GetSQLValueString($_POST['cuentas_id'], "int");

  header(sprintf("Location: %s", $insertGoTo));
}

if ((isset($_POST["MM_insert"])) && ($_POST["MM_insert"] == "form2")) {
  $insertSQL = sprintf("INSERT INTO stock (titulo, consola, cuentas_id, medio_pago, costo_usd, costo, Day, Notas, usuario) VALUES (%s, %s, %s, 'Saldo', %s, %s, '$date', %s, '$vendedor')", 
                       GetSQLValueString($_POST['titulo'], "text"),
                       GetSQLValueString($_POST['consola'], "text"),
					   GetSQLValueString($_POST['cuentas_id'], "int"),
					   GetSQLValueString($_POST['costo_usd'], "double"),
                       GetSQLValueString($_POST['costo_total'], "double"),
                       GetSQLValueString($_POST['Notas'], "text"));

  mysql_select_db($database_Conexion, $Conexion);
  $Result1 = mysql_query($insertSQL, $Conexion) or die(mysql_error());

  $insertGoTo = "cuentas_detalles.php?id=";
  $insertGoTo .= GetSQLValueString($_POST['cuentas_id'], "int");

  header(sprintf("Location: %s", $insertGoTo));
}
$nombresito = $_SESSION['MM_Username'];
mysql_select_db($database_Conexion, $Conexion);
$query_rsUsuarios = sprintf("SELECT * FROM usuarios WHERE Nombre = '$nombresito' ORDER BY Nombre ASC",$nombresito);
$rsUsuarios = mysql_query($query_rsUsuarios, $Conexion) or die(mysql_error());
$row_rsUsuarios = mysql_fetch_assoc($rsUsuarios);
$totalRows_rsUsuarios = mysql_num_rows($rsUsuarios);

// 2017-09-26 Filtro tomando en cuenta los ultimos 45 días unicamente, y quito las gift cards que logicamente no cargo en una cuenta salvo plus 12m slot
$query_rsTitulos = "SELECT web.*, stk.*
FROM
(SELECT REPLACE(REPLACE(REPLACE(REPLACE(TRIM(LCASE(post_title)), ' ', '-'), '''', ''), '’', ''), '.', '') AS producto FROM cbgw_posts WHERE post_type = 'product' and post_status = 'publish' group by producto) as web
LEFT JOIN
(SELECT titulo, COUNT(*) AS Q_Stk, Day FROM stock WHERE Day >= (DATE_ADD(CURDATE(), INTERVAL -45 DAY)) and (titulo LIKE '%slot%' or consola != 'ps') GROUP BY titulo) AS stk
ON producto = titulo 
ORDER BY Q_Stk DESC";
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
    <link rel="icon" href="../favicon.ico">
	
<!-- InstanceBeginEditable name="doctitle" -->
    <title><?php $titulo = 'Insertar stock'; echo $titulo; ?></title>
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
    <div class="row">
    <div class="col-sm-3" style="text-align:right;">
    <span id="alerta" class="label label-danger"></span>
	<img class="img-rounded pull-right" width="100" id="image-swap" src="" alt="" />
    </div>
    <div class="col-sm-5">
    <form method="post" name="form1" action="<?php echo $editFormAction; ?>">
    

            <input type="text" name="cuentas_id" value="<?php echo $row_rsCuentas['ID'];?>" hidden>
            
            <div class="input-group form-group">
            <span class="input-group-addon"><i class="fa fa-gamepad fa-fw"></i></span>
              <select id="titulo-selec" name="titulo" class="selectpicker form-control" data-live-search="true" data-size="5">
			  <?php do {  ?>
            	<option value="<?php echo $row_rsTitulos['producto']?>"><?php echo str_replace('-', ' ', $row_rsTitulos['producto']);?></option>
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
            <span class="input-group-addon"><i class="fa fa-cube fa-fw"></i></span>
            <select id="consola" name="consola" class="selectpicker form-control">
            	<option selected value="ps4" data-content="<span class='label label-primary'>ps4</span>">ps4</option>
                <option value="ps3" data-content="<span class='label label-warning' style='background-color:#000;'>ps3</span>">ps3</option>
                <option value="ps" data-content="<span class='label label-danger'>psn</span>">psn</option>
                <option value="steam" data-content="<span class='label label-default'>steam</span>">steam</option>
                <option value="psvita" data-content="<span class='label label-info'>psvita</span>">psvita</option>
            </select>
            </div>
            
            <div class="input-group form-group text-center" style="width:100%">
			</div>
                
            <div class="input-group form-group">
             	<span class="input-group-addon"><em>Costo en USD</em></span>
                
                <input <?php // Si ya hay un producto cargado, el saldo <<USD>> será cargado al segundo producto sin permitir modificarlo
				 if ($row_rsGastado['costo_usd']): echo 'readonly';
				 endif;?>
                 id="proporcion_usd" class="form-control" type="text" name="costo_usd" value="<?php echo ($row_rsSaldo['costo_usd'] - $row_rsGastado['costo_usd']);?>">
                 <span class="input-group-addon"> <em style="opacity:0.7" class="text-muted">Saldo: <?php echo ($row_rsSaldo['costo_usd'] - $row_rsGastado['costo_usd']);?> <img src="https://exur-exur.netdna-ssl.com/realskin/images/usa.png" style="opacity:0.7"></em></span>
            </div>
           
           <?php // saldo total <<USD>> cargado a esta cuenta ?>
           <input readonly <?php if (!($_SESSION['MM_UserGroup'] ==  'Adm')):?>type="hidden"<?php endif;?> class="form-control" type="text" id="total_usd" value="<?php echo (number_format($row_rsSaldo['costo_usd'], 2, '.', ''));?>">
           
           <?php  //si ya hay un producto cargado, el saldo restante en <<ARS>> si o si lo asigno al nuevo producto
		   if ($row_rsGastado['costo_usd']):?>
            <input readonly <?php if (!($_SESSION['MM_UserGroup'] ==  'Adm')):?>type="hidden"<?php endif;?> name="costo" value="<?php echo (number_format(($row_rsSaldo['costo'] - $row_rsGastado['costo']), 2, '.', ''));?>">
            
            <?php // saldo <<ARS>> a cargar si es el primer producto 
			else:?>
            <input <?php if (!($_SESSION['MM_UserGroup'] ==  'Adm')):?>type="hidden"<?php endif;?> id="resultado_ars" name="costo" value="">
            
            <?php // saldo total <<ARS>> cargado a esta cuenta ?>
            <input <?php if (!($_SESSION['MM_UserGroup'] ==  'Adm')):?>type="hidden"<?php endif;?> id="total_ars" name="costo_total" value="<?php echo (number_format($row_rsSaldo['costo'], 2, '.', ''));?>">
            <?php endif;?>

            <div class="input-group form-group">
              <span class="input-group-addon"><i class="fa fa-comment fa-fw"></i></span>
              <input class="form-control" type="text" name="Notas" placeholder="Notas de stock">
            </div>
            <?php
			// si la cuenta todavía no tiene juego cargado
			 if (!($row_rsGastado['costo_usd'])):?>
            
            <p><label class="btn-cuenta"><input type="radio" name="MM_insert" value="form1"  />Asignar el <span id="porcentaje">00</span>%  del costo en $</label> <label class="btn-info btn-cuenta"><input type="radio" name="MM_insert" value="form2" checked="checked" />Asignar el 100% del costo en $</label></p>
            <div class="top" style="display:inline; background-color:#e8eaf6 ; border-color:#e8eaf6 ;">
              <div class="arrow"></div>
              <h5 class="" style="color:#000;">Asignar Costo en Pesos</h5>
              <div class="">
                <p style="color:#000;">Evaluamos CUANTO SALDO EN USD quedará libre en la cuenta<br />
                <span class="badge badge-normal">A) Si queda 9,99 USD o mas</span> Asignar el costo proporcional (botón gris)<br /><span class="badge badge-info">B) Si queda menos a 9,99 USD</span> Asignar el 100% del costo (botón verde)</p>
              </div>
			</div>
            <?php 
			// si la cuenta ya tiene un juego cargado
			else:?>
            <input hidden="" type="radio" name="MM_insert" value="form1" checked="checked" />
            <?php endif;?>

            <button class="btn btn-primary" type="submit">Insertar</button>
    </form>
		asd
		<?php $someValue = "7.99";
		$arr = array(9.99,19.99,29.99,39.99,49.99,59.99,69.99,79.99,89.99,99.99);
		if(in_array($someValue, $arr)) {
			echo "fff!";
		} else {
		   echo "nnn!";
		}?>
    </div>

    <div class="col-sm-4">
        <div class="popover right" style="display:inline; background-color:#e8eaf6 ; border-color:#e8eaf6 ;">
          <div class="arrow"></div>
          <h3 class="popover-title" style="color:#aaa;">Indicaciones</h3>
          <div class="popover-content">
            <p style="color:#aaa;">El costo en USD siempre reflejo la realidad, excepto cuando falta 1 centavo para llegar a multiplo de 10. Ejemplos:<br> A) Cuesta 6,74 &gt; Cargo 6,74<br> B) Cuesta 14,99 &gt; Cargo 14,99<br><br>Pero si falta 1 centavo para llegar a 10 o multiplo de 10, ejemplos:<br> C) Cuesta 9,99 &gt; Cargo 10<br> D) Cuesta 19,99 &gt; Cargo 20<br> E) Cuesta 39,99 &gt; Cargo 40<br>etc...</p>
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
    <script type="text/javascript">
	 jQuery(function($) {
            $("form").on('change', function() {
                var titulo = document.getElementById('titulo-selec').value;
				var consola = document.getElementById('consola').value;
				$("#image-swap").attr("src", (titulo !== "" &&  + consola !== "") ? "/img/productos/" + consola + "/" + titulo + ".jpg" : "");
				$('#image-swap').load(function() {
                document.getElementById("alerta").innerHTML = "";    
                });
				$('#image-swap').error(function() {
  				document.getElementById("alerta").innerHTML = "no se encuentra";
				});
            }).trigger('change');
        })
	</script>
	<script type="text/javascript">
	window.setInterval(function() {
      m1_1 = document.getElementById("proporcion_usd").value;
      m2_1 = document.getElementById("total_usd").value;
	  m3_1 = document.getElementById("total_ars").value;
      r_1 = (m1_1/m2_1)*m3_1;
      document.getElementById("resultado_ars").value = r_1;
    },500);
	</script>
    <script type="text/javascript">
	window.setInterval(function() {
      as1_1 = document.getElementById("proporcion_usd").value;
      as2_1 = document.getElementById("total_usd").value;
      re_1 = Math.round((as1_1/as2_1)*100);
      document.getElementById("porcentaje").innerHTML = re_1;
    },500);
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

mysql_free_result($rsSaldo);

?>