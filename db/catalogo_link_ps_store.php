<?php require_once('../Connections/Conexion.php'); ?>
<?php
$MM_authorizedUsers = "Adm,Vendedor";
$MM_donotCheckaccess = "false";
require_once('_autentificacion.php');
?>
<?php 
$editFormAction = $_SERVER['PHP_SELF'];
if (isset($_SERVER['QUERY_STRING'])) {
  $editFormAction .= "?" . htmlentities($_SERVER['QUERY_STRING']);
}

if ((isset($_POST["MM_insert"])) && ($_POST["MM_insert"] == "form1")) {
  $insertSQL = sprintf("INSERT INTO cbgw_postmeta (post_id, meta_key, meta_value) VALUES (%s, 'link_ps', %s)",
                       GetSQLValueString($_POST['id'], "int"),
                       GetSQLValueString($_POST['link'], "text"));

  mysql_select_db($database_Conexion, $Conexion);
  $Result1 = mysql_query($insertSQL, $Conexion) or die(mysql_error());

  $insertGoTo = "catalogo_link_ps_store.php#";
  $insertGoTo .= GetSQLValueString($_POST['id'], "int");
  //if (isset($_SERVER['QUERY_STRING'])) {
    //$insertGoTo .= (strpos($insertGoTo, '?')) ? "&" : "?";
    //$insertGoTo .= $_SERVER['QUERY_STRING'];
  //}
  header(sprintf("Location: %s", $insertGoTo));
}
?>
<?php 
/*** SUPER QUERY DE STOCK ***/

