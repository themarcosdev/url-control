<?php
	/**
	 * Função responsável por retornar o json com todas as configs  ;
	 *
	 */
	function getConfigs(){

		$json = file_get_contents("./site_config_urls.json");
		$dados = json_decode($json);

		return json_encode($dados);
	}
	// echo getConfigs();
