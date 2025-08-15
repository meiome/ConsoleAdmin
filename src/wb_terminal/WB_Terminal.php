<?php

namespace App\php\wb_terminal;
/*
 * Authore: Andrea Bettiol
 *
 *
 * Class: WB_Terminal
 *
 * Version: 0.1
*/
use App\php\wb_terminal\WB_Maschera;

class WB_Terminal {

	private $database;
	private $logger;
	private $maschere = array();

	public function __construct($database) {
		$this->database = $database;
		$this->logger = $GLOBALS["WB_LOGGER"];
		//require_once($_SERVER['DOCUMENT_ROOT'].'/php/wb_terminal/WB_Maschera.php');
	}

	public function addMaschera($mas): void{
		$this->logger->log('WB_Terminal', 'Aggiunta maschera al terminale'.$mas->mascheraname, 3);
		$this->maschere[$mas->mascheraname] = $mas;
		$mas->setDatabase($this->database);
	}

	public function getmascherabyname($name){
		if (array_key_exists($name, $this->maschere)){
			return $this->maschere[$name];
		} else{
			throw new \Exception("WB_Terminal maschera non trovata", 1);
		}
	}
}//fine classe

?>
