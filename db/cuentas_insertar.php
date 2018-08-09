<?php require_once('../Connections/Conexion.php'); ?>
<?php
$MM_authorizedUsers = "Adm,Vendedor";
$MM_donotCheckaccess = "false";
require_once('_autentificacion.php');
?>
<?php 
$editFormAction = $_SERVER['PHP_SELF'];
if (isset($_SERVER['QUERY_STRING'])) {
  $editFormAction .= "?" . htmlentities($_SERVER['QUERY_STRING']);
}

$vendedor = $_SESSION['MM_Username'];
$date = date('Y-m-d H:i:s', time());
if ((isset($_POST["MM_insert"])) && ($_POST["MM_insert"] == "form1")) {
  $insertSQL = sprintf("INSERT INTO cuentas (mail_fake, mail, pass, name, surname, country, state, city, pc, address, days, months, years, nacimiento, usuario) VALUES (%s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, '$vendedor')", 
                       GetSQLValueString($_POST['mail_fake'], "text"),
                       GetSQLValueString($_POST['mail'], "text"),
					   GetSQLValueString($_POST['pass'], "text"),
                       GetSQLValueString($_POST['name'], "text"),
                       GetSQLValueString($_POST['surname'], "text"),
                       GetSQLValueString($_POST['country'], "text"),
                       GetSQLValueString($_POST['state'], "text"),
                       GetSQLValueString($_POST['city'], "text"),
                       GetSQLValueString($_POST['pc'], "text"),
					   GetSQLValueString($_POST['address'], "text"),
					   GetSQLValueString($_POST['days'], "int"),
					   GetSQLValueString($_POST['months'], "int"),
					   GetSQLValueString($_POST['years'], "int"),
					   GetSQLValueString($_POST['nacimiento'], "text"));

  mysql_select_db($database_Conexion, $Conexion);
  $Result1 = mysql_query($insertSQL, $Conexion) or die(mysql_error());

	// si es el admin vamos a la pagina normal de cargar productos 
	//if ($_SESSION['MM_UserGroup'] ==  'Adm'):
	  //$insertGoTo = "stock_insertar.php";
	// si es vendedor vamos a la pagina de la cuenta
   //else:
	  $insertGoTo = "cuentas_detalles.php?id=";
	  $insertGoTo .= mysql_insert_id();
	 // endif;

  header(sprintf("Location: %s", $insertGoTo));
}

if ((isset($_POST["MM_insert"])) && ($_POST["MM_insert"] == "form2")) {
  $insertSQL = sprintf("INSERT INTO cuentas (mail_fake, mail, pass, name, surname, country, state, city, pc, address, usuario) VALUES (%s, %s, %s, %s, %s, %s, %s, %s, %s, %s, '$vendedor')", 
                       GetSQLValueString($_POST['mail_fake'], "text"),
                       GetSQLValueString($_POST['mail'], "text"),
					   GetSQLValueString($_POST['pass'], "text"),
                       GetSQLValueString($_POST['name'], "text"),
                       GetSQLValueString($_POST['surname'], "text"),
                       GetSQLValueString($_POST['country'], "text"),
                       GetSQLValueString($_POST['state'], "text"),
                       GetSQLValueString($_POST['city'], "text"),
                       GetSQLValueString($_POST['pc'], "text"),
					   GetSQLValueString($_POST['address'], "text"));

  mysql_select_db($database_Conexion, $Conexion);
  $Result1 = mysql_query($insertSQL, $Conexion) or die(mysql_error());
  
  $cuentaid = mysql_insert_id(); // ultimo ID de una consulta INSERT , en este caso seria el ID de la ultima cuenta creada
  $insertSQL2 = sprintf("INSERT INTO stock (titulo, consola, cuentas_id, medio_pago, costo_usd, costo, Day) SELECT titulo, consola, '$cuentaid', medio_pago, costo_usd, costo, '$date' FROM stock WHERE usuario = '$vendedor' ORDER BY ID DESC LIMIT 1");

  mysql_select_db($database_Conexion, $Conexion);
  $Result2 = mysql_query($insertSQL2, $Conexion) or die(mysql_error());

  $insertGoTo = "cuentas_detalles.php?id=";
  $insertGoTo .= $cuentaid;
  if (isset($_SERVER['QUERY_STRING'])) {
    $insertGoTo .= (strpos($insertGoTo, '?')) ? "&" : "?";
    $insertGoTo .= $_SERVER['QUERY_STRING'];
  }
  header(sprintf("Location: %s", $insertGoTo));
}

if ((isset($_POST["MM_insert"])) && ($_POST["MM_insert"] == "form3")) {
  $insertSQL = sprintf("INSERT INTO cuentas (mail_fake, mail, pass, name, surname, country, state, city, pc, address, usuario) VALUES (%s, %s, %s, %s, %s, %s, %s, %s, %s, %s, '$vendedor')", 
                       GetSQLValueString($_POST['mail_fake'], "text"),
                       GetSQLValueString($_POST['mail'], "text"),
					   GetSQLValueString($_POST['pass'], "text"),
                       GetSQLValueString($_POST['name'], "text"),
                       GetSQLValueString($_POST['surname'], "text"),
                       GetSQLValueString($_POST['country'], "text"),
                       GetSQLValueString($_POST['state'], "text"),
                       GetSQLValueString($_POST['city'], "text"),
                       GetSQLValueString($_POST['pc'], "text"),
					   GetSQLValueString($_POST['address'], "text"));

  mysql_select_db($database_Conexion, $Conexion);
  $Result1 = mysql_query($insertSQL, $Conexion) or die(mysql_error());
  
  // 25-08-2017 actualizo la consulta
  $cuentaid = mysql_insert_id(); // ultimo ID de una consulta INSERT , en este caso seria el ID de la ultima cuenta creada
  $insertSQL2 = sprintf("INSERT INTO stock (titulo, consola, cuentas_id, medio_pago, costo_usd, costo, Day) SELECT titulo, consola, '$cuentaid', medio_pago, costo_usd, costo, '$date' FROM stock WHERE usuario = '$vendedor' ORDER BY ID DESC LIMIT 2");
  mysql_select_db($database_Conexion, $Conexion);
  $Result2 = mysql_query($insertSQL2, $Conexion) or die(mysql_error());
  
  /***
  $cuentaid = mysql_insert_id(); // ultimo ID de una consulta INSERT , en este caso seria el ID de la ultima cuenta creada
  $insertSQL2 = sprintf("INSERT INTO stock (titulo, consola, cuentas_id, medio_pago, costo_usd, costo, Day, Notas) SELECT titulo, consola, '$cuentaid', medio_pago, costo_usd, costo, '$date', Notas FROM stock WHERE usuario = '$vendedor' ORDER BY ID DESC LIMIT 1,1");
  mysql_select_db($database_Conexion, $Conexion);
  $Result2 = mysql_query($insertSQL2, $Conexion) or die(mysql_error());
  
  $insertSQL22 = sprintf("INSERT INTO stock (titulo, consola, cuentas_id, medio_pago, costo_usd, costo, Day, Notas) SELECT titulo, consola, '$cuentaid', medio_pago, costo_usd, costo, '$date', Notas FROM stock WHERE usuario = '$vendedor'  ORDER BY ID DESC LIMIT 1,1");
  mysql_select_db($database_Conexion, $Conexion);
  $Result22 = mysql_query($insertSQL22, $Conexion) or die(mysql_error());
  */
  $insertGoTo = "cuentas_detalles.php?id=";
  $insertGoTo .= $cuentaid;
  if (isset($_SERVER['QUERY_STRING'])) {
    $insertGoTo .= (strpos($insertGoTo, '?')) ? "&" : "?";
    $insertGoTo .= $_SERVER['QUERY_STRING'];
  }
  header(sprintf("Location: %s", $insertGoTo));
}

