<?php

namespace CasPHP\Cas;

use CasPHP\Cas\Path;

class Service
{
    
    private $name       = '';
    private $paths      = array();
    private $actualPath = null;
    
    public function __construct($name = '', array $paths = array()) {
        $this->name = $name;
        $this->paths = $paths;
    }
   
    public function getName() {
        return $this->name;
    }

    public function getPaths() {
        return $this->paths;
    }    

    public function setName($name) {
        $this->name = $name;
        return $this;
    }

    public function setPaths($paths) {
        $this->paths = $paths;
        return $this;
    }

    public function addPath($name, $uri = '', $parameters = array(), $type = 'POST'){
        if(is_object($name)){
            $objPath = $name;
        } else {
            $objPath = new Path($name, $uri,$parameters,$type);
        }
        
        $this->paths[] = $objPath;
        return $this;
    }
    
    public function getPathByName($name){
        $objPath = null;       
       
        foreach ($this->paths as $path){
            if($path->getName() == $name){
               $objPath = $path;
               break;
            }
        }
         $this->actualPath = $objPath;
        return $this->actualPath;
    }
            
    public function getActualPath() {
        return $this->actualPath;
    }
}

