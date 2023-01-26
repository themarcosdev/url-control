<?php
	/** Função para quebrar a url requisitada após o host por / */
	function urlRequestParser(){
		/* Pegando URL de qualquer página dinâmicamente */
		$uri = $_SERVER['REQUEST_URI'];

		$caminho = trim(parse_url($uri, PHP_URL_PATH), '/');

		$url_lv = (explode('/', $caminho));

		return $url_lv ;
	}


	/**
	 * Função responsável por retornar o json com todas as configs  ;
	 */
	function getConfigs(){

		$json = file_get_contents(__DIR__."/index_site_config_urls.json");
		$dados = json_decode($json);

		return json_encode($dados);
	}

	/**
	 * Função responsável por validar se o arquivo .htacess existe ;
	 */
	function validaHtAcess(){
		if(!file_exists(__DIR__."/.htaccess")){
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
			file_put_contents(__DIR__."/.htaccess",$pontoHtAccess);
		}
	}

	/**
	 * Função responsável por validar se o arquivo index_site_config_urls.json existe ;
	 */
	function validaConfigsJson(){
		if(!file_exists(__DIR__."/index_site_config_urls.json")){
			$json = array();
			$json['urls'] = array();
			$json['ultimo_acesso_interface'] = "";
			$json['debug_ips_validos'] = array("::1");
			$json['debug_string_valida'] = "debug_url_lv";
			$json['user_configs'] = "";
			$json['pass_configs'] = "";

			$url = urlRequestParser();

			/* Criando o primeiro url customizado */
			array_push($json['urls'],array(
					"id"=>"1",
					"url_customizado"=>"painel-controle-urls",
					"url_lv"=> count($url),
					"url_anterior"=> $url[count($url)-1],
					"caminho_no_server" => "index_interface_control.php",
					"lv_solicitado"=> "require_once"
			));

			$configs = json_encode($json,JSON_PRETTY_PRINT);
			$fp = fopen(__DIR__."/index_site_config_urls.json", 'w');
			fwrite($fp, $configs);
			fclose($fp);
		}
	}

	/**
	 * Função responsável por retornar o ip de quem chamou o servidor ;
	 */
	function getIp(){
		// Verificando o IP que fez a chamada ;
		if (!empty($_SERVER['HTTP_CLIENT_IP'])) {
			$ip = $_SERVER['HTTP_CLIENT_IP'];
		} elseif (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {
			$ip = $_SERVER['HTTP_X_FORWARDED_FOR'];
		} else {
			$ip = $_SERVER['REMOTE_ADDR'];
		}
		return ($ip ? $ip : null);
	}
