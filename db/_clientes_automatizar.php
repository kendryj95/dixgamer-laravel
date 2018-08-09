<?php
mysql_select_db($database_Conexion, $Conexion);
$query_rsEstadoss = "UPDATE clientes as a
LEFT JOIN
(SELECT 
clientes_id,
COUNT(*) as q_vtas,
DATEDIFF(NOW(), min( Day )) as dias_de_primer_venta
FROM ventas GROUP BY clientes_id) as b
ON a.ID = b.clientes_id
SET
    a.auto = 'si'
WHERE a.auto='no' and ((dias_de_primer_venta > 180) or ((dias_de_primer_venta > 120) and (q_vtas > 1))) 
";
$rsEstadoss = mysql_query($query_rsEstadoss, $Conexion) or die(mysql_error());
?>