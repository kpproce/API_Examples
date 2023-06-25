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
    $DB_ErrorMessage = "";
    $accessCode = "geen"; 
    $from = new DateTime(); $from->setDate(1900, 1, 1); 
    $to = new DateTime();
    $actie = "geen";
    $type = "-1";
    $waarde = "-1";
    $stop = false;

    $dataArray = []; 

    //  in de get(post) meesturen:
    //  bij read:
    //      actie=read (verplicht)
    //      from (mag)
    //      to (mag)
    //  
    //  bij insert: 
    //      actie = insert
    //      accessCode = edit123 of admin123 
    //      date_time --> hoeft niet
    //      type = 1 .. zoveel als er zijn in de tabel waarneming_types
    //      waarde = -1 - 10 

    if (empty($_GET)) {
        // no data passed by get
        $infoOverParam = "Je hebt geen parameters meegestuurd naar API";
        $stop = true;

    } else {
        $infoOverParam = "GET gebruikt ";
        if (isset($_GET['actie'])) {
            $actie = strtolower($_GET["actie"]);
        } else {
            $message = $message . " geen actie aangeven (create of read.. ";    
            $stop = true;
        }   

        if (strtolower($actie) === "create" && !$stop) {
            if (isset($_GET['accessCode'])) {
                $accessCode = $_GET['accessCode'];
                if ($accessCode == "edit123" || $accessCode == "admin123") {
                    if (isset($_GET['waarde'])) {$waarde = strtolower($_GET["waarde"]); } 
                    else { $message = $message . "geen waarde gevonden, waarde wordt -1 ";};
                    if (isset($_GET['type'])) {$type = strtolower($_GET["type"]); }
                    else { $message = $message . "geen type gevonden, type wordt -1 ";};
                } else {
                    $message = $message . " Accesscode " . $accessCode.  " is niet voldoende voor insert.. ";  
                    $stop = true;
                }

            } else {
                $message = $message . " geen Accesscode doorgegeven via API.. ";  
                $stop = true;  
            }
        }

        // read is nu nog geen input nodig, later ==> from to

        if (!$stop) {
            $database = new Database();      // get database connection
            $db = $database->getConnection();

            // *************** de READ ****************

            if (strtolower($actie) == "read") {
                $result = $database->getWaarnemingen("kijk123",  new DateTime(),  new DateTime());
                $dataArray = $result['dataArray']; 
                $DB_ErrorMessage = $result['errorMessage'];
                if (count($dataArray)>0 ) {
                    $message = $message . count($dataArray).  " waarnemingen opgehaald";
                } else {
                    $message = $message . "resultaat is 0 waarnemingen";
                }
                
            } elseif (strtolower($actie) == "create") {
                
                $result = $database->insertWaarneming($accessCode, $type, $waarde );        
                $message = "niewe waarneming ingevoerd: " . " op " . $to->format('d-M-Y H:i:s') . " waarde: " . $waarde . " type: " . $type;
            
            } elseif (strtolower($actie) == "readtypes") {
                
                $result = $database->getWaarnemingTypes();
                $dataArray = $result['dataArray']; 
                $message = "Waarnemingstypes opgehaald ";
        }
            $db->close();    
        }
    }
      

    $response = [
        'actie'         => $actie,
        'infoOverParam' => $infoOverParam,
        'type'          => $type,
        'waarde'        => $waarde,
        'message'       => $message,
        'accessCode'    => $accessCode,  // de aan de API meegestuurde accessCode
        'errorMessage'  => $DB_ErrorMessage,
        'from'          => $from->format('Y-M-d'),
        'to'            => $to->format('Y-M-d'),
        'dataArray'     => $dataArray
    ];

    echo json_encode($response);
    // echo ($response)
    // let op dat je data terugstuurt. In JSON format
    // echo "hallo"
?>