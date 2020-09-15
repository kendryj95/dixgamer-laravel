<?php

namespace App\Helpers;
use Illuminate\Pagination\LengthAwarePaginator;
use App\Stock;
use App\Balance;
use DB;
class Helper
{
    // Todos menos administrador
    public static function lessAdministrator($level){
      if ($level != 'Adm') {
        return true;
      }

      return false;
    }
    // Valida si es administrador
    public static function validateAdministrator($level){
      if ($level == 'Adm') {
        return true;
      }

      return false;
    }

    // Valida si es analista o administrador
    public static function validateAdminAnalyst($level){
      if ($level == 'Adm' || $level == 'Analista') {
        return true;
      }

      return false;
    }// Valida si es analista, asistente o administrador
    public static function validateAdminAnalystAsistent($level){
      if ($level == 'Adm' || $level == 'Analista' || $level == 'Asistente') {
        return true;
      }

      return false;
    }

    public static function permissionPerUser($nombre, $acceso)
    {
      $usuarios = [
        'Fran'
      ];
      switch ($acceso) {
        case 'Gift':
          return in_array($nombre, $usuarios); // Si el usuario está en la lista, tiene permisos.
          break;
      }

      return false;
    }


    // Retorna nombre aleatorio
    public static function getRandomName(){
      $name = array("Noah", "Liam", "William", "Mason", "James", "Benjamin", "Jacob", "Michael", "Elijah", "Ethan", "Alexander", "Oliver", "Daniel", "Lucas", "Matthew", "Aiden", "Jackson", "Logan", "David", "Joseph", "Samuel", "Henry", "Owen", "Sebastian", "Gabriel", "Carter", "Jayden", "John", "Luke", "Anthony", "Isaac", "Dylan", "Wyatt", "Andrew", "Joshua", "Christopher", "Grayson", "Jack", "Julian", "Ryan", "Jaxon", "Levi", "Nathan", "Caleb", "Hunter", "Christian", "Isaiah", "Thomas", "Aaron", "Lincoln", "Charles", "Eli", "Landon", "Connor", "Josiah", "Jonathan", "Cameron", "Jeremiah", "Mateo", "Adrian", "Hudson", "Robert", "Nicholas", "Brayden", "Nolan", "Easton", "Jordan", "Colton", "Evan", "Angel", "Asher", "Dominic", "Austin", "Leo", "Adam", "Jace", "Jose", "Ian", "Cooper", "Gavin", "Carson", "Jaxson", "Theodore", "Jason", "Ezra", "Chase", "Parker", "Xavier", "Kevin", "Zachary", "Tyler", "Ayden", "Elias", "Bryson", "Leonardo", "Greyson", "Sawyer", "Roman", "Brandon", "Bentley", "Kayden", "Ryder", "Nathaniel", "Vincent", "Miles", "Santiago", "Harrison", "Tristan", "Declan", "Cole", "Maxwell", "Luis", "Justin", "Everett", "Micah", "Axel", "Wesley", "Max", "Silas", "Weston", "Ezekiel", "Juan", "Damian", "Camden", "George", "Braxton", "Blake", "Jameson", "Diego", "Carlos", "Ivan", "Kingston", "Ashton", "Jesus", "Brody", "Emmett", "Abel", "Jayce", "Maverick", "Bennett", "Giovanni", "Eric", "Maddox", "Kaiden", "Kai", "Bryce", "Alex", "Calvin", "Ryker", "Jonah", "Luca", "King", "Timothy", "Alan", "Brantley", "Malachi", "Emmanuel", "Abraham", "Antonio", "Richard", "Jude", "Miguel", "Edward", "Victor", "Amir", "Joel", "Steven", "Matteo", "Hayden", "Patrick", "Grant", "Preston", "Tucker", "Jesse", "Finn", "Oscar", "Kaleb", "Gael", "Graham", "Elliot", "Alejandro", "Rowan", "Marcus", "Jeremy", "Zayden", "Karter", "Beau", "Bryan", "Maximus", "Aidan", "Avery", "Elliott", "August", "Nicolas", "Mark", "Colin", "Waylon", "Bradley", "Kyle", "Kaden", "Xander", "Caden", "Paxton", "Brian", "Dean", "Paul", "Peter", "Kenneth", "Jasper", "Lorenzo", "Zane", "Zion", "Beckett", "River", "Jax", "Andres", "Dawson", "Messiah", "Jaden", "Rhett", "Brady", "Lukas", "Omar", "Jorge", "Riley", "Derek", "Charlie", "Emiliano", "Griffin", "Myles", "Brooks", "Israel", "Sean", "Judah", "Iker", "Javier", "Erick", "Tanner", "Corbin", "Adriel", "Jase", "Jake", "Simon", "Cayden", "Knox", "Tobias", "Felix", "Milo", "Jayceon", "Gunner", "Francisco", "Kameron", "Cash", "Remington", "Reid", "Cody", "Martin", "Andre", "Rylan", "Maximiliano", "Zander", "Archer", "Barrett", "Killian", "Stephen", "Clayton", "Thiago", "Spencer", "Amari", "Josue", "Holden", "Emilio", "Arthur", "Chance", "Eduardo", "Leon", "Travis", "Ricardo", "Damien", "Manuel", "Gage", "Keegan", "Titus", "Raymond", "Kyrie", "Nash", "Finley", "Fernando", "Louis", "Peyton", "Rafael", "Phoenix", "Jaiden", "Lane", "Dallas", "Emerson", "Cristian", "Collin", "Kyler", "Devin", "Jeffrey", "Walter", "Anderson", "Cesar", "Mario", "Donovan", "Seth", "Garrett", "Enzo", "Conner", "Legend", "Caiden", "Beckham", "Jett", "Ronan", "Troy", "Karson", "Edwin", "Hector", "Cohen", "Ali", "Trevor", "Conor", "Orion", "Shane", "Andy", "Marco", "Walker", "Angelo", "Quinn", "Dalton", "Sergio", "Ace", "Tyson", "Johnny", "Dominick", "Colt", "Johnathan", "Gideon", "Julius", "Cruz", "Edgar", "Prince", "Dante", "Marshall", "Ellis", "Joaquin", "Major", "Arlo", "Alexis", "Reed", "Muhammad", "Frank", "Theo", "Shawn", "Erik", "Grady", "Nehemiah", "Daxton", "Atticus", "Gregory", "Matias", "Bodhi", "Emanuel", "Jensen", "Kash", "Romeo", "Desmond", "Solomon", "Allen", "Jaylen", "Leonel", "Roberto", "Pedro", "Kason", "Fabian", "Clark", "Dakota", "Abram", "Noel", "Kayson", "Malik", "Odin", "Jared", "Warren", "Kendrick", "Rory", "Jonas", "Adan", "Ibrahim", "Trenton", "Finnegan", "Landen", "Adonis", "Jay", "Ruben", "Drew", "Gunnar", "Ismael", "Jaxton", "Kane", "Hendrix", "Atlas", "Pablo", "Zaiden", "Wade", "Russell", "Cade", "Sullivan", "Malcolm", "Kade", "Harvey", "Princeton", "Skyler", "Corey", "Esteban", "Leland", "Derrick", "Ari", "Kamden", "Zayn", "Porter", "Franklin", "Raiden", "Braylon", "Ronald", "Cyrus", "Benson", "Malakai", "Hugo", "Marcos", "Maximilian", "Hayes", "Philip", "Lawson", "Phillip", "Bruce", "Braylen", "Zachariah", "Damon", "Dexter", "Enrique", "Aden", "Lennox", "Drake", "Khalil", "Tate", "Zayne", "Milan", "Brock", "Brendan", "Armando", "Gerardo", "Jamison", "Rocco", "Nasir", "Augustus", "Sterling", "Dillon", "Royal", "Royce", "Moses", "Jaime", "Johan", "Scott", "Chandler", "Raul", "Remy", "Cason", "Luka", "Mohamed", "Deacon", "Winston", "Albert", "Pierce", "Taylor", "Nikolai", "Bowen", "Danny", "Francis", "Brycen", "Jayson", "Moises", "Keith", "Hank", "Quentin", "Kasen", "Donald", "Julio", "Davis", "Alec", "Kolton", "Lawrence", "Rhys", "Kian", "Nico", "Matthias", "Kellan", "Mathias", "Ariel", "Justice", "Braden", "Rodrigo", "Ryland", "Leonidas", "Jerry", "Ronin", "Alijah", "Kobe", "Lewis", "Dennis", "Luciano", "Ahmed", "Frederick", "Darius", "Arjun", "Dax", "Asa", "Nixon", "Ezequiel", "Eden", "Tony", "Landyn", "Emmitt", "Mathew", "Kyson", "Otto", "Saul", "Uriel", "Colby", "Dustin", "Omari", "Raphael", "Brennan", "Callen", "Keaton", "Arturo", "Isaias", "Roy", "Kieran", "Ty", "Dorian", "Cannon", "Marvin", "Cullen", "Sage", "Uriah", "Darren", "Cayson", "Aarav", "Case", "Izaiah", "Armani", "Gustavo", "Jimmy", "Alberto", "Duke", "Rayan", "Chris", "Casey", "Roland", "Moshe", "Curtis", "Mauricio", "Alonzo", "Yusuf", "Nikolas", "Soren", "Hamza", "Jasiah", "Alfredo", "Devon", "Jalen", "Raylan", "Edison", "Jamari", "Oakley", "Samson", "Lionel", "Reece", "Sam", "Quincy", "Jakob", "Apollo", "Kingsley", "Ahmad", "Bryant", "Alvin", "Trey", "Mohammed", "Conrad", "Mitchell", "Salvador", "Quinton", "Bo", "Mohammad", "Elian", "Gianni", "Lennon", "Leonard", "Douglas", "Cassius", "Ricky", "Carl", "Gary", "Larry", "Colten", "Ramon", "Kellen", "Korbin", "Wilson", "Kylan", "Santino", "Niko", "Issac", "Jagger", "Lance", "Joe", "Julien", "Orlando", "Jefferson", "Memphis", "Crosby", "Mekhi", "Nelson", "Lucian", "Ayaan", "Nathanael", "Neil", "Makai", "Finnley", "Rex", "Forrest", "Layton", "Randy", "Boston", "Tristen", "Tatum", "Brayan", "Sylas", "Thaddeus", "Trent", "Morgan", "Roger", "Abdullah", "Casen", "Maurice", "Sincere", "Titan", "Kyree", "Talon", "Fletcher", "Langston", "Eddie", "Briggs", "Noe", "Kamari", "Rowen", "Zeke", "Aldo", "Kaison", "Valentino", "Vihaan", "Alden", "Terry", "Bruno", "Canaan", "Lee", "Byron", "Kohen", "Reese", "Braydon", "Madden", "Deandre", "Flynn", "Harley", "Hezekiah", "Amos", "Harry", "Zain", "Alessandro", "Stanley", "Lucca", "Branson", "Ernesto", "Joziah", "Leandro", "Ares", "Marc", "Blaine", "Joey", "Jon", "Yosef", "Carmelo", "Franco", "Jamal", "Mack", "Kristian", "Dane", "Lachlan", "Callum", "Graysen", "Kye", "Ben", "Aryan", "Gannon", "London", "Kareem", "Stetson", "Kristopher", "Tomas", "Ford", "Bronson", "Enoch", "Baylor", "Kaysen", "Axton", "Jaxen", "Rodney", "Dominik", "Emery", "Layne", "Wilder", "Jamir", "Tripp", "Kelvin", "Vicente", "Augustine", "Brett", "Callan", "Clay", "Crew", "Brecken", "Jacoby", "Abdiel", "Allan", "Maxton", "Melvin", "Rayden", "Terrance", "Demetrius", "Rohan", "Wayne", "Yahir", "Arian", "Fox", "Brentley", "Ray", "Zechariah", "Cain", "Guillermo", "Otis", "Tommy", "Alonso", "Dariel", "Jedidiah", "Maximo", "Cory", "Grey", "Reyansh", "Skylar", "Marcelo", "Castiel", "Kase", "Toby", "Bobby", "Jadiel", "Marcel", "Lochlan", "Jeffery", "Zackary", "Fisher", "Yousef", "Aron", "Chaim", "Felipe", "Axl", "Anakin", "Brodie", "Dash", "Anson", "Maison", "Zaire", "Samir", "Damari", "Jonathon");


      return $name[array_rand($name, 1)];
    }

