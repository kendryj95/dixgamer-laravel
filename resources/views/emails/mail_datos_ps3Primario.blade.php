@php
$id_ventas = $row_rsClient->ID_ventas;
$last2digits = substr($id_ventas,-2,2);
@endphp

<!-- PARA EVITAR PROBLEMAS CON UTF8 E ISO-8859-1 CONVERTIR TODO EL HTML EN... -->
<!-- ESTE SITIO WEB https://www.emailonacid.com/character-converter -->
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=ISO-8859-1" /> <!-- utf-8 works for most cases -->
	<meta name="viewport" content="width=device-width"> <!-- Forcing initial-scale shouldn't be necessary -->
	<meta http-equiv="X-UA-Compatible" content="IE=edge"> <!-- Use the latest (edge) version of IE rendering engine -->
    <meta name=&#8221;x-apple-disable-message-reformatting&#8221;>  <!-- Disable auto-scale in iOS 10 Mail entirely -->
	<title></title> <!-- The title tag shows in email notifications, like Android 4.4. -->

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
			.emailImage{
            height:auto !important;
            max-width:600px !important;
            width: 100% !important;
        	}
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
		
		.cursiva {color:#999;font-size:12px;}
		.roja {color: #e74c3c;}

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
				<td align="center">
                <img src="https://dixgamer.com/img/web/mails/encabezado-nuevo.jpg" class="emailImage"  />
				</td>
            </tr>
            <!-- Hero Image, Flush : END -->

            <!-- 1 Column Text : BEGIN -->
            <tr>
                <td bgcolor="#ffffff" style="padding: 40px 40px; text-align: left; font-family: sans-serif; font-size: 15px; mso-height-rule: exactly; line-height: 20px; color: #555555;">
                
                <p style="font-size:18px">Hola {{ $row_rsClient->nombre }} {{ $row_rsClient->apellido }}, gracias por tu nueva compra.</p>
                <p><em style="color:#e74c3c;">Ten&eacute;s 3 d&iacute;as para iniciar la descarga.</em><br />
					<em style="color:#999;">Leer COMPLETO para evitar riesgos de perder tu juego.</em></p>

				<p>Ver <a href="https://www.facebook.com/watch/?v=2534001146657963" title="Tutorial crear cuenta USA" target="_blank">video tutorial</a> paso a paso.</p> 
                <p>1) <strong>Prender</strong> la consola &gt; Crear <strong>Usuario Nuevo</strong><br />
                  2) Le colocamos de nombre <strong>"{{$last2digits . " " . substr($row_rsClient->titulo,0,8) }} NO Tocar"</strong><br />
                  3) <strong>Abrimos el usuario creado</strong> y vamos a <strong>PSN</strong> <em>(PlayStation Network)</em> &gt; <strong>Inscribirse</strong> > Opción "Usar una <strong>cuenta existente</strong>"<br /><br />

				  4) Completamos con estos datos:<br />
                  <em>El <strong>ID de inicio de sesi&oacute;n</strong> son todas <span style="text-decoration:underline;">letras min&uacute;sculas y n&uacute;meros, debemos incluir el punto y si hay un gui&oacute;n medio tambi&eacute;n.</span></em><br />
  <strong> ID</strong> de inicio de sesi&oacute;n: {{ $row_rsCuenta->mail_fake }} <br />
  <em>En la <strong>contrase&ntilde;a</strong> <span style="text-decoration:underline;">respetar las letras min&uacute;sculas y may&uacute;sculas.</span></em><br />
  <strong> Contrase&#241;a</strong>: {{ $row_rsCuenta->pass }} <br />
  <br />
                  5) Guardar la contrase&#241;a y <strong>continuar.</strong><br >
				6) Volvemos al <strong>menú principal del usuario creado > PSN</strong> > Iniciar Sesi&oacute;n > Administraci&oacute;n de Cuentas > Transaction Management > <strong>Download List</strong><br/>
