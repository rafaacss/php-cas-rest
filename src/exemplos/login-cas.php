<?php
include_once '../../vendor/autoload.php';

error_reporting(E_ALL); 
ini_set("display_errors", 1); 

use CasPHP\Cas\Client;
use Symfony\Component\Yaml;

/*/api/v1/swq sweguer*/

$yaml       = new Yaml\Parser();
$config     = file_get_contents(realpath(dirname(__DIR__)."/configs/{$_SERVER['CONFIG_ENVIRONMENT']}.yml"));
$options    = $yaml->parse($config);

try {
    
    $client = new Client($options);
    
    $client->getSystemByname('isei')
           ->getServiceByname('pesquisa_processo')
           ->getPathByName('pesquisarProcessos')
           ->setParameters(array(
                'unidade' => array(
                    'idUnidade'     => '',
                    "sigla"         => "TESTE",
                    "descricao"     => ""
                ),
                "protocoloProcedimento"                 => "6770",
                "sinRetornarAssuntos"                   => "S",
                "sinRetornarInteressados"               => "S",
                "sinRetornarObservacoes"                => "S",
                "sinRetornarAndamentoGeracao"           => "S",
                "sinRetornarAndamentoConclusao"         => "S",
                "sinRetornarUltimoAndamento"            => "S",
                "sinRetornarUnidadesProcedimentoAberto" => "S",
                "sinRetornarProcedimentosRelacionados"  => "S",
                "sinRetornarProcedimentosAnexados"      => "S"
           ));
   
    $result = $client->request();
    
    /*$client2 = new Client($options);
    $client2->getSystemByname('isei');
    $result = $client2->request('lista_sistemas','listarSistemas');*/
    echo '<pre>';
    var_dump($result);
    exit('exit');

    
} catch (Exception $exc) {
    var_dump($exc->getMessage());
exit;
}


