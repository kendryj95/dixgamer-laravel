<?php require_once('../Connections/Conexion.php'); ?>
<?php
$MM_authorizedUsers = "Adm,Vendedor";
$MM_donotCheckaccess = "false";
require_once('_autentificacion.php');
?>
<?php

$vendedor = $_SESSION['MM_Username'];
$date = date('Y-m-d H:i:s', time());

if (isset($_GET['cta_id'],$_GET['ultima_cta_id'],$_GET['saldo_usd'],$_GET['saldo_ars'])) {
  $cta_id = (get_magic_quotes_gpc()) ? $_GET['cta_id'] : addslashes($_GET['cta_id']);
  $ultima_cta_id = (get_magic_quotes_gpc()) ? $_GET['ultima_cta_id'] : addslashes($_GET['ultima_cta_id']);
  $saldo_usd = (get_magic_quotes_gpc()) ? $_GET['saldo_usd'] : addslashes($_GET['saldo_usd']);
  $saldo_ars = (get_magic_quotes_gpc()) ? $_GET['saldo_ars'] : addslashes($_GET['saldo_ars']);
	

	mysql_select_db($database_Conexion, $Conexion);
	$query_rsEstado = "SELECT ID, titulo, consola, '$ultima_cta_id', costo_usd FROM stock WHERE usuario = '$vendedor' AND cuentas_id='$ultima_cta_id' ORDER BY ID ASC LIMIT 2";
	$rsEstado = mysql_query($query_rsEstado, $Conexion) or die(mysql_error());
	$row_rsEstado = mysql_fetch_assoc($rsEstado);
	$totalRows_rsEstado = mysql_num_rows($rsEstado);

		/*** Insertar stocks a cuenta basado en la ultima cuenta cargada */
		 do {
			$titulo = $row_rsEstado['titulo'];
			$consola = $row_rsEstado['consola'];
			$costo_usd = $row_rsEstado['costo_usd'];
			$costo = ($row_rsEstado['costo_usd'] / $saldo_usd) * $saldo_ars;

		  $updateSQL3 = "INSERT INTO stock (cuentas_id, consola, titulo, medio_pago, costo_usd, costo, Day, usuario) VALUES ('$cta_id', '$consola', '$titulo', 'Saldo', '$costo_usd', '$costo', '$date', '$vendedor')";
		  mysql_select_db($database_Conexion, $Conexion);
		  $Result3 = mysql_query($updateSQL3, $Conexion) or die(mysql_error());
		} while ($row_rsEstado = mysql_fetch_assoc($rsEstado)); 
	
}
?> 

<?php echo "<script>window.top.location.href = \"cuentas_detalles.php?id=$cta_id\";</script>";
	exit; ?>