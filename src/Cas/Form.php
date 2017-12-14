<?php

namespace CasPHP\Cas;

class Form
{
    const POST = 'POST';
    
    const GET  = 'GET';

    private $inputs;
    
    private $action;
    
    private $method;
    
    private $json = false;
    
    private $query = true;
    
    
    public function __construct(array $inputs = array(), $method = Form::POST)
    {
        $this->inputs = $inputs;
        $this->method = $method;
    }
    
    
    public function setMethod($method)
    {
        $this->method = $method;
    }
    
    public function getMethod()
    {
        return $this->method;
    }
    
    public function getInputs() {
        return $this->inputs;
    }

    public function getJson() {
        return $this->json;
    }

    public function getQuery() {
        return $this->query;
    }

    public function setInputs($inputs) {
        $this->inputs = $inputs;
        return $this;
    }

    public function setJson($json) {
        $this->json = $json;
        $this->query = false;
        return $this;
    }

    public function setQuery($query) {
        $this->query = $query;
        $this->json = false;
        return $this;
    }

        
    public function get()
    {
        if($this->query){
            return http_build_query($this->inputs);
        }
        
        if($this->json){
            return json_encode($this->inputs);
        }

        return $this->inputs;
    }
    
    public function setAction($action)
    {
        $this->action = $action;
    }
    
    public function getAction()
    {
        return $this->action;
    }
    
    public function submit(){
        
    }
    
    public function add($input, $value = '')
    {
        if(is_array($input)){
            $this->inputs = array_merge($this->inputs, $input);    
        } else if(is_string($input)){
            $this->inputs[$input] = $value;
        }
    }

    public function remove($input = ''){
        if(empty($input)){
            $this->inputs = array();
        } else {
            unset($this->inputs[$input]);
        }
    }
    
    
}
