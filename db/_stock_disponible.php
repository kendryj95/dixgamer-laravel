<?php
if( ($colname_rsCON && ($colname_rsCON == "ps4")) or ($colname_rsTIT && ($colname_rsTIT == "plus-12-meses-slot")) ):
	if($colname_rsSlot && (ucwords($colname_rsSlot) == "Primario")):
		mysql_select_db($database_Conexion, $Conexion);
		$query_rsSTK = sprintf("SELECT ID AS ID_stk, titulo, consola, cuentas_id AS stk_ctas_id, round(AVG(costo),0) as costo, ID_vta, Q_vta, dayvta, Q_vta_pri, Q_vta_sec, COUNT(*) AS Q_Stock
		FROM stock
		### 2018-05-17 de acá quité el left join con reseteo sin sentido
		LEFT JOIN
		(SELECT ventas.ID as ID_vta, stock_id, SUM(case when slot = 'Primario' then 1 else null end) AS Q_vta_pri, SUM(case when slot = 'Secundario' then 1 else null end) AS Q_vta_sec, COUNT(*) AS Q_vta, Day AS dayvta
		FROM ventas
		GROUP BY stock_id) AS vendido
		ON ID = stock_id
		WHERE (consola = 'ps4' OR titulo = 'plus-12-meses-slot') AND (Q_vta_pri IS NULL) AND titulo='%s'
		GROUP BY  consola, titulo
		ORDER BY consola, titulo, ID DESC", $colname_rsTIT);
		$rsSTK = mysql_query($query_rsSTK, $Conexion) or die(mysql_error());
		$row_rsSTK = mysql_fetch_assoc($rsSTK);
		$totalRows_rsSTK = mysql_num_rows($rsSTK);

	elseif($colname_rsSlot && (ucwords($colname_rsSlot) == "Secundario")):
		mysql_select_db($database_Conexion, $Conexion);
		$query_rsSTK = sprintf("SELECT ID AS ID_stk, titulo, consola, cuentas_id AS stk_ctas_id, round(AVG(costo),0) as costo, ID_vta, Q_vta, dayvta, Q_vta_pri, Q_vta_sec, COUNT(*) AS Q_Stock
		FROM stock
		### 2018-05-17 de acá quité el left join con reseteo sin sentido
		LEFT JOIN
		(SELECT ventas.ID as ID_vta, stock_id, SUM(case when slot = 'Primario' then 1 else null end) AS Q_vta_pri, SUM(case when slot = 'Secundario' then 1 else null end) AS Q_vta_sec, COUNT(*) AS Q_vta, Day AS dayvta
		FROM ventas
		GROUP BY stock_id) AS vendido
		ON ID = stock_id
		WHERE (consola = 'ps4' OR titulo = 'plus-12-meses-slot') AND (Q_vta_sec IS NULL) AND titulo='%s'
		GROUP BY  consola, titulo
		ORDER BY consola, titulo, ID DESC", $colname_rsTIT);
		$rsSTK = mysql_query($query_rsSTK, $Conexion) or die(mysql_error());
		$row_rsSTK = mysql_fetch_assoc($rsSTK);
		$totalRows_rsSTK = mysql_num_rows($rsSTK);
	endif;

elseif  ($colname_rsCON && ($colname_rsCON == "ps3")):
	   	mysql_select_db($database_Conexion, $Conexion);
		$query_rsSTK = sprintf("SELECT ID_stk, titulo, consola, stk_ctas_id, dayreset, Q_reset, days_from_reset, Q_vta, round(AVG(costo),0) as costo, SUM(Q_Stock) AS Q_Stock FROM (SELECT ID AS ID_stk, titulo, consola, round(AVG(costo),0) as costo, cuentas_id AS stk_ctas_id, ID_reseteo AS ID_reset, r_cuentas_id AS reset_ctas_id, dayreseteo AS dayreset, reset.Q_reseteado AS Q_reset, DATEDIFF(NOW(), dayreseteo) AS days_from_reset, ID_vta, Q_vta, dayvta, ((2 + (IFNULL(Q_reseteado, 0) * 2)) - IFNULL(Q_vta, 0)) AS Q_Stock
		FROM stock 
		LEFT JOIN
		(SELECT ID AS ID_reseteo, cuentas_id AS r_cuentas_id, COUNT(*) AS Q_reseteado, MAX(Day) AS dayreseteo
		FROM reseteo
		GROUP BY cuentas_id
		ORDER BY ID DESC) AS reset
		ON cuentas_id = r_cuentas_id
		LEFT JOIN
		(SELECT ventas.ID as ID_vta, stock_id, COUNT(*) AS Q_vta, Day AS dayvta
		FROM ventas
		GROUP BY stock_id) AS vendido
		ON ID = stock_id
		WHERE consola = 'ps3' AND (((Q_vta IS NULL) OR (Q_vta < '2')) OR (((Q_vta >= '2') AND (Q_reseteado = FLOOR(Q_vta/2)))))
		GROUP BY ID
		ORDER BY Q_reset, consola, titulo, ID DESC) AS consulta
		WHERE titulo='%s'
		GROUP BY consola, titulo
		ORDER BY consola, titulo, ID_stk", $colname_rsTIT); 
		$rsSTK = mysql_query($query_rsSTK, $Conexion) or die(mysql_error());
		$row_rsSTK = mysql_fetch_assoc($rsSTK);
		$totalRows_rsSTK = mysql_num_rows($rsSTK);
		
else:
		mysql_select_db($database_Conexion, $Conexion);
		$query_rsSTK = sprintf("SELECT ID AS ID_stk, titulo, consola, cuentas_id AS stk_ctas_id, round(AVG(costo),0) as costo, ID_vtas, Q_vta, dayvta, COUNT(*) AS Q_Stock
		FROM stock
		LEFT JOIN
		(SELECT ventas.ID as ID_vtas, stock_id, slot, COUNT(*) AS Q_vta, Day AS dayvta
		FROM ventas
		GROUP BY stock_id
		ORDER BY ID DESC) AS vendido
		ON ID = stock_id
		WHERE (consola != 'ps4') AND (consola != 'ps3') AND (Q_vta IS NULL) AND (titulo != 'plus-12-meses-slot') AND titulo='%s'
		GROUP BY consola, titulo
		ORDER BY consola, titulo DESC", $colname_rsTIT);
		$rsSTK = mysql_query($query_rsSTK, $Conexion) or die(mysql_error());
		$row_rsSTK = mysql_fetch_assoc($rsSTK);
		$totalRows_rsSTK = mysql_num_rows($rsSTK);

endif;
?>
