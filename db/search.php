<?php
$hostname_Conexion = "mysql.hostinger.com.ar";
$database_Conexion = "u335667088_dagab";
$username_Conexion = "u335667088_zehax";
$password_Conexion = "CEDkj77YDvAr";
error_reporting(E_COMPILE_ERROR|E_ERROR|E_CORE_ERROR);
$Conexion = mysql_connect($hostname_Conexion, $username_Conexion, $password_Conexion) or trigger_error(mysql_error(),E_USER_ERROR); 

$sql = "SELECT nombre FROM clientes WHERE nombre LIKE '%".$_GET['query']."%' LIMIT 20";
$resultset = mysqli_query($conn, $sql) or die("database error:". mysqli_error($conn));
$json = array();
while( $rows = mysqli_fetch_assoc($resultset) ) {
$json[] = $rows["nombre"];
}
echo json_encode($json);
?>
