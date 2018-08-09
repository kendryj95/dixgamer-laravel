<?php
error_reporting(E_ALL & ~E_NOTICE);
ini_set('display_errors', 1);

// DEFINIR FECHA EN ARGENTINA
date_default_timezone_set('America/Argentina/Buenos_Aires');

# FileName="Connection_php_mysql.htm"
# Type="MYSQL"
# HTTP="true"
$hostname_Conexion = 'localhost';
$database_Conexion = 'dixgamer';
$username_Conexion = 'root';
$password_Conexion = '';
$Conexion = mysqli_connect($hostname_Conexion, $username_Conexion, $password_Conexion);
//
// Funciones
//
function GetSQLValueString($theValue, $theType, $theDefinedValue = "", $theNotDefinedValue = "") 
{
  $theValue = (!get_magic_quotes_gpc()) ? addslashes($theValue) : $theValue;

  switch ($theType) {
    case "text":
      $theValue = ($theValue != "") ? "'" . $theValue . "'" : "NULL";
      break;    
    case "long":
    case "int":
      $theValue = ($theValue != "") ? intval($theValue) : "NULL";
      break;
    case "double":
      $theValue = ($theValue != "") ? "'" . doubleval($theValue) . "'" : "NULL";
      break;
    case "date":
      $theValue = ($theValue != "") ? "'" . $theValue . "'" : "NULL";
      break;
    case "defined":
      $theValue = ($theValue != "") ? $theDefinedValue : $theNotDefinedValue;
      break;
  }
  return $theValue;
}

//
// Si hay que utilizar mysqli en lugar de mysql 
//
if ( !function_exists("mysql_query") ) 
{
    function mysql_query($query,$conexion=false)
    {
        global $Conexion;
        if( !$conexion ) $conexion = $Conexion;
        
        if( $query ) return mysqli_query($conexion,$query);  
    }
    function mysql_error()
    {
        global $Conexion;
        return mysqli_error($Conexion);    
    }
    function mysql_select_db($database,$conexion)
    {
        global $Conexion;
        if( !$conexion ) $conexion = $Conexion;
        
        return mysqli_select_db($conexion,$database);    
    }
    function mysql_num_rows($mysql_result)
    {
        return mysqli_num_rows($mysql_result);    
    }
    function mysql_fetch_assoc($mysql_result)
    {
        return mysqli_fetch_assoc($mysql_result);    
    }
    function mysql_free_result($mysql_result)
    {
        if( $mysql_result ) return mysqli_free_result($mysql_result); 
    }
    function mysql_connect($bd_servidor,$bd_usuario,$bd_contrasenya)
    {
        return mysqli_connect($bd_servidor,$bd_usuario,$bd_contrasenya); 
    }
    function mysql_fetch_row($mysql_result)
    {
        if( $mysql_result ) return mysqli_fetch_row($mysql_result); 
    }
    function mysql_fetch_array($mysql_result)
    {
        if( $mysql_result ) return mysqli_fetch_array($mysql_result); 
    }
    function mysql_insert_id($conexion=false)
    {
        global $Conexion;
        if( !$conexion ) $conexion = $Conexion;
        
        return mysqli_insert_id($Conexion); 
    }
    function mysql_data_seek($mysql_result, $offset)
    {
        return mysqli_data_seek($mysql_result, $offset);    
    }
}


?>