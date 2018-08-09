<?php require_once('../Connections/Conexion.php'); ?>
<?php
// DEFINIR FECHA EN ARGENTINA
date_default_timezone_set('America/Argentina/Buenos_Aires');
?>
<?php
function GetSQLValueString($theValue, $theType, $theDefinedValue = "", $theNotDefinedValue = "") 
{
  $theValue = (!get_magic_quotes_gpc()) ? addslashes($theValue) : $theValue;

  switch ($theType) {
    case "text":
      $theValue = ($theValue != "") ? "'" . $theValue . "'" : "NULL";
      break;    
    case "long":
    case "int":
      $theValue = ($theValue != "") ? intval($theValue) : "NULL";
      break;
    case "double":
      $theValue = ($theValue != "") ? "'" . doubleval($theValue) . "'" : "NULL";
      break;
    case "date":
      $theValue = ($theValue != "") ? "'" . $theValue . "'" : "NULL";
      break;
    case "defined":
      $theValue = ($theValue != "") ? $theDefinedValue : $theNotDefinedValue;
      break;
  }
  return $theValue;
}

    $chars = "abcdefghijkmnopqrstuvwxyzABCDEFGHJKLMNPQRSTUVWXYZ23456789";
    $password = substr( str_shuffle( $chars ), 0, 8 );
$date = date('Y-m-d H:i:s', time());
if ((isset($_GET['id'])) && ($_GET['id'] != "")) {
$insertSQL = sprintf("INSERT INTO cta_pass (cuentas_id, new_pass, Day) VALUES (%s, '$password', '$date')",
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