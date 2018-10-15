<?php
require_once('../../Connections/Conexion.php');

// DEFINIR FECHA EN ARGENTINA
date_default_timezone_set('America/Argentina/Buenos_Aires');
?>
<?php
//initialize the session
if (!isset($_SESSION)) {
  session_start();
}

// ** Logout the current user. **
$logoutAction = $_SERVER['PHP_SELF']."?doLogout=true";
if ((isset($_SERVER['QUERY_STRING'])) && ($_SERVER['QUERY_STRING'] != "")){
  $logoutAction .="&". htmlentities($_SERVER['QUERY_STRING']);
}

if ((isset($_GET['doLogout'])) &&($_GET['doLogout']=="true")){
  //to fully log out a visitor we need to clear the session varialbles
  $_SESSION['MM_Username'] = NULL;
  $_SESSION['MM_UserGroup'] = NULL;
  $_SESSION['PrevUrl'] = NULL;
  unset($_SESSION['MM_Username']);
  unset($_SESSION['MM_UserGroup']);
  unset($_SESSION['PrevUrl']);
	
  $logoutGoTo = "salida.php";
  if ($logoutGoTo) {
    header("Location: $logoutGoTo");
    exit;
  }
}
?>
<?php
if (!isset($_SESSION)) {
  session_start();
}
$MM_authorizedUsers = "Adm,Vendedor";
$MM_donotCheckaccess = "false";

// *** Restrict Access To Page: Grant or deny access to this page
function isAuthorized($strUsers, $strGroups, $UserName, $UserGroup) { 
  // For security, start by assuming the visitor is NOT authorized. 
  $isValid = False; 

  // When a visitor has logged into this site, the Session variable MM_Username set equal to their username. 
  // Therefore, we know that a user is NOT logged in if that Session variable is blank. 
  if (!empty($UserName)) { 
    // Besides being logged in, you may restrict access to only certain users based on an ID established when they login. 
    // Parse the strings into arrays. 
    $arrUsers = Explode(",", $strUsers); 
    $arrGroups = Explode(",", $strGroups); 
    if (in_array($UserName, $arrUsers)) { 
      $isValid = true; 
    } 
    // Or, you may restrict access to only certain users based on their username. 
    if (in_array($UserGroup, $arrGroups)) { 
      $isValid = true; 
    } 
    if (($strUsers == "") && false) { 
      $isValid = true; 
    } 
  } 
  return $isValid; 
}

$MM_restrictGoTo = "index.php";
if (!((isset($_SESSION['MM_Username'])) && (isAuthorized("",$MM_authorizedUsers, $_SESSION['MM_Username'], $_SESSION['MM_UserGroup'])))) {   
  $MM_qsChar = "?";
  $MM_referrer = $_SERVER['PHP_SELF'];
  if (strpos($MM_restrictGoTo, "?")) $MM_qsChar = "&";
  if (isset($QUERY_STRING) && strlen($QUERY_STRING) > 0) 
  $MM_referrer .= "?" . $QUERY_STRING;
  $MM_restrictGoTo = $MM_restrictGoTo. $MM_qsChar . "accesscheck=" . urlencode($MM_referrer);
  header("Location: ". $MM_restrictGoTo); 
  exit;
}
?>
<?php
$vendedor = $_SESSION['MM_Username'];

$colname_rsClient = "-1";
if (isset($_GET['id'])) {
  $colname_rsClient = (get_magic_quotes_gpc()) ? $_GET['id'] : addslashes($_GET['id']);
}
mysql_select_db($database_Conexion, $Conexion);
$query_rsClient = sprintf("SELECT ID AS ID_stock, titulo, consola, code, cuentas_id, client.*
FROM stock
RIGHT JOIN
(SELECT ventas.ID AS ID_ventas, clientes_id, stock_id, slot, estado, Day, clientes.ID AS ID_clientes, apellido, nombre, email
FROM ventas
LEFT JOIN
clientes
ON ventas.clientes_id = clientes.ID) AS client
ON stock.ID = client.stock_id
WHERE ID_ventas = %s
ORDER BY client.Day DESC", $colname_rsClient);
$rsClient = mysql_query($query_rsClient, $Conexion) or die(mysql_error());
$row_rsClient = mysql_fetch_assoc($rsClient);
$totalRows_rsClient = mysql_num_rows($rsClient);

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
$mail->addAddress($row_rsClient['email'], utf8_decode($row_rsClient['nombre']).' '.utf8_decode($row_rsClient['apellido']));
//Set the subject line
$titulo = ucwords(preg_replace('/([-])/'," ",$row_rsClient['titulo']));
$mail->Subject = '[Nueva Compra] '.$titulo.' ('.$row_rsClient['consola'].') #' . $row_rsClient['clientes_id'] . '-' . $row_rsClient['ID_stock'];
//Read an HTML message body from an external file, convert referenced images to embedded,
//convert HTML into a basic plain-text alternative body
// Agrego utfdecode para no tener problemas de acentos y demas yerbas (el contenido del mail viene en ISO no en UTF8)
$mail->msgHTML(str_replace(array('[nombrecliente]','[apellidocliente]','[nombrejuego]','[vta_id]','[clientes_id]','[stock_id]','[code]'), array(utf8_decode($row_rsClient['nombre']),utf8_decode($row_rsClient['apellido']),$row_rsClient['titulo'],$row_rsClient['ID_ventas'],$row_rsClient['clientes_id'],$row_rsClient['stock_id'],$row_rsClient['code']), file_get_contents('mail_datos_psNo_contenido.php')), dirname(__FILE__));
//Replace the plain text body with one created manually
$mail->AltBody = 'Estos son los datos de descarga';
//Attach an image file
//                  $mail->addAttachment('images/phpmailer_mini.png');
//send the message, check for errors
if (!$mail->send()) {
    echo "Mailer Error: " . $mail->ErrorInfo;
} else {
    echo "<h3>Datos de PS PLUS slot</h3> <p>enviados a </p><h2>".$row_rsClient['email']."</h2>";
	$date = date('Y-m-d H:i:s', time());
	$insertSQL = sprintf("INSERT INTO mailer (ventas_id, concepto, Day, usuario) VALUES (%s, 'datos1', '$date', '$vendedor')",$row_rsClient['ID_ventas']);
	mysql_select_db($database_Conexion, $Conexion);
	$Result1 = mysql_query($insertSQL, $Conexion) or die(mysql_error());
}?>
<?php
mysql_free_result($rsClient);
mysql_free_result($rsCuenta);
?>
<?php 
$idcliente = $row_rsClient['clientes_id'];
echo "<script>window.top.location.href = \"../clientes_detalles.php?id=$idcliente\";</script>"; ?>