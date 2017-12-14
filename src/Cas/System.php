<?php

namespace CasPHP\Cas;

class System
{
    private $name = '';
    private $services = array();
    private $baseUrl   = '';
    private $actualService = null;
    
    public function __construct($name = '', array $services = array(),$baseUrl='')
    {
        $this->name = $name;
        $this->services = $services;
    }
    
    public function getName() {
        return $this->name;
    }

    public function setName($name) {
        $this->name = $name;
        return $this;
    }

    public function getBaseUrl() {
        return $this->baseUrl;
    }
    
    public function setBaseUrl($baseUrl) {
        $this->baseUrl = $baseUrl;
        return $this;
    }
        
    public function getServices() {
        return $this->services;
    }

    public function setServices($services) {
        $this->services = $services;
        return $this;
    }
    
    public function getActualService() {
        return $this->actualService;
    }
    
    public function getServiceByName($name){
        $objService = null;
        foreach ($this->services as $service){
            if($service->getName() == $name){
               $objService = $service;
               break;
            }
        }
        $this->actualService = $objService;
        return $this->actualService;
    }


}

