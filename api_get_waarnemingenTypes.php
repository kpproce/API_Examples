<?php   
    //https://www.youtube.com/watch?v=OEWXbpUMODk
    header('Access-Control-Allow-Origin: *');
    header('Content-Type: application/json');

    // required headers
    header("Access-Control-Allow-Methods: GET");
    // header("Access-Control-Allow-Methods: POST");

    header("Access-Control-Max-Age: 3600");
    header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");
  
    include_once 'DB_obj_basic_CR.php'; // is een class/object
  
    $infoOverParam = "";
    $response = []; 
    $message = "";  
    $allowedGet = false;
    $dataArray = []; 
    
    if (empty($_GET)) {
        // no data passed by get
        $infoOverParam = "Je hebt geen GET parameters meegestuurd. Stuur parameter accessCode mee en eventueel gameName bijv?accessCode=kijken123";

        $response = [
            'infoOverParam' => $infoOverParam,
            'allowedGet'   => $allowedGet, 
            'message'       => "geen toegang",
            'accessCode'    => "",
            'errorMessage'  => "",
            'dataArray'     =>  $dataArray
        ];

    } else {
       
        $infoOverParam = "GET gebruikt ";
        if (isset($_GET['accessCode'])) {
            $accessCode = strtolower($_GET["accessCode"]);

            $database = new Database();      // get database connection
            $db = $database->getConnection();

            // ***********************************************************
            // je checked niet of de accessCode toegang geeft 
            // maar je vraagt direct data op via het object $database en stuurt de accessCode mee. 
            // bij het bevragen van de database (SQL) wordt de accessCode gebruikt

            // ****** hier wordt de data dus opgevraagd opgehaald ************
            $result = $database->getWaarnemingTypes();
            
            $dataArray = $result['dataArray']; 
            if (count($dataArray)>0 ) {
                $message = $message . "Er zijn games opgehaald";
            } else {
                $message = $message . "Er zijn GEEN games opgehaald";
            }

            $db->close();      
        
        } else {
            // er is geen code als parameter gevonden
            $accessCode = "";
            $infoOverParam = "Je hebt GEEN accessCode= ....  doorgegeven, dus geen toegang tot data";
        }

        $response = [
            'infoOverParam' => $infoOverParam,
            'message'       => $message,
            'accessCode'    => $accessCode,  // de aan de API meegestuurde accessCode
            'errorMessage'  => $result['errorMessage'],
            'dataArray'     => $dataArray
        ];

    }

    echo json_encode($response);
    // let op dat je data terugstuurt. In JSON format
    // echo "hallo"
?>