$nombresito = $_SESSION['MM_Username'];
mysql_select_db($database_Conexion, $Conexion);
$query_rsUsuarios = sprintf("SELECT * FROM usuarios WHERE Nombre = '$nombresito' ORDER BY Nombre ASC",$nombresito);
$rsUsuarios = mysql_query($query_rsUsuarios, $Conexion) or die(mysql_error());
$row_rsUsuarios = mysql_fetch_assoc($rsUsuarios);
$totalRows_rsUsuarios = mysql_num_rows($rsUsuarios);

$colname_rsClientes = "-1";
if (isset($_GET['id'])) {
  $colname_rsClientes = (get_magic_quotes_gpc()) ? $_GET['id'] : addslashes($_GET['id']);
}
mysql_select_db($database_Conexion, $Conexion);
$query_rsClientes = sprintf("SELECT * FROM clientes", $colname_rsClientes);
$rsClientes = mysql_query($query_rsClientes, $Conexion) or die(mysql_error());
$row_rsClientes = mysql_fetch_assoc($rsClientes);
$totalRows_rsClientes = mysql_num_rows($rsClientes);
?>
<!DOCTYPE html>
<html lang="es"><!-- InstanceBegin template="/Templates/db.dwt.php" codeOutsideHTMLIsLocked="false" -->
  <head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <!-- The above 3 meta tags *must* come first in the head; any other head content must come *after* these tags -->
    <meta name="description" content="base de datos">
    <meta name="author" content="vic">
    <link rel="icon" href="favicon.ico">
	
<!-- InstanceBeginEditable name="doctitle" -->
<link rel="stylesheet" href="//code.jquery.com/ui/1.11.4/themes/smoothness/jquery-ui.css">
<script src="//code.jquery.com/jquery-1.10.2.js"></script>
<script src="//code.jquery.com/ui/1.11.4/jquery-ui.js"></script>
<script src="http://ajax.aspnetcdn.com/ajax/jquery.validate/1.11.0/jquery.validate.min.js"></script>
<!-- FORM VALIDATION -->
<script type="text/javascript">
$(document).ready(function() {
    var x_timer;    
    $("#mail").keyup(function (e){
        clearTimeout(x_timer);
        var user_name = $(this).val();
        x_timer = setTimeout(function(){
            check_mail_ajax(user_name);
        }, 1000);
    });

function check_mail_ajax(mail){
	document.getElementById("user-result").className = "fa fa-spinner fa-pulse fa-fw";
    $.post('cuentas_insertar_control_mail.php', {'mail':mail}, function(data) {
	document.getElementById("user-result").className = (data);
	var test = document.getElementById("user-result");
	var testClass = test.className;
	switch(testClass){
    case "fa fa-ban": document.getElementById("user-result-div").className = "input-group form-group has-error"; break;
	case "fa fa-check": document.getElementById("user-result-div").className = "input-group form-group has-success"; break;
}
  	});
}
});
</script>

<script type="text/javascript">
$(document).ready(function() {
    var x_timer;    
    $("#mail_fake").keyup(function (e){
        clearTimeout(x_timer);
        var user_name = $(this).val();
        x_timer = setTimeout(function(){
            check_mail_fake_ajax(user_name);
        }, 1000);
    });

function check_mail_fake_ajax(mail_fake){
	document.getElementById("ml-user-result").className = "fa fa-spinner fa-pulse fa-fw";
    $.post('cuentas_insertar_control_mail_fake.php', {'mail_fake':mail_fake}, function(data) {
	document.getElementById("ml-user-result").className = (data);
	var test = document.getElementById("ml-user-result");
	var testClass = test.className;
	switch(testClass){
    case "fa fa-ban": document.getElementById("ml-user-result-div").className = "input-group form-group has-error"; break;
	case "fa fa-check": document.getElementById("ml-user-result-div").className = "input-group form-group has-success"; break;
}
  	});
}
});
</script>
    <title><?php $titulo = 'Insertar cuenta'; echo $titulo; ?></title>
<!-- InstanceEndEditable -->

	  
    <!-- Font Awesome style desde mi servidor -->
    <link href="css/font-awesome.min.css" rel="stylesheet">
    
    <!-- link a mi css -->
    <link href="css/bootstrap.css" rel="stylesheet">
    
    <!-- Bootstrap SITE CSS -->
    <link href="css/site.css" rel="stylesheet">

    <!-- Custom styles for this template -->
    <link href="css/offcanvas.css" rel="stylesheet">
    
	<!-- 2017-12-30 Agrego nuevo css de BootFLAT --> 
    <link href="css/bootflat.css" rel="stylesheet">
	  
	<!-- Estilo personalizado por mi -->
	<link href="css/personalizado.css" rel="stylesheet">
	 	
    <!--- BootFLAT core CSS 
    <link href="../db/css/site.min.css" rel="stylesheet">
	-->

    <!-- InstanceBeginEditable name="head" -->
    <!-- antes que termine head-->

