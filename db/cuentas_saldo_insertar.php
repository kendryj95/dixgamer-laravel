<?php require_once('../Connections/Conexion.php'); ?>
<?php
$MM_authorizedUsers = "Adm,Vendedor";
$MM_donotCheckaccess = "false";
require_once('_autentificacion.php');
?>
<?php

$vendedor = $_SESSION['MM_Username'];
$date = date('Y-m-d H:i:s', time());
if (isset($_GET['cta_id'],$_GET['titulo'],$_GET['consola'])) {
  $colname_rsClient = (get_magic_quotes_gpc()) ? $_GET['cta_id'] : addslashes($_GET['cta_id']);
  $colname_rsTIT = (get_magic_quotes_gpc()) ? $_GET['titulo'] : addslashes($_GET['titulo']);
  $colname_rsCON = (get_magic_quotes_gpc()) ? $_GET['consola'] : addslashes($_GET['consola']);

//cargo el stock disponible en este mismo segundo y busco el producto que quiero asignar
require_once('_stock_disponible.php');
$stock_id = $row_rsSTK['ID_stk'];

$insertSQL = sprintf("INSERT INTO saldo (cuentas_id, ex_stock_id, titulo, consola, medio_pago, costo_usd, costo, code, code_prov, n_order, Day, ex_Day_stock, Notas, usuario, ex_usuario) SELECT %s, ID, titulo, consola, medio_pago, costo_usd, costo, code, code_prov, n_order, '$date', Day, Notas, '$vendedor', usuario FROM stock WHERE ID=%s", $colname_rsClient, $stock_id);
mysql_select_db($database_Conexion, $Conexion);
$Result1 = mysql_query($insertSQL, $Conexion) or die(mysql_error());

	$updateSQL2 = sprintf("DELETE FROM stock WHERE ID=%s", $stock_id);
	mysql_select_db($database_Conexion, $Conexion);
	$Result12 = mysql_query($updateSQL2, $Conexion) or die(mysql_error());

	// Script para redirigir el top 
echo "<script>window.top.location.href = \"cuentas_detalles.php?id=$colname_rsClient\";</script>";
	exit;
  //$deleteGoTo = "cuentas_detalles.php";
  //if (isset($_SERVER['QUERY_STRING'])) {
    //$deleteGoTo .= (strpos($deleteGoTo, '?')) ? "&" : "?";
	//$deleteGoTo .= "id=";
	//$deleteGoTo .= $colname_rsClient;
    //$deleteGoTo .= $_SERVER['QUERY_STRING'];
  //}
  //header(sprintf("Location: %s", $deleteGoTo));
}

?>