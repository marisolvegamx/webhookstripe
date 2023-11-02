
<?php


    require_once('../lib/nusoap.php');
    require_once('../lib/Constantes.php');
    session_start();
    include "peticionescita.php";
    
    
    
    
    try {
        $jsonStr = file_get_contents('php://input');
        $jsonObj = json_decode($jsonStr);
        //   var_dump($jsonObj);
    
        $petcit=new PeticionesCita();
        $idcita=0;
       
        //guardo la cita y la recupero
        $idcita=$petcit->guardarCita($jsonObj);
        $_SESSION["citaapagar"]=null;
        
        //datos de prueba
       // $idcita=42945;
        if($idcita>0&&isset( $_SESSION["clientSecret"]))
        {//guardo el client secret
           // echo "guardo cliente".$_SESSION["clientSecret"];
            $_SESSION["citaapagar"]=$idcita;
            $petcit=new PeticionesCita();
           
            $validaDatosJson =$petcit->actualizaClientSecret($idcita, $_SESSION["clientSecret"]);
        }
      
        echo $idcita;
    } catch (Error $e) {
        http_response_code(500);
        echo json_encode(['error' => $e->getMessage()]);
    }