mysql_select_db($database_Conexion, $Conexion);
$query_rsCXP = "select
    p.ID,
    REPLACE(REPLACE(REPLACE(REPLACE(TRIM(LCASE(p.post_title)), ' ', '-'), '''', ''), '’', ''), '.', '') AS titulo,
    max( CASE WHEN pm.meta_key = 'consola' and  p.ID = pm.post_id THEN pm.meta_value END ) as consola,
	GROUP_CONCAT( CASE WHEN pm.meta_key = 'link_ps' and p.ID = pm.post_id THEN pm.meta_value END ) as link_ps,
    post_status
from
    cbgw_posts as p
LEFT JOIN
    cbgw_postmeta as pm
ON
   p.ID = pm.post_id
where
    post_type = 'product' and
    post_status = 'publish'
group BY
	p.ID
order BY
consola DESC, titulo ASC";
$rsCXP = mysql_query($query_rsCXP, $Conexion) or die(mysql_error());
$row_rsCXP = mysql_fetch_assoc($rsCXP);
$totalRows_rsCXP = mysql_num_rows($rsCXP);
?>
<!DOCTYPE html>
<html lang="es"><!-- InstanceBegin template="/Templates/db.dwt.php" codeOutsideHTMLIsLocked="false" -->
  <head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <!-- The above 3 meta tags *must* come first in the head; any other head content must come *after* these tags -->
    <meta name="description" content="base de datos">
    <meta name="author" content="vic">
    <link rel="icon" href="favicon.ico">
	
<!-- InstanceBeginEditable name="doctitle" -->
    <title><?php $titulo = 'Listado de Links a PS Store'; echo $titulo; ?></title>
<!-- InstanceEndEditable -->

	  
    <!-- Font Awesome style desde mi servidor -->
    <link href="css/font-awesome.min.css" rel="stylesheet">
    
    <!-- link a mi css -->
    <link href="css/bootstrap.css" rel="stylesheet">
    
    <!-- Bootstrap SITE CSS -->
    <link href="css/site.css" rel="stylesheet">

    <!-- Custom styles for this template -->
    <link href="css/offcanvas.css" rel="stylesheet">
    
	<!-- 2017-12-30 Agrego nuevo css de BootFLAT --> 
    <link href="css/bootflat.css" rel="stylesheet">
	  
	<!-- Estilo personalizado por mi -->
	<link href="css/personalizado.css" rel="stylesheet">
	 	
    <!--- BootFLAT core CSS 
    <link href="../db/css/site.min.css" rel="stylesheet">
	-->

    <!-- InstanceBeginEditable name="head" -->
    <!-- antes que termine head-->
<!-- InstanceEndEditable -->
  </head>

  <body>
  <?php include('_barra_nav.php'); ?>

    <div class="container">
	<h1><?php echo $titulo; ?></h1>
    <!-- InstanceBeginEditable name="body" -->
	<table class="table table-striped table-responsive" border="0" cellpadding="0" cellspacing="5">
      <tr>
        <th width="50">Cover</th>
        <th>Titulo</th>
        <th>Links PS</th>
        <?php if ($_SESSION['MM_UserGroup'] ==  'Adm'):?>
        <th>Agregar Link</th>
        <th>post_id</th>
		<?php endif;?>
      </tr>
      <?php $i = 0; do { ?>
      <?php $ID = $row_rsCXP['ID'];?>
      <tr id="<?php echo $row_rsCXP['ID'];?>">
        <td><img class="img-rounded" width="50" id="image-swap" src="/img/productos/<?php echo $row_rsCXP['consola']."/".$row_rsCXP['titulo'].".jpg";?>"alt="" /></td>
        <td title="<?php echo $row_rsCXP['titulo']; ?> (<?php echo $row_rsCXP['consola']; ?>)"><?php echo str_replace("-", " ", $row_rsCXP['titulo']); ?> <?php echo $row_rsCXP['consola']; if (strpos($row_rsCXP['slot'], 'primario') !== false): echo ' 1ro'; endif; ?></td>  
        <td title="<?php echo $row_rsCXP['link_ps']; ?>"> <?php if(($row_rsCXP['link_ps']) && $row_rsCXP['link_ps'] !== ""): $array = (explode(',', $row_rsCXP['link_ps'], 10)); foreach ($array as $valor) { echo "<a title='ver en la tienda de PS' target='_blank' href='$valor'><i aria-hidden='true' class='fa fa-external-link'></i> Tienda PS</a><br />"; } else: '<span class="label label-danger">NO HAY LINK</span>'; endif;?> 
        
			
		<?php 
		
		//$updateSQL = sprintf("UPDATE cbgw_posts SET post_excerpt='%s' WHERE ID=%s",$plain_text,$ID);
		//mysql_select_db($database_Conexion, $Conexion);
		//$Result2 = mysql_query($updateSQL, $Conexion) or die(mysql_error());?>
        </td>
        <?php if ($_SESSION['MM_UserGroup'] ==  'Adm'):?>
        <td title="Insertar Link">
        <form method="post" name="form1" action="<?php echo $editFormAction; ?>">
        		<input type="text" name="id" value="<?php echo $row_rsCXP['ID'];?>" hidden>
             <div class="input-group form-group">
              <input class="form-control" type="text" name="link" value="" autocomplete="off">
            </div>
            <button class="btn btn-primary btn-xs" type="submit">Insertar</button>
        <input type="hidden" name="MM_insert" value="form1">
    	</form>
        </td>
        <td>
        <?php echo $row_rsCXP['ID'];?>
        </td>
        <?php endif;?>
       </tr>   
      <?php } while ( $row_rsCXP = mysql_fetch_assoc($rsCXP)); ?>     
    </table>
     <!--/row-->
     <!-- InstanceEndEditable -->
    </div><!--/.container-->
	<!-- Bootstrap core JavaScript
    ================================================== -->
	<!-- Placed at the end of the document so the pages load faster -->
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/1.12.4/jquery.min.js"></script>
	<script src="assets/js/docs.min.js"></script>
    <script src="js/bootstrap.min.js"></script>
    <script src="https://cdn.jsdelivr.net/clipboard.js/1.5.12/clipboard.min.js"></script>
	<script>new Clipboard('.btn-copiador');</script>
    <!-- Activar popover -->
    <script>
	$(document).ready(function(){
		$('[data-toggle="popover"]').popover();
	});
	</script>
    <!-- InstanceBeginEditable name="script-extras" -->
    <!-- extras de script y demás yerbas -->
    <script type="text/javascript">
		(function(){
			new Clipboard('#copy-button');
		})();
	</script>
<!-- InstanceEndEditable -->
  </body>
  
<!-- InstanceEnd --></html>
<?php
mysql_free_result($rsUsuarios);

mysql_free_result($rsCXP);
?>

