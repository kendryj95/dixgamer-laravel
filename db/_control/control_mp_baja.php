<?php require_once('../../Connections/Conexion.php'); ?>
<?php
$MM_authorizedUsers = "Adm";
$MM_donotCheckaccess = "false";
require_once('../_autentificacion.php');
?>
<?php
$vendedor = $_SESSION['MM_Username'];

if ((isset($_GET['nro_mov'])) && ($_GET['nro_mov'] != "")) {
$insertSQL1 = sprintf("INSERT INTO mercadopago_baja (nro_mov, concepto, ref_op, importe, saldo) SELECT nro_mov, concat('Anulación de ',concepto), ref_op, (importe * -1), saldo FROM mercadopago WHERE nro_mov=%s",
                       GetSQLValueString($_GET['nro_mov'], "int"));
mysql_select_db($database_Conexion, $Conexion);
$Result11 = mysql_query($insertSQL1, $Conexion) or die(mysql_error());

echo 'éxito';
}

?>