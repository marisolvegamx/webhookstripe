<?php
require_once '../vendor/autoload.php';
require_once('../lib/nusoap.php');
require_once('../lib/Constantes.php');
include "peticionescita.php";

//busco mas datos del pago

$stripe= new \Stripe\StripeClient($stripeSecretKey);
class WebhookController
{
    //stripe listen --forward-to http://localhost/stripe-sample-code/public2/stripewebhook.php --skip-verify
    
    public static function index($endpoint_secret)
    {
        
        
        $payload = @file_get_contents('php://input');
        $sig_header = $_SERVER['HTTP_STRIPE_SIGNATURE'];
        
        $event = null;
        
        try {
           // $event = \Stripe\Event::constructFrom(
          //     json_decode($payload, true));
        $event = \Stripe\Webhook::constructEvent( $payload, $sig_header, $endpoint_secret);
            
            //  var_dump($event);
        } catch(\UnexpectedValueException $e) {
            $e->printStacktrace();
            WebhookController::guardarError("WebhookController ".$e->getMessage());
            // Invalid payload
            http_response_code(400);
            exit();
        } catch(\Stripe\Exception\SignatureVerificationException $e) {
            WebhookController::guardarError("Hubo una petición que no era de stripe");
            // Invalid signature
            http_response_code(400);
            exit();
        }
        
        // Handle the event
        switch ($event->type) {
            case 'payment_intent.succeeded':
                $paymentIntent = $event->data->object; // contains a \Stripe\PaymentIntent
                WebhookController::guardarError("Llegó un payment_intent.succeeded:".$paymentIntent);
                // Then define and call a method to handle the successful payment intent.
                //guardar en la bd
                // $existe=actualizarEstatus($paymentIntent->id);
                $estatus=2;
                // "status" =>$paymentIntent->status,
                
                break;
                
                
          /* case 'payment_intent.processing':
                $paymentIntent = $event->data->object;  // contains a \Stripe\PaymentMethod
                //guardo en bitacora
                WebhookController::guardarError("Llegó notificacion de payment_intent.processing:".$session);
                //        var_dump($session);
                
                //        //verifico que no exista
                
                $estatus=1;//sigue en 1
                
                break;*/
                
                
            case 'payment_intent.payment_failed':
                $paymentIntent = $event->data->object;   // contains a \Stripe\PaymentMethod
                
                //guardo en bitacora
                WebhookController::guardarError("Llegó notificacion de payment_intent.payment_failed:".$session);
                
                
                $estatus=3;
                break;
                // ... handle other event types
                
            default:
                //echo "ninguna";
                // Unexpected event type
                http_response_code(200);
                exit();
        }
        WebhookController::guardarError($paymentIntent->id);
        
        //actualizo el estatus
        if(isset($paymentIntent)&&isset($paymentIntent->id)){
            try{
               
               global $stripe;
                
                $paymentIntent=$stripe->paymentIntents->retrieve(
                    $paymentIntent->id,
                    []
                    );
                WebhookController::guardarError("Pidiendo pi:".$paymentIntent);
                
                $tipotarjeta=0;
                //busco el tipo de tarjeta
                if($estatus==2)
                {  $cargos= $paymentIntent->charges->data;
                    if(sizeof($cargos)>0)
                        $vartipotarjeta=$cargos[0]->payment_method_details->card->funding;
                    
                    //credit, debit, prepaid, or unknown
                    
                    if($vartipotarjeta=="credit")
                        $tipotarjeta=5;
                        else if($vartipotarjeta=="debit")
                            $tipotarjeta=6;
                            echo $tipotarjeta;
                }
                WebhookController::guardarError("datos:".$paymentIntent->client_secret."--".$estatus."--".$tipotarjeta);
                
                $petcit=new PeticionesCita();
                $validaDatosJson =$petcit->actualizaEstatusStripexCSecret($paymentIntent->client_secret,$estatus,$tipotarjeta);
                
                WebhookController::guardarError("Respuesta actualizaEstatusStripexCSecret:".$validaDatosJson);
                if(strpos($validaDatosJson,"No existe una cita para el IdIntent" )===false)
                {  //se actualizó
                    http_response_code(200);
                
                
                }else {
                  
                    http_response_code(400);
                
                }
            }catch(Exception $ex){
              
                WebhookController::guardarError($ex->getMessage());
                http_response_code(400);
            }
        }
        else  {
            
            http_response_code(400); //para que lo vueva a enviar
        }
        
    }
    
    public static function guardarError($mensaje){
        
        date_default_timezone_set('America/Mexico_City');
        $fecvis=date("Y-m-d H:i:s");
        
        error_log("\n".$fecvis.": ".$mensaje,3,"../logs/enviosstripe.log");
        
    }
}