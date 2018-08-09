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

  $insertGoTo = "stock_insertar.php";
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

mysql_select_db($database_Conexion, $Conexion);
$query_rsCuentas = sprintf("SELECT cuentas.ID, mail_fake, name, surname, stk.Q_Stk
FROM cuentas 
LEFT JOIN
(SELECT cuentas_id, COUNT(*) AS Q_Stk FROM stock GROUP BY cuentas_id) AS stk
ON cuentas.ID = stk.cuentas_id
WHERE (Q_Stk < 2 OR Q_Stk IS NULL)
ORDER BY ID DESC", $colname_rsTitulos);
$rsCuentas = mysql_query($query_rsCuentas, $Conexion) or die(mysql_error());
$row_rsCuentas = mysql_fetch_assoc($rsCuentas);
$totalRows_rsCuentas = mysql_num_rows($rsCuentas);
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
    <title><?php $titulo = 'Insertar stock'; echo $titulo; ?></title>
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
    <div class="col-sm-5">
    <form method="post" name="form1" action="<?php echo $editFormAction; ?>">
    
			<div class="input-group form-group">
              <span class="input-group-addon"><i class="fa fa-link fa-fw"></i></span>
              <select name="cuentas_id" class="selectpicker form-control" data-size="5" autofocus>
            	<option value="" data-content="<span class='label label-danger'>no-vincula-cuenta</span>">no-vincula-cuenta</option>
				<?php ?>
				<?php $i = 0; do { ?>
                    <option <?php if($i == 0): echo 'selected'; endif; ?> data-content="<span class='label label-normal'>#<?php echo $row_rsCuentas['ID']?></span> <span class='label label-<?php if ($row_rsCuentas['Q_Stk'] > '0'): ?>warning<?php else:?>success<?php endif;?>'><?php echo $row_rsCuentas['mail_fake']?> <?php if ($row_rsCuentas['Q_Stk'] > '0'): ?>(<?php echo $row_rsCuentas['Q_Stk']?>)<?php endif; ?></span> <span class='label label-info'><?php echo $row_rsCuentas['name']?>-<?php echo $row_rsCuentas['surname']?></span>"
                    value="<?php echo $row_rsCuentas['ID']?>"><?php echo $row_rsCuentas['ID']?> <?php echo $row_rsCuentas['mail_fake']?> <?php if ($row_rsCuentas['Q_Stk'] > '0'): ?>(<?php echo $row_rsCuentas['Q_Stk']?>)<?php endif; ?>
                     </option>
				  <?php 
				  $i++;} while ($row_rsCuentas = mysql_fetch_assoc($rsCuentas));
                  $rows = mysql_num_rows($rsCuentas);
				  if($rows > 0) {
					  
					  mysql_data_seek($rsCuentas, 0);
					  $row_rsCuentas = mysql_fetch_assoc($rsCuentas);
					  }
					?>
				</select>
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
            
            <div class="input-group form-group text-center" style="width:100%">
            	<label class="btn-lg active"><input type="radio" name="medio_pago" value="Tarjeta" checked="checked"><i class="fa fa-credit-card fa-fw" aria-hidden="true"></i> Tarjeta </label>
                <label class="btn-lg"><input type="radio" name="medio_pago" value="Efectivo"><i class="fa fa-dollar fa-fw" aria-hidden="true"></i> Efectivo </label>
                <label class="btn-lg"><input type="radio" name="medio_pago" value="Paypal"><i class="fa fa-paypal fa-fw" aria-hidden="true"></i> Paypal</label>
            </div>
            
              <div class="col-md-4">
                <div class="input-group form-group" id="caja1">
                <span class="input-group-addon">usd</span>
                  <input class="form-control" type="text" name="costo_usd"  id="multiplicando" value="">
                </div><!-- /input-group -->
              </div><!-- /.col-lg-6 -->
              
              <?php $amount = urlencode($amount);
				  $get = file_get_contents("https://www.google.com/finance/converter?a=1&from=usd&to=ars");
				  $get = explode("<span class=bld>",$get);
				  $get = explode("</span>",$get[1]);  
				  $converted_amount = preg_replace("/[^0-9\.]/", null, $get[0]);
				  $cotiz =  round(($converted_amount + 0.27), 1, PHP_ROUND_HALF_UP);
				?>
              <div class="col-md-4">
                <div class="input-group form-group" id="caja2">
                <span class="input-group-addon">ctz</span>
                  <input class="form-control" type="text" id="multiplicador" value="<?php echo $cotiz; ?>">
                </div><!-- /input-group -->
              </div><!-- /.col-lg-6 -->
            
            <div class="col-md-4">
            <div class="input-group form-group" id="caja3">
              <span class="input-group-addon"><i class="fa fa-dollar fa-fw"></i></span>
              <input class="form-control" type="text" name="costo" id="resultado" value="" style="text-align:right;">
			</div>
            </div>
            

            <div class="input-group form-group">
              <span class="input-group-addon"><i class="fa fa-comment fa-fw"></i></span>
              <input class="form-control" type="text" name="Notas" placeholder="Notas">
            </div>

            <button class="btn btn-primary" type="submit">Insertar</button>
        <input type="hidden" name="MM_insert" value="form1">
    </form>
    </div>
    <div class="col-sm-1">
    </div>
    <div class="col-sm-3">
    <h4 style="text-align:right;">Calculadora</h4>
    <div class="col-md-4">
    <div class="input-group form-group">
    <input class="form-control" type="text" name="costo_usd_1"  id="multiplicando_1" value="" placeholder="usd">
    </div><!-- /input-group -->
    </div><!-- /.col-lg-6 -->
          
    <div class="col-md-4">
    <div class="input-group form-group">
    <input class="form-control" type="text" id="multiplicador_1" value="" placeholder="cotiz">
    </div><!-- /input-group -->
    </div><!-- /.col-lg-6 -->
        
    <div class="col-md-4">
    <div class="input-group form-group">
    <input class="form-control" type="text" name="costo_1" id="resultado_1" value="" style="text-align:right;" placeholder="ars" disabled>
    </div>
    </div>
        <div class="col-md-4">
    <div class="input-group form-group">
    <input class="form-control" type="text" name="costo_usd_2"  id="multiplicando_2" value="" placeholder="usd">
    </div><!-- /input-group -->
    </div><!-- /.col-lg-6 -->
          
    <div class="col-md-4">
    <div class="input-group form-group">
    <input class="form-control" type="text" id="multiplicador_2" value="" placeholder="cotiz">
    </div><!-- /input-group -->
    </div><!-- /.col-lg-6 -->
        
    <div class="col-md-4">
    <div class="input-group form-group">
    <input class="form-control" type="text" name="costo_2" id="resultado_2" value="" style="text-align:right;" placeholder="ars" disabled>
    </div>
    </div>
        <div class="col-md-4">
    <div class="input-group form-group">
    <input class="form-control" type="text" name="costo_usd_3"  id="multiplicando_3" value="" placeholder="usd">
    </div><!-- /input-group -->
    </div><!-- /.col-lg-6 -->
          
    <div class="col-md-4">
    <div class="input-group form-group">
    <input class="form-control" type="text" id="multiplicador_3" value="" placeholder="cotiz">
    </div><!-- /input-group -->
    </div><!-- /.col-lg-6 -->
        
    <div class="col-md-4">
    <div class="input-group form-group">
    <input class="form-control" type="text" name="costo_3" id="resultado_3" value="" style="text-align:right;" placeholder="ars" disabled>
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
	$("#multiplicando, #multiplicador").keyup(function() {
      m1 = document.getElementById("multiplicando").value;
      m2 = document.getElementById("multiplicador").value;
      r = m1*m2;
      document.getElementById("resultado").value = r;
    });
	</script>
    <script type="text/javascript">
	$("#multiplicando_1, #multiplicador_1").keyup(function() {
      m1_1 = document.getElementById("multiplicando_1").value;
      m2_1 = document.getElementById("multiplicador_1").value;
      r_1 = m1_1*m2_1;
      document.getElementById("resultado_1").value = r_1;
    });
	$("#multiplicando_2, #multiplicador_2").keyup(function() {
      m1_2 = document.getElementById("multiplicando_2").value;
      m2_2 = document.getElementById("multiplicador_2").value;
      r_2 = m1_2*m2_2;
      document.getElementById("resultado_2").value = r_2;
    });
	$("#multiplicando_3, #multiplicador_3").keyup(function() {
      m1_3 = document.getElementById("multiplicando_3").value;
      m2_3 = document.getElementById("multiplicador_3").value;
      r_3 = m1_3*m2_3;
      document.getElementById("resultado_3").value = r_3;
    });
	$("#multiplicador_1, #multiplicador_2, #multiplicador_3, #multiplicando_1, #multiplicando_2, #multiplicando_3").keyup(function() {
      suma_1 = +m1_1 + +m1_2 + +m1_3;
	  suma_2 = r_1+r_2+r_3;
	  document.getElementById("multiplicando").value = suma_1;
      document.getElementById("resultado").value = suma_2;
	  document.getElementById("multiplicador").value = (suma_2/suma_1);
	  if (suma_1 > "1") {
	  $("#multiplicando, #multiplicador, #resultado").attr('readonly', (suma_1 > "1" &&  + suma_2 > "1") ? 'readonly' : "");
	  document.getElementById("caja1").className = "input-group form-group has-success";
	  document.getElementById("caja2").className = "input-group form-group has-success";
	  document.getElementById("caja3").className = "input-group form-group has-success";
	  
	  }
	  else {
		 	$('#multiplicando, #multiplicador, #resultado').prop('readonly', false);
	  document.getElementById("caja1").className = "input-group form-group";
	  document.getElementById("caja2").className = "input-group form-group";
	  document.getElementById("caja3").className = "input-group form-group";
		}
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