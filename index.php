<?php 
	/* Incluindo arquivo com as funções */
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
		$htmlDebug = "";
		$htmlDebug .= "<div class='bg-danger text-white'>";
		error_reporting(-1);
		foreach($url_lv as $i => $lv){
			$htmlDebug .= "<span> lv[$i]=$lv </span><br>";
		}
		$htmlDebug .= "<span> IP = $ip</span>";
		$htmlDebug .= "</div>";
		echo $htmlDebug ;
	}

	/* Criando variável de validação para saber se incluímos algo ou não, caso não exibir 404 */
	$inclusao = 0;

	/* Percorrendo cada configs->urls */
	foreach($configs->urls as $i=> $file){
		/*  Verificamos se existe o arquivo solicitado através da url invocada ;
			Se o url_lv[informado] na chamada do URL requisitado existe e a partir do index for igual ao url_customizado da config ;
			Se o se o url_anterior é igual url_lv[informado-1] ; 
			Se não existir uma próxima declaração de lvl+1, caso exista teremos que validar sempre por ela, caso não exista fazer solicitação do arquivo atual;
		*/
		if((isset($url_lv[$file->url_lv]) && $url_lv[$file->url_lv] == $file->url_customizado) && ($file->url_anterior == (isset($url_lv[$file->url_lv-1]) ? $url_lv[$file->url_lv-1] : "")) && !isset($url_lv[$file->url_lv+1])){
			if(file_exists($file->caminho_no_server)){
				$inclusao += 1;
				if($file->tipo_solicitado == "require_once"){
					require_once $file->caminho_no_server;
				} else if ($file->tipo_solicitado == "include_once"){
					include_once $file->caminho_no_server;
				} else if($file->tipo_solicitado == "require"){
					require $file->caminho_no_server;
				} else if ($file->tipo_solicitado == "include"){
					include $file->caminho_no_server;
				} else {
					header("Content-type:$file->tipo_solicitado");
					include $file->caminho_no_server;
				}
			} 
		}
	}

	/* Caso não incluirmos nada, a busca falhou */
	if($inclusao == 0){
		require_once __DIR__."/404.php";
	}