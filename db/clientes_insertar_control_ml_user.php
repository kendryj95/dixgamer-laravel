<?php if(isset($_POST["ml_user"]))
{
    if(!isset($_SERVER['HTTP_X_REQUESTED_WITH']) AND strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) != 'xmlhttprequest') {
        die();
    }
    
	//PROGRAMADOR necesito que ésta conexión se haga directamente al archivo ../Connections/Conexion.php 
    $mysqli = new mysqli('localhost' , 'AAA', 'BBBcontra', 'AAAA');
    if ($mysqli->connect_error){
        die('Could not connect to database!');
    }
   
    $ml_user = filter_var($_POST["ml_user"], FILTER_SANITIZE_STRING, FILTER_FLAG_STRIP_LOW|FILTER_FLAG_STRIP_HIGH);
   
    $statement = $mysqli->prepare("SELECT ml_user FROM clientes WHERE ml_user=?");
    $statement->bind_param('s', $ml_user);
    $statement->execute();
    $statement->bind_result($ml_user);
    if($statement->fetch()){
		die('fa fa-ban');
    }else{
        die('fa fa-check');
    }
};
?>