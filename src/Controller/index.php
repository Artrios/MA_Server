<?php

$BASE_URI = "/pokemonrse/";
$endpoints = array();
$requestData = array();

//collect incoming parameters
switch ($_SERVER['REQUEST_METHOD']) {
    case 'POST':
        $requestData = $_POST;
        break;
    case 'GET':
        $requestData = $_GET;
        break;
    case 'DELETE':
        $requestData = $_DELETE;
        break;
    case 'PUT':
    case 'PATCH':
        parse_str(file_get_contents('php://input'), $requestData);

        //if the information received cannot be interpreted as an arrangement it is ignored.
        if (!is_array($requestData)) {
            $requestData = array();
        }

        break;
    default:
        break;
}



// Auth procedure
// Will only be performed if hash is incluided as a query
// All functions give the user a session that is to be used for the next request
// The check online function however seems to immediately return the requested data
function doAuth() {
    if(isset($_GET['pid'])) { // is the Auth ID set?
        $pid = $_GET['pid'];
        if(!isset($_GET['hash'])) { // If Auth ID isnt set but there's an Auth header, that means they've sent us something to check.
            // Generate a token
            $token = substr(str_shuffle(str_repeat($x='0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ', ceil(32/strlen($x)) )),1,32); //substr(base64_encode(random_bytes(32)), 0, 32);
            

            $hash = sha1("sAdeqWo3voLeC5r16DYv".$token);

            // Temporarily store the token using the part that the game sends back as key
            session_id($hash);
            session_start();
            $_SESSION['pid'] = $pid;

            header_remove();
            //libma doesn't like getting 401 response so setting to 200 for now
            http_response_code(200);
            header('Content-Length: 32');
            print $token;
            exit();
        } else {
            $hash = $_GET['hash'];
            
            // Get the full token back
            session_id($hash);
            session_start();
            if (!isset($_SESSION['pid'])) {
                // If the pid is not set here, something is wrong with the auth string
                header_remove();
                http_response_code(401);
                exit();
            }

            if ($pid != $_SESSION['pid']) {
                // If the pid is wrong then this is a different player
                header_remove();
                http_response_code(401);
                exit();
            }

            session_destroy();
            
        }
    }
    
    header_remove();
    // When getting here, everything should be OK
    return $hash;
}

function decrypt_data(){
    $rData = $_GET['data'];
    $pid = $_GET['pid'];
    $checksum = $rData[0].$rData[1].$rData[2].$rData[3];
    $checksum = 0x4a3b2c1d xor $checksum;

    $rData = substr($rData, 4);
    $rData = str_replace("-", "+", $rData);
    $rData = str_replace("_", "/", $rData);
    $rData = base64_decode($rData);

    $GRNG = $checksum | ($checksum << 16);
    $keystream = ($GRNG >> 16) & 0xFF;
    $i=4;
    while($i<len($rData)){
        $data[i]=$data[i] ^ $keystream;
        $GRNG = ($GRNG * 0x45 + 0x1111) & 0x7FFFFFFF;
        $keystream = ($GRNG >> 16) & 0xFF;
    }

    //Check if checksum is same
    $verify = 0;
    for($i = 0; $i <= strlen($pid); $i++){
        $verify=$verify+$pid[$i];
    }

    for($i = 0; $i <= strlen($rData); $i++){
        $verify=$verify+$rData[$i];
    }

    if($verify != $checksum){
        return 0;
    }

    return $data;
}