    // Retorna apellido aleatorio
    public static function getRandomLastName(){
      $lastname = array("SMITH", "JOHNSON", "WILLIAMS", "BROWN", "JONES", "GARCIA", "MILLER", "DAVIS", "RODRIGUEZ", "MARTINEZ", "HERNANDEZ", "LOPEZ", "GONZALEZ", "WILSON", "ANDERSON", "THOMAS", "TAYLOR", "MOORE", "JACKSON", "MARTIN", "LEE", "PEREZ", "THOMPSON", "WHITE", "HARRIS", "SANCHEZ", "CLARK", "RAMIREZ", "LEWIS", "ROBINSON", "WALKER", "YOUNG", "ALLEN", "KING", "WRIGHT", "SCOTT", "TORRES", "NGUYEN", "HILL", "FLORES", "GREEN", "ADAMS", "NELSON", "BAKER", "HALL", "RIVERA", "CAMPBELL", "MITCHELL", "CARTER", "ROBERTS", "GOMEZ", "PHILLIPS", "EVANS", "TURNER", "DIAZ", "PARKER", "CRUZ", "EDWARDS", "COLLINS", "REYES", "STEWART", "MORRIS", "MORALES", "MURPHY", "COOK", "ROGERS", "GUTIERREZ", "ORTIZ", "MORGAN", "COOPER", "PETERSON", "BAILEY", "REED", "KELLY", "HOWARD", "RAMOS", "KIM", "COX", "WARD", "RICHARDSON", "WATSON", "BROOKS", "CHAVEZ", "WOOD", "JAMES", "BENNETT", "GRAY", "MENDOZA", "RUIZ", "HUGHES", "PRICE", "ALVAREZ", "CASTILLO", "SANDERS", "PATEL", "MYERS", "LONG", "FOSTER", "JIMENEZ", "POWELL", "JENKINS", "PERRY", "RUSSELL", "SULLIVAN", "BELL", "COLEMAN", "BUTLER", "HENDERSON", "BARNES", "GONZALES", "FISHER", "VASQUEZ", "SIMMONS", "ROMERO", "JORDAN", "PATTERSON", "ALEXANDER", "HAMILTON", "GRAHAM", "REYNOLDS", "GRIFFIN", "WALLACE", "MORENO", "WEST", "COLE", "HAYES", "BRYANT", "HERRERA", "GIBSON", "ELLIS", "TRAN", "MEDINA", "AGUILAR", "STEVENS", "MURRAY", "FORD", "CASTRO", "MARSHALL", "OWENS", "HARRISON", "FERNANDEZ", "MCDONALD", "WOODS", "WASHINGTON", "KENNEDY", "WELLS", "VARGAS", "HENRY", "CHEN", "FREEMAN", "WEBB", "TUCKER", "GUZMAN", "BURNS", "CRAWFORD", "OLSON", "SIMPSON", "PORTER", "HUNTER", "GORDON", "MENDEZ", "SILVA", "SHAW", "SNYDER", "MASON", "DIXON", "MUNOZ", "HUNT", "HICKS", "HOLMES", "PALMER", "WAGNER", "BLACK", "ROBERTSON", "BOYD", "ROSE", "STONE", "SALAZAR", "FOX", "WARREN", "MILLS", "MEYER", "RICE", "SCHMIDT", "GARZA", "DANIELS", "FERGUSON", "NICHOLS", "STEPHENS", "SOTO", "WEAVER", "RYAN", "GARDNER", "PAYNE", "GRANT", "DUNN", "KELLEY", "SPENCER", "HAWKINS", "ARNOLD", "PIERCE", "VAZQUEZ", "HANSEN", "PETERS", "SANTOS", "HART", "BRADLEY", "KNIGHT", "ELLIOTT", "CUNNINGHAM", "DUNCAN", "ARMSTRONG", "HUDSON", "CARROLL", "LANE", "RILEY", "ANDREWS", "ALVARADO", "RAY", "DELGADO", "BERRY", "PERKINS", "HOFFMAN", "JOHNSTON", "MATTHEWS", "PENA", "RICHARDS", "CONTRERAS", "WILLIS", "CARPENTER", "LAWRENCE", "SANDOVAL", "GUERRERO", "GEORGE", "CHAPMAN", "RIOS", "ESTRADA", "ORTEGA", "WATKINS", "GREENE", "NUNEZ", "WHEELER", "VALDEZ", "HARPER", "BURKE", "LARSON", "SANTIAGO", "MALDONADO", "MORRISON", "FRANKLIN", "CARLSON", "AUSTIN", "DOMINGUEZ", "CARR", "LAWSON", "JACOBS", "OBRIEN", "LYNCH", "SINGH", "VEGA", "BISHOP", "MONTGOMERY", "OLIVER", "JENSEN", "HARVEY", "WILLIAMSON", "GILBERT", "DEAN", "SIMS", "ESPINOZA", "HOWELL", "LI", "WONG", "REID", "HANSON", "LE", "MCCOY", "GARRETT", "BURTON", "FULLER", "WANG", "WEBER", "WELCH", "ROJAS", "LUCAS", "MARQUEZ", "FIELDS", "PARK", "YANG", "LITTLE", "BANKS", "PADILLA", "DAY", "WALSH", "BOWMAN", "SCHULTZ", "LUNA", "FOWLER", "MEJIA", "DAVIDSON", "ACOSTA", "BREWER", "MAY", "HOLLAND", "JUAREZ", "NEWMAN", "PEARSON", "CURTIS", "CORTEZ", "DOUGLAS", "SCHNEIDER", "JOSEPH", "BARRETT", "NAVARRO", "FIGUEROA", "KELLER", "AVILA", "WADE", "MOLINA", "STANLEY", "HOPKINS", "CAMPOS", "BARNETT", "BATES", "CHAMBERS", "CALDWELL", "BECK", "LAMBERT", "MIRANDA", "BYRD", "CRAIG", "AYALA", "LOWE", "FRAZIER", "POWERS", "NEAL", "LEONARD", "GREGORY", "CARRILLO", "SUTTON", "FLEMING", "RHODES", "SHELTON", "SCHWARTZ", "NORRIS", "JENNINGS", "WATTS", "DURAN", "WALTERS", "COHEN", "MCDANIEL", "MORAN", "PARKS", "STEELE", "VAUGHN", "BECKER", "HOLT", "DELEON", "BARKER", "TERRY", "HALE", "LEON", "HAIL", "BENSON", "HAYNES", "HORTON", "MILES", "LYONS", "PHAM", "GRAVES", "BUSH", "THORNTON", "WOLFE", "WARNER", "CABRERA", "MCKINNEY", "MANN", "ZIMMERMAN", "DAWSON", "LARA", "FLETCHER", "PAGE", "MCCARTHY", "LOVE", "ROBLES", "CERVANTES", "SOLIS", "ERICKSON", "REEVES", "CHANG", "KLEIN", "SALINAS", "FUENTES", "BALDWIN", "DANIEL", "SIMON", "VELASQUEZ", "HARDY", "HIGGINS", "AGUIRRE", "LIN", "CUMMINGS", "CHANDLER", "SHARP", "BARBER", "BOWEN", "OCHOA", "DENNIS", "ROBBINS", "LIU", "RAMSEY", "FRANCIS", "GRIFFITH", "PAUL", "BLAIR", "OCONNOR", "CARDENAS", "PACHECO", "CROSS", "CALDERON", "QUINN", "MOSS", "SWANSON", "CHAN", "RIVAS", "KHAN", "RODGERS", "SERRANO", "FITZGERALD", "ROSALES", "STEVENSON", "CHRISTENSEN", "MANNING", "GILL", "CURRY", "MCLAUGHLIN", "HARMON", "MCGEE", "GROSS", "DOYLE", "GARNER", "NEWTON", "BURGESS", "REESE", "WALTON", "BLAKE", "TRUJILLO", "ADKINS", "BRADY", "GOODMAN", "ROMAN", "WEBSTER", "GOODWIN", "FISCHER", "HUANG", "DELACRUZ", "MONTOYA", "TODD", "WU", "HINES", "MULLINS", "CASTANEDA", "MALONE", "CANNON", "TATE", "MACK", "SHERMAN", "HUBBARD", "HODGES", "ZHANG", "GUERRA", "WOLF", "VALENCIA", "SAUNDERS", "FRANCO", "ROWE", "GALLAGHER", "FARMER", "HAMMOND", "HAMPTON", "TOWNSEND", "INGRAM", "WISE", "GALLEGOS", "CLARKE", "BARTON", "SCHROEDER", "MAXWELL", "WATERS", "LOGAN", "CAMACHO", "STRICKLAND", "NORMAN", "PERSON", "COLON", "PARSONS", "FRANK", "HARRINGTON", "GLOVER", "OSBORNE", "BUCHANAN", "CASEY", "FLOYD", "PATTON", "IBARRA", "BALL", "TYLER", "SUAREZ", "BOWERS", "OROZCO", "SALAS", "COBB", "GIBBS", "ANDRADE", "BAUER", "CONNER", "MOODY", "ESCOBAR", "MCGUIRE", "LLOYD", "MUELLER", "HARTMAN", "FRENCH", "KRAMER", "MCBRIDE", "POPE", "LINDSEY", "VELAZQUEZ", "NORTON", "MCCORMICK", "SPARKS", "FLYNN", "YATES", "HOGAN", "MARSH", "MACIAS", "VILLANUEVA", "ZAMORA", "PRATT", "STOKES", "OWEN", "BALLARD", "LANG", "BROCK", "VILLARREAL", "CHARLES", "DRAKE", "BARRERA", "CAIN", "PATRICK", "PINEDA", "BURNETT", "MERCADO", "SANTANA", "SHEPHERD", "BAUTISTA", "ALI", "SHAFFER", "LAMB", "TREVINO", "MCKENZIE", "HESS", "BEIL", "OLSEN", "COCHRAN", "MORTON", "NASH", "WILKINS", "PETERSEN", "BRIGGS", "SHAH", "ROTH", "NICHOLSON", "HOLLOWAY", "LOZANO", "RANGEL", "FLOWERS", "HOOVER", "SHORT", "ARIAS", "MORA", "VALENZUELA", "BRYAN", "MEYERS", "WEISS", "UNDERWOOD", "BASS", "GREER", "SUMMERS", "HOUSTON", "CARSON", "MORROW", "CLAYTON", "WHITAKER", "DECKER", "YODER", "COLLIER", "ZUNIGA", "CAREY", "WILCOX", "MELENDEZ", "POOLE", "ROBERSON", "LARSEN", "CONLEY", "DAVENPORT", "COPELAND", "MASSEY", "LAM", "HUFF", "ROCHA", "CAMERON", "JEFFERSON", "HOOD", "MONROE", "ANTHONY", "PITTMAN", "HUYNH", "RANDALL", "SINGLETON", "KIRK", "COMBS", "MATHIS", "CHRISTIAN", "SKINNER", "BRADFORD", "RICHARD", "GALVAN", "WALL", "BOONE", "KIRBY", "WILKINSON", "BRIDGES", "BRUCE", "ATKINSON", "VELEZ", "MEZA", "ROY", "VINCENT", "YORK", "HODGE", "VILLA", "ABBOTT", "ALLISON", "TAPIA", "GATES", "CHASE", "SOSA", "SWEENEY", "FARRELL", "WYATT", "DALTON", "HORN", "BARRON", "PHELPS", "YU", "DICKERSON", "HEATH", "FOLEY", "ATKINS", "MATHEWS", "BONILLA", "ACEVEDO", "BENITEZ", "ZAVALA", "HENSLEY", "GLENN", "CISNEROS", "HARRELL", "SHIELDS", "RUBIO", "HUFFMAN", "CHOI", "BOYER", "GARRISON", "ARROYO", "BOND", "KANE", "HANCOCK", "CALLAHAN", "DILLON", "CLINE", "WIGGINS", "GRIMES", "ARELLANO", "MELTON", "ONEILL", "SAVAGE", "HO", "BELTRAN", "PITTS", "PARRISH", "PONCE", "RICH", "BOOTH", "KOCH", "GOLDEN", "WARE", "BRENNAN", "MCDOWELL", "MARKS", "CANTU", "HUMPHREY", "BAXTER", "SAWYER", "CLAY", "TANNER", "HUTCHINSON", "KAUR", "BERG", "WILEY", "GILMORE", "RUSSO", "VILLEGAS", "HOBBS", "KEITH", "WILKERSON", "AHMED", "BEARD", "MCCLAIN", "MONTES", "MATA", "ROSARIO", "VANG", "WALTER", "HENSON", "ONEAL", "MOSLEY", "MCCLURE", "BEASLEY", "STEPHENSON", "SNOW", "HUERTA", "PRESTON", "VANCE", "BARRY", "JOHNS", "EATON", "BLACKWELL", "DYER", "PRINCE", "MACDONALD", "SOLOMON", "STAFFORD", "ENGLISH", "HURST", "WOODARD", "CORTES", "SHANNON", "KEMP", "NOLAN", "MCCULLOUGH", "MERRITT", "MURILLO", "MOON", "SALGADO", "STRONG", "KLINE", "CORDOVA", "BARAJAS", "ROACH", "ROSAS", "WINTERS", "JACOBSON", "LESTER", "KNOX", "BULLOCK", "KERR", "LEACH", "MEADOWS", "ORR", "DAVILA", "WHITEHEAD", "PRUITT", "KENT", "CONWAY", "MCKEE", "BARR", "DAVID", "DEJESUS", "MARIN", "BERGER", "MCINTYRE", "BLANKENSHIP", "GAINES", "PALACIOS", "CUEVAS", "BARTLETT", "DURHAM", "DORSEY", "MCCALL", "ODONNELL", "STEIN", "BROWNING", "STOUT", "LOWERY", "SLOAN", "MCLEAN", "HENDRICKS", "CALHOUN", "SEXTON", "CHUNG", "GENTRY", "HULL", "DUARTE", "ELLISON", "NIELSEN", "GILLESPIE", "BUCK", "MIDDLETON", "SELLERS", "LEBLANC", "ESPARZA", "HARDIN", "BRADSHAW", "MCINTOSH", "HOWE", "LIVINGSTON", "FROST", "GLASS", "MORSE", "KNAPP", "HERMAN", "STARK", "BRAVO", "NOBLE", "SPEARS", "WEEKS", "CORONA", "FREDERICK", "BUCKLEY", "MCFARLAND", "HEBERT", "ENRIQUEZ", "HICKMAN", "QUINTERO", "RANDOLPH", "SCHAEFER", "WALLS", "TREJO", "HOUSE", "REILLY", "PENNINGTON", "MICHAEL", "CONRAD", "GILES", "BENJAMIN", "CROSBY", "FITZPATRICK", "DONOVAN", "MAYS", "MAHONEY", "VALENTINE", "RAYMOND", "MEDRANO", "HAHN", "MCMILLAN", "SMALL", "BENTLEY", "FELIX", "PECK", "LUCERO", "BOYLE", "HANNA", "PACE", "RUSH", "HURLEY", "HARDING", "MCCONNELL", "BERNAL", "NAVA", "AYERS", "EVERETT", "VENTURA", "AVERY", "PUGH", "MAYER", "BENDER", "SHEPARD", "MCMAHON", "LANDRY", "CASE", "SAMPSON", "MOSES", "MAGANA", "BLACKBURN", "DUNLAP", "GOULD", "DUFFY", "VAUGHAN", "HERRING", "MCKAY", "ESPINOSA", "RIVERS", "FARLEY", "BERNARD", "ASHLEY", "FRIEDMAN", "POTTS", "TRUONG", "COSTA", "CORREA", "BLEVINS", "NIXON", "CLEMENTS", "FRY", "DELAROSA", "BEST", "BENTON", "LUGO", "PORTILLO", "DOUGHERTY", "CRANE", "HALEY", "PHAN", "VILLALOBOS", "BLANCHARD", "HORNE", "FINLEY", "QUINTANA", "LYNN", "ESQUIVEL", "BEAN", "DODSON", "MULLEN", "XIONG", "HAYDEN", "CANO", "LEVY", "HUBER", "RICHMOND", "MOYER", "LIM", "FRYE", "SHEPPARD", "MCCARTY", "AVALOS", "BOOKER", "WALLER", "PARRA", "WOODWARD", "JARAMILLO", "KRUEGER", "RASMUSSEN", "BRANDT", "PERALTA", "DONALDSON", "STUART", "FAULKNER", "MAYNARD", "GALINDO", "COFFEY", "ESTES", "SANFORD", "BURCH", "MADDOX", "OCONNELL", "ANDERSEN", "SPENCE", "MCPHERSON", "CHURCH", "SCHMITT", "STANTON", "LEAL", "CHERRY", "COMPTON", "DUDLEY", "SIERRA", "POLLARD", "ALFARO", "HESTER", "PROCTOR", "HINTON", "NOVAK", "GOOD", "MADDEN", "MCCANN", "TERRELL", "JARVIS", "DICKSON", "REYNA", "CANTRELL", "MAYO", "BRANCH", "HENDRIX", "ROLLINS", "ROWLAND", "WHITNEY", "DUKE", "ODOM", "DAUGHERTY", "TRAVIS", "TANG", "ARCHER");
      return ucwords(strtolower($lastname[array_rand($lastname, 1)]));
    }

