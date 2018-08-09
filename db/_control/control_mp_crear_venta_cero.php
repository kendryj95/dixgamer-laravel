<?php require_once('../../Connections/Conexion.php'); ?>
<?php
$MM_authorizedUsers = "Adm,Vendedor";
$MM_donotCheckaccess = "false";
require_once('../_autentificacion.php');
?>
<?php
$vendedor = $_SESSION['MM_Username'];

$ref_cobro = "-1";
if (isset($_GET['ref_cobro'])) {
  $ref_cobro = (get_magic_quotes_gpc()) ? $_GET['ref_cobro'] : addslashes($_GET['ref_cobro']);}
  
$importe = "-1";
if (isset($_GET['importe'])) {
  $importe = (get_magic_quotes_gpc()) ? $_GET['importe'] : addslashes($_GET['importe']);}
  
$colname_rsCliente = "-1";
if (isset($_GET['c_id'])) {
  $colname_rsCliente = (get_magic_quotes_gpc()) ? $_GET['c_id'] : addslashes($_GET['c_id']);}

$date = date('Y-m-d H:i:s', time());
	
	$insertSQL = "INSERT INTO ventas (clientes_id, stock_id, cons, slot, medio_venta, estado, Day, usuario, Notas) VALUES ('$colname_rsCliente', '0', 'ps', 'No', 'Mail', 'listo', '$date', '$vendedor', 'Creado por control para reflejar realidad de cobro')";
	mysql_select_db($database_Conexion, $Conexion);
	$Result1 = mysql_query($insertSQL, $Conexion) or die(mysql_error());
	
	$ventaid = mysql_insert_id(); // ultimo ID de una consulta INSERT , en este caso seria el ID de la ultima venta creada
	$importeTotal = ($importe / 0.9446);
	$comision = ($importeTotal * 0.0538);
	
  	$insertSQL222 = "INSERT INTO ventas_cobro (ventas_id, medio_cobro, ref_cobro, precio, comision, Day, usuario) VALUES ('$ventaid', 'MercadoPago', '$ref_cobro', '$importeTotal', '$comision', '$date', '$vendedor')";
	mysql_select_db($database_Conexion, $Conexion);
    $Result222 = mysql_query($insertSQL222, $Conexion) or die(mysql_error());

	  
// modifico el lugar de ésto y le agrego el exit al final para ver si deja de insertar dobles ventas 04/10/2017
$deleteGoTo = "clientes_detalles.php?id=";
$deleteGoTo .= $colname_rsCliente;
header(sprintf("Location: %s", $deleteGoTo));
exit;
?>