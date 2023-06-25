<?php   
    //https://www.youtube.com/watch?v=OEWXbpUMODk

    // dit is een ingekorte versie van de API, waar veel uitgehaald is van checks en veiligheid

    header('Access-Control-Allow-Origin: *');
    header('Content-Type: application/json');

    // required headers
    header("Access-Control-Allow-Methods: GET");
    // header("Access-Control-Allow-Methods: POST");

    header("Access-Control-Max-Age: 3600");
    header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");
  

    $errorMessage = "--";  // moet je hier declaren

    // Read input
    $name     = $_GET["name"];  
    $myRating = $_GET["myRating"]; 
    $url      = $_GET["myRating"]; 
    $accessCode = strtolower($_GET["accessCode"]);
    // $id wordt niet gebruikt, want die wordt door database gegenereerd.
    
    $accessCode = strtolower($_GET["accessCode"]);
    
        // if data (like name) is not send it will be default, zie regel 22-24

    $database = new Database();      // get database connection
    $db = $database->getConnection();
    
            $errorMessage = $database->insertGame($accessCode, $name, $url, $myRating);
    
    $db->close();      

    echo json_encode($errorMessage);  // errorMessage is een text

    /*
        http://localhost/php_api_test/apiAstrum_Games/basic_example/api_new_game.php?accessCode='edit123'&name='mygame12'&url='www.myurl126'.nl&myScore=8
    */
?>