    // Retorna nombre aleatorio
    public static function getRandomPlace(){

      $place = array("Corrientes", "Salta", "Misiones", "San Juan", "Mendoza", "Rio Negro", "Santa Cruz", "Chubut", "Tucuman",  "San Luis", "Catamarca", "Chaco");
      return $place[array_rand($place, 1)];
    }

    // Retorna nombre aleatorio
    public static function getRandomStreet(){

      $street = array("Ocean Dr", "Collins Ave", "Washintong Ave", "Meridian Ave", "Elucid Ave", "Lenox Ave");
      return $street[array_rand($street, 1)];
    }

    // Retorna numero aleatorio
    public static function getRandomNumber(){
      $chars0 = "3456789";
      $chars1 = "123456789";
      $number = substr( str_shuffle( $chars0 ), 0, 1 );
      $number .= substr( str_shuffle( $chars1 ), 0, 2 );
      return $number;
    }

    // Retorna el numero de un dia
    public static function getRandomDay(){
      $days = array("1", "2", "3", "4", "5", "6", "7", "8", "9",  "10", "11", "12", "13", "14", "15", "16", "17", "18", "19", "20", "21", "22",  "23", "24", "25", "26", "27", "28");

      return $days[array_rand($days, 1)];
    }

    // Retorna el numero de mes aleatorio
    public static function getRandomMonth(){
      $months = array("1", "2", "3", "4", "5", "6", "7", "8", "9",  "10", "11", "12");

      return $months[array_rand($months, 1)];
    }

