<?php

namespace CasPHP\Cas;

use CasPHP\Cas\HttpHandler;

class Path
{
    private $name = '';
    private $uri  = '';
    private $parameters = array();
    private $type = 'POST';
    
    public function __construct($name = '', $uri = '', $parameters = array(), $type = 'POST') {

        $this->uri          = $uri;
        $this->parameters   = $parameters;
        $this->type         = $type;
    }

    public function getUri() {
        return $this->uri;
    }

    public function getParameters() {
        return $this->parameters;
    }

    public function getType() {
        return $this->type;
    }

    public function setName($name) {
        $this->name = $name;
        return $this;
    }
    
    public function getName() {
        return $this->name;
    }

    
    public function setUri($uri) {
        $this->uri = $uri;
        return $this;
    }

    public function setParameters($parameters) {
        $this->parameters = $parameters;
        return $this;
    }
    
    public function addParameter($parameter,$value) {
        $this->parameters[] = array($parameter => $value);
        return $this;
    }

    public function setType($type) {
        $this->type = $type;
        return $this;
    }

    public function request()
    {
        echo '<pre>';
        var_dump($this);
        exit;
    }
    
    
}

    
