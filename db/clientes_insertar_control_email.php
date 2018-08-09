<?php if(isset($_POST["email"]))
{
    if(!isset($_SERVER['HTTP_X_REQUESTED_WITH']) AND strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) != 'xmlhttprequest') {
        die();
    }
    
	//PROGRAMADOR necesito que ésta conexión se haga directamente al archivo ../Connections/Conexion.php 
    $mysqli = new mysqli('localhost' , 'AAAA', 'BBBBcotra', 'AAAA');
    if ($mysqli->connect_error){
        die('Could not connect to database!');
    }
   
    $email = filter_var($_POST["email"], FILTER_SANITIZE_STRING, FILTER_FLAG_STRIP_LOW|FILTER_FLAG_STRIP_HIGH);
   
    $statement = $mysqli->prepare("SELECT email FROM clientes WHERE email=?");
    $statement->bind_param('s', $email);
    $statement->execute();
    $statement->bind_result($email);
    if($statement->fetch()){
		die('fa fa-ban');
    }else{
        die('fa fa-check');
    }
};
?>