    // Retorna un year aleatorio
    public static function getRandomYear(){
      $years = array("1980", "1981", "1982", "1983", "1984", "1985", "1986", "1987", "1988",  "1989", "1990", "1991");

      return $years[array_rand($years, 1)];
    }

    // Retorna un password aleatorio
    public static function getRandomPass(){
      $chars11 = "123456789";
      $chars22 = "ASDFG";
      $chars = "qwert";
      $password = substr( str_shuffle( $chars11 ), 0, 2 );
      $password .= substr( str_shuffle( $chars22 ), 0, 2 );
      $password .= substr( str_shuffle( $chars ), 0, 2 );
      $password .= substr( str_shuffle( $chars11 ), 0, 2 );
      return $password;
    }


    // Crea un paginador
    public static function arrayPaginator($array, $request,$perPage = 50)
    {
      $page = 1;
      if (!empty($request->page)) {
        $page = $request->page;
      }
      $offset = ($page * $perPage) - $perPage;

      return new LengthAwarePaginator(array_slice($array, $offset, $perPage, true), count($array), $perPage, $page,
          ['path' => $request->url(), 'query' => $request->query()]);
    }


    // Creando session flash titulo y body
    public static function messageFlash($title = '',$body = '', $tipo = 'success'){
      $obj = new \stdClass;
      $obj->title = $title;
      $obj->body = $body;
      \Session::flash($tipo, $obj);
    }


