<?php require_once('../Connections/Conexion.php'); ?>
<?php
$MM_authorizedUsers = "Adm,Vendedor";
$MM_donotCheckaccess = "false";
require_once('_autentificacion.php');
?>
<?php
$vendedor = $_SESSION['MM_Username'];
$date = date('Y-m-d H:i:s', time());
$colname_rsCuentas = "-1";
if (isset($_GET['c_id'])) {
  $colname_rsCuentas = (get_magic_quotes_gpc()) ? $_GET['c_id'] : addslashes($_GET['c_id']);
}
if ((isset($_GET['id'])) && ($_GET['id'] != "")) {
$insertSQL = sprintf("INSERT INTO stock (ID, titulo, consola, cuentas_id, medio_pago, costo_usd, costo, code, code_prov, n_order, Day, Notas, usuario) SELECT ex_stock_id, titulo, consola, NULL, medio_pago, costo_usd, costo, code, code_prov, n_order, '$date', 'Devuelto de cta #$colname_rsCuentas', ex_usuario FROM saldo WHERE ex_stock_id=%s", GetSQLValueString($_GET['id'], "int"));
mysql_select_db($database_Conexion, $Conexion);
$Result1 = mysql_query($insertSQL, $Conexion) or die(mysql_error());
	
	$updateSQL2 = sprintf("DELETE FROM saldo WHERE ex_stock_id=%s", GetSQLValueString($_GET['id'], "int"));
	mysql_select_db($database_Conexion, $Conexion);
	$Result12 = mysql_query($updateSQL2, $Conexion) or die(mysql_error());
}

  $deleteGoTo = "cuentas_detalles.php";
  if (isset($_SERVER['QUERY_STRING'])) {
    $deleteGoTo .= (strpos($deleteGoTo, '?')) ? "&" : "?";
	$deleteGoTo .= "id=";
	$deleteGoTo .= $colname_rsCuentas;
    //$deleteGoTo .= $_SERVER['QUERY_STRING'];
  }

  header(sprintf("Location: %s", $deleteGoTo));

?>