<?php if(isset($_POST["mail"]))
{
    if(!isset($_SERVER['HTTP_X_REQUESTED_WITH']) AND strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) != 'xmlhttprequest') {
        die();
    }
    
	//PROGRAMADOR necesito que ésta conexión se haga directamente al archivo ../Connections/Conexion.php 
    $mysqli = new mysqli('localhost' , 'AAA', 'BBBcontra', 'AAA');
    if ($mysqli->connect_error){
        die('Could not connect to database!');
    }
   
    $mail = filter_var($_POST["mail"], FILTER_SANITIZE_STRING, FILTER_FLAG_STRIP_LOW|FILTER_FLAG_STRIP_HIGH);
   
    $statement = $mysqli->prepare("SELECT mail FROM cuentas WHERE mail=?");
    $statement->bind_param('s', $mail);
    $statement->execute();
    $statement->bind_result($mail);
    if($statement->fetch()){
		die('fa fa-ban');
    }else{
        die('fa fa-check');
    }
};
?>