    public static function notEmptyShow($val,$data){
      if (!empty($val)) {
        echo $data;
      }
    }


    public static function userColor($person){
      $color = '';
      if (strpos($person, 'ariano') !== false): $color = "primary";
      elseif (strpos($person, 'antiago') !== false): $color = "info";
      elseif (strpos($person, 'rancisco') !== false): $color = "success";
      elseif (strpos($person, 'aquel') !== false): $color = "danger";
      elseif (strpos($person, 'nrique') !== false): $color = "warning";
      elseif (strpos($person, 'Leo') !== false): $color = "normal";
      elseif (strpos($person, 'Hernan') !== false): $color = "normal";
      elseif (strpos($person, 'anuel') !== false): $color = "normal";
      elseif (strpos($person, 'ictor') !== false): $color = "default";
      elseif (strpos($person, 'ugenio') !== false): $color = "primary";
      elseif (strpos($person, 'etina') !== false): $color = "danger";
      endif;

      return $color;
    }

    public static function medioCobroColor($medio){
      $color = '';
      if (strpos($medio, '_card') !== false): $color = "primary";
      elseif (strpos($medio, 'account_money') !== false): $color = "primary";
      elseif (strpos($medio, '-basic') !== false): $color = "primary";
      elseif (strpos($medio, 'digital_currency_consumer_credits') !== false): $color = "success";
      elseif (strpos($medio, 'ticket') !== false): $color = "success";
      elseif (strpos($medio, 'atm') !== false): $color = "success";
      elseif (strpos($medio, 'bacs') !== false): $color = "default";
      elseif (strpos($medio, 'yith_funds') !== false): $color = "normal";
      elseif (strpos($medio, 'paypal') !== false): $color = "primary";
      elseif (strpos($medio, 'payulatam') !== false): $color = "warning";
      else: $color = "danger";
      endif;

      return $color;
    }

