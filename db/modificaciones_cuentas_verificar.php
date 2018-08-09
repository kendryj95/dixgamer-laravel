<?php require_once('../Connections/Conexion.php'); ?>
<?php
$MM_authorizedUsers = "Adm,Vendedor";
$MM_donotCheckaccess = "false";
require_once('_autentificacion.php');
?>
<?php
$vendedor = $_SESSION['MM_Username'];


if ((isset($_GET['ID'])) && ($_GET['ID'] != "")) {
$insertSQL = sprintf("UPDATE cuentas_modif SET verificado='si', verificador='$vendedor' WHERE cuentas_id=%s",
                       GetSQLValueString($_GET['ID'], "int"));
mysql_select_db($database_Conexion, $Conexion);
$Result1 = mysql_query($insertSQL, $Conexion) or die(mysql_error());

  $deleteGoTo = "modificaciones_control.php";
  //if (isset($_SERVER['QUERY_STRING'])) {
    //$deleteGoTo .= (strpos($deleteGoTo, '?')) ? "&" : "?";
    //$deleteGoTo .= $_SERVER['QUERY_STRING'];
  //}
  header(sprintf("Location: %s", $deleteGoTo));
}

?>