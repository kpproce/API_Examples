<?php   
    class Database{

        private $servername;
        private $username;
        private $password;
        private $myDatabase;
        private $conn;
        private $connectMessage = "";
        public  $connected = false;

        public function __construct(){
           
            $this->user_host = $_SERVER['HTTP_HOST']; //
           
            if  ($this->user_host == "localhost") {
                // echo "localhost:";
                $this->servername = "localhost";
                $this->username = "root";
                $this->password = "";
                $this->myDatabase = "games";

            } else {
                // echo "geen localhost:";
                $this->servername = "localhost";
                $this->username = "kimproce_gegevens";
                $this->password = "K3kuEjxwQV55";
                $this->myDatabase = "kimproce_games";
            }
        }


        public function getConnection() {
            // https://www.codeofaninja.com/2014/06/php-object-oriented-crud-example-oop.html
            // https://www.youtube.com/watch?v=OEWXbpUMODk
            // echo "Get_data.php wordt aangeroepen " . " host:" . $this->user_host;

            $this->conn = new mysqli($this->servername, $this->username, $this->password,$this->myDatabase);
            if ($this->conn->connect_error) {
                $this->connectMessage = "Connection failed: " . $this->conn->connect_error;
            } else {
                $this->connectMessage = "Connected successfully";
                $this->connected = true;
            }
            return $this->conn;
        } 

        
        public function getLastConnectMessage(){
            return $this.connectMessage;
        }


        public function getRoles($accessCode) {
            // get role based on $accessCode
            // roles are CRUD (ore a part like CR) 
            // er zit een houdbaarheid aan de rechten bij de rol

            // **************   TEST of databaseDeel werkt *************
            // De accessCode:  staatAlleenInDatabase -- kun je gebruiken om te testen, die staat in de database bij toegestande codes
            // **************  Je kunt uiteraard ook zelf een code toevoegen in je database  *************

            $stmt = $this->conn->prepare("SELECT roles, validTill FROM accesscodes WHERE code=?");
            $stmt->bind_param("s", $accessCode);
            // echo (" code:" . $code);
            $result = $stmt->execute();
            $result = $stmt->get_result();

            $roles = "X";
            $codeErrorMessage = "no error";

            // nog bouwen code outdatet

            if ($result->num_rows>0) { // code found in the accessCodes list
                $errorMessage = " code: " . $accessCode . " found ";
                $codeFound = true;

                while($row = $result->fetch_assoc()) {
                    $roles     = $row["roles"];
                    $validTill = $row["validTill"];
                }
            } else {
                $errorMessage = "Access denied, code: " . $accessCode . " NOT FOUND " ;
                $codeFound = false;
            }

            $result = [ 
                'code'         => $accessCode,
                'codeFound'    => $codeFound,
                'roles'        => $roles,
                'errorMessage' => $errorMessage   
           ];
           return $result;
        
        }

        public function allowedRead($rolesResult) {
            // checks if this roles contains an R
            $posR = strpos($rolesResult['roles'], 'R');
            // echo (" ---- " . $roles['roles'] .  " R gezien op positie : " . $posR);
            if ($posR || !$rolesResult['codeFound'] ) {
                // echo (' false ');
                return false; 
               
            } else {
                //if ()
                // echo (' true ');
                return true;
            }
            // return str_contains($accessCode, "R"); 
        }


        public function getGames($accessCode, $nameFilter) {
            $allowedGet = false;
            $errorMessage = $accessCode;
            $resultArray = [];
            $rolesResult = $this->getRoles($accessCode);
            $roles = $rolesResult['roles'];
            
            if ($this->allowedRead($rolesResult)) {
                $allowedGet = true;
                $sql = "SELECT id, name, url, myRating FROM games";
                $result = $this->conn->query($sql);
                if ($result->num_rows > 0) {
                    // output data of each row
                    while($row = $result->fetch_assoc()) {
                        array_push($resultArray,$row);    
                    }
                }
                $errorMessage = $errorMessage . " is allowed to get (view) data" ;
               
            } else {
                $errorMessage = $errorMessage . " not allowed, no get (view) rights" ;
            }
            $result = [ 
                'code'         => $accessCode,
                'roles'        => $roles,
                'allowedGet'   => $allowedGet,
                'errorMessage' => $errorMessage,  
                'dataArray'    => $resultArray
            ];
            return $result;

        }

        public function insertData($sql) {
            if ($this->$conn->query($sql) === TRUE) {
              return "New record created successfully";
            } else {
              return "Error: " . $sql . "<br>" . $this->$conn->error;
            }
        }   
    }

function testGetGameData($code) {
    $database = new Database();  
    $db = $database->getConnection();
    $result = $database->getGames($code, "noFilter");
    echo( nl2br ("\nde code: " . $result['code'] . " levert de volgende rechten: " . $result['allowedGet'] 
        . " __ (" . $result['errorMessage'] . " \r\n " ));
 
    $dataArray = $result['dataArray']; 
    if (count($dataArray)>0 ) {
        foreach ($dataArray as $game) {
            echo ($game['name'] . " " . $game['myRating']);
            echo nl2br ("\n");
        }
    } else {
        echo nl2br ("Er zijn geen games");
         
    }

    $db->close();
}

function test_getAccessRoles($code) {
    // in: accesscode, uit: resultaat, met roles/rechten behorende bij die code.

    $database = new Database();  
    $db = $database->getConnection();
    $result = $database->getRoles($code);
    // echo ( "result: ");
    // print_r($result);
    // nl2br leest het als htmlcode zodat de linebreak aan het einde wordt gezien.
    echo( nl2br (" de code: " . $result['code'] . " levert de volgende rechten: " . $result['roles'] . 
    " __ (" . $result['errorMessage'] . " \r\n " ));
    $db->close();

}

function testInsertData() {
    $database = new Database();  
    $db = $database->getConnection();
    $resultTable = $database->getData_AssArray("INSERT INTO messages() "); // result = array met objecten
    $htmlTable = $database->getData_HTMLTable($resultTable); // result is een htmltable van deze data
    echo $htmlTable;
}
    //  testgetRoleWithAccessCode("beheer", "12");
    // testGetData();

    /* 
    echo nl2br ("check rechten: \n");
    test_getAccessRoles("kijken123");
    test_getAccessRoles("beheer12");
    
    echo nl2br ("\nget data: \n");
    testGetGameData("kijken123");
    testGetGameData("kijken13"); 
    */
    // echo nl2br ("\n PHP: " .  phpversion());

?>

<?php ?>