<!-- InstanceEndEditable -->
  </head>

  <body>
  <?php include('_barra_nav.php'); ?>

    <div class="container">
	<h1><?php echo $titulo; ?></h1>
    <!-- InstanceBeginEditable name="body" -->
    <div class="row">
    <div class="col-sm-3">
    </div>
    <div class="col-sm-6">
    <?php $nombre = array("Noah", "Liam", "William", "Mason", "James", "Benjamin", "Jacob", "Michael", "Elijah", "Ethan", "Alexander", "Oliver", "Daniel", "Lucas", "Matthew", "Aiden", "Jackson", "Logan", "David", "Joseph", "Samuel", "Henry", "Owen", "Sebastian", "Gabriel", "Carter", "Jayden", "John", "Luke", "Anthony", "Isaac", "Dylan", "Wyatt", "Andrew", "Joshua", "Christopher", "Grayson", "Jack", "Julian", "Ryan", "Jaxon", "Levi", "Nathan", "Caleb", "Hunter", "Christian", "Isaiah", "Thomas", "Aaron", "Lincoln", "Charles", "Eli", "Landon", "Connor", "Josiah", "Jonathan", "Cameron", "Jeremiah", "Mateo", "Adrian", "Hudson", "Robert", "Nicholas", "Brayden", "Nolan", "Easton", "Jordan", "Colton", "Evan", "Angel", "Asher", "Dominic", "Austin", "Leo", "Adam", "Jace", "Jose", "Ian", "Cooper", "Gavin", "Carson", "Jaxson", "Theodore", "Jason", "Ezra", "Chase", "Parker", "Xavier", "Kevin", "Zachary", "Tyler", "Ayden", "Elias", "Bryson", "Leonardo", "Greyson", "Sawyer", "Roman", "Brandon", "Bentley", "Kayden", "Ryder", "Nathaniel", "Vincent", "Miles", "Santiago", "Harrison", "Tristan", "Declan", "Cole", "Maxwell", "Luis", "Justin", "Everett", "Micah", "Axel", "Wesley", "Max", "Silas", "Weston", "Ezekiel", "Juan", "Damian", "Camden", "George", "Braxton", "Blake", "Jameson", "Diego", "Carlos", "Ivan", "Kingston", "Ashton", "Jesus", "Brody", "Emmett", "Abel", "Jayce", "Maverick", "Bennett", "Giovanni", "Eric", "Maddox", "Kaiden", "Kai", "Bryce", "Alex", "Calvin", "Ryker", "Jonah", "Luca", "King", "Timothy", "Alan", "Brantley", "Malachi", "Emmanuel", "Abraham", "Antonio", "Richard", "Jude", "Miguel", "Edward", "Victor", "Amir", "Joel", "Steven", "Matteo", "Hayden", "Patrick", "Grant", "Preston", "Tucker", "Jesse", "Finn", "Oscar", "Kaleb", "Gael", "Graham", "Elliot", "Alejandro", "Rowan", "Marcus", "Jeremy", "Zayden", "Karter", "Beau", "Bryan", "Maximus", "Aidan", "Avery", "Elliott", "August", "Nicolas", "Mark", "Colin", "Waylon", "Bradley", "Kyle", "Kaden", "Xander", "Caden", "Paxton", "Brian", "Dean", "Paul", "Peter", "Kenneth", "Jasper", "Lorenzo", "Zane", "Zion", "Beckett", "River", "Jax", "Andres", "Dawson", "Messiah", "Jaden", "Rhett", "Brady", "Lukas", "Omar", "Jorge", "Riley", "Derek", "Charlie", "Emiliano", "Griffin", "Myles", "Brooks", "Israel", "Sean", "Judah", "Iker", "Javier", "Erick", "Tanner", "Corbin", "Adriel", "Jase", "Jake", "Simon", "Cayden", "Knox", "Tobias", "Felix", "Milo", "Jayceon", "Gunner", "Francisco", "Kameron", "Cash", "Remington", "Reid", "Cody", "Martin", "Andre", "Rylan", "Maximiliano", "Zander", "Archer", "Barrett", "Killian", "Stephen", "Clayton", "Thiago", "Spencer", "Amari", "Josue", "Holden", "Emilio", "Arthur", "Chance", "Eduardo", "Leon", "Travis", "Ricardo", "Damien", "Manuel", "Gage", "Keegan", "Titus", "Raymond", "Kyrie", "Nash", "Finley", "Fernando", "Louis", "Peyton", "Rafael", "Phoenix", "Jaiden", "Lane", "Dallas", "Emerson", "Cristian", "Collin", "Kyler", "Devin", "Jeffrey", "Walter", "Anderson", "Cesar", "Mario", "Donovan", "Seth", "Garrett", "Enzo", "Conner", "Legend", "Caiden", "Beckham", "Jett", "Ronan", "Troy", "Karson", "Edwin", "Hector", "Cohen", "Ali", "Trevor", "Conor", "Orion", "Shane", "Andy", "Marco", "Walker", "Angelo", "Quinn", "Dalton", "Sergio", "Ace", "Tyson", "Johnny", "Dominick", "Colt", "Johnathan", "Gideon", "Julius", "Cruz", "Edgar", "Prince", "Dante", "Marshall", "Ellis", "Joaquin", "Major", "Arlo", "Alexis", "Reed", "Muhammad", "Frank", "Theo", "Shawn", "Erik", "Grady", "Nehemiah", "Daxton", "Atticus", "Gregory", "Matias", "Bodhi", "Emanuel", "Jensen", "Kash", "Romeo", "Desmond", "Solomon", "Allen", "Jaylen", "Leonel", "Roberto", "Pedro", "Kason", "Fabian", "Clark", "Dakota", "Abram", "Noel", "Kayson", "Malik", "Odin", "Jared", "Warren", "Kendrick", "Rory", "Jonas", "Adan", "Ibrahim", "Trenton", "Finnegan", "Landen", "Adonis", "Jay", "Ruben", "Drew", "Gunnar", "Ismael", "Jaxton", "Kane", "Hendrix", "Atlas", "Pablo", "Zaiden", "Wade", "Russell", "Cade", "Sullivan", "Malcolm", "Kade", "Harvey", "Princeton", "Skyler", "Corey", "Esteban", "Leland", "Derrick", "Ari", "Kamden", "Zayn", "Porter", "Franklin", "Raiden", "Braylon", "Ronald", "Cyrus", "Benson", "Malakai", "Hugo", "Marcos", "Maximilian", "Hayes", "Philip", "Lawson", "Phillip", "Bruce", "Braylen", "Zachariah", "Damon", "Dexter", "Enrique", "Aden", "Lennox", "Drake", "Khalil", "Tate", "Zayne", "Milan", "Brock", "Brendan", "Armando", "Gerardo", "Jamison", "Rocco", "Nasir", "Augustus", "Sterling", "Dillon", "Royal", "Royce", "Moses", "Jaime", "Johan", "Scott", "Chandler", "Raul", "Remy", "Cason", "Luka", "Mohamed", "Deacon", "Winston", "Albert", "Pierce", "Taylor", "Nikolai", "Bowen", "Danny", "Francis", "Brycen", "Jayson", "Moises", "Keith", "Hank", "Quentin", "Kasen", "Donald", "Julio", "Davis", "Alec", "Kolton", "Lawrence", "Rhys", "Kian", "Nico", "Matthias", "Kellan", "Mathias", "Ariel", "Justice", "Braden", "Rodrigo", "Ryland", "Leonidas", "Jerry", "Ronin", "Alijah", "Kobe", "Lewis", "Dennis", "Luciano", "Ahmed", "Frederick", "Darius", "Arjun", "Dax", "Asa", "Nixon", "Ezequiel", "Eden", "Tony", "Landyn", "Emmitt", "Mathew", "Kyson", "Otto", "Saul", "Uriel", "Colby", "Dustin", "Omari", "Raphael", "Brennan", "Callen", "Keaton", "Arturo", "Isaias", "Roy", "Kieran", "Ty", "Dorian", "Cannon", "Marvin", "Cullen", "Sage", "Uriah", "Darren", "Cayson", "Aarav", "Case", "Izaiah", "Armani", "Gustavo", "Jimmy", "Alberto", "Duke", "Rayan", "Chris", "Casey", "Roland", "Moshe", "Curtis", "Mauricio", "Alonzo", "Yusuf", "Nikolas", "Soren", "Hamza", "Jasiah", "Alfredo", "Devon", "Jalen", "Raylan", "Edison", "Jamari", "Oakley", "Samson", "Lionel", "Reece", "Sam", "Quincy", "Jakob", "Apollo", "Kingsley", "Ahmad", "Bryant", "Alvin", "Trey", "Mohammed", "Conrad", "Mitchell", "Salvador", "Quinton", "Bo", "Mohammad", "Elian", "Gianni", "Lennon", "Leonard", "Douglas", "Cassius", "Ricky", "Carl", "Gary", "Larry", "Colten", "Ramon", "Kellen", "Korbin", "Wilson", "Kylan", "Santino", "Niko", "Issac", "Jagger", "Lance", "Joe", "Julien", "Orlando", "Jefferson", "Memphis", "Crosby", "Mekhi", "Nelson", "Lucian", "Ayaan", "Nathanael", "Neil", "Makai", "Finnley", "Rex", "Forrest", "Layton", "Randy", "Boston", "Tristen", "Tatum", "Brayan", "Sylas", "Thaddeus", "Trent", "Morgan", "Roger", "Abdullah", "Casen", "Maurice", "Sincere", "Titan", "Kyree", "Talon", "Fletcher", "Langston", "Eddie", "Briggs", "Noe", "Kamari", "Rowen", "Zeke", "Aldo", "Kaison", "Valentino", "Vihaan", "Alden", "Terry", "Bruno", "Canaan", "Lee", "Byron", "Kohen", "Reese", "Braydon", "Madden", "Deandre", "Flynn", "Harley", "Hezekiah", "Amos", "Harry", "Zain", "Alessandro", "Stanley", "Lucca", "Branson", "Ernesto", "Joziah", "Leandro", "Ares", "Marc", "Blaine", "Joey", "Jon", "Yosef", "Carmelo", "Franco", "Jamal", "Mack", "Kristian", "Dane", "Lachlan", "Callum", "Graysen", "Kye", "Ben", "Aryan", "Gannon", "London", "Kareem", "Stetson", "Kristopher", "Tomas", "Ford", "Bronson", "Enoch", "Baylor", "Kaysen", "Axton", "Jaxen", "Rodney", "Dominik", "Emery", "Layne", "Wilder", "Jamir", "Tripp", "Kelvin", "Vicente", "Augustine", "Brett", "Callan", "Clay", "Crew", "Brecken", "Jacoby", "Abdiel", "Allan", "Maxton", "Melvin", "Rayden", "Terrance", "Demetrius", "Rohan", "Wayne", "Yahir", "Arian", "Fox", "Brentley", "Ray", "Zechariah", "Cain", "Guillermo", "Otis", "Tommy", "Alonso", "Dariel", "Jedidiah", "Maximo", "Cory", "Grey", "Reyansh", "Skylar", "Marcelo", "Castiel", "Kase", "Toby", "Bobby", "Jadiel", "Marcel", "Lochlan", "Jeffery", "Zackary", "Fisher", "Yousef", "Aron", "Chaim", "Felipe", "Axl", "Anakin", "Brodie", "Dash", "Anson", "Maison", "Zaire", "Samir", "Damari", "Jonathon");?>
    <?php $apellido = array("SMITH", "JOHNSON", "WILLIAMS", "BROWN", "JONES", "GARCIA", "MILLER", "DAVIS", "RODRIGUEZ", "MARTINEZ", "HERNANDEZ", "LOPEZ", "GONZALEZ", "WILSON", "ANDERSON", "THOMAS", "TAYLOR", "MOORE", "JACKSON", "MARTIN", "LEE", "PEREZ", "THOMPSON", "WHITE", "HARRIS", "SANCHEZ", "CLARK", "RAMIREZ", "LEWIS", "ROBINSON", "WALKER", "YOUNG", "ALLEN", "KING", "WRIGHT", "SCOTT", "TORRES", "NGUYEN", "HILL", "FLORES", "GREEN", "ADAMS", "NELSON", "BAKER", "HALL", "RIVERA", "CAMPBELL", "MITCHELL", "CARTER", "ROBERTS", "GOMEZ", "PHILLIPS", "EVANS", "TURNER", "DIAZ", "PARKER", "CRUZ", "EDWARDS", "COLLINS", "REYES", "STEWART", "MORRIS", "MORALES", "MURPHY", "COOK", "ROGERS", "GUTIERREZ", "ORTIZ", "MORGAN", "COOPER", "PETERSON", "BAILEY", "REED", "KELLY", "HOWARD", "RAMOS", "KIM", "COX", "WARD", "RICHARDSON", "WATSON", "BROOKS", "CHAVEZ", "WOOD", "JAMES", "BENNETT", "GRAY", "MENDOZA", "RUIZ", "HUGHES", "PRICE", "ALVAREZ", "CASTILLO", "SANDERS", "PATEL", "MYERS", "LONG", "FOSTER", "JIMENEZ", "POWELL", "JENKINS", "PERRY", "RUSSELL", "SULLIVAN", "BELL", "COLEMAN", "BUTLER", "HENDERSON", "BARNES", "GONZALES", "FISHER", "VASQUEZ", "SIMMONS", "ROMERO", "JORDAN", "PATTERSON", "ALEXANDER", "HAMILTON", "GRAHAM", "REYNOLDS", "GRIFFIN", "WALLACE", "MORENO", "WEST", "COLE", "HAYES", "BRYANT", "HERRERA", "GIBSON", "ELLIS", "TRAN", "MEDINA", "AGUILAR", "STEVENS", "MURRAY", "FORD", "CASTRO", "MARSHALL", "OWENS", "HARRISON", "FERNANDEZ", "MCDONALD", "WOODS", "WASHINGTON", "KENNEDY", "WELLS", "VARGAS", "HENRY", "CHEN", "FREEMAN", "WEBB", "TUCKER", "GUZMAN", "BURNS", "CRAWFORD", "OLSON", "SIMPSON", "PORTER", "HUNTER", "GORDON", "MENDEZ", "SILVA", "SHAW", "SNYDER", "MASON", "DIXON", "MUNOZ", "HUNT", "HICKS", "HOLMES", "PALMER", "WAGNER", "BLACK", "ROBERTSON", "BOYD", "ROSE", "STONE", "SALAZAR", "FOX", "WARREN", "MILLS", "MEYER", "RICE", "SCHMIDT", "GARZA", "DANIELS", "FERGUSON", "NICHOLS", "STEPHENS", "SOTO", "WEAVER", "RYAN", "GARDNER", "PAYNE", "GRANT", "DUNN", "KELLEY", "SPENCER", "HAWKINS", "ARNOLD", "PIERCE", "VAZQUEZ", "HANSEN", "PETERS", "SANTOS", "HART", "BRADLEY", "KNIGHT", "ELLIOTT", "CUNNINGHAM", "DUNCAN", "ARMSTRONG", "HUDSON", "CARROLL", "LANE", "RILEY", "ANDREWS", "ALVARADO", "RAY", "DELGADO", "BERRY", "PERKINS", "HOFFMAN", "JOHNSTON", "MATTHEWS", "PENA", "RICHARDS", "CONTRERAS", "WILLIS", "CARPENTER", "LAWRENCE", "SANDOVAL", "GUERRERO", "GEORGE", "CHAPMAN", "RIOS", "ESTRADA", "ORTEGA", "WATKINS", "GREENE", "NUNEZ", "WHEELER", "VALDEZ", "HARPER", "BURKE", "LARSON", "SANTIAGO", "MALDONADO", "MORRISON", "FRANKLIN", "CARLSON", "AUSTIN", "DOMINGUEZ", "CARR", "LAWSON", "JACOBS", "OBRIEN", "LYNCH", "SINGH", "VEGA", "BISHOP", "MONTGOMERY", "OLIVER", "JENSEN", "HARVEY", "WILLIAMSON", "GILBERT", "DEAN", "SIMS", "ESPINOZA", "HOWELL", "LI", "WONG", "REID", "HANSON", "LE", "MCCOY", "GARRETT", "BURTON", "FULLER", "WANG", "WEBER", "WELCH", "ROJAS", "LUCAS", "MARQUEZ", "FIELDS", "PARK", "YANG", "LITTLE", "BANKS", "PADILLA", "DAY", "WALSH", "BOWMAN", "SCHULTZ", "LUNA", "FOWLER", "MEJIA", "DAVIDSON", "ACOSTA", "BREWER", "MAY", "HOLLAND", "JUAREZ", "NEWMAN", "PEARSON", "CURTIS", "CORTEZ", "DOUGLAS", "SCHNEIDER", "JOSEPH", "BARRETT", "NAVARRO", "FIGUEROA", "KELLER", "AVILA", "WADE", "MOLINA", "STANLEY", "HOPKINS", "CAMPOS", "BARNETT", "BATES", "CHAMBERS", "CALDWELL", "BECK", "LAMBERT", "MIRANDA", "BYRD", "CRAIG", "AYALA", "LOWE", "FRAZIER", "POWERS", "NEAL", "LEONARD", "GREGORY", "CARRILLO", "SUTTON", "FLEMING", "RHODES", "SHELTON", "SCHWARTZ", "NORRIS", "JENNINGS", "WATTS", "DURAN", "WALTERS", "COHEN", "MCDANIEL", "MORAN", "PARKS", "STEELE", "VAUGHN", "BECKER", "HOLT", "DELEON", "BARKER", "TERRY", "HALE", "LEON", "HAIL", "BENSON", "HAYNES", "HORTON", "MILES", "LYONS", "PHAM", "GRAVES", "BUSH", "THORNTON", "WOLFE", "WARNER", "CABRERA", "MCKINNEY", "MANN", "ZIMMERMAN", "DAWSON", "LARA", "FLETCHER", "PAGE", "MCCARTHY", "LOVE", "ROBLES", "CERVANTES", "SOLIS", "ERICKSON", "REEVES", "CHANG", "KLEIN", "SALINAS", "FUENTES", "BALDWIN", "DANIEL", "SIMON", "VELASQUEZ", "HARDY", "HIGGINS", "AGUIRRE", "LIN", "CUMMINGS", "CHANDLER", "SHARP", "BARBER", "BOWEN", "OCHOA", "DENNIS", "ROBBINS", "LIU", "RAMSEY", "FRANCIS", "GRIFFITH", "PAUL", "BLAIR", "OCONNOR", "CARDENAS", "PACHECO", "CROSS", "CALDERON", "QUINN", "MOSS", "SWANSON", "CHAN", "RIVAS", "KHAN", "RODGERS", "SERRANO", "FITZGERALD", "ROSALES", "STEVENSON", "CHRISTENSEN", "MANNING", "GILL", "CURRY", "MCLAUGHLIN", "HARMON", "MCGEE", "GROSS", "DOYLE", "GARNER", "NEWTON", "BURGESS", "REESE", "WALTON", "BLAKE", "TRUJILLO", "ADKINS", "BRADY", "GOODMAN", "ROMAN", "WEBSTER", "GOODWIN", "FISCHER", "HUANG", "DELACRUZ", "MONTOYA", "TODD", "WU", "HINES", "MULLINS", "CASTANEDA", "MALONE", "CANNON", "TATE", "MACK", "SHERMAN", "HUBBARD", "HODGES", "ZHANG", "GUERRA", "WOLF", "VALENCIA", "SAUNDERS", "FRANCO", "ROWE", "GALLAGHER", "FARMER", "HAMMOND", "HAMPTON", "TOWNSEND", "INGRAM", "WISE", "GALLEGOS", "CLARKE", "BARTON", "SCHROEDER", "MAXWELL", "WATERS", "LOGAN", "CAMACHO", "STRICKLAND", "NORMAN", "PERSON", "COLON", "PARSONS", "FRANK", "HARRINGTON", "GLOVER", "OSBORNE", "BUCHANAN", "CASEY", "FLOYD", "PATTON", "IBARRA", "BALL", "TYLER", "SUAREZ", "BOWERS", "OROZCO", "SALAS", "COBB", "GIBBS", "ANDRADE", "BAUER", "CONNER", "MOODY", "ESCOBAR", "MCGUIRE", "LLOYD", "MUELLER", "HARTMAN", "FRENCH", "KRAMER", "MCBRIDE", "POPE", "LINDSEY", "VELAZQUEZ", "NORTON", "MCCORMICK", "SPARKS", "FLYNN", "YATES", "HOGAN", "MARSH", "MACIAS", "VILLANUEVA", "ZAMORA", "PRATT", "STOKES", "OWEN", "BALLARD", "LANG", "BROCK", "VILLARREAL", "CHARLES", "DRAKE", "BARRERA", "CAIN", "PATRICK", "PINEDA", "BURNETT", "MERCADO", "SANTANA", "SHEPHERD", "BAUTISTA", "ALI", "SHAFFER", "LAMB", "TREVINO", "MCKENZIE", "HESS", "BEIL", "OLSEN", "COCHRAN", "MORTON", "NASH", "WILKINS", "PETERSEN", "BRIGGS", "SHAH", "ROTH", "NICHOLSON", "HOLLOWAY", "LOZANO", "RANGEL", "FLOWERS", "HOOVER", "SHORT", "ARIAS", "MORA", "VALENZUELA", "BRYAN", "MEYERS", "WEISS", "UNDERWOOD", "BASS", "GREER", "SUMMERS", "HOUSTON", "CARSON", "MORROW", "CLAYTON", "WHITAKER", "DECKER", "YODER", "COLLIER", "ZUNIGA", "CAREY", "WILCOX", "MELENDEZ", "POOLE", "ROBERSON", "LARSEN", "CONLEY", "DAVENPORT", "COPELAND", "MASSEY", "LAM", "HUFF", "ROCHA", "CAMERON", "JEFFERSON", "HOOD", "MONROE", "ANTHONY", "PITTMAN", "HUYNH", "RANDALL", "SINGLETON", "KIRK", "COMBS", "MATHIS", "CHRISTIAN", "SKINNER", "BRADFORD", "RICHARD", "GALVAN", "WALL", "BOONE", "KIRBY", "WILKINSON", "BRIDGES", "BRUCE", "ATKINSON", "VELEZ", "MEZA", "ROY", "VINCENT", "YORK", "HODGE", "VILLA", "ABBOTT", "ALLISON", "TAPIA", "GATES", "CHASE", "SOSA", "SWEENEY", "FARRELL", "WYATT", "DALTON", "HORN", "BARRON", "PHELPS", "YU", "DICKERSON", "HEATH", "FOLEY", "ATKINS", "MATHEWS", "BONILLA", "ACEVEDO", "BENITEZ", "ZAVALA", "HENSLEY", "GLENN", "CISNEROS", "HARRELL", "SHIELDS", "RUBIO", "HUFFMAN", "CHOI", "BOYER", "GARRISON", "ARROYO", "BOND", "KANE", "HANCOCK", "CALLAHAN", "DILLON", "CLINE", "WIGGINS", "GRIMES", "ARELLANO", "MELTON", "ONEILL", "SAVAGE", "HO", "BELTRAN", "PITTS", "PARRISH", "PONCE", "RICH", "BOOTH", "KOCH", "GOLDEN", "WARE", "BRENNAN", "MCDOWELL", "MARKS", "CANTU", "HUMPHREY", "BAXTER", "SAWYER", "CLAY", "TANNER", "HUTCHINSON", "KAUR", "BERG", "WILEY", "GILMORE", "RUSSO", "VILLEGAS", "HOBBS", "KEITH", "WILKERSON", "AHMED", "BEARD", "MCCLAIN", "MONTES", "MATA", "ROSARIO", "VANG", "WALTER", "HENSON", "ONEAL", "MOSLEY", "MCCLURE", "BEASLEY", "STEPHENSON", "SNOW", "HUERTA", "PRESTON", "VANCE", "BARRY", "JOHNS", "EATON", "BLACKWELL", "DYER", "PRINCE", "MACDONALD", "SOLOMON", "STAFFORD", "ENGLISH", "HURST", "WOODARD", "CORTES", "SHANNON", "KEMP", "NOLAN", "MCCULLOUGH", "MERRITT", "MURILLO", "MOON", "SALGADO", "STRONG", "KLINE", "CORDOVA", "BARAJAS", "ROACH", "ROSAS", "WINTERS", "JACOBSON", "LESTER", "KNOX", "BULLOCK", "KERR", "LEACH", "MEADOWS", "ORR", "DAVILA", "WHITEHEAD", "PRUITT", "KENT", "CONWAY", "MCKEE", "BARR", "DAVID", "DEJESUS", "MARIN", "BERGER", "MCINTYRE", "BLANKENSHIP", "GAINES", "PALACIOS", "CUEVAS", "BARTLETT", "DURHAM", "DORSEY", "MCCALL", "ODONNELL", "STEIN", "BROWNING", "STOUT", "LOWERY", "SLOAN", "MCLEAN", "HENDRICKS", "CALHOUN", "SEXTON", "CHUNG", "GENTRY", "HULL", "DUARTE", "ELLISON", "NIELSEN", "GILLESPIE", "BUCK", "MIDDLETON", "SELLERS", "LEBLANC", "ESPARZA", "HARDIN", "BRADSHAW", "MCINTOSH", "HOWE", "LIVINGSTON", "FROST", "GLASS", "MORSE", "KNAPP", "HERMAN", "STARK", "BRAVO", "NOBLE", "SPEARS", "WEEKS", "CORONA", "FREDERICK", "BUCKLEY", "MCFARLAND", "HEBERT", "ENRIQUEZ", "HICKMAN", "QUINTERO", "RANDOLPH", "SCHAEFER", "WALLS", "TREJO", "HOUSE", "REILLY", "PENNINGTON", "MICHAEL", "CONRAD", "GILES", "BENJAMIN", "CROSBY", "FITZPATRICK", "DONOVAN", "MAYS", "MAHONEY", "VALENTINE", "RAYMOND", "MEDRANO", "HAHN", "MCMILLAN", "SMALL", "BENTLEY", "FELIX", "PECK", "LUCERO", "BOYLE", "HANNA", "PACE", "RUSH", "HURLEY", "HARDING", "MCCONNELL", "BERNAL", "NAVA", "AYERS", "EVERETT", "VENTURA", "AVERY", "PUGH", "MAYER", "BENDER", "SHEPARD", "MCMAHON", "LANDRY", "CASE", "SAMPSON", "MOSES", "MAGANA", "BLACKBURN", "DUNLAP", "GOULD", "DUFFY", "VAUGHAN", "HERRING", "MCKAY", "ESPINOSA", "RIVERS", "FARLEY", "BERNARD", "ASHLEY", "FRIEDMAN", "POTTS", "TRUONG", "COSTA", "CORREA", "BLEVINS", "NIXON", "CLEMENTS", "FRY", "DELAROSA", "BEST", "BENTON", "LUGO", "PORTILLO", "DOUGHERTY", "CRANE", "HALEY", "PHAN", "VILLALOBOS", "BLANCHARD", "HORNE", "FINLEY", "QUINTANA", "LYNN", "ESQUIVEL", "BEAN", "DODSON", "MULLEN", "XIONG", "HAYDEN", "CANO", "LEVY", "HUBER", "RICHMOND", "MOYER", "LIM", "FRYE", "SHEPPARD", "MCCARTY", "AVALOS", "BOOKER", "WALLER", "PARRA", "WOODWARD", "JARAMILLO", "KRUEGER", "RASMUSSEN", "BRANDT", "PERALTA", "DONALDSON", "STUART", "FAULKNER", "MAYNARD", "GALINDO", "COFFEY", "ESTES", "SANFORD", "BURCH", "MADDOX", "OCONNELL", "ANDERSEN", "SPENCE", "MCPHERSON", "CHURCH", "SCHMITT", "STANTON", "LEAL", "CHERRY", "COMPTON", "DUDLEY", "SIERRA", "POLLARD", "ALFARO", "HESTER", "PROCTOR", "HINTON", "NOVAK", "GOOD", "MADDEN", "MCCANN", "TERRELL", "JARVIS", "DICKSON", "REYNA", "CANTRELL", "MAYO", "BRANCH", "HENDRIX", "ROLLINS", "ROWLAND", "WHITNEY", "DUKE", "ODOM", "DAUGHERTY", "TRAVIS", "TANG", "ARCHER");?>
    <?php 
	$chars11 = "123456789";
	$chars22 = "ASDFG";
    $chars = "qwert";
	$password = substr( str_shuffle( $chars11 ), 0, 2 );
	$password .= substr( str_shuffle( $chars22 ), 0, 2 );
    $password .= substr( str_shuffle( $chars ), 0, 2 );
	$password .= substr( str_shuffle( $chars11 ), 0, 2 );
    $chars0 = "3456789";
    $chars1 = "123456789";
    $altura = substr( str_shuffle( $chars0 ), 0, 1 );
    $altura .= substr( str_shuffle( $chars1 ), 0, 2 );
    $calle = array("Ocean Dr", "Collins Ave", "Washintong Ave", "Meridian Ave", "Elucid Ave", "Lenox Ave");
    $nacimiento = array("Corrientes", "Salta", "Misiones", "San Juan", "Mendoza", "Rio Negro", "Santa Cruz", "Chubut", "Tucuman",  "San Luis", "Catamarca", "Chaco");
	$days = array("1", "2", "3", "4", "5", "6", "7", "8", "9",  "10", "11", "12", "13", "14", "15", "16", "17", "18", "19", "20", "21", "22",  "23", "24", "25", "26", "27", "28");
    $months = array("1", "2", "3", "4", "5", "6", "7", "8", "9",  "10", "11", "12");
    $years = array("1980", "1981", "1982", "1983", "1984", "1985", "1986", "1987", "1988",  "1989", "1990", "1991");
	?>
            
            
    <form method="post" name="form1" action="<?php echo $editFormAction; ?>">
            <div id="user-result-div" class="input-group form-group">
              <span class="input-group-addon"><i class="fa fa-user-secret fa-fw"></i></span>
              <input class="form-control" type="text" name="mail" id="mail" autocomplete="off" spellcheck="false" placeholder="Email real (secreto)" autofocus>
              <span class="input-group-addon"><i id="user-result" class="fa fa-pencil" aria-hidden="true"></i></span>
            </div>
            <div id="ml-user-result-div" class="input-group form-group">
              <span class="input-group-addon"><i class="fa fa-envelope-o fa-fw"></i></span>
              <input class="form-control" type="text" name="mail_fake" id="mail_fake" autocomplete="off" placeholder="Email falso: 'alt. ...'">
              <span class="input-group-addon"><i id="ml-user-result" class="fa fa-pencil" aria-hidden="true"></i></span>
            </div>
            <div class="input-group form-group">
              <span class="input-group-addon"><i class="fa fa-key fa-fw"></i></span>
              <input class="form-control" type="text" id="pass" name="pass" value="<?php echo $password;?>">
            </div>
            
            <div class="col-sm-6">
            <div class="input-group form-group">
              <span class="input-group-addon"><i class="fa fa-user fa-fw"></i> Nombre</span>
              <input class="form-control" type="text" id="name" name="name" autocomplete="off" value="<?= $nombre[array_rand($nombre, 1)] ?>">
            </div>
            </div>
            <div class="col-sm-6">
            <div class="input-group form-group">
              <span class="input-group-addon"><i class="fa fa-user fa-fw"></i> Apellido</span>
              <input class="form-control" type="text" id="surname" name="surname" autocomplete="off" value="<?= ucwords(strtolower($apellido[array_rand($apellido, 1)])); ?>">
            </div>
            </div>
            
            <div class="col-sm-2" style="opacity:0.5">
            <div class="input-group form-group">
              <input class="form-control" type="text" name="country" value="EEUU" readonly>
            </div>
            </div>
            <div class="col-sm-3" style="opacity:0.5">
            <div class="input-group form-group">
              <span class="input-group-addon"><i class="fa fa-map-marker fa-fw"></i></span>
              <input class="form-control" type="text" name="state" value="Florida" readonly>
            </div>
            </div>
 			<div class="col-sm-4" style="opacity:0.5">
            <div class="input-group form-group">
              <span class="input-group-addon"><i class="fa fa-street-view fa-fw"></i></span>
              <input class="form-control" type="text" name="city" value="Miami Beach" readonly>
            </div>
            </div>
            <div class="col-sm-3" style="opacity:0.5">
            <div class="input-group form-group">
              <span class="input-group-addon">cp</span>
              <input class="form-control" type="text" name="pc" value="33139" readonly>
            </div>
            </div>
            
            <div class="col-sm-6" style="opacity:0.8;">
            <div class="input-group form-group">
              <span class="input-group-addon" style="background-color:#FFCE55;border-color:#F6BB43;"><i class="fa fa-location-arrow fa-fw"></i></span>
              <input style="border-color:#F6BB43;" class="form-control" type="text" name="address" id="address" value="<?php echo $altura . ' ' . $calle[array_rand($calle, 1)];?>">
            </div>
            </div>

            <div class="col-sm-6" style="opacity:0.8;">
            <div class="input-group form-group">
              <span class="input-group-addon" style="background-color:#FFCE55;border-color:#F6BB43;">Nac</span>
              <input style="border-color:#F6BB43;" class="form-control" type="text" name="nacimiento" id="nacimiento" value="<?= $nacimiento[array_rand($nacimiento, 1)] ?>">
            </div>
            </div>


            <div class="col-sm-4" style="opacity:0.6">
            <div class="input-group form-group">
            	<span class="input-group-addon" style="background-color:#FB6E52; border-color:#E9573E; color:#efefef">día</span>
              <input style=" background-color:#F6F6F6; border-color:#E9573E;" class="form-control" type="text" name="days" id="days" value="<?= $days[array_rand($days, 1)] ?>">
            </div>
            </div>
            <div class="col-sm-4" style="opacity:0.6">
            <div class="input-group form-group">
              <span class="input-group-addon" style="background-color:#FB6E52; border-color:#E9573E; color:#efefef">mes</span>
              <input style="background-color:#F6F6F6; border-color:#E9573E;" class="form-control" type="text" name="months" id="months" value="<?= $months[array_rand($months, 1)] ?>">
            </div>
            </div>
            <div class="col-sm-4" style="opacity:0.6">
            <div class="input-group form-group">
              <span class="input-group-addon" style="background-color:#FB6E52; border-color:#E9573E; color:#efefef">año</span>
              <input style="background-color:#F6F6F6; border-color:#E9573E;" class="form-control" type="text" name="years" id="years" value="<?= $years[array_rand($years, 1)] ?>">
            </div>
            </div>

            <button class="btn btn-primary" type="submit">Insertar</button> <label class="btn-cuenta"><input type="radio" name="MM_insert" value="form1" checked="checked" />New <i class="fa fa-plus" aria-hidden="true"></i></label> 
            <!-- oculto las opciones de cargar cuenta con ultimo stock o dos ultimos stock (repetir stocks / carga masiva)
            <label class="btn-info btn-cuenta"><input type="radio" name="MM_insert" value="form2" />Last Stk <i class="fa fa-database" aria-hidden="true"></i></label> <label class="btn-info btn-cuenta"><input type="radio" name="MM_insert" value="form3" />2x Last Stk <i class="fa fa-database" aria-hidden="true"></i></label> -->
    </form>
    </div>
    <div class="col-sm-3">
    </div>
    </div>
     <!--/row-->
     <!-- InstanceEndEditable -->
    </div><!--/.container-->
	<!-- Bootstrap core JavaScript
    ================================================== -->
	<!-- Placed at the end of the document so the pages load faster -->
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/1.12.4/jquery.min.js"></script>
	<script src="assets/js/docs.min.js"></script>
    <script src="js/bootstrap.min.js"></script>
    <script src="https://cdn.jsdelivr.net/clipboard.js/1.5.12/clipboard.min.js"></script>
	<script>new Clipboard('.btn-copiador');</script>
    <!-- Activar popover -->
    <script>
	$(document).ready(function(){
		$('[data-toggle="popover"]').popover();
	});
	</script>
    <!-- InstanceBeginEditable name="script-extras" -->
    <!-- extras de script y demás yerbas -->
<!-- InstanceEndEditable -->
  </body>
  
<!-- InstanceEnd --></html>
<?php
mysql_free_result($rsUsuarios);

mysql_free_result($rsClientes);
?>