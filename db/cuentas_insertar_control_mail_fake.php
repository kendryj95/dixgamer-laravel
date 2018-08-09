<?php if(isset($_POST["mail_fake"]))
{
    if(!isset($_SERVER['HTTP_X_REQUESTED_WITH']) AND strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) != 'xmlhttprequest') {
        die();
    }
    
	//PROGRAMADOR necesito que ésta conexión se haga directamente al archivo ../Connections/Conexion.php 
    $mysqli = new mysqli('localhost' , 'AAAA', 'contra', 'AAAA');
    if ($mysqli->connect_error){
        die('Could not connect to database!');
    }
   
    $mail_fake = filter_var($_POST["mail_fake"], FILTER_SANITIZE_STRING, FILTER_FLAG_STRIP_LOW|FILTER_FLAG_STRIP_HIGH);
   
    $statement = $mysqli->prepare("SELECT mail_fake FROM cuentas WHERE mail_fake=?");
    $statement->bind_param('s', $mail_fake);
    $statement->execute();
    $statement->bind_result($mail_fake);
    if($statement->fetch()){
		die('fa fa-ban');
    }else{
        die('fa fa-check');
    }
};
?>