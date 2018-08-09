<?php require_once('../../Connections/Conexion.php'); ?>
<?php
$MM_authorizedUsers = "Adm,Vendedor";
$MM_donotCheckaccess = "false";
require_once('../_autentificacion.php');
?>
<?php

$ref_op = "-1";
if (isset($_GET['ref_op'])) {
  $ref_op = (get_magic_quotes_gpc()) ? $_GET['ref_op'] : addslashes($_GET['ref_op']);
}
mysql_select_db($database_Conexion, $Conexion);
$query_Cobro = sprintf("SELECT * FROM mercadopago WHERE ref_op=%s AND importe > 0", $ref_op);
$Cobro = mysql_query($query_Cobro, $Conexion) or die(mysql_error());
$row_Cobro = mysql_fetch_assoc($Cobro);
$totalRows_Cobro = mysql_num_rows($Cobro);

mysql_select_db($database_Conexion, $Conexion);
$query_Comis = sprintf("SELECT * FROM mercadopago WHERE ref_op=%s AND (importe < 0.00) AND (concepto = 'Cargo Mercado Pago' or concepto = 'Costo de Mercado Pago' or concepto = 'Comisión por venta de Mercado Libre')", $ref_op);
$Comis = mysql_query($query_Comis, $Conexion) or die(mysql_error());
$row_Comis = mysql_fetch_assoc($Comis);
$totalRows_Comis = mysql_num_rows($Comis);

mysql_select_db($database_Conexion, $Conexion);
$query_Actual = sprintf("SELECT * FROM ventas_cobro WHERE ref_cobro=%s", $ref_op);
$Actual = mysql_query($query_Actual, $Conexion) or die(mysql_error());
$row_Actual = mysql_fetch_assoc($Actual);
$totalRows_Actual = mysql_num_rows($Actual);

$actual_cobro = $row_Actual['precio'];
$actual_comision = $row_Actual['comision'];

$cobrado = $row_Cobro['importe'];
$comision = (-1.00 * $row_Comis['importe']);

$insertSQL = sprintf("UPDATE ventas_cobro SET precio='$cobrado', comision='$comision' WHERE ref_cobro=%s",$ref_op);
mysql_select_db($database_Conexion, $Conexion);
$Result1 = mysql_query($insertSQL, $Conexion) or die(mysql_error());

echo 'éxito<br>';
if (($cobrado - $actual_cobro) != 0): echo 'importe de ' . $actual_cobro . ' a ' . $cobrado; endif;
if (($comision - $actual_comision) != 0): echo '<br>comision de ' . $actual_comision . ' a ' . $comision; endif;

?>