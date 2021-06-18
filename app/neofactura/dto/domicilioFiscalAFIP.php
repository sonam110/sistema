<?php
include_once (__DIR__ . '/enums/tipoDomicilioAFIP.php');

class DomicilioFiscalAFIP {
    
    public $tipoDomicilio;
    public $direccion = "";
    public $localidad = "";
    public $codPostal = 0;
    public $idProvincia = 0;
    public $descripcionProvincia = "";
    // public $tipoDatoAdicional;
    // public $datoAdicional = "";

    public function __construct($responseObject)
    {
        $this->_map($responseObject);
    }

    private function _map($responseObject) {
        $this->tipoDomicilio = $responseObject->tipoDomicilio;
        $this->direccion = $responseObject->direccion;
        $this->localidad = $responseObject->localidad;

        if (isset($responseObject->codPostal)) {
            // Compataibilidad con PadronA5
            $this->codPostal = $responseObject->codPostal;
        } else if (isset($responseObject->codigoPostal)) {
            $this->codPostal = $responseObject->codigoPostal;
        }

        $this->idProvincia = $responseObject->idProvincia;
        $this->descripcionProvincia = $responseObject->descripcionProvincia;
    }
}