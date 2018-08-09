<?php
require_once('../Connections/Conexion.php');

// DEFINIR FECHA EN ARGENTINA
date_default_timezone_set('America/Argentina/Buenos_Aires');

$colname_rsClient = "-1";
if (isset($_GET['id'],$_GET['c_id'],$_GET['s_id'])) {
  $colname_rsClient = (get_magic_quotes_gpc()) ? $_GET['id'] : addslashes($_GET['id']);
  $colname_rsClient2 = (get_magic_quotes_gpc()) ? $_GET['c_id'] : addslashes($_GET['c_id']);
  $colname_rsClient3 = (get_magic_quotes_gpc()) ? $_GET['s_id'] : addslashes($_GET['s_id']);

mysql_select_db($database_Conexion, $Conexion);
$query_rsClient = sprintf("SELECT ID AS ID_stock, titulo, consola, cuentas_id, client.*
FROM stock
RIGHT JOIN
(SELECT ventas.ID AS ID_ventas, clientes_id, stock_id, slot, medio_cobro, precio, comision, estado, estado_cali, Day, clientes.ID AS ID_clientes, apellido, nombre, email
FROM ventas
LEFT JOIN
clientes
ON ventas.clientes_id = clientes.ID) AS client
ON stock.ID = client.stock_id
WHERE ID_ventas = %s AND clientes_id = %s AND stock_id = %s AND estado_cali != 'listo'
ORDER BY client.Day DESC", $colname_rsClient, $colname_rsClient2, $colname_rsClient3);
$rsClient = mysql_query($query_rsClient, $Conexion) or die(mysql_error());
$row_rsClient = mysql_fetch_assoc($rsClient);
$totalRows_rsClient = mysql_num_rows($rsClient);
}

$date = date('Y-m-d H:i:s', time());
if ($row_rsClient){
  $insertSQL = sprintf("INSERT INTO estados_cali (ventas_id, concepto, Day) VALUES (%s, 'listo', '$date')",$colname_rsClient);
  mysql_select_db($database_Conexion, $Conexion);
  $Result1 = mysql_query($insertSQL, $Conexion) or die(mysql_error());
  $deleteSQL = sprintf("UPDATE ventas SET estado_cali='listo' WHERE ID=%s",$colname_rsClient);
  mysql_select_db($database_Conexion, $Conexion);
  $Result2 = mysql_query($deleteSQL, $Conexion) or die(mysql_error());
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <!-- The above 3 meta tags *must* come first in the head; any other head content must come *after* these tags -->

    <meta name=”x-apple-disable-message-reformatting”>  <!-- Disable auto-scale in iOS 10 Mail entirely -->
	<title>Descarga confirmada</title> <!-- The title tag shows in email notifications, like Android 4.4. -->
    	<!-- Estilo personalizado por mi -->
	<link href="css/personalizado.css" rel="stylesheet">
    
    <!-- Font Awesome style desde mi servidor -->
    <link href="css/font-awesome.min.css" rel="stylesheet">
    
    <!-- Bootstrap core CSS -->
    <link href="css/bootstrap.min.css" rel="stylesheet">
    
    <!-- BootFLAT core CSS -->
    <link href="css/site.min.css" rel="stylesheet">

	<!-- Web Font / @font-face : BEGIN -->
	<!-- NOTE: If web fonts are not required, lines 9 - 26 can be safely removed. -->
	
	<!-- Desktop Outlook chokes on web font references and defaults to Times New Roman, so we force a safe fallback font. -->
	<!--[if mso]>
		<style>
			* {
				font-family: sans-serif !important;
			}
		</style>
	<![endif]-->
	
	<!-- All other clients get the webfont reference; some will render the font and others will silently fail to the fallbacks. More on that here: http://stylecampaign.com/blog/2015/02/webfont-support-in-email/ -->
	<!--[if !mso]><!-->
		<!-- insert web font reference, eg: <link href='https://fonts.googleapis.com/css?family=Roboto:400,700' rel='stylesheet' type='text/css'> -->
	<!--<![endif]-->

	<!-- Web Font / @font-face : END -->
	
	<!-- CSS Reset -->
    <style>

		/* What it does: Remove spaces around the email design added by some email clients. */
		/* Beware: It can remove the padding / margin and add a background color to the compose a reply window. */
        html,
        body {
	        margin: 0 auto !important;
            padding: 0 !important;
            height: 100% !important;
            width: 100% !important;
        }
        
        /* What it does: Stops email clients resizing small text. */
        * {
            -ms-text-size-adjust: 100%;
            -webkit-text-size-adjust: 100%;
        }
        
        /* What is does: Centers email on Android 4.4 */
        div[style*="margin: 16px 0"] {
            margin:0 !important;
        }
        
        /* What it does: Stops Outlook from adding extra spacing to tables. */
        table,
        td {
            mso-table-lspace: 0pt !important;
            mso-table-rspace: 0pt !important;
        }
                
        /* What it does: Fixes webkit padding issue. Fix for Yahoo mail table alignment bug. Applies table-layout to the first 2 tables then removes for anything nested deeper. */
        table {
            border-spacing: 0 !important;
            border-collapse: collapse !important;
            table-layout: fixed !important;
            margin: 0 auto !important;
        }
        table table table {
            table-layout: auto; 
        }
        
        /* What it does: Uses a better rendering method when resizing images in IE. */
        img {
            -ms-interpolation-mode:bicubic;
        }
        
        /* What it does: A work-around for iOS meddling in triggered links. */
        .mobile-link--footer a,
        a[x-apple-data-detectors] {
            color:inherit !important;
            text-decoration: underline !important;
        }
      
    </style>
    
    <!-- Progressive Enhancements -->
    <style>
        
        /* What it does: Hover styles for buttons */
        .button-td,
        .button-a {
            transition: all 100ms ease-in;
        }
        .button-td:hover,
        .button-a:hover {
            background: #555555 !important;
            border-color: #555555 !important;
        }

        /* Media Queries */
        @media screen and (max-width: 600px) {

            .email-container {
                width: 100% !important;
                margin: auto !important;
            }

            /* What it does: Forces elements to resize to the full width of their container. Useful for resizing images beyond their max-width. */
            .fluid,
            .fluid-centered {
                max-width: 100% !important;
                height: auto !important;
                margin-left: auto !important;
                margin-right: auto !important;
            }
            /* And center justify these ones. */
            .fluid-centered {
                margin-left: auto !important;
                margin-right: auto !important;
            }

            /* What it does: Forces table cells into full-width rows. */
            .stack-column,
            .stack-column-center {
                display: block !important;
                width: 100% !important;
                max-width: 100% !important;
                direction: ltr !important;
            }
            /* And center justify these ones. */
            .stack-column-center {
                text-align: center !important;
            }
        
            /* What it does: Generic utility class for centering. Useful for images, buttons, and nested tables. */
            .center-on-narrow {
                text-align: center !important;
                display: block !important;
                margin-left: auto !important;
                margin-right: auto !important;
                float: none !important;
            }
            table.center-on-narrow {
                display: inline-block !important;
            }
                
        }

    </style>

</head>
<body bgcolor="#222222" width="100%" style="margin: 0;">
<center style="width: 100%; background: #eee;">

    <!-- Visually Hidden Preheader Text : BEGIN -->
    <div style="display:none;font-size:1px;line-height:1px;max-height:0px;max-width:0px;opacity:0;overflow:hidden;mso-hide:all;font-family: sans-serif;">
            Estos son los datos de descarga...
        </div>
        <!-- Visually Hidden Preheader Text : END -->

        <!-- Email Header : BEGIN
        <table role="presentation" cellspacing="0" cellpadding="0" border="0" align="center" width="600" style="margin: auto;" class="email-container">
			<tr>
				<td style="padding: 20px 0; text-align: center">
					<img src="../img/logo/logo-light.png">
				</td>
			</tr>
        </table>
        <!-- Email Header : END -->
        
        <!-- Email Body : BEGIN -->
        <table role="presentation" cellspacing="0" cellpadding="0" border="0" align="center" width="600" style="margin: auto;" class="email-container">
            
            <!-- Hero Image, Flush : BEGIN -->
            <tr>
				<td align="center" height="300" background="https://dixgamer.com/img/ml/gral/encabezado.jpg" style=" background-repeat: no-repeat; background-position: center middle;">
				</td>
            </tr>
            <!-- Hero Image, Flush : END -->

            <!-- 1 Column Text : BEGIN -->
            <tr>
                <td bgcolor="#ffffff" style="padding: 40px 40px; text-align: center; font-family: sans-serif; font-size: 15px; mso-height-rule: exactly; line-height: 20px; color: #555555;">
                
                <p style="font-size:22px"><?php echo $row_rsClient['nombre']; ?> <?php echo $row_rsClient['apellido']; ?></p>
    <br />
    <i class="fa fa-plus-circle fa-fw fa-5x text-success"></i> <br /><br />
    <strong>Gracias por calificarnos</strong><br />
                  Te esperamos en tus pr&oacute;ximas compras online.<br />

                  <br />
                  <p><div class="alert alert-warning"><em class="fa fa-comment fa-fw"></em> Visitá nuestro sitio web: <a href="https://dixgamer.com" target="_self">dixgamer.com</a></div></p>
              </td>
            </tr>
            <!-- 1 Column Text : BEGIN -->
            
			
            <!-- Background Image with Text : BEGIN -->
            <tr>
                <!-- Bulletproof Background Images c/o https://backgrounds.cm -->
                <td bgcolor="#444" valign="middle" style="text-align: center; background-position: center center !important; background-size: cover !important;">

                    <!--[if gte mso 9]>
                    <v:rect xmlns:v="urn:schemas-microsoft-com:vml" fill="true" stroke="false" style="width:600px;height:175px; background-position: center center !important;">
                    <v:fill type="tile" src="http://placehold.it/600x230/222222/666666" color="#222222" />
                    <v:textbox inset="0,0,0,0">
                    <![endif]-->
                    <div>
                        <table role="presentation" align="center" border="0" cellpadding="0" cellspacing="0" width="100%" style="border-top:2px solid #999;">
                            <tr>
                                <td valign="middle" style="text-align: center; padding: 40px; font-family: sans-serif; font-size: 15px; mso-height-rule: exactly; line-height: 20px; color: #ffffff;"><p>Para consultas: contacto@dixgamer.com</p></td>
                            </tr>
                        </table>
                        </div>
                    <!--[if gte mso 9]>
                    </v:textbox>
                    </v:rect>
                    <![endif]-->
                </td>
            </tr>
            <!-- Background Image with Text : END -->
           
            
            <!-- Clear Spacer : BEGIN
            <tr>
                <td height="40" style="font-size: 0; line-height: 0;">&nbsp;
                    
                </td>
            </tr>
            <!-- Clear Spacer : END -->

            <!-- 1 Column Text + Button : BEGIN -->

        </table>
        <!-- Email Body : END -->
          
        <!-- Email Footer : BEGIN -->
        <table role="presentation" cellspacing="0" cellpadding="0" border="0" align="center" width="600" style="margin: auto;" class="email-container">
            <tr>
                <td style="padding: 0px;width: 100%;font-size: 12px; font-family: sans-serif; mso-height-rule: exactly; line-height:18px; text-align: center; color: #888888;">
                <table class="flexible" width="600" align="center" style="margin:0 auto; background-color:#efefef;" cellpadding="0" cellspacing="0">
									<tr>
										<td style="padding:0 0 10px;">
											<table width="100%" cellpadding="0" cellspacing="0">
												<tr class="table-holder">
													<th class="tfoot" width="400" align="left" style="vertical-align:top; padding:0;">
														<table width="100%" cellpadding="0" cellspacing="0">
															<tr>
																<td data-color="text" data-link-color="link text color" data-link-style="text-decoration:underline; color:#797c82;" class="aligncenter" style="font:12px/16px Arial, Helvetica, sans-serif; color:#797c82; padding:0 0 10px;">
																	dixgamer.com, 2016. Todos los derechos reservados. <a style="text-decoration:underline; color:#797c82;"></a>
																</td>
															</tr>
														</table>
													</th>
													<th class="thead" width="150" align="left" style="vertical-align:top; padding:0;">
														<table class="center" align="right" cellpadding="0" cellspacing="0">
															<tr>
																<td class="btn" valign="top" style="line-height:0; padding:3px 0 0;">
																	<a target="_blank" style="text-decoration:none;" href="#"><img src="file:///D|/Downloads/markup/images/ico-facebook.png" border="0" style="font:12px/15px Arial, Helvetica, sans-serif; color:#797c82;" align="left" vspace="0" hspace="0" width="6" height="13" alt="fb" /></a>
																</td>
																<td width="20"></td>
																<td class="btn" valign="top" style="line-height:0; padding:3px 0 0;">
																	<a target="_blank" style="text-decoration:none;" href="#"><img src="file:///D|/Downloads/markup/images/ico-twitter.png" border="0" style="font:12px/15px Arial, Helvetica, sans-serif; color:#797c82;" align="left" vspace="0" hspace="0" width="13" height="11" alt="tw" /></a>
																</td>
																<td width="19"></td>
																<td class="btn" valign="top" style="line-height:0; padding:3px 0 0;">
																	<a target="_blank" style="text-decoration:none;" href="#"><img src="file:///D|/Downloads/markup/images/ico-google-plus.png" border="0" style="font:12px/15px Arial, Helvetica, sans-serif; color:#797c82;" align="left" vspace="0" hspace="0" width="19" height="15" alt="g+" /></a>
																</td>
																<td width="20"></td>
																<td class="btn" valign="top" style="line-height:0; padding:3px 0 0;">
																	<a target="_blank" style="text-decoration:none;" href="#"><img src="file:///D|/Downloads/markup/images/ico-linkedin.png" border="0" style="font:12px/15px Arial, Helvetica, sans-serif; color:#797c82;" align="left" vspace="0" hspace="0" width="13" height="11" alt="in" /></a>
																</td>
															</tr>
														</table>
													</th>
												</tr>
											</table>
										</td>
									</tr>
								</table>
                </td>
            </tr>
        </table>
        <!-- Email Footer : END -->

    </center>
</body>
</html>
<?php
mysql_free_result($rsClient);
?>

