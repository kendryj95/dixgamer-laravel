<?php
require_once('../Connections/Conexion.php');

// DEFINIR FECHA EN ARGENTINA
date_default_timezone_set('America/Argentina/Buenos_Aires');

mysql_select_db($database_Conexion, $Conexion);
// selecciono todos los productos (post_id) que todavía no tienen un campo personalizado (meta_key) "_video_url" osea que no tienen asignado el ID de video de YT y por lo tanto no sincronizan aún con ML
$query_rsEstado = "SELECT * FROM
(SELECT * FROM cbgw_postmeta WHERE meta_key='wc_productdata_options' AND meta_value LIKE '%youtube%') as sinvideo
LEFT JOIN
(SELECT post_id as cv_post_id, meta_key as cv_meta_key, meta_value as cv_meta_value FROM cbgw_postmeta WHERE meta_key='_video_url' AND meta_value !='') as convideo
ON sinvideo.post_id = convideo.cv_post_id
WHERE convideo.cv_meta_value IS NOT NULL";
$rsEstado = mysql_query($query_rsEstado, $Conexion) or die(mysql_error());
$row_rsEstado = mysql_fetch_assoc($rsEstado);
$totalRows_rsEstado = mysql_num_rows($rsEstado);
?>

<?php do {
if ($row_rsEstado['post_id']) {
  
	$post_id = $row_rsEstado['post_id'];
	$meta_key = "_video_url";
	$video = $row_rsEstado['meta_value'];
	parse_str( parse_url( $video, PHP_URL_QUERY ), $my_array_of_vars );
	$video_trabajando = $my_array_of_vars['v'];
	$video_id = substr($video_trabajando, 0, strpos($video_trabajando, '"'));
	//obtengo el ID del video de YOUTUB que es lo que me pide ML excatamente
	
	//Si el video actual para WS no es el mismo que el video en el campo extra de WooCommerce -> Actualizo el video
	if(("" !== trim($video_id)) && ($video_id != $row_rsEstado['cv_meta_value'])){
	$updateSQL = "UPDATE cbgw_postmeta SET meta_value='$video_id' WHERE post_id='$post_id' AND meta_key='$meta_key' AND meta_value!='$video_id'";
	mysql_select_db($database_Conexion, $Conexion);
	$Result2 = mysql_query($updateSQL, $Conexion) or die(mysql_error());
	echo 'producto ' . $post_id. ' actualizado video id <em>' . $video_id . '</em> a WS<br>' ;
	}

}
} while ($row_rsEstado = mysql_fetch_assoc($rsEstado)); ?> 
<?php
mysql_free_result($rsEstado);
?>