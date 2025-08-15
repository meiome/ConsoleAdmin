<?php
namespace App\php\wb_cookie;
/*
 * Authore: Andrea Bettiol
 *
 *
 * Class: WB_CookieResult
 *
 * Version: 0.1
*/
class WB_CookieResult{

	/*ci possono essere 3 tipi di risultato per ogni appunf:
	 null-> non settato , account -> 0 settato  ma non corrispondente a niente
	 */

	private $data;

	public function __construct(){
		$this->data = array();
		//$this->data['html'] = '';
	}


	public function append($type, $data ): void{
		$this->data[$type] = $data;
	}

	public function appendunset($type): void{
		$this->append($type, null );
	}


	public function getContent( $type ){
		if(array_key_exists($type, $this->data) ){
			return $this->data[$type];
		}else{
			return '';
		}
	}


	public function printContent( $type='html'): void{
		if(array_key_exists($type, $this->data) ){
			echo $this->data[$type];
		}else{
			echo 'stampo un bel niente';
		}
	}


}
?>
