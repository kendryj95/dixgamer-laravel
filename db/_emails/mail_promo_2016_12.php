<?php require_once('../../Connections/Conexion.php'); ?>
<?php
mysql_select_db($database_Conexion, $Conexion);
$query_rsEstadoCali = sprintf("SELECT ventas.ID as ID_ventas, clientes.ID AS ID_clientes, clientes.email, nombre, apellido, promociones.clientes_id
FROM ventas
LEFT JOIN clientes
ON ventas.clientes_id = clientes.ID
LEFT JOIN promociones
ON ventas.clientes_id = promociones.clientes_id
WHERE promociones.clientes_id IS NULL AND precio > '10'
GROUP BY ventas.clientes_id
ORDER BY clientes.ID ASC
LIMIT 10", $colname_rsEstadoCali);
$rsEstadoCali = mysql_query($query_rsEstadoCali, $Conexion) or die(mysql_error());
$row_rsEstadoCali = mysql_fetch_assoc($rsEstadoCali);
$totalRows_rsEstadoCali = mysql_num_rows($rsEstadoCali);


/**
 * This example shows settings to use when sending via Google's Gmail servers.
 */
//SMTP needs accurate times, and the PHP time zone MUST be set
//This should be done in your php.ini, but this is how to do it if you don't have access to that
date_default_timezone_set('America/Argentina/Buenos_Aires');
// Desactivo porque ya agrego el archivo desde mail_control_estado incluido en inicio.php
require '../PHPMailer-master/PHPMailerAutoload.php';
?>
<?php if($row_rsEstadoCali):?>
<?php $existeconsulta = 'si'; ?>
<div class="alert alert-warning alert-dismissable">
<button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>
<?php endif;?>
<?php do {

//Create a new PHPMailer instance
$mail = new PHPMailer;
//Tell PHPMailer to use SMTP
$mail->isSMTP();
//Enable SMTP debugging
// 0 = off (for production use)
// 1 = client messages
// 2 = client and server messages
$mail->SMTPDebug = 0;
//Ask for HTML-friendly debug output
$mail->Debugoutput = 'html';
//Set the hostname of the mail server
$mail->Host = 'smtp.gmail.com';
// use
// $mail->Host = gethostbyname('smtp.gmail.com');
// if your network does not support SMTP over IPv6
//Set the SMTP port number - 587 for authenticated TLS, a.k.a. RFC4409 SMTP submission
$mail->Port = 587;
//Set the encryption system to use - ssl (deprecated) or tls
$mail->SMTPSecure = 'tls';
//Whether to use SMTP authentication
$mail->SMTPAuth = true;
//Username to use for SMTP authentication - use full email address for gmail
$mail->Username = "contacto@dixgamer.com";
//Password to use for SMTP authentication
$mail->Password = "dqldervenulhfiad";
//Set who the message is to be sent from
$mail->setFrom('contacto@dixgamer.com', 'dixgamer.com');
//Set an alternative reply-to address
$mail->addReplyTo('contacto@dixgamer.com', 'dixgamer.com');
//Set who the message is to be sent to
$mail->addAddress($row_rsEstadoCali['email'], utf8_decode($row_rsEstadoCali['nombre']).' '.utf8_decode($row_rsEstadoCali['apellido']));
//Set the subject line
$mail->Subject = '[dixgamer.com] 10% OFF en todas tus compras';
//Read an HTML message body from an external file, convert referenced images to embedded,
//convert HTML into a basic plain-text alternative body
// Agrego utfdecode para no tener problemas de acentos y demas yerbas (el contenido del mail viene en ISO no en UTF8)
$mail->msgHTML(str_replace(array('[nombrecliente]','[apellidocliente]'), array(utf8_decode($row_rsEstadoCali['nombre']),utf8_decode($row_rsEstadoCali['apellido'])), file_get_contents('mail_promo_2016_12_contenido.php')), dirname(__FILE__));
//Replace the plain text body with one created manually
$mail->AltBody = 'Apoyanos en Facebook, te ofrecemos 10% OFF de por vida.';
//Attach an image file
//                  $mail->addAttachment('images/phpmailer_mini.png');
//send the message, check for errors
if (!$mail->send()) {
    //echo "Mailer Error: " . $mail->ErrorInfo;
	// Imprimo que no hay mails para enviar
} else {
    echo "Enviada a <strong>".$row_rsEstadoCali['email']."</strong><br />";
	$date = date('Y-m-d H:i:s', time());
	$email = $row_rsEstadoCali['email'];
	$insertSQL = sprintf("INSERT INTO promociones (clientes_id, email, concepto, Day) VALUES (%s, '$email', 'promo_2016_12', '$date')",$row_rsEstadoCali['ID_clientes']);
	mysql_select_db($database_Conexion, $Conexion);
	$Result1 = mysql_query($insertSQL, $Conexion) or die(mysql_error());
}
} while ($row_rsEstadoCali = mysql_fetch_assoc($rsEstadoCali)); ?> 
<?php if($existeconsulta = 'si'):?>
</div>
<?php endif; ?>
<?php
mysql_free_result($rsEstadoCali);
?>