    public static function medioVentaColor($medio){
      $color = '';
      if ($medio == 'MercadoLibre'): $color = "warning";
      elseif ($medio == 'Web'): $color = "info";
      elseif ($medio == 'Mail'): $color = "danger";
      endif;

      return $color;
    }



    public static function availableStock($account,$title,$console,$slot = null){
      if( ($console && ($console == "ps4")) or ($title && ($title == "plus-12-meses-slot")) ):
         if($slot && (ucwords($slot) == "primary")):
            /***     PS4 PRIMARIO     ***/
            $obj = new \stdClass;
            $obj->console = 'ps4';
            $obj->title = 'plus-12-meses-slot';
            $obj->type = 'primary';
            return [Stock::primaryOrSecundaryConsole($obj,$title)->first()->toArray()];
        elseif($slot && (ucwords($slot) == "secundary")):
            /***     PS4 SECUNDARIO     ***/
            $obj = new \stdClass;
            $obj->console = 'ps4';
            $obj->title = 'plus-12-meses-slot';
            $obj->type = 'secundary';
            return [Stock::primaryOrSecundaryConsole($obj,$title)->first()->toArray()];
        endif;
      elseif  ($console && ($console == "ps3")):
            /***     PS3     ***/
            return $ps3_stocks =  Stock::ps3ByTitle($title);
      else:
            return $ps3_stocks =  Stock::gift($title);
      endif;
    }