7) Iniciamos la descarga de <strong>todos los items</strong> poniendo en <strong>segundo plano.</strong><br />
                  8) <strong>Salimos del usuario creado</strong> abriendo tu usuario personal (el que usas siempre).<br />
				9) Sub� una foto a IG, etiqueta a @dixgamer.ok y gan� un cup�n.
                <p>&iquest;Problemas? Mira el <a href="https://www.facebook.com/watch/?v=2534001146657963" title="Tutorial crear cuenta USA" target="_blank">video tutorial</a> paso a paso.</p>
  {{-- <strong>Al comenzar la descarga</strong> click aqu&iacute;: <br /><br/>
  <a href="https://dixgamer.com/db/clientes_conf_est.php?id={{ $row_rsClient->ID_ventas }}&amp;c_id={{ $row_rsClient->clientes_id }}&amp;s_id={{ $row_rsClient->stock_id }}" style="border-radius: 6px;font-size: 18px;line-height: 1.33333;padding: 10px 26px;background-color: #1d9d74;border-color: #198764;color: #fff;moz-user-select: none;background-image: none;cursor: pointer;display: inline-block;font-weight: normal;margin-bottom: 0;text-align: center; vertical-align: middle; white-space: nowrap; text-decoration:none;" target="_blank">Ya inici&eacute; la descarga</a><br /><br /> --}}
                </p></td>
            </tr>
            <!-- 1 Column Text : BEGIN -->
            <tr>
                <td bgcolor="#ffffff">
                    <table role="presentation" cellspacing="0" cellpadding="0" border="0" width="100%" style="border-top:2px solid #efefef;">
                    	<tr>
                          <td align="justify" style="padding: 20px 40px; font-family: sans-serif; font-size: 15px; mso-height-rule: exactly; line-height: 20px; color: #666;"><p class="roja" style="font-size:22px;">IMPORTANTE</p>
                            <p class="roja">Al comenzar la descarga salir de nuestro usuario y<br />
                              1) No abrirlo nunca m&aacute;s.<br />
                              2) No borrarlo de tu consola.<br />
                              En ambos casos se pierde el juego para siempre.</p>
                              <p style="color: #e74c3c;">No modificar ningun dato de nuestra cuenta, si MODIFICAS la vamos a bloquear y vas a perder el juego para siempre.</p>
                            <p class="roja">PARA JUGAR VAS A USAR TU USUARIO PERSONAL<br />
                            </p>
                            <p><strong>&gt;</strong> Para ver el <strong>progreso de la descarga abrí tu usuario</strong> &gt; red &gt; administracion de descargas</p>
                            <p><strong>&gt; Si la descarga demora mucho</strong> deber&aacute;s ser paciente, depende de tu <strong>conexi&oacute;n a internet</strong>, el peso del juego y    los servidores de descarga.</p>
                          <p><strong>&gt; &iquest;Puedo compartir la cuenta? NO,</strong> la cuenta sirve para una consola, si se comparte reportamos a Sony y te damos de baja, <strong>perd&eacute;s tu juego para siempre.</strong></p></td>
					  </tr>
                           
                    </table>
                </td>
            </tr>

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
                              <td valign="middle" style="text-align: center; padding: 40px; font-family: sans-serif; font-size: 15px; mso-height-rule: exactly; line-height: 20px; color: #ffffff;"><p>¿PROBLEMAS?</p>
                                <p dir="ltr">Si ten&#233;s dificultades respond&#233; &#233;ste email adjuntando una foto del error que sale en pantalla. Sin la foto es imposible ayudarte.</p><p>
                                &#xA1;Muchas Gracias!</p></td>
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
                <table class="flexible" width="600" align="center" style="margin:0 auto;" cellpadding="0" cellspacing="0">
									<tr>
										<td class="footer" style="padding:0 0 10px;">
											<table width="100%" cellpadding="0" cellspacing="0">
												<tr class="table-holder">
													<th class="tfoot" width="400" align="left" style="vertical-align:top; padding:0;">
														<table width="100%" cellpadding="0" cellspacing="0">
															<tr>
																<td data-color="text" data-link-color="link text color" data-link-style="text-decoration:underline; color:#797c82;" class="aligncenter" style="font:12px/16px Arial, Helvetica, sans-serif; color:#797c82; padding:0 0 10px;"> DixGamer.com,
                                                                    {{date('Y')}}. Todos los derechos reservados. <a style="text-decoration:underline; color:#797c82;"></a></td>
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

