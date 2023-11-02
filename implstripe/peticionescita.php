<?php
//error_reporting(E_ALL);
//ini_set('display_errors', 'Off');

class PeticionesCita{
 
 
   private $client;
   private $result ;
   public function __construct(){
       $wsdl = Constantes::WSDL;
       $this->client = new nusoap_client($wsdl, 'wsdl');
       $this->result = array();
    }


    function getCita($idcita){
        try{
            $action = "consultaCita"; // webservice method name
            
            
            if (isset($action))
            {
                $this->result['response'] = $this->client->call($action, array('idCita' => $idcita));
            }
            if(isset($this->result['response']))
            {
             //   var_dump( $this->result['response']);
                return utf8_encode($this->result['response']['consultaCitaReturn']);
            }
        }catch(Exception $ex){
            throw new Exception("Cita no encontrada ");
        }
            
    }
    
    function actualizaClientSecret($idcita,$clientSecret){
        try{
            $action = "actualizaClientSecret"; // webservice method name
            
          
            
            if (isset($action))
            {
                $this->result['response'] = $this->client->call($action, array('idCita' => $idcita, "clientSecret"=>$clientSecret));
            }
            
           // var_dump( $this->result);
        //    $validaDatosJson = $this->result['response']['actualizaClientSecretReturn'];
         //   var_dump($validaDatosJson);
        
        }catch(Exception $ex){
            throw new Exception("Hubo un error al actualizar");
        }
    }
    
    function actualizaEstatusStripe($idcita,$estatusStripe){
        try{
            $action = "actualizaEstatusStripe"; // webservice method name
            
            if (isset($action))
            {
                $this->result['response'] = $this->client->call($action, array('idCita' => $idcita,"estatusStripe"=>$estatusStripe));
            }
            
            if ($this->client->fault) {
                throw new Exception("Hubo un error al actualizar");
            }
           
            $validaDatosJson = $this->result['response']['actualizaEstatusStripeReturn'];
         return $validaDatosJson;   
        }catch(Exception $ex){
            echo "Hubo un error al actualizar";
            throw new Exception("Hubo un error al actualizar");
        }
        
    }
    
    //  $pacienteNombre,$fechaNacimiento,$pacienteEdad,$pacienteEmail,$pacienteTelefono,$idSucursal, $idHorarioCita, $fechaCita,$estudios
    
    function guardarCita($datoscita ){
        try{
            $nvoarr=array( "pacienteNombre"=>$datoscita->pacienteNombre,
                "fechaNacimiento"=>$datoscita->pacienteFechaNacimiento,
                "pacienteEdad"=>$datoscita->pacienteEdad,
                "pacienteEmail"=>$datoscita->pacienteEmail,
                "pacienteTelefono"=>$datoscita->pacienteTelefono,
                "idSucursal"=>$datoscita->idSucursal, 
                "idHorarioCita"=>$datoscita->idHorarioCita, 
                "fechaCita"=>$datoscita->fechaCita,
                "estudios"=>$datoscita->estudios);
           // var_dump($nvoarr);
            $action = "registraCitaConPago"; // webservice method name
            
            if (isset($action))
            {
                $this->result['response'] = $this->client->call($action, $nvoarr);
            }
        
            if ($this->client->fault) {
                throw new Exception("Hubo un error al guardar la cita");
            }
            if(isset($this->result['response']))
            {
                $validaDatosJson = utf8_encode($this->result['response']['registraCitaConPagoReturn']);
              
                
                $objcita=json_decode($validaDatosJson);
           
                return $objcita->idCita;
            }
            return 0;
        
        }catch(Exception $ex){
            throw new Exception("Hubo un error al guardar la cita");
        }
    }
    
    function actualizaIdIntent($idcita,$idintent){
        try{
            $action = "actualizaIdIntent"; // webservice method name
            
            if (isset($action))
            {
                $this->result['response'] = $this->client->call($action, array('idCita' => $idcita,"idIntent"=>$idintent));
            }
            if ($this->client->fault) { // echo 'Error: '; } else { $err_msg =
                throw new Exception("Hubo un error al actualizar ".$this->client->getError());
            }
           //   var_dump( $this->result);
            $validaDatosJson = $this->result['response']['actualizaIdIntentReturn'];
           //   var_dump($validaDatosJson);
        }catch(Exception $ex){
            throw new Exception("Hubo un error al actualizar");
        }
        
    }
    /****para el webhook actualizar el estatus********/
    function actualizaEstatusStripexIntent($idIntent,$estatusStripe,$tipopago){
        try{
            $action = "actualizaEstatusStripePorIdIntent"; // webservice method name
          
            if (isset($action))
            {
                $this->result['response'] = $this->client->call($action, array('idTipoPago'=>$tipopago,'idIntent' => $idIntent,"estatusStripe"=>$estatusStripe));
            }
           
            if ($this->client->fault) {
                throw new Exception(" Hubo un error al actualizar estatus ". $this->result['response']["faultstring"]);
            }
           
            $validaDatosJson = $this->result['response']['actualizaEstatusStripePorIdIntentReturn'];
          return $validaDatosJson;
        }catch(Exception $ex){
            echo "Hubo un error al actualizar";
            throw new Exception("PeticionesCita-actualizaEstatusStripexIntent ".$ex->getMessage());
        }
        
    }
    
    function actualizaEstatusStripexCSecret($clientSecret,$estatusStripe,$tipopago){
        try{
            $action = "actualizaEstatusStripePorClientSecret"; // webservice method name
            
            if (isset($action))
            {
                $this->result['response'] = $this->client->call($action, array('idTipoPago'=>$tipopago,'clientSecret' => $clientSecret,"estatusStripe"=>$estatusStripe));
            }
            
            if ($this->client->fault) {
                throw new Exception(" Hubo un error al actualizar estatus ". $this->result['response']["faultstring"]);
            }
            
            $validaDatosJson = $this->result['response']['actualizaEstatusStripePorClientSecretReturn'];
            return $validaDatosJson;
        }catch(Exception $ex){
            echo "Hubo un error al actualizar";
            throw new Exception("PeticionesCita-actualizaEstatusStripexCSecret ".$ex->getMessage());
        }
        
    }
    
    function cancelaCita($idcita){
        try{
            $action = "cancelaCita"; // webservice method name
            
            
            if (isset($action))
            {
                $this->result['response'] = $this->client->call($action, array('idCita' => $idcita));
            }
            if(isset($this->result['response']))
            {
                //   var_dump( $this->result['response']);
                return utf8_encode($this->result['response']['cancelaCitaReturn']);
            }
        }catch(Exception $ex){
            throw new Exception("Cita no encontrada ");
        }
        
    }
   
}