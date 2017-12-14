# # Exemplos
######Exemplo de utilização da component PHPCas Rest 
###### Arquivo de Configuração *.yml
	username: nome_usuario
	password: senha_usuario
	debug: false
	temp: ''
	systens:
    -   name: nome_sistema
        base_url: 'https://servico/rest'
        services:
            lista_sistemas:
                paths:
                    listarSistemas:
                        uri: /sistema/sistemas-servico
                        type: POST
            pesquisa_processo:
                paths:
                    pesquisarProcessos:
                        uri: /pesquisa-processo/consultar-procedimento-processo
                        type: POST
	cas:
    host: 'https://server_cas'
    port: '443'
    context: cas
    check_ssl: false
    server_ca_cert_path: certificadoCAServer.cer
######  1- Exemplo de Utilização do componente PHPCasRest
	$client = new Client($options);
	$client->getSystemByname('nome_sistema')
           	->getServiceByname('pesquisa_processo')
           	->getPathByName('pesquisarProcessos')
           	->setParameters(array('campo' => 'Valor')
           ));
    $result = $client->request();
######  1- Exemplo de Utilização do componente PHPCasRest
	$client2 = new Client($options);
    $client2->getSystemByname('nome_sistema');
    $result = $client2->request('lista_sistemas','listarSistemas', array('campo' => 'Valor'));