    // retorna el saldo actual de la cuenta saldo - gastos
    public static function getBalanceAccount($account_id){
      // Traemos el saldo de la cuenta
      $accountBalance = Balance::totalBalanceAccount($account_id)->first();

      // Gastos
      $expense = Stock::stockExpensesByAccountId($account_id)->first();

      // si la cuenta no tiene gastos validamos los datos a cero
      if (empty($expense)) {
        $expense = new \stdClass;
        $expense->costo_usd = 0;
        $expense->costo = 0;
      }

      return $accountBalance->costo_usd - $expense->costo_usd;
    }

    public static function getMonthLetter($mes)
    {
      $meses = [
        'Ene',
        'Feb',
        'Mar',
        'Abr',
        'May',
        'Jun',
        'Jul',
        'Ago',
        'Sep',
        'Oct',
        'Nov',
        'Dic'
      ];

      return $meses[$mes-1];
    }

    public static function operatorsRecoverSecu($usuario)
    {
      $operators = ['Euge_2','Enri_2','Fran_2','Leo_2','Fran_1', 'Leo_1', 'Enri_1', 'Euge_1','Beti_1'];

      if (in_array($usuario, $operators)) {
        return true;
      }

      return false;
    }

    public static function operatorsRecoverPri($usuario)
    {
      $operators = ['Kendry','Fran_1', 'Leo_1', 'Enri_1', 'Euge_1','Beti_1','Brian_1','Giuli_1'];

      if (in_array($usuario, $operators)) {
        return true;
      }

      return false;
    }

