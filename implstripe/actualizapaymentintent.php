
<?php

require_once './vendor/autoload.php';
require_once('lib/nusoap.php');
require_once('lib/Constantes.php');
session_start();
include "peticionescita.php";

/***recibe la respuesta de  stripe******/

function actualizarClientSecret($clientSecret,$payment_intent,$redirect_status){
    switch ($redirect_status) {
        case 'succeeded':
            $estatus=2;
            break;
            
        case 'processing':
            $estatus=1;
            break;
            
        case 'requires_payment_method':
            //sigue pendiente
            // Redirect your user back to your payment page to attempt collecting
            // payment again
            break;
            
        default:
            $estatus=3;
            break;
    }
    //guardo en la bd
    $petcit=new PeticionesCita();
    $validaDatosJson =$petcit->actualizaEstatusStripe($_SESSION["citaapagar"],$estatus);
    //guardo el paymentintent
    $petcit2=new PeticionesCita();
    $validaDatosJson =$petcit2->actualizaIdIntent($_SESSION["citaapagar"],$payment_intent);
    // echo "se actualizo la cita".$_SESSION["citaapagar"];
}



try {
    foreach($_GET as $nombre_campo=>$valor) {
        $asignacion = "\$" . $nombre_campo . "='" .filter_input(INPUT_GET,$nombre_campo, FILTER_SANITIZE_STRING). "';";
        
        eval($asignacion);
        // echo $asignacion;
    }
    
    
    actualizarClientSecret($payment_intent_client_secret,$payment_intent,$redirect_status);
    
} catch (Exception $e) {
    
    $e->getMessage();
}