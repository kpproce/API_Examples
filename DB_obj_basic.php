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
                $this->password = "notavailable";
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

        public function getGames($accessCode) {
            $allowedGet = false;
            $errorMessage = $accessCode;
            $resultArray = [];
            $roles = []; // not in use in thhis version
            
            if ($accessCode==="kijk123" || $accessCode==="admin123") {
                $sql = "SELECT id, name, url, myRating FROM games";
                $result = $this->conn->query($sql);
                if ($result->num_rows > 0) {
                    // output data of each row
                    while($row = $result->fetch_assoc()) {
                        array_push($resultArray,$row);    
                    }
                }
                $errorMessage = $errorMessage . " is allowed to get (view) data" ;
                $allowedGet = true;
            } else {
                $errorMessage = $errorMessage . " not allowed, no get (view) rights" ;

            }
            $result = [
                'code'         => $accessCode, // de aan de API meegestuurde accessCode
                'roles'        => $roles,  // not in use in this version
                'allowedGet'   => $allowedGet, // voor het gemak van de ontvangende partij, wel of geen data gekregen
                'errorMessage' => $errorMessage,  
                'dataArray'    => $resultArray  // hier zit de data in.
            ];
            return $result;
        }
    }

    function testGetGameData($code) {
        $database = new Database();  
        $db = $database->getConnection();
        $result = $database->getGames($code, "noFilter");
        echo ( nl2br("\nde code: " . $result['code'] . " (" . $result['errorMessage'] . " \r\n " ));
    
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

    // *************************************************

    // dit deel is om de API en PHP code los te testen
        // echo nl2br ("\nget data: \n");
        // testGetGameData("kijken123");
        // testGetGameData("kijk123"); 
        // echo nl2br ("\n PHP: " .  phpversion());

    // *************************************************



?>

<?php ?>
