<?php 
	/* Incluindo getConfigs */
	require_once "./index_control.php";

	/* Escondendo erros */
	error_reporting(0);

	/* Quebrando o url chamado em partes */
	$url_lv = urlRequestParser();

	$max_lv = count($url_lv) - 1;

	/* Criando .htaccess, caso ele não exista */
	validaHtAcess();

	/* Criando arquivo de configuração json, caso ele não exista */
	validaConfigsJson();

	$configs = json_decode(getConfigs());

	$ip = getIp();

	/* Exibindo debug caso debug_string_valida definida pelo user esteja presente no querystring e seja um ip válido a efetuar debug  */
	if(isset($_GET[$configs->debug_string_valida]) && in_array($ip,$configs->debug_ips_validos)){
		error_reporting(-1);
		foreach($url_lv as $i => $lv){
			echo "lv[$i]:$lv<br>";
		}
	}

	/* Criando variável de validação para saber se incluímos algo ou não, caso não exibir 404 */
	$inclusao = 0;

	/* Percorrendo cada configs->urls */
	foreach($configs->urls as $i=> $url){
		/*  Verificamos se existe o arquivo solicitado através da url invocada ;
			Se o url_lv[informado] na chamada do URL requisitado existe e a partir do index for igual ao url_customizado da config ;
			Se o se o url_anterior é igual url_lv[informado-1] ; 
			Se não existir uma próxima declaração de lvl+1, caso exista teremos que validar sempre por ela, caso não exista fazer solicitação do arquivo atual;
		*/
		if((isset($url_lv[$url->url_lv]) && $url_lv[$url->url_lv] == $url->url_customizado) && ($url->url_anterior == (isset($url_lv[$url->url_lv-1]) ? $url_lv[$url->url_lv-1] : "")) && !isset($url_lv[$url->url_lv+1])){
			if(file_exists($url->caminho_no_server)){
				$inclusao += 1;
				if($url->tp_cont_solic == "require_once"){
					require_once $url->caminho_no_server;
				} else if ($url->tp_cont_solic == "include_once"){
					include_once $url->caminho_no_server;
				} else if($url->tp_cont_solic == "require"){
					require $url->caminho_no_server;
				} else if ($url->tp_cont_solic == "include"){
					include $url->caminho_no_server;
				} else {
					header("Content-type:$url->tp_cont_solic");
					include $url->caminho_no_server;
				}
			} 
		}
	}

	/* Caso não incluirmos nada, a busca falhou */
	if($inclusao == 0){
		require_once __DIR__."/404.php";
	}
