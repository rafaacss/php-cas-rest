<?php

namespace CasPHP\Cas;

use CasPHP\Cas\Form;

class HttpHandler
{
    private $_curlOptions = array(
        CURLOPT_USERAGENT         => 'Mozilla/5.0 (Windows; U; Windows NT 5.0; en-US; rv:1.7.12) Gecko/20050915 Firefox/1.0.7',
        CURLOPT_HTTPHEADER        => array("Content-Type:application/x-www-form-urlencoded"),
        CURLOPT_RETURNTRANSFER    => true,
        CURLOPT_HEADER            => false,
        CURLOPT_FOLLOWLOCATION    => false,
    );
    
    private $form;

    private $caCertPath = '';
    
    private $validarCa;
    
    private $url;
    
    private $debug = false;
    
    public function __construct(Form $form = null)
    {
        $this->form = $form;
    }

    /**
     * Set additional curl options
     *
     * @param array $options option to set
     *
     * @return void
     */
    public function setCurlOptions (array $options)
    {
        $this->_curlOptions = array_replace($this->_curlOptions, $options);
    }
    
    public function getCurlOptions(){
        return $this->_curlOptions;
    }
    
    public function setType($type){
        $this->_curlOptions = array_merge($this->_curlOptions, array(CURLOPT_CUSTOMREQUEST => $type));
    }
    
    public function setUrl($url = null)
    {
        $this->url = $url;
    }
    
    public function setCaCert ($caCertPath, $validarCa = true)
    {
        $this->caCertPath   = $caCertPath;
        $this->validarCa    = $validarCa;
    }
    
    public function setDebug($debug){
        $this->debug = $debug;
    }
    
    public function sendRequest()
    {

        $ch = $this->init();
        
        if($this->debug){
            echo '<pre><<<=== ';
            $out = fopen('php://output', 'w');
            curl_setopt($ch, CURLOPT_VERBOSE, true);
            curl_setopt($ch, CURLOPT_STDERR, $out);
        }
        
        $buf = curl_exec($ch);
        
        if($this->debug){
            fclose($out);
            var_dump(curl_error($ch));
            exit('===>>> debug');
        }
        
        if ( $buf === false ) {
           $res = false; 
        } else {
            $res = $buf; 
        }

        curl_close($ch);
        return $res;
    }
    
    /**
     * Metodo interno para montar o curl com os options passados por setCurlOptions
     *
     * @return resource cURL handle em caso de sucesso, false em caso de erro
     */
    public function init()
    {
        $ch = curl_init($this->url);
        curl_setopt_array($ch, $this->_curlOptions);
        
        //configura SSL CaCertificado servidor CAS
        if ($this->caCertPath) {
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, true);
            curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
            curl_setopt($ch, CURLOPT_CAINFO, $this->caCertPath);
        } else if($this->validarCa){
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, true);
            curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, true);
        } else {
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
            curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
        }
        
        if ($this->form->get()){
            curl_setopt($ch, CURLOPT_POST, true);
            curl_setopt($ch, CURLOPT_POSTFIELDS, $this->form->get());
        } 
        
        curl_setopt( $ch, CURLOPT_RETURNTRANSFER, true);
        return $ch;
    }
    
    public static function Get(){
        
    }
    
    
}

