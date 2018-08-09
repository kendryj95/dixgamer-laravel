<?php require_once('../../Connections/Conexion.php'); ?>
<?php
$MM_authorizedUsers = "Adm";
$MM_donotCheckaccess = "false";
require_once('../_autentificacion.php');
?>
<?php
$vendedor = $_SESSION['MM_Username'];

if (((isset($_GET['dif'])) && ($_GET['dif'] != "")) && ((isset($_GET['ref_cobro'])) && ($_GET['ref_cobro'] != ""))) {
$insertSQL1 = sprintf("INSERT INTO mercadopago_baja (concepto, ref_op, importe, saldo) SELECT 'Regala plata - No descargó', ref_op, (%s * -1), saldo FROM mercadopago WHERE ref_op=%s LIMIT 1",
					   GetSQLValueString($_GET['dif'], "double"),
                       GetSQLValueString($_GET['ref_cobro'], "int"));
mysql_select_db($database_Conexion, $Conexion);
$Result11 = mysql_query($insertSQL1, $Conexion) or die(mysql_error());

  $deleteGoTo = "inicio.php";
  header(sprintf("Location: %s", $deleteGoTo));
}

?>