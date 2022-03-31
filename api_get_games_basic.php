<?php   
    //https://www.youtube.com/watch?v=OEWXbpUMODk
    header('Access-Control-Allow-Origin: *');
    header('Content-Type: application/json');

    // required headers
    header("Access-Control-Allow-Methods: GET");
    // header("Access-Control-Allow-Methods: POST");

    header("Access-Control-Max-Age: 3600");
    header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");
  
    function accessCodeOK($accessCode) {    
        return ($accessCode==="kijk123" || $accessCode==="admin123");
    }

    // ***********************************************************
    // Stap 1  Voor later .. database OO
    // include_once 'DB_obj_basic.php'; // is een class/object
    
     // dit moet vervangen worden door data van de database te halen met dataobject via SQL

    $jsonData = ' {
        "gameData": [
             {"id":"1", "name":"GTA 5 Expanded and Enhanced Edition","url": "https://www.gtaboom.com/gta-5-expanded-and-enhanced-faq","myRating":"8"},
             {"id":"2", "name":"Horizon Forbidden West\r\n","url":"https://www.gamesradar.com/horizon-forbidden-west-review","myRating":"9"},
             {"id":"3", "name":"Elden Ring","url":"https://www.techradar.com/reviews/elden-ring","myRating":"8"},
             {"id":"4", "name":"Mario Party Superstars (2021)","url":"https://www.gamesmeter.nl/game/20959/info/filtered","myRating":"9"}
         ]
     }';
        
    $gameData = json_decode($jsonData, true)['gameData'];
    
    // voor testen
    // print_r($gameData);

    // *************************************************************************************
    // stap 2 zet enkele variabele, vooral voor de response die terug moet vanuit deze api
    
      $response    = [];             // hierin komt alle response die terug gaat

       $infoOverParam = "--";  
       $message       = "--"; 
       $accessCode    = "--";
       $dataArray     =  [];   // zodat deze wel bestaat,we sturen namelijk wel deze variabele terug     


    // *************************************************************************************
    // STAP 3 check de parameters van de GET CALL

    if (empty($_GET)) { // no parameters passed by get
        $infoOverParam = "Je hebt geen GET parameters meegestuurd. Stuur parameter accessCode mee: bijv?accessCode=kijken123";
        $accessCode  = "X";
        $message     = "geen toegang zonder code"; 

    } else {  // Stap 3b er zijn parameters (maar je weet nog niet welke en of ze goed zijn)..., 
       
        $infoOverParam = "GET gebruikt..<br> ";

        if (isset($_GET['accessCode'])) {   // Stap 3c (true) there is a parameter accessCode ...,                

            // ********** STAP 4 Check de accesscode  *********************
            $accessCode = strtolower($_GET["accessCode"]);

            if (accessCodeOK($accessCode)) { // je mag data teruggeven
                $message = "accessCode: is okay";
                $dataArray = $gameData;

            } else {  // accessCode niet OKAY, je krijgt geen data
                $message = "accessCode: is NIET okay, u krijgt geen data"; 
            }
        
        } else {  // STAP 3c (false) er is een parameter gevonden, maar geen accessCode
            $accessCode = "";
            $infoOverParam = "Je hebt GEEN accessCode= ....  doorgegeven, dus geen toegang tot data";
            $message     = "geen toegang zonder accessCode";    
        }
    }


    // dit stuur je terug
    $response = [
        'infoOverParam' => $infoOverParam,
        'message'       => $message,
        'accessCode'    => $accessCode,
        'dataArray'     => $dataArray // de data die terug gaat
    ];

    echo json_encode($response);
    
    // let op dat je het liefst data terug stuurt. En liefst in JSON format

?>