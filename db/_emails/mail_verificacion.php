<?php require_once('../../Connections/Conexion.php'); ?>
<?php
$vendedor = $_SESSION['MM_Username'];

$Img1 = "";
if (isset($_GET['imgurl1'])) {
  $Img1 = (get_magic_quotes_gpc()) ? $_GET['imgurl1'] : addslashes($_GET['imgurl1']);
}
$Img2 = "";
if (isset($_GET['imgurl2'])) {
  $Img2 = (get_magic_quotes_gpc()) ? $_GET['imgurl2'] : addslashes($_GET['imgurl2']);
}
$Img3 = "";
if (isset($_GET['imgurl3'])) {
  $Img3 = (get_magic_quotes_gpc()) ? $_GET['imgurl3'] : addslashes($_GET['imgurl3']);
}
$email = "-1";
if (isset($_GET['email'])) {
  $email = (get_magic_quotes_gpc()) ? $_GET['email'] : addslashes($_GET['email']);
}
$nombre = "-1";
if (isset($_GET['nombre'])) {
  $nombre = (get_magic_quotes_gpc()) ? $_GET['nombre'] : addslashes($_GET['nombre']);
}
$apellido = "-1";
if (isset($_GET['apellido'])) {
  $apellido = (get_magic_quotes_gpc()) ? $_GET['apellido'] : addslashes($_GET['apellido']);
}

/**
 * This example shows settings to use when sending via Google's Gmail servers.
 */
//SMTP needs accurate times, and the PHP time zone MUST be set
//This should be done in your php.ini, but this is how to do it if you don't have access to that
date_default_timezone_set('America/Argentina/Buenos_Aires');
require '../PHPMailer-master/PHPMailerAutoload.php';
//Create a new PHPMailer instance
$mail = new PHPMailer;
//Tell PHPMailer to use SMTP
$mail->isSMTP();
// aplico UTF - 8 para ver si andan los emojis en los asuntos de email
$mail->CharSet = 'UTF-8';
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
$mail->addReplyTo($email, $nombre . ' ' . $apellido);
//Set who the message is to be sent to
$mail->addAddress('contacto@dixgamer.com', 'DixGamer.com');
//Set the subject line
$mail->Subject = 'Verificación de ' . $nombre .' '. $apellido ;
//Read an HTML message body from an external file, convert referenced images to embedded,
//convert HTML into a basic plain-text alternative body
// Agrego utfdecode para no tener problemas de acentos y demas yerbas (el contenido del mail viene en ISO no en UTF8)
//$mail->msgHTML(str_replace(array('[nombrecliente]','[apellidocliente]','[nombrejuego]','[vta_id]','[clientes_id]','[stock_id]','[code]'), array(utf8_decode($row_rsClient['nombre']),utf8_decode($row_rsClient['apellido']),$row_rsClient['titulo'],$row_rsClient['ID_ventas'],$row_rsClient['clientes_id'],$row_rsClient['stock_id'],$row_rsClient['code']), file_get_contents('mail_datos_gift_contenido.php')), dirname(__FILE__));
//Replace the plain text body with one created manually

//$mail->Body = 'test verif ' . $colname_rsClient . ' ' . $colname_rsImg . ' ' . $colname_rsClientName . ' ' . $colname_rsClientApellido . '<img alt="test" src="cid:banana" />';
$mail->Body = ' <img src="' . $Img1 .'">';
if ($Img2 !== "blank"): $mail->Body .=  '<img alt="img2" src="' . $Img2 .'" />'; endif;
if ($Img3 !== "blank"): $mail->Body .= '<img alt="img2" src="' . $Img3 .'" />'; endif;
$mail->Body .= '<br /><a href="' . $Img1 . '">link1</a> <br /><a href="'.$Img2.'">link2</a> <br /><a href="'.$Img3.'">link3</a>';
$mail->AltBody = 'Estos son los datos de descarga';
//Attach an image file
//send the message, check for errors
if (!$mail->send()) {
    echo "Mailer Error: " . $mail->ErrorInfo;
} else {
    echo "<h3>funciono</h3>";
}?>