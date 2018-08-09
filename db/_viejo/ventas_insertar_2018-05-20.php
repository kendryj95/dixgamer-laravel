<?php require_once('../../Connections/Conexion.php'); ?>
<?php
$MM_authorizedUsers = "Adm,Vendedor";
$MM_donotCheckaccess = "false";
require_once('../_autentificacion.php');
?>
<?php 

$editFormAction = $_SERVER['PHP_SELF'];
if (isset($_SERVER['QUERY_STRING'])) {
  $editFormAction .= "?" . htmlentities($_SERVER['QUERY_STRING']);
}

$date = date('Y-m-d H:i:s', time());
$vendedor = $_SESSION['MM_Username'];
if ((isset($_POST["MM_insert"])) && ($_POST["MM_insert"] == "form1")) {

		$insertSQL = sprintf("INSERT INTO ventas (clientes_id, stock_id, cons, slot, medio_venta, estado, Day, Notas, usuario) VALUES (%s, %s, %s, %s, %s, %s, '$date', %s, '$vendedor')",
                       GetSQLValueString($_POST['clientes_id'], "int"),
                       GetSQLValueString($_POST['stock_id'], "int"),
					   GetSQLValueString($_POST['cons'], "text"),
                       GetSQLValueString($_POST['slot'], "text"),
					   GetSQLValueString($_POST['medio_venta'], "text"),
					   GetSQLValueString($_POST['estado'], "text"),
                       GetSQLValueString($_POST['Notas'], "text"));
					   
		mysql_select_db($database_Conexion, $Conexion);
		$Result1 = mysql_query($insertSQL, $Conexion) or die(mysql_error());
  
		$ventaid = mysql_insert_id(); // ultimo ID de una consulta INSERT , en este caso seria el ID de la ultima venta creada
		$insertSQL2 = sprintf("INSERT INTO ventas_cobro (ventas_id, medio_cobro, ref_cobro, precio, comision, Day, Notas, usuario) VALUES ('$ventaid', %s, %s, %s, %s, '$date', %s, '$vendedor')",
                       GetSQLValueString($_POST['medio_cobro'], "text"),
					   GetSQLValueString($_POST['ref_cobro'], "text"),
					   GetSQLValueString($_POST['precio'], "double"),
                       GetSQLValueString($_POST['comision'], "double"),
                       GetSQLValueString($_POST['Notas_cobro'], "text"));
					   
		mysql_select_db($database_Conexion, $Conexion);
	  	$Result2 = mysql_query($insertSQL2, $Conexion) or die(mysql_error());
	
	$insertGoTo = "clientes_detalles.php";
  		if (isset($_SERVER['QUERY_STRING'])) {
   		$insertGoTo .= (strpos($insertGoTo, '?')) ? "&" : "?";
    	// $insertGoTo .= $_SERVER['QUERY_STRING'];
		// Cambio al ID del cliente, quito el ID del stock que viene de antes a esta pagina
		$insertGoTo .= 'id=';
		$insertGoTo .= $_POST['clientes_id'];
 		 }
  	header(sprintf("Location: %s", $insertGoTo));
	exit;
  
}

$nombresito = $_SESSION['MM_Username'];
mysql_select_db($database_Conexion, $Conexion);
$query_rsUsuarios = sprintf("SELECT * FROM usuarios WHERE Nombre = '$nombresito' ORDER BY Nombre ASC",$nombresito);
$rsUsuarios = mysql_query($query_rsUsuarios, $Conexion) or die(mysql_error());
$row_rsUsuarios = mysql_fetch_assoc($rsUsuarios);
$totalRows_rsUsuarios = mysql_num_rows($rsUsuarios);

mysql_select_db($database_Conexion, $Conexion);
$sqlAA = "SELECT CONCAT('[ ',ID,' ] ',nombre,' ',apellido,' - ',email) as nombre FROM `game24hs`.`clientes` ORDER BY ID DESC";
$resultAA = mysql_query($sqlAA, $Conexion) or die(mysql_error());
$rowsAA = array();
while($r = mysql_fetch_assoc($resultAA)) {
    $rowsAA[]=$r['nombre'];
  }

// Si ya seleccionó el slot desde el stock de inicio lo ocupo y listo
$colname_rsSlotDefinido = "-1";
if (isset($_GET['slot'])) {
  $colname_rsSlotDefinido = (get_magic_quotes_gpc()) ? $_GET['slot'] : addslashes($_GET['slot']);
}
$colname_rsStock = "-1";
if (isset($_GET['id'])) {
  $colname_rsStock = (get_magic_quotes_gpc()) ? $_GET['id'] : addslashes($_GET['id']);
}
mysql_select_db($database_Conexion, $Conexion);
$query_rsStock = sprintf("SELECT * FROM stock WHERE ID = %s", $colname_rsStock);
$rsStock = mysql_query($query_rsStock, $Conexion) or die(mysql_error());
$row_rsStock = mysql_fetch_assoc($rsStock);
$totalRows_rsStock = mysql_num_rows($rsStock);