    public static function getOperatorsEspecials($tipo)
    {
      $operators = [];

      if ($tipo == 'Secu') {
        $operators = ['Kendry','Euge_2','Enri_2','Fran_2','Leo_2','Fran_1', 'Leo_1', 'Enri_1', 'Euge_1','Beti_1'];
      } elseif($tipo == 'Pri') {
        $operators = ['Kendry','Fran_1', 'Leo_1', 'Enri_1', 'Euge_1','Beti_1','Brian_1','Giuli_1'];
      }

      return $operators;
    }

    public static function formatFechaReferencia($fecha)
    {
      $meses = ["ENERO","FEBRERO","MARZO","ABRIL","MAYO","JUNIO","JULIO","AGOSTO","SEPTIEMBRE","OCTUBRE","NOVIEMBRE","DICIEMBRE"];

      $dia = date('d', strtotime($fecha));
      $mes = date('n', strtotime($fecha));
      $mes = $meses[$mes-1];
      $anio = date('Y', strtotime($fecha));

      $strFecha = "$dia de $mes de $anio";

      return $strFecha;
    }

    public static function strTitleStock($title)
    {
      $titulo = str_replace('-',' ',$title);

      return $titulo;
    }

    public static function nicetime($date)
    {
        if(empty($date)) {
            return "No hay fecha que mostrar";
        }
        
        $periods         = array("segundo", "minuto", "hora", "día", "semana", "mes", "año", "decada");
        $lengths         = array("60","60","24","7","4.35","12","10");
        
        $now             = time();
        $unix_date         = strtotime($date);
        
           // check validity of date
        if(empty($unix_date)) {    
            return "Bad date";
        }
    
        // is it future date or past date
        if($now > $unix_date) {    
            $difference     = $now - $unix_date;
            $tense         = "Hace";
            
        } else {
            $difference     = $unix_date - $now;
            $tense         = "Desde hace";
        }
        
        for($j = 0; $difference >= $lengths[$j] && $j < count($lengths)-1; $j++) {
            $difference /= $lengths[$j];
        }
        
        $difference = round($difference);
        
        if($difference == 0) {
          return "<span class=\"mini\">Desde hace un instante</span> ";
        } elseif($difference > 1) {
          $periods[$j].= "s";
        }
        
        return "<span class=\"mini\">{$tense}</span><span class=\"big\"> $difference </span><span class=\"mini\">$periods[$j]</span> ";

    }

    public static function showBtnSecuSigueJugando($account_id,$id_venta)
    {
      $operadores_especiales = self::getOperatorsEspecials('Secu');
      $show = false;

      ## CONSULTANDO SI ESTA CUENTA TIENE UNA VENTA SECUNDARIA

      $venta = DB::table('ventas')->where('ID',$id_venta)->first();

      ## CONSULTANDO SI HUBO UN CAMBIO DE CONTRASEÑA PARA ESTA CUENTA CON ALGUNOS DE LOS OPERADORES ESPECIALES.
      $cuenta_pass = DB::table('cta_pass')->where('cuentas_id',$account_id)->whereIn('usuario',$operadores_especiales)->orderBy('Day','DESC')->first();

      if ($cuenta_pass && $venta) {

        ## VALIDANDO QUE LA VENTA SE HAYA HECHO ANTES DEL CAMBIO DE CONTRASEÑA

        if ($venta->Day_modif < $cuenta_pass->Day) {
          $show = true;
        }
        
      }

      return $show;
      
    }

    public static function showBtnPriSigueJugando($account_id,$id_venta)
    {
      $operadores_especiales = self::getOperatorsEspecials('Pri');
      $show = false;

      ## CONSULTANDO SI ESTA CUENTA TIENE UNA VENTA PRIMARIA

      $venta = DB::table('ventas')->where('ID',$id_venta)->first();

      ## CONSULTANDO SI HUBO UN RESETEO PARA ESTA CUENTA CON ALGUNOS DE LOS OPERADORES ESPECIALES.
      $cuenta_reset = DB::table('reseteo')->where('cuentas_id',$account_id)->whereIn('usuario',$operadores_especiales)->orderBy('Day','DESC')->first();

      if ($cuenta_reset && $venta) {

        ## VALIDANDO QUE LA VENTA SE HAYA HECHO ANTES DEL RESETEO

        if ($venta->Day_modif < $cuenta_reset->Day) {
          $show = true;
        }
        
      }

      return $show;
      
    }

    public static function formatCodeStock($code)
    {
      $code = str_replace('-','',$code);
      $lenCode = strlen($code);
      $tam = ceil($lenCode / 4);
      $size = 4;
      $codigo = '';
      for ($i=0; $i < $tam; $i++) { 
        $start = 4*$i;
        $c = substr($code,$start,$size);
        $codigo .= $c . "-";
      }

      return trim($codigo,"-");
    }

}
