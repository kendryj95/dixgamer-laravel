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

if (isset($_GET['c_id'])) {
  $colname_rsClient = (get_magic_quotes_gpc()) ? $_GET['c_id'] : addslashes($_GET['c_id']);
}

$vendedor = $_SESSION['MM_Username'];
if ($vendedor === "Victor") { $verificado = 'si';} else { $verificado = 'no';}
$date = date('Y-m-d H:i:s', time());

// 2018-05-21 Agrego la verificación -> ID de cliente tiene que ser mayor a cero y numérico
if ((isset($_POST["MM_update"])) && ($_POST["MM_update"] == "form1") && ((GetSQLValueString($_POST['clientes_id'], "int")) > 0))  {
	$modificaciones = sprintf("INSERT INTO ventas_modif(ventas_id, clientes_id, stock_id, order_item_id, cons, slot, medio_venta, Day, Notas, verificado, usuario ) SELECT ID, clientes_id, stock_id, order_item_id, cons, slot, medio_venta, '$date', Notas, '$verificado', '$vendedor' FROM ventas WHERE ID=%s",GetSQLValueString($_POST['ID'], "int"));
	mysql_select_db($database_Conexion, $Conexion);
  $ResultadoModif = mysql_query($modificaciones, $Conexion) or die(mysql_error());
	
  $updateSQL = sprintf("UPDATE ventas SET clientes_id=%s, order_item_id=%s, medio_venta=%s, Notas=%s WHERE ID=%s", 
                       GetSQLValueString($_POST['clientes_id'], "int"),
					   GetSQLValueString($_POST['order_item_id'], "int"),
                       GetSQLValueString($_POST['medio_venta'], "text"),
					   GetSQLValueString($_POST['Notas'], "text"),
					   GetSQLValueString($_POST['ID'], "int"));

  mysql_select_db($database_Conexion, $Conexion);
  $Result1 = mysql_query($updateSQL, $Conexion) or die(mysql_error());

	$cliente=GetSQLValueString($_POST['clientes_id'], "int");
	// Script para redirigir el top 
	echo "<script>window.top.location.href = \"clientes_detalles.php?id=$cliente\";</script>";
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

$colname_rsClientes = "-1";
if (isset($_GET['id'])) {
  $colname_rsClientes = (get_magic_quotes_gpc()) ? $_GET['id'] : addslashes($_GET['id']);
}

mysql_select_db($database_Conexion, $Conexion);
$query_rsClientes = sprintf("SELECT ventas.ID, clientes_id, order_item_id, medio_venta, ventas.Notas, nombre, apellido, email FROM ventas LEFT JOIN clientes on ventas.clientes_id = clientes.ID WHERE ventas.ID = %s", $colname_rsClientes);
$rsClientes = mysql_query($query_rsClientes, $Conexion) or die(mysql_error());
$row_rsClientes = mysql_fetch_assoc($rsClientes);
$totalRows_rsClientes = mysql_num_rows($rsClientes);

mysql_select_db($database_Conexion, $Conexion);
$query_rsClientesListado = "SELECT clientes.ID, apellido, nombre, vta.Q_Vta, email
FROM clientes
LEFT JOIN
(SELECT clientes_id, COUNT(*) AS Q_Vta FROM ventas GROUP BY clientes_id) AS vta
ON clientes.ID = vta.clientes_id
ORDER BY ID DESC";
$rsClientesListado = mysql_query($query_rsClientesListado, $Conexion) or die(mysql_error());
$row_rsClientesListado = mysql_fetch_assoc($rsClientesListado);
$totalRows_rsClientesListado = mysql_num_rows($rsClientesListado);
?>
<!DOCTYPE html>
<html lang="es">
  <head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <!-- The above 3 meta tags *must* come first in the head; any other head content must come *after* these tags -->
    <meta name="description" content="base de datos">
    <meta name="author" content="vic">
    <link rel="icon" href="favicon.ico">
  <link rel="stylesheet" href="//code.jquery.com/ui/1.11.4/themes/smoothness/jquery-ui.css">
<script src="//code.jquery.com/jquery-1.10.2.js"></script>
<script src="//code.jquery.com/ui/1.11.4/jquery-ui.js"></script>
<script src="http://ajax.aspnetcdn.com/ajax/jquery.validate/1.11.0/jquery.validate.min.js"></script>
    <title><?php $titulo = 'Modificar Venta'; echo $titulo; ?></title>
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
  <!-- antes que termine head-->
</head>

  <body style="padding: 0px;">

    <div class="container">
	<h1><?php echo $titulo; ?></h1>
<div class="row">
    <div class="col-sm-2">
    </div>
    <div class="col-sm-8">
    <form method="post" name="form1" action="<?php echo $editFormAction; ?>">
    
    		            
            <input id="clientes" type="text" class="form-control typeahead" autocomplete="off" spellcheck="false" value="<?php echo '[ ' .$row_rsClientes['clientes_id'] . ' ] ' . $row_rsClientes['nombre'] . ' ' . $row_rsClientes['apellido'] . ' - '. $row_rsClientes['email'] ?>">              <br /><br />

            
            <input type="text" id="clientes_id" name="clientes_id" value="<?php echo $row_rsClientes['clientes_id']?>" hidden>
            
			<?php 
				if (strpos($row_rsClientes['medio_venta'], 'Mercado') !== false): $colorventa = 'warning';
				elseif (strpos($row_rsClientes['medio_venta'], 'Web') !== false): $colorventa = 'info';
				elseif (strpos($row_rsClientes['medio_venta'], 'Mail') !== false): $colorventa = 'danger';
			endif;?>
            <div class="input-group form-group">
            <span class="input-group-addon"><i class="fa fa-shopping-bag fa-fw"></i></span> 
           <select name="medio_venta" class="selectpicker form-control">
           		<option value="<?php echo $row_rsClientes['medio_venta']; ?>" selected="selected" data-content="<span class='label label-<?php echo $colorventa; ?>'><?php echo $row_rsClientes['medio_venta']; ?></span> - <span class='label label-success'>Actual</span>"><?php echo $row_rsClientes['medio_venta']; ?> - Actual</option>
           		<option value="MercadoLibre" data-content="<span class='label label-warning'>MercadoLibre</span>">MercadoLibre</option>
                <option value="Mail" data-content="<span class='label label-danger'>Mail</span>">Mail</option>
                <option value="Web" data-content="<span class='label label-info'>Web</span>">Web</option>
            </select>                
            </div> 
            
            <div class="input-group form-group">
              <span class="input-group-addon"><i class="fa fa-shopping-cart fa-fw"></i></span>
              <input value="<?php echo $row_rsClientes['order_item_id']; ?>" class="form-control" type="text" name="order_item_id" placeholder="order_item_id">
              <span class="input-group-addon"><em class="text-muted">order item id</em></span>
            </div>
            
            <em class="text-muted"><?php echo $row_rsClientes['Notas']; ?></em>
            <div class="input-group form-group">
              <span class="input-group-addon"><i class="fa fa-comment fa-fw"></i></span>
              <input value="<?php echo $row_rsClientes['Notas']; ?>" class="form-control" type="text" name="Notas" placeholder="Notas de la venta">
            </div>
            <button class="btn btn-primary" type="submit">Modificar</button>
            <input type="reset">
        <input type="hidden" name="MM_update" value="form1">
        <input type="hidden" name="ID" value="<?php echo $row_rsClientes['ID']; ?>">
    </form>
    </div>
    <div class="col-sm-2">
    </div>
    </div>
				<br /><br />
     <!--/row-->
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
	</script> <script  type="text/javascript" src="js/typeahead.bundle.js"></script>
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
	$(document).ready(function() {
      m1 = document.getElementById("precio").value;
      m2 = document.getElementById("porcentaje").value;
      r = m1*m2;
      document.getElementById("comision").value = r;
	});
	$("#precio").keyup(function() {
      m1 = document.getElementById("precio").value;
      m2 = document.getElementById("porcentaje").value;
      r = m1*m2;
      document.getElementById("comision").value = r;
    });
	$("#porcentaje").change(function() {
      m1 = document.getElementById("precio").value;
      m2 = document.getElementById("porcentaje").value;
      r = m1*m2;
      document.getElementById("comision").value = r;
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
  <!-- extras de script y demás yerbas -->
</body>
</html>
<?php
mysql_free_result($rsUsuarios);

mysql_free_result($rsClientes);

mysql_free_result($rsClientesListado);

?>