<?php require_once('../Connections/Conexion.php'); ?>
<?php
$MM_authorizedUsers = "Adm";
$MM_donotCheckaccess = "false";
require_once('_autentificacion.php');
?>
<?php
if ((isset($_GET['id'])) && ($_GET['id'] != "") && ($_GET['a'] != "")) {
$insertSQL = sprintf("UPDATE clientes SET auto=%s WHERE ID=%s",
		   GetSQLValueString($_GET['a'], "text"),
           GetSQLValueString($_GET['id'], "int"));
mysql_select_db($database_Conexion, $Conexion);
$Result1 = mysql_query($insertSQL, $Conexion) or die(mysql_error());

  $deleteGoTo = "clientes_detalles.php";
  if (isset($_SERVER['QUERY_STRING'])) {
    $deleteGoTo .= (strpos($deleteGoTo, '?')) ? "&" : "?";
    $deleteGoTo .= $_SERVER['QUERY_STRING'];
  }
  header(sprintf("Location: %s", $deleteGoTo));
}

?>