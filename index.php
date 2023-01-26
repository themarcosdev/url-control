<?php 
	/* Escondendo erros */
	error_reporting(0);

	/* Pegando URL de qualquer página dinâmicamente */
	$uri = $_SERVER['REQUEST_URI'];

	$caminho = trim(parse_url($uri, PHP_URL_PATH), '/');

	$url_lv = (explode('/', $caminho));

	$max_lv = count($url_lv) - 1;

	/* Criando .htaccess caso ele não exista */
	if(!file_exists("./.htaccess")){
		$pontoHtAccess = '
			## REFERENCIA : https://webmasters.stackexchange.com/q/101391 ;
			<IfModule mod_rewrite.c>

			############################################
			## Enable rewrites

				Options +FollowSymLinks
				RewriteEngine on

			## Business Rewrite
				RewriteRule (^|.*?/)nl/business/(.*)$ /$1nl/$2 [NC]

			############################################
			## You can put here your magento root folder
			## path relative to web root

				#RewriteBase /magento/

			############################################
			## Workaround for HTTP authorization
			## in CGI environment

				RewriteRule .* - [E=HTTP_AUTHORIZATION:%{HTTP:Authorization}]

			############################################
			## TRACE and TRACK HTTP methods disabled to prevent XSS attacks

				RewriteCond %{REQUEST_METHOD} ^TRAC[EK]
				RewriteRule .* - [L,R=405]

			############################################
			## Never rewrite for existing files, directories and links

				RewriteCond %{REQUEST_FILENAME} !-f
				RewriteCond %{REQUEST_FILENAME} !-d
				RewriteCond %{REQUEST_FILENAME} !-l

			############################################
			## Rewrite everything else to index.php

				RewriteRule .* index.php [L]

			</IfModule>
		';

		/* Escrevendo arquivo .htaccess */
		file_put_contents("./.htaccess",$pontoHtAccess);
	}

	/* Exibindo debug caso debug_url_lv esteja presente no querystring  */
	if(isset($_GET['debug_url_lv'])){
		error_reporting(-1);
		foreach($url_lv as $i => $lv){
			echo "lv[$i]-".$lv.'<br>';
		}
	}

	/* Incluindo getConfigs */
	require_once "./index_control.php";

	$configs = json_decode(getConfigs());

	/* Criando variavel de validação para saber se incluímos algo ou não, caso não exibir 404 */
	$inclusao = 0;

	/* Percorrendo cada configs->urls */
	foreach($configs->urls as $i=> $url){
		/*  Verificamos se existe o arquivo solicitado através da url invocada ;
			Se o url_lv[informado] na chamada do URL requisitado existe e a partir do index for igual ao url_customizado da config ;
			Se o se o url_anterior é igual url_lv[informado-1] ; 
			Se não existir uma próxima declaração de lvl+1, caso exista teremos que validar sempre por ela;
		*/
		if((isset($url_lv[$url->url_lv]) && $url_lv[$url->url_lv] == $url->url_customizado) && ($url->url_anterior == (isset($url_lv[$url->url_lv-1]) ? $url_lv[$url->url_lv-1] : "")) && !isset($url_lv[$url->url_lv+1])){
			if(file_exists($url->file_server)){
				$inclusao += 1;
				if($url->lv_solicitado == "require_once"){
					require_once $url->file_server;
				} else if ($url->lv_solicitado == "include_once"){
					include_once $url->file_server;
				} 
			} 
		}
	}

	/* Caso não incluirmos nada , a busca falhou */
	if($inclusao == 0){
		require_once "./404.php";
	}
