<?php
require_once('../../Connections/Conexion.php');

// DEFINIR FECHA EN ARGENTINA
date_default_timezone_set('America/Argentina/Buenos_Aires');

mysql_select_db($database_Conexion, $Conexion);
$query_rsEstado = sprintf("SELECT ventas.ID AS ID_ventas, clientes.ID AS clientes_id, apellido, nombre, email, stock_id, medio_venta, estado, ventas.Day AS dayventa, TIMESTAMPDIFF(hour, ventas.Day, NOW()) AS hs_from_sell, mail.daymailer, TIMESTAMPDIFF(hour, daymailer, NOW()) AS hs_from_sent, titulo, consola
FROM ventas
LEFT JOIN
stock
ON ventas.stock_id = stock.ID
LEFT JOIN
clientes
ON ventas.clientes_id = clientes.ID
LEFT JOIN
(SELECT ventas_id, MAX(Day) AS daymailer FROM mailer GROUP BY ventas_id) AS mail
ON ventas.ID = mail.ventas_id
WHERE estado = 'pendiente' AND ((daymailer IS NULL) OR (TIMESTAMPDIFF(hour, daymailer, NOW()) > 27)) AND (TIMESTAMPDIFF(hour, ventas.Day, NOW()) > 27)
ORDER BY ventas.Day DESC", $colname_rsEstado);
$rsEstado = mysql_query($query_rsEstado, $Conexion) or die(mysql_error());
$row_rsEstado = mysql_fetch_assoc($rsEstado);
$totalRows_rsEstado = mysql_num_rows($rsEstado);


/**
 * This example shows settings to use when sending via Google's Gmail servers.
 */
//SMTP needs accurate times, and the PHP time zone MUST be set
//This should be done in your php.ini, but this is how to do it if you don't have access to that
date_default_timezone_set('America/Argentina/Buenos_Aires');
require '../PHPMailer-master/PHPMailerAutoload.php';
?>
<?php if($row_rsEstado):?>
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
$mail->setFrom('contacto@dixgamer.com', 'DixGamer.com');
//Set an alternative reply-to address
$mail->addReplyTo('contacto@dixgamer.com', 'DixGamer.com');
//Set who the message is to be sent to
$mail->addAddress($row_rsEstado['email'], utf8_decode($row_rsEstado['nombre']).' '.utf8_decode($row_rsEstado['apellido']));
//Set the subject line
$titulo = ucwords(preg_replace('/([-])/'," ",$row_rsEstado['titulo']));
$mail->Subject = 'Compraste '.$titulo.' ('.$row_rsEstado['consola'].')';
//Read an HTML message body from an external file, convert referenced images to embedded,
//convert HTML into a basic plain-text alternative body
// Agrego utfdecode para no tener problemas de acentos y demas yerbas (el contenido del mail viene en ISO no en UTF8)
$mail->msgHTML(str_replace(array('[nombrecliente]','[apellidocliente]','[nombrejuego]','[vta_id]','[clientes_id]','[stock_id]','[cuentamail]','[cuentapass]'), array(utf8_decode($row_rsEstado['nombre']),utf8_decode($row_rsEstado['apellido']),$row_rsEstado['titulo'],$row_rsEstado['ID_ventas'],$row_rsEstado['clientes_id'],$row_rsEstado['stock_id'],$row_rsEstado['mail_fake'],$row_rsEstado['pass']), file_get_contents('mail_control_estado_contenido.php')), dirname(__FILE__));
//Replace the plain text body with one created manually
$mail->AltBody = 'Por favor confirmar descarga del juego. Gracias.';
//Attach an image file
//                  $mail->addAttachment('images/phpmailer_mini.png');
//send the message, check for errors
if (!$mail->send()) {
    //echo "Mailer Error: " . $mail->ErrorInfo;
	// Imprimo que no hay mails para enviar
} else {
    echo "Enviada a <strong>".$row_rsEstado['email']."</strong><br />";
	$date = date('Y-m-d H:i:s', time());
	$insertSQL = sprintf("INSERT INTO mailer (ventas_id, concepto, Day) VALUES (%s, 'estado', '$date')",$row_rsEstado['ID_ventas']);
	mysql_select_db($database_Conexion, $Conexion);
	$Result1 = mysql_query($insertSQL, $Conexion) or die(mysql_error());
}
} while ($row_rsEstado = mysql_fetch_assoc($rsEstado)); ?> 
<?php if($row_rsEstado):?>
</div> <!-- end div mail estado --><span>pruebo si anda esta parte</span>
<?php endif;?>
<?php
mysql_free_result($rsEstado);
?>