$colname_rsStock2 = "-1";
if (isset($_GET['id'])) {
  $colname_rsStock2 = (get_magic_quotes_gpc()) ? $_GET['id'] : addslashes($_GET['id']);
}
mysql_select_db($database_Conexion, $Conexion);
$query_rsStock2 = sprintf("SELECT ventas.ID AS vtasID, stock_id, precio, encontrado.* FROM ventas
LEFT JOIN ventas_cobro ON ventas.ID = ventas_cobro.ventas_id
RIGHT JOIN
(SELECT ID, titulo, consola, buscando.* FROM stock
RIGHT JOIN
(SELECT stock.ID as stkid, concat(titulo, ':', consola) AS juego FROM stock WHERE ID = %s) as buscando
ON concat(titulo, ':', consola) = buscando.juego
ORDER BY ID desc) AS encontrado
ON stock_id = encontrado.ID
ORDER BY ventas.ID DESC", $colname_rsStock2);
$rsStock2 = mysql_query($query_rsStock2, $Conexion) or die(mysql_error());
$row_rsStock2 = mysql_fetch_assoc($rsStock2);
$totalRows_rsStock2 = mysql_num_rows($rsStock2);

$colname_rsStock3 = "-1";
if (isset($_GET['id'])) {
  $colname_rsStock3 = (get_magic_quotes_gpc()) ? $_GET['id'] : addslashes($_GET['id']);
}
mysql_select_db($database_Conexion, $Conexion);
$query_rsStock3 = sprintf("SELECT stock.ID, titulo, consola, cuentas_id, ventas.stock_id, ventas.slot FROM stock LEFT JOIN ventas ON stock.ID = ventas.stock_id  WHERE stock.ID = %s", $colname_rsStock3);
$rsStock3 = mysql_query($query_rsStock3, $Conexion) or die(mysql_error());
$row_rsStock3 = mysql_fetch_assoc($rsStock3);
$totalRows_rsStock3 = mysql_num_rows($rsStock3);

mysql_select_db($database_Conexion, $Conexion);
$query_rsClientes = "SELECT clientes.ID, apellido, nombre, vta.Q_Vta, email
FROM clientes
LEFT JOIN
(SELECT clientes_id, COUNT(*) AS Q_Vta FROM ventas GROUP BY clientes_id) AS vta
ON clientes.ID = vta.clientes_id
ORDER BY ID DESC";
$rsClientes = mysql_query($query_rsClientes, $Conexion) or die(mysql_error());
$row_rsClientes = mysql_fetch_assoc($rsClientes);
$totalRows_rsClientes = mysql_num_rows($rsClientes);
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
    <title><?php $titulo = 'Insertar venta'; echo $titulo; ?></title>
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
    <div class="col-sm-3">
	<img class="img-rounded pull-right" width="100" src="/img/productos/<?php echo $row_rsStock['consola']."/".$row_rsStock['titulo'];?>.jpg" alt="<?php echo $row_rsStock['titulo']." - ".$row_rsStock['consola'];?>" />
    </div>
    <div class="col-sm-5">
    <form method="post" name="form1" id="form1" action="<?php echo $editFormAction; ?>">
    		
        
                <input id="clientes" type="text" class="form-control typeahead" autocomplete="off" spellcheck="false" value="<?php echo '[ ' .$row_rsClientes['ID'] . ' ] ' . $row_rsClientes['nombre'] . ' ' . $row_rsClientes['apellido'] . ' - '. $row_rsClientes['email'] ?>">              <br /><br />

            
            <input type="text" id="clientes_id" name="clientes_id" value="<?php echo $row_rsClientes['ID']?>" hidden>
            
            <input type="text" name="stock_id" value="<?php echo $row_rsStock['ID'];?>" hidden>
           
            <div class="input-group form-group">
            <span class="input-group-addon"><i class="fa fa-shopping-bag fa-fw"></i></span> 
           <select id="medio_venta" name="medio_venta" class="selectpicker form-control">
            	<option selected value="MercadoLibre" data-content="<span class='label label-warning'>MercadoLibre</span>">MercadoLibre</option>
                <option value="Web" data-content="<span class='label label-info'>Web</span>">Web</option>
                <option value="Mail" data-content="<span class='label label-danger'>Mail</span>">Mail</option>
            </select>                
            </div> 
            
           <div class="input-group form-group">
           <span class="input-group-addon"><i class="fa fa-shopping-bag fa-fw"></i></span> 
           <select id="medio_cobro" name="medio_cobro" class="selectpicker form-control">
            	<option selected value="MercadoPago" data-content="<span class='label label-primary'>MercadoPago</span>">MercadoPago</option>
                <option value="Deposito/Transferencia" data-content="<span class='label label-info'>Deposito/Transferencia</span>">Deposito/Transferencia</option>
            </select>                
            </div> 
			<div class="input-group form-group" id="n_cobro">
              <span class="input-group-addon"><i class="fa fa-hashtag fa-fw"></i></span>
              <input class="form-control" type="text" name="ref_cobro" id="ref_cobro" placeholder="N° de Cobro">             
            </div>
            <span id="faltacobro" style="color:#777;display:none;"><i id="user-result" class="fa fa-pencil" aria-hidden="true"></i> completar</span>
                 
			<br /><br /><br />

			<?php if ($row_rsStock['titulo'] == 'plus-12-meses-slot'):?>
            <div class="input-group form-group">
            <span class="input-group-addon"><i class="fa fa-gamepad fa-fw"></i></span>
            <select name="cons" class="selectpicker form-control">
            	<option selected value="ps4" data-content="<span class='label label-primary'>ps4</span>">ps4</option>
                <option value="ps3" data-content="<span class='label label-warning' style='background-color:#000;'>ps3</span>">ps3</option>
                <option value="psvita" data-content="<span class='label label-info'>psvita</span>">psvita</option>
            </select>
            </div>
            <?php else:?><input type="text" name="cons" value="<?php echo $row_rsStock['consola'];?>" hidden="hidden">
            <?php endif; ?>
            
            
			<?php if(($colname_rsSlotDefinido) && ($colname_rsSlotDefinido != "-1")):?>
			<div class="input-group form-group">
			<span class="input-group-addon"><i class="fa fa-gamepad fa-fw"></i></span>
            <select name="slot" class="selectpicker form-control">
					<option <?php if($colname_rsSlotDefinido == 'Primario'):?>selected="selected"<?php endif;?> value="Primario" data-content="<span class='label label-primary'>Primario</span>">Primario</option>
					<option <?php if($colname_rsSlotDefinido == 'Secundario'):?>selected="selected"<?php endif;?> value="Secundario" data-content="<span class='label label-normal'>Secundario</span>">Secundario</option>
					<option value="No" data-content="<span class='label label-danger'>No</span>">No</option>
				</select>
            </div>
			<?php else:
				if($row_rsStock3['consola'] == 'ps4' OR $row_rsStock3['titulo'] == 'plus-12-meses-slot'): ?>
				<div class="input-group form-group">
				<span class="input-group-addon"><i class="fa fa-gamepad fa-fw"></i></span>
				<select name="slot" class="selectpicker form-control">
					<?php if($row_rsStock3['slot'] != 'Primario'):?>
					<option selected="selected" value="Primario" data-content="<span class='label label-primary'>Primario</span>">Primario</option>
					<?php endif;?>
					<?php if($row_rsStock3['slot'] != 'Secundario'):?>
					<option value="Secundario" data-content="<span class='label label-normal'>Secundario</span>">Secundario</option>
					<?php endif; ?>
					<option value="No" data-content="<span class='label label-danger'>No</span>">No</option>
				</select>
				</div>
				<?php elseif($row_rsStock3['consola'] == 'ps3'):?>
            		<input type="text" name="slot" value="Primario" hidden>
            	<?php elseif(($row_rsStock3['consola'] !== 'ps4') && ($row_rsStock3['consola'] !== 'ps3') && ($row_rsStock3['titulo'] !== 'plus-12-meses-slot')):?><input type="text" name="slot" value="No" hidden>
            	<?php endif;?>
            <?php endif;?>
            
            <br /><br /><br /><br />

			<div class="col-md-5">
            <div class="input-group form-group">
              <span class="input-group-addon">precio</span>
              <input class="form-control" type="text" id="precio" name="precio" value="<?php echo $row_rsStock2['precio']; ?>">
			</div>
          	</div>
            
            <div class="col-md-3" style="opacity:0.7">
            <div class="input-group form-group">
            <select id="porcentaje" class="form-control">
            	<option selected value="0.12">12 %</option>
                <option value="0.0538">6 %</option>
                <option value="0.00">0 %</option>
            </select>  
            </div>
            </div>
            
			<div class="col-md-4">
            <div class="input-group form-group">
                <span class="input-group-addon">comision</span>
                <input class="form-control" type="text" id="comision" name="comision" value="">
            </div>
            </div>
            
            <input type="text" name="estado" value="<?php if(($row_rsStock['consola'] == 'ps') || ($row_rsStock['consola'] == 'steam')):?>listo<?php else:?>pendiente<?php endif;?>" hidden>
                        
			<div class="input-group form-group">
              <span class="input-group-addon"><i class="fa fa-comment fa-fw"></i></span>
              <input class="form-control" type="text" name="Notas" placeholder="Notas de la venta">
            </div>
            
            <div class="input-group form-group">
              <span class="input-group-addon"><i class="fa fa-comment fa-fw"></i></span>
              <input class="form-control" type="text" name="Notas_cobro" placeholder="Notas del cobro">
            </div>

            <button class="btn btn-primary botonero" id="submiter" type="submit">Insertar</button>
        <input type="hidden" name="MM_insert" value="form1">
    </form>
    </div>
    <div class="col-sm-4">
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
    <script  type="text/javascript" src="../js/typeahead.bundle.js"></script>
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
	var isFormValid = false;
	$('#form1').submit(function() {
	
	if (!isFormValid) {
    if($('#ref_cobro').val() == ''){
		document.getElementById("n_cobro").className = "input-group form-group has-error";
		$("#faltacobro").show(300);
		isFormValid = true;
        return false;
	}
    }
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
    <script type="text/javascript">
	// invento para mejorar la carga de clientes y consulta de la lista de clientes
			$("#clientes").blur(function(){
        if(this.value==""){
         this.value = arr[0];   
        }
     });
	window.setInterval(function() {

      cliente = document.getElementById("clientes").value;
	  var String = cliente.substring(cliente.lastIndexOf('[ ')+1,cliente.lastIndexOf(' ]'));
      document.getElementById("clientes_id").value = String;
    },500);
	</script>
	<script type="text/javascript">
	$(document).ready(function () {
		$("form :input").change(function() {
			var val = $('#medio_venta').val();
			var val2 = $('#medio_cobro').val();
			//alert(val2); 
			if (val == "MercadoLibre") {
				$("#porcentaje").html("<option value='0.12'>12 %</option>");
			} else if (val == "Mail" && val2 == "MercadoPago") {
				$("#porcentaje").html("<option value='0.0538'>6 %</option>");
			} else if (val == "Mail" && val2 == "Deposito/Transferencia") {
				$("#porcentaje").html("<option value='0.00'>0 %</option>");
			} else if (val == "Web" && val2 == "MercadoPago") {
				$("#porcentaje").html("<option value='0.0538'>6 %</option>");
			} else if (val == "Web" && val2 == "Deposito/Transferencia") {
				$("#porcentaje").html("<option value='0.00'>0 %</option>");
			}
		});
	});
	</script>
    <script type="text/javascript">
	 $(document).ready(function() {
            $("#titulo-selec").change(function() {
                var titulo = document.getElementById('titulo-selec').value;
				var consola = document.getElementById('consola').value;
				$("#image-swap").attr("src", (titulo !== "" &&  + consola !== "") ? "/img/productos/" + consola + "/" + titulo + ".jpg" : "");
				$('#image-swap').error(function() {
  				$("#image-swap").attr("alt", "no se encuentra");
				});
            });
        });
	$(document).ready(function() {
            $("#consola").change(function() {
                var consola = $(this).val();
				var titulo = document.getElementById('titulo-selec').value;
				$("#image-swap").attr("src", (titulo !== "" &&  + consola !== "") ? "/img/productos/" + consola + "/" + titulo + ".jpg" : "");
				$('#image-swap').error(function() {
  				$("#image-swap").attr("alt", "no se encuentra");
				});
            });
        });
	</script>
    <script type="text/javascript">
	//$(document).ready(function() {
      //m1 = document.getElementById("precio").value;
      //m2 = document.getElementById("porcentaje").value;
      //r = m1*m2;
      //document.getElementById("comision").value = r;
	//});
	window.setInterval(function() {
      m1 = document.getElementById("precio").value;
      m2 = document.getElementById("porcentaje").value;
      r = m1*m2;
      document.getElementById("comision").value = r;
    },500);
	//$("#porcentaje").change(function() {
     // m1 = document.getElementById("precio").value;
      //m2 = document.getElementById("porcentaje").value;
      //r = m1*m2;
      //document.getElementById("comision").value = r;
    //});
	</script>
    <!-- IMAGE PICKER  
    <link rel="stylesheet" href="css/image-picker.css">
    <script src="js/image-picker.min.js"></script>
    <script type="text/javascript">
	$("#consola").imagepicker()
	</script>
    <!-- Latest compiled and minified CSS -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-select/1.12.2/css/bootstrap-select.min.css">
    
    <!-- Latest compiled and minified JavaScript -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-select/1.12.2/js/bootstrap-select.min.js"></script>
    
    
<!-- InstanceEndEditable -->
  </body>
  
<!-- InstanceEnd --></html>
<?php
mysql_free_result($rsUsuarios);

mysql_free_result($rsStock);

mysql_free_result($rsStock2);

mysql_free_result($rsClientes);
?>