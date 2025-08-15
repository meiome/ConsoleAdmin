<?php

namespace App\php\wb_framework;

use \Exception as Exception;

/*
 * Authore: Alessandro Carrer
 *
 * To do:
 *
 * Version: 0.2
 *
 * Response provides an interface to wrap the common response-related tasks such as:

Sending headers for redirects.
Sending content type headers.
Sending any header.
Sending the response body.
*/
class WB_Response{

	private $code;

	private $content_type;

	private $content_disposition;

	private $content_language;

	public $content;

	/*
	Header options
	array(
		'Content-Type'=>'text/event-stream',
		'Content-Disposition'=>'attachment; filename="file.txt"',
		'Content-Language'=>it-IT
	)
	*/
	private $header_options = null;

	private $expires = null;
	private $caching = null;


	/* Costruttore */
	public function __construct( ) {
		$this->header_options = array();
		/*
		$this->code = null;
		$this->content_type = null;
		$this->content_disposition = null;
		$this->$content_language = null;
		*/
		$this->content = null;
	}




	/*
	* Append a string to the response body content
	* @param string
	*/
	public function appendContent( $string ): void{
		$this->content .= $string;
	}


	public function appendFile( $file ){

	}

	/*
	* Replace the current content with a string
	* @param string
	*/
	public function setContent( $string ): void{
		$this->content = $string;
	}



	/*
	* Return the response content
	* @return string
	*/
	public function getContent(){
		return $this->content;
	}



	/*
	* Set a header parameter
	* @param parameter name
	* @param value
	*/
	public function setHeader( $parameter, $value): void{
		$this->header_options[$parameter] = $value;

	}



	/*
	* Send the response
	* Used by the kernel to send the response
	*/
	public function send(): void{
		$this->sendHeader();
		echo $this->content;

	}



	public function sendHeader(): void{
		foreach( $this->header_options as $key=>$value ){
			if($value != null){
				header($key.':'.$value);
			}
		}
	}



	public function sendBody(): void{
		echo $this->content;
	}

	public function redirect( $url ): void{
		header('location:'.$url);
		//$this->response->setLocation($url);
		die();
	}

}//end class


class WB_HttpResponse extends WB_Response{

	public function __construct( ) {
		parent::__construct();
	}

}


class WB_StreamResponse extends WB_Response{

	public function __construct( ) {
		parent::__construct();
		$this->setHeader('Content-Type', 'text/event-stream');
		$this->sendHeader();
	}


	public function send(): void{
		echo $this->content;
		while (ob_get_level() > 0) {
			ob_end_flush();
		}
		flush();
		$this->content = null;
	}

}


class WB_RedirectResponse extends WB_Response{

	public function __construct( $url ) {
		parent::__construct();
		$this->setHeader('Location', $url);
	}

}

?>
