<?php

namespace App\php\wb_framework;
/*
 * Authore: Alessandro Carrer
 *

 * formato record:
 * Timestamp Classe, messaggio
 * To do:
 * Implementare interfaccia Iterator per l'iterazione del registro errori
 Version: 0.1
*/
class WB_Logger {

	private $records;
	private $file;
	private $level;
	private $serverconfig;

	private $enable;

	/* Costruttore */
	public function __construct( $file=null ) {

		define('phpclassDIR_BASE', dirname(dirname(dirname(dirname( __FILE__ )))).'/');
		$this->serverconfig = require(phpclassDIR_BASE.'/config/wb/server/serverconfig.php');
		$this->enable=$this->serverconfig['debug'];

		if ($this->enable){
			$this->records = array();
			if( $file != null ){
				$this->file = $file;
			}
		}
	}


	public function enable($en): void{
		$this->enable = $en;
	}

	public function getClientIP(){
		//whether ip is from share internet
		$ip_address = '0.0.0.0';
		if (!empty($_SERVER['HTTP_CLIENT_IP']))
		  {
			$ip_address = $_SERVER['HTTP_CLIENT_IP'];
		  }
		//whether ip is from proxy
		elseif (!empty($_SERVER['HTTP_X_FORWARDED_FOR']))
		  {
			$ip_address = $_SERVER['HTTP_X_FORWARDED_FOR'];
		  }
		//whether ip is from remote address
		elseif (!empty($_SERVER['REMOTE_ADDR']))
		  {
			$ip_address = $_SERVER['REMOTE_ADDR'];
		  }
		else //aggiunto da andrea -> serve nel caso in caso cui la richiesta fosse dallo stesso e non da remoto
		  {
			$ip_address = '127.0.0.1';
		  }
		return $ip_address;
	}

	public function log( $modules, $message, $level ): void{
		if ($this->enable){
			$msg = new WB_LogRecord( $modules, $message, $level, $this->getClientIP());
			$this->records[]=$msg;
			$this->save();
		}
	}

    public function save(){
			if ($this->enable){
				if($this->file == null){return;}
				if(!$this->enable){return;}
		        try{
		            /* apre il file in scrittura */
		            $log_file=fopen($this->file, "a");
		            if( $log_file==NULL ) {
		                return false;
		            }
		            foreach( $this->records as $record){
		                fprintf($log_file, "%s\n", $record);
		            }
		            fclose($log_file);
		                    $this->records = array();
		        }catch(\Throwable $e){
		            echo 'Exception Throwable nel save del logger';
		        }
				}
	}

	public function append( $message ): void{
		if ($this->enable){
			$t = time();
			$time = date("H:M:s", $t);
			$this->records[] = array( $time, );
		}
	}

	public function read(): void{
		if ($this->enable){
			$myfile = fopen($this->file, "r");
			// Output one line until end-of-file
			while(!feof($myfile)) {
			  echo '<div>';
			  echo fgets($myfile) . "<br>";
				echo '</div>';
			}
			fclose($myfile);
		}
	}

	public function getLogs(){
		return $this->records;
	}

	/* Restituisce il messaggio dell'elemento corrente
	*/
	public function getMessage(){

	}

	public function getTime(){

	}

	public function printHtmlLog(){

	}

}//end class


class WB_LogRecord {
	public $time;
	public $message;
	public $level;
	public $modules;
	public $ip;

	public function __construct( $modules, $message, $level, $ip) {
		$t = time();
		//$time = date("H:m:s", $t);
		$time = gmdate("H:m:s", $t);

		$this->time = $time;
		//$this->time = '01/01/2018';
		$this->message = $message;
		$this->modules = $modules;
		$this->level = $level;
		$this->ip = $ip;
	}

	public function __toString(){
		$str = $this->time.' - '.$this->ip.' - '.$this->modules.' - '.$this->message;
		return $str;
	}
/*
	public function getString(){
		$str = $this->time.' - '.$this->level.' - '.$this->message;
		return $str;
	}*/

	public function getLevelName(){
		$str = 'none';
		switch ($this->level) {
			case 0;
				$str = 'User';
				break;
			case 1:
				$str = 'Critical';
				break;
			case 2:
				$str = 'Error';
				break;
			case 3:
				$str = 'Warning';
				break;
			case 4:
				$str ='Info';
				break;
			case 5:
				$str = 'Debug';
				break;
			case 6:
				$str = 'Trace';
				break;
		}
		return $str;

	}

	/*


	*/
	public function toHtml( $prefix=null, $suffix=null){
		$str = $this->time.' - '.$this->level.' - '.$this->message;
        if( $prefix != null ){ $str = $prefix.$str; }
        if( $suffix != null ){ $str = $str.$suffix; }
		return $str;
	}

}//end class








?>
