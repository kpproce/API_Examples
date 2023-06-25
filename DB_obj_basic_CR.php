<?php   
    class Database {

        // Deze class handelt vooral de SQL commandos af, insert, select etc. 
        // Onderin dit bestand staan een aantal testfuncties. Om te kijken of het werkt.

       private $servername;
        private $username;
        private $password;
        private $myDatabase;
        private $conn;
        private $connectMessage = "";
        public  $connected = false; 

        public function __construct(){
        // 
        }

        public function getConnection() {
            // https://www.codeofaninja.com/2014/06/php-object-oriented-crud-example-oop.html
            // https://www.youtube.com/watch?v=OEWXbpUMODk
           
            $this->conn = new mysqli("localhost", "root", "","waarnemingen");
            if ($this->conn->connect_error) {
                $this->connectMessage = "Connection failed: " . $this->conn->connect_error;
            } else {
                $this->connectMessage = "Connected successfully";
                $this->connected = true;
            }
            return $this->conn;
        } 


        public function insertWaarneming($accessCode, $type, $waarde ) {
            $errorMessage = $accessCode;
            if ($accessCode==="edit123" || $accessCode==="admin123") {
                $sql = "INSERT INTO waarneming (type, waarde) VALUES ($type, $waarde)";
                
                if ($this->conn->query($sql)) { // if true (waar) dan is het gelukt
                    // $this is de connectie met de database, daar stel je de vraag aan, in dit geval een insert

                    $errorMessage = "waarneming opgeslagen"; 
                } else {
                    $errorMessage = "waarneming opgeslagen, " . $this->conn->error;
                }        
                // moet nog prepare en bind_parameter gebruiken 
                // zie bijv. https://www.php.net/manual/en/mysqli.prepare.php

            } else {
                $errorMessage = "$accessCode is not allowed, no edit or insert " ;
            }
          
            return $errorMessage; // alleen een tekst
        }

        public function getWaarnemingTypes() { //accesscode niet nodig voor opvragen lijst
            $allowedGet = false;
            $errorMessage = '';
            $resultArray = [];
            $roles = []; // not in use in thhis version
                
                $sql = "SELECT id, omschrijving FROM waarneming_types";
                $result = $this->conn->query($sql);
                // wat doe je als er geen data is? Of error?
                if ($result->num_rows > 0) {
                    // output data of each row
                    while($row = $result->fetch_assoc()) {
                        array_push($resultArray,$row);    
                    }
                }
          
            // ******************** ERROR HANDLING ********************************
            // Als de database een errorcode geeft zou je die in $errorMessage moeten zetten
            // https://www.w3schools.com/php/func_mysqli_error.asp 

            // gebruik eventueel try catch...

            $result = [
                'errorMessage' => $errorMessage,  
                'dataArray'    => $resultArray  // hier zit de data in.
            ];
            return $result;
        }

        public function getWaarnemingen($accessCode, $fromDate, $toDate) {
            $allowedGet = false;
            $errorMessage = $accessCode;
            $resultArray = [];
            
            if ($accessCode==="kijk123" || $accessCode==="admin123") {
                $sql = "SELECT * FROM waarnemingen_dagdelen";
                $result = $this->conn->query($sql);
                // wat doe je als er geen data is? Of error?
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

            // ******************** ERROR HANDLING ********************************
            // Als de database een errorcode geeft zou je die in $errorMessage moeten zetten
            // https://www.w3schools.com/php/func_mysqli_error.asp 

            // gebruik eventueel try catch...

            $result = [
                'errorMessage' => $errorMessage,  
                'dataArray'    => $resultArray  // hier zit de data in.
            ];
            return $result;
        }
      
    }

    function testGetWaarnemingen($code, $fromDate, $toDate) {
        $database = new Database();  
        $db = $database->getConnection();
        $result = $database->getWaarnemingen($code, "noFilter");
        echo ( nl2br("\nde code: " . $result['code'] . " (" . $result['errorMessage'] . " \r\n " ));
    
        $dataArray = $result['dataArray']; 
        if (count($dataArray)>0 ) {
            foreach ($dataArray as $waarneming) {
                echo ($waarneming['date_time'] . " " . $waarneming['type'] . " " . $waarneming['omschrijving'] );
                echo nl2br ("\n");
            }
        } else {
            echo nl2br ("Er zijn geen waarnemingen");     
        }

        $db->close();
    }

    function testInsertGame($code, $name, $url, $myRating ) {

        // hiermee kun je testen of het insert-deel werkt.  
        $database = new Database();  
        $db = $database->getConnection();
        $result = $database->insertGame($code, $name, $url, $myRating);
        echo ( nl2br("\n" . $result['errorMessage'] . " \r\n " ));
    
      
        $db->close();
    }

    // *************************************************

    // dit deel is om de API en PHP code los te testen
        // echo nl2br ("\nget data: \n");
            // testGetWaarnemingen('kijk123')
        // testInsertGame('edit123', 'nieuwe Game 123', 'www.myGame123.nl', 3 ) 
        // echo nl2br ("\n PHP: " .  phpversion());

    // *************************************************
    // echo ("zo werkt dit")


?>

<?php ?>
