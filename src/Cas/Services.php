<?php

namespace CasPHP\Cas;

class Services
{
    private $services;

    public function __construct($services = array())
    {
        if(empty($services)){
            throw new \Exception('Serviçoes são requeridos!');
        }
        $this->services = $services;
        
        $this->setServices();
    }
    
    /*

     * Exemplo de array
     *      array(
     *          'isei' => array( //key do sistema
     *              'lista_sistemas' => $service1, //serviços 
     *              'pesquisa_processo' => $service2
     *          )
     *      ),
     * 
     * @return void();
     * 
     */
    private function setServices()
    {
        $servicos =  $this->services;
        
        foreach ($servicos as $keySys => $arrServicos){
            $servSisObj = new \stdClass();
            $servSisObj->$keySis = new \stdClass();

            foreach ($arrServicos as $keyServ => $servico){
                $servSisObj->$keySis->$keyServ = $servico;
            }
        }
        $this->services = $servSisObj;
    }

    
    public function getServices(){
        return $this->services;
    }
}

