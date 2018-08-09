<?php require_once('../Connections/Conexion.php'); ?>
<?php
$MM_authorizedUsers = "Adm,Vendedor";
$MM_donotCheckaccess = "false";
require_once('_autentificacion.php');
?>
<?php
$vendedor = $_SESSION['MM_Username'];

	$chars1 = "123456789";
	$chars2 = "ASDFG";
    $chars = "qwert";
	$password = substr( str_shuffle( $chars1 ), 0, 2 );
	$password .= substr( str_shuffle( $chars2 ), 0, 2 );
    $password .= substr( str_shuffle( $chars ), 0, 2 );
	$password .= substr( str_shuffle( $chars1 ), 0, 2 );
$date = date('Y-m-d H:i:s', time());
if ((isset($_GET['id'])) && ($_GET['id'] != "")) {
$insertSQL = sprintf("INSERT INTO cta_pass (cuentas_id, new_pass, Day, usuario) VALUES (%s, '$password', '$date', '$vendedor')",
                       GetSQLValueString($_GET['id'], "int"));
mysql_select_db($database_Conexion, $Conexion);
$Result1 = mysql_query($insertSQL, $Conexion) or die(mysql_error());
}
if ((isset($_GET['id'])) && ($_GET['id'] != "")) {
  $deleteSQL = sprintf("UPDATE cuentas SET pass='$password' WHERE ID=%s",
                       GetSQLValueString($_GET['id'], "int"));

  mysql_select_db($database_Conexion, $Conexion);
  $Result1 = mysql_query($deleteSQL, $Conexion) or die(mysql_error());

  $deleteGoTo = "cuentas_detalles.php";
  if (isset($_SERVER['QUERY_STRING'])) {
    $deleteGoTo .= (strpos($deleteGoTo, '?')) ? "&" : "?";
    $deleteGoTo .= $_SERVER['QUERY_STRING'];
  }
  header(sprintf("Location: %s", $deleteGoTo));
}

?>