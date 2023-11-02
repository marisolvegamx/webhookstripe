
<?php

require_once('../lib/nusoap.php');
require_once('../lib/Constantes.php');
session_start();
include "peticionescita.php";


try {
    foreach($_POST as $nombre_campo=>$valor) {
        $asignacion = "\$" . $nombre_campo . "='" .filter_input(INPUT_POST,$nombre_campo, FILTER_SANITIZE_STRING). "';";
        
        eval($asignacion);
        // echo $asignacion;
    }
    
    
    $petcit=new PeticionesCita();
    $validaDatosJson =$petcit->actualizaEstatusStripe($idcita,3);
    $validaDatosJson =$petcit->cancelaCita($idcita);
    echo json_encode(['response' => "Se canceló la cita"]);
} catch (Exception $e) {
    
    echo $e->getMessage();
}

