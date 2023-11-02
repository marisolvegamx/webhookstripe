
<?php

require_once '../vendor/autoload.php';

require_once('../lib/nusoap.php');
require_once('../lib/Constantes.php');
session_start();
include "peticionescita.php";



function calcularTotal($validaDatosJson): int { //calculo el total de la compra del json del formulario
    $total=0;
   
  
    if(isset($validaDatosJson))
    { 
      
        if(isset($validaDatosJson->estudiosSelectedJSON)){
            $estudios=json_decode($validaDatosJson->estudiosSelectedJSON);
            foreach ($estudios as $estudio) {
               // echo "<br>".$estudio->importe;
                $total+=$estudio->costo;
            }
        }
    }
    return $total*100; //stripe lo maneja en centavos
}


function obtenerIdSuc($jsonObj){ //busco la sucursal en el json del formulario
    if(isset($jsonObj))
    {
        
        if(isset($jsonObj->idSucursal)){
            
            return $jsonObj->idSucursal;
        }
            
    }
    return 0; //no la encontré
}


try {
    $jsonStr = file_get_contents('php://input');
    $jsonObj = json_decode($jsonStr);
    
    $idSucursal=obtenerIdSuc($jsonObj);
    if($idSucursal<=0){
        throw new Exception("No seleccionó sucursal sucursal");
    }
    $_SESSION["idsucursal"]=null;
    $_SESSION["idsucursal"]=$idSucursal;
    require_once '../secrets_'.$idSucursal.'.php';
    \Stripe\Stripe::setApiKey($stripeSecretKey);
 //   var_dump($jsonObj);
  //  $idcita=$jsonObj->idcita;
   // $idcita=42945;
  
   //devuelve el monto total
    $monto=calcularTotal($jsonObj);
    if($monto==0)
        return;
    $_SESSION["clientSecret"]=null;
   // echo $monto;
   // die();
    // Create a PaymentIntent with amount and currency
    $paymentIntent = \Stripe\PaymentIntent::create([
        'amount' => $monto,
        'currency' => 'mxn',
        'automatic_payment_methods' => [
            'enabled' => true,
        ],
    ]);

    $output = [
        'clientSecret' => $paymentIntent->client_secret,
    ];
    //guardo el client secret
    $_SESSION["clientSecret"]=$paymentIntent->client_secret;
    echo json_encode($output);
} catch (Error $e) {
    http_response_code(500);
    echo json_encode(['error' => $e->getMessage()]);
}