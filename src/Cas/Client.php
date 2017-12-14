<?php

namespace CasPHP\Cas;

use CasPHP\Cas\Form;
use CasPHP\Cas\HttpHandler;
use CasPHP\Cas\Service;
use CasPHP\Cas\System;
use CasPHP\Cas\Path;

class Client
{
    private $cas_service_validade   = '/p3/proxyValidate'; 
    private $cas_ticket_store       = '/v1/tickets';
    private $cas_proxy              = '/proxy';
    private $cas_proxy_validate     = '/p3/proxyValidate';
    private $options;
    private $ticket                 = '';
    private $ticket_servico         = '';
    private $dirTicketJson;
    private $systens                = array();
    private $actualSystem           = null;
    
    
    public function __construct($options = array())
    {
        $this->options = $options;
        $this->dirTicketJson = realpath( __DIR__ . '/repository');

        $this->getTicket();
        $this->configure();
        
    }
    
    private function configure(){
        //@todo percorrer o array de options e montar os objetos 'System' com 
        //seus devidos atributos
        $system = null;
        $service = null;
        $path    = null;
        foreach ($this->options['systens'] as $systemItem){
            $system = new System();
            $system->setName($systemItem['name']);
            $system->setBaseUrl($systemItem['base_url']);
            
            $arrServices = array();
            
            foreach ($systemItem['services'] as $serviceName => $serviceItem){
                $objService = new Service();                                
                $objService->setName($serviceName);
                $arrServices[] =  $this->addPathsToService($serviceItem,$objService);                
            }
            $system->setServices($arrServices);
            $this->systens[$systemItem['name']] = $system;
        }
        
    }
    
    private function addPathsToservice($serviceItem,$objService){
         foreach ($serviceItem['paths'] as $pathName => $pathItem){
            $path = new Path();
            $path->setName($pathName);
            $path->setUri($pathItem['uri']);
            $path->setType($pathItem['type']);
            $objService->addPath($path);
        }
        return $objService;
    }
    
    public function setUrlTicketJson($path){
        $this->urlTicketJson = $path;
    }
    
    public function getUrlTicketJson(){
       return $this->urlTicketJson;
    }
    
    public function getTicket(){
        $date = new \DateTime();
        $fileJson = $this->dirTicketJson.'/ticket.json';

        if(file_exists($fileJson)){
            $conteudo =  file_get_contents($fileJson);
            
            $json = json_decode($conteudo);

            if(strtotime('+30 minutes', $json->timestamp) >= $date->getTimestamp()){
                $this->ticket = $json->ticket;
            } else {
                $this->obterTicket();
            }
        } else {
            $this->obterTicket();
        }
        return $this->ticket;
    }
    
    private function obterTicket()
    {   
        $date = new \DateTime();

        if(empty($this->options['username']) 
                || empty($this->options['password'])){
            throw new Exception('Usuário ou Senha inválida');
        }

        $form = new Form();
        $form->add('username',$this->options['username']);
        $form->add('password',$this->options['password']);
        
        $httpHandler = new HttpHandler($form);
        
        if(!empty($this->options['cas']['server_ca_cert_path'])){
            $httpHandler->setCaCert(
                $this->options['cas']['server_ca_cert_path'], 
                $this->options['cas']['check_ssl']
            );
        }
        
        $url = $this->options['cas']['host']
                .'/'.$this->options['cas']['context']
                .$this->cas_ticket_store;
        
        $httpHandler->setUrl($url);
        $httpHandler->setDebug($this->options['debug']);
        
        $httpHandler->setCurlOptions(array(CURLOPT_PORT => $this->options['cas']['port']));
        
        $result = $httpHandler->sendRequest();
        
        if($result){
            //captura parametros para do form
            preg_match('/action="(.*?)"/', $result, $this->ticket);
            if(isset($this->ticket[1])){
                $this->ticket = $this->ticket[1];
            }
        }
        
        $arrTemp = array('ticket' => $this->ticket, 'timestamp' => $date->getTimestamp());
        $fp = fopen($this->dirTicketJson.'/ticket.json', "wb");
        fwrite($fp, json_encode($arrTemp));
        fclose($fp);
        
        return $this->ticket;
    }    
    
    public function setSystem($systemName, $systemObject)
    {
        return $this->systens[$systemName] = $systemObject;
    }
    
    public function getSystemByname($systemName)
    {
        $this->actualSystem = $this->systens[$systemName];
        return $this->actualSystem;
        
    }
    
    private function obterTicketServico($servico)
    {   
        $form = new Form();
        $form->add('service', $servico);

        $httpHandler = new HttpHandler($form);
        
        $httpHandler->setUrl($this->getTicket());
        
        $httpHandler->setCaCert(
            $this->options['cas']['server_ca_cert_path'], 
            $this->options['cas']['check_ssl']
        );

        /*$curlOptions = array(
            CURLOPT_FOLLOWLOCATION => false,
        );*/
        
       // $httpHandler->setCurlOptions($options);
       $result = $httpHandler->sendRequest();
        
        if(!empty($result)){
            return $result;
        } 
        return false;
        
    }
    
    public function request($serviceName='', $pathName='', $parameters = array())
    {  
        $baseUrl    = $this->actualSystem->getBaseUrl();
        $service    = null;
        $path       = null;
        
        if( !empty($serviceName) && !empty($pathName) ){
            $service = $this->actualSystem->getServiceByName($serviceName);
            $path = $service->getPathByName($pathName);
        }else{
             $service = $this->actualSystem->getActualService();
             $path = $service->getActualPath();
        }
        
        if(!$parameters){
            $parameters = $path->getParameters();
        }
        
        $form = new Form();
        $form->setJson(true);

        if($parameters){
             $form->add($parameters);
        }
        
        $ticketServico = $this->obterTicketServico($baseUrl.$path->getUri());

        return $this->getRequest($form, $ticketServico, $baseUrl.$path->getUri(), $path->getType());
    }
    
    private function getRequest($form, $ticketServico, $uri, $type = 'POST'){
        $uri .= '?ticket='.$ticketServico;

        $curlOptions = array(
            CURLOPT_POST => true,
            CURLOPT_HTTPHEADER => array('Content-Type: application/json'),
        );
        
        $httpHandler = new HttpHandler($form);
        $httpHandler->setUrl($uri);
        $httpHandler->setType($type);
        $httpHandler->setDebug(false);
        $httpHandler->setCurlOptions($curlOptions);
        
        $result = $httpHandler->sendRequest();
        
        if(!empty($result)){
            $json = mb_convert_encoding($result, "windows-1252", 'UTF-8');
            return json_decode($json);
        } 
        return false;
    }
    
    private function validarTicketServico()
    {
        
    }
    
    private function obterProxyTicket()
    {
        
    }
    
    private function validarProxyTicket()
    {
        
    }
    
    public function logout()
    {
        
    }
}

