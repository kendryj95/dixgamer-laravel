<?php require_once('../Connections/Conexion.php'); ?>
<?php
$MM_authorizedUsers = "Adm,Vendedor";
$MM_donotCheckaccess = "false";
require_once('_autentificacion.php');
?>
<?php
$vendedor = $_SESSION['MM_Username'];
$date = date('Y-m-d H:i:s', time());
if (isset($_GET['cta_id'],$_GET['consola'],$_GET['titulo'],$_GET['costo_usd'])) {
  $cta_id = (get_magic_quotes_gpc()) ? $_GET['cta_id'] : addslashes($_GET['cta_id']);
  $consola = (get_magic_quotes_gpc()) ? $_GET['consola'] : addslashes($_GET['consola']);
  $titulo = (get_magic_quotes_gpc()) ? $_GET['titulo'] : addslashes($_GET['titulo']);
  $costo_usd = (get_magic_quotes_gpc()) ? $_GET['costo_usd'] : addslashes($_GET['costo_usd']);

mysql_select_db($database_Conexion, $Conexion);
$query_rsSaldo = sprintf("SELECT SUM(costo) as costo, SUM(costo_usd) as costo_usd FROM
(SELECT ex_stock_id as stk_id, 'carga' as concepto, cuentas_id, costo, costo_usd, code, code_prov, n_order, usuario FROM saldo WHERE cuentas_id = %s
UNION ALL
SELECT ID as stk_id, 'descarga' as concepto, cuentas_id, (-1 * SUM(costo)) as costo, (-1 * SUM(costo_usd)) as costo_usd, '' as code, '' as code_prov, '' as n_order, usuario FROM stock WHERE cuentas_id = %s)
As resultado", $cta_id, $cta_id);
$rsSaldo = mysql_query($query_rsSaldo, $Conexion) or die(mysql_error());
$row_rsSaldo = mysql_fetch_assoc($rsSaldo);
$totalRows_rsSaldo = mysql_num_rows($rsSaldo);
	
mysql_select_db($database_Conexion, $Conexion);
$query_rsGastado = sprintf("SELECT cuentas_id, SUM(costo_usd) as costo_usd, SUM(costo) as costo
FROM stock 
WHERE cuentas_id = %s
GROUP BY cuentas_id
ORDER BY ID DESC", $cta_id);
$rsGastado = mysql_query($query_rsGastado, $Conexion) or die(mysql_error());
$row_rsGastado = mysql_fetch_assoc($rsGastado);
$totalRows_rsGastado = mysql_num_rows($rsGastado);

/*** Si hay un stock -> costo usd y costo ars = lo que queda en la cuenta.
Else: Si no hay stock costo usd = el ultimo costo usd de ese mismo juego, y costo ars = a proporcional de usd */
if ($row_rsGastado):
$costo_usd = $row_rsSaldo['costo_usd'];
$costo = $row_rsSaldo['costo'];
else:	
$costo = ($costo_usd / $row_rsSaldo['costo_usd']) * $row_rsSaldo['costo'];
endif;

$insertSQL = "INSERT INTO stock (cuentas_id, consola, titulo, medio_pago, costo_usd, costo, Day, usuario) VALUES ('$cta_id', '$consola', '$titulo', 'Saldo', '$costo_usd', '$costo', '$date', '$vendedor')";
mysql_select_db($database_Conexion, $Conexion);
$Result1 = mysql_query($insertSQL, $Conexion) or die(mysql_error());

	// Script para redirigir el top 
echo "<script>window.top.location.href = \"cuentas_detalles.php?id=$cta_id\";</script>";
	exit;

}

?>