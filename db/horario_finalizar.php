<?php require_once('../Connections/Conexion.php'); ?>
<?php
$MM_authorizedUsers = "Adm,Vendedor,Asistente";
$MM_donotCheckaccess = "false";
require_once('_autentificacion.php');
?>
<?php
$vendedor = $_SESSION['MM_Username'];

$day = date('Y-m-d', time());
$date = date('Y-m-d H:i:s', time());
$vendedor = $_SESSION['MM_Username'];
	// esto es lo unico que cambia con horario_iniciar.php , en este caso actualizo el row generado del día de hoy y cargo la hora de finalizacion
    $insertSQL = sprintf("UPDATE horario SET fin='$date' WHERE fin IS NULL AND usuario = '$vendedor'");
					   
  mysql_select_db($database_Conexion, $Conexion);
  $Result1 = mysql_query($insertSQL, $Conexion) or die(mysql_error());

  $deleteGoTo = "horario.php";
  if (isset($_SERVER['QUERY_STRING'])) {
    $deleteGoTo .= (strpos($deleteGoTo, '?')) ? "&" : "?";
    $deleteGoTo .= $_SERVER['QUERY_STRING'];
  
  header(sprintf("Location: %s", $deleteGoTo));
  }
?>