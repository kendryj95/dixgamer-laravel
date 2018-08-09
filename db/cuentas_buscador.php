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

$bd_tabla = "cuentas"; // Tabla donde se harán las búsquedas
$link = $Conexion;

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
    <title><?php $titulo = 'Buscar cuentas'; echo $titulo; ?></title>
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
    <form class="form-inline" name="buscador" method="post" action="cuentas_buscador.php"><br>
    <div class="form-group">
    	<label for="campo">Buscar en: </label><select name="campo" id="campo"><option selected value="mail_fake">mail_fake</option>
    <?php
    
    //Con este query obtendremos los campos por los cuales el usuario puede buscar
    $result = mysql_query("SHOW FIELDS FROM `$bd_tabla`",$link);
    while($row = mysql_fetch_row($result)) {
    // en $row[0] tenemos el nombre del campo
    // de esta manera no necesitamos conocer el nombre de los campos
    // por lo que cualquier tabla nos valdrá
    ?>
    <option value="<?php echo $row[0]; ?>"><?php echo $row[0]; ?></option>
    <?php
    }
    ?>
    </select>
    </div>
    <div class="form-group"><label for="palabra">Palabra(s): </label><input id="palabra" type="text" name="palabra"></div>
    <button type="submit" value="Buscar" name="enviar" class="btn btn-default">Buscar</button>
    </form>

    <?php if(isset($_POST['enviar'])):{?>
    <div class="table-responsive">
    <table border="0" align="center" cellpadding="0" cellspacing="5" class="table table-striped">
    <thead>
              <tr>
                <th>#</th>
                <th>Cuenta</th>
                <th>Pass</th>
                <th>name-surname</th>
              </tr>
            </thead>
            <tbody>
    <?php }endif;?>
    <?php
    
    ////////////////////////////
    // Proceso del Formulario
    ///////////////////////////
    
    if(isset($_POST['enviar'])) {
    // Solo se ejecuta si se ha enviado el formulario
    $query = "SELECT * from $bd_tabla WHERE `{$_POST['campo']}` LIKE '%{$_POST['palabra']}%' ORDER BY ID DESC; ";
    $result = mysql_query($query,$link);
    $found = false; // Si el query ha devuelto algo pondrá a true esta variable
    while ($row = mysql_fetch_array($result)) {
    $found = true;
    echo "<tr>";
    ?>
        <td><a title="Ir a Cuenta" href="cuentas_detalles.php?id=<?php echo $row['ID']; ?>"><?php echo $row['ID']; ?></a></td>
        <td><a title="Ir a Cuenta" href="cuentas_detalles.php?id=<?php echo $row['ID']; ?>"><?php echo $row['mail_fake']; ?></a></td>
        <td><a title="Ir a Cuenta" href="cuentas_detalles.php?id=<?php echo $row['ID']; ?>"><?php echo $row['pass']; ?></a></td>
        <td><a title="Ir a Cuenta" href="cuentas_detalles.php?id=<?php echo $row['ID']; ?>"><?php echo $row['name']; ?>-<?php echo $row['surname']; ?></a></td>
    <?php } echo "</tr>";
    if(!$found) {  echo "No se encontró coincidencia";}
    }  ?>
	<?php if(isset($_POST['enviar'])):{?>
    </tbody>
    </table>
    </div>
    <?php } endif; ?>
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
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/2.1.1/jquery.min.js"></script>
    <script type="text/javascript">
    $(function(){
    $('input[type="text"]').change(function(){
        this.value = $.trim(this.value);
    });
	});
	</script>
<!-- InstanceEndEditable -->
  </body>
  
<!-- InstanceEnd --></html>
<?php
mysql_free_result($rsUsuarios);
?>
