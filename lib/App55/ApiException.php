<?php

abstract class App55_ApiException extends Exception {
	public $type;
	public $message;
	public $code;
	public $body;

	public function __construct($type, $message, $code, $body) {
		$this->type = $type;
		$this->message = $message;
		$this->code = $code;
		$this->body = $body;		
	}

	public static function create($type, $message = null, $code = null, $body = null) {
		if($type == 'request-error')
			return new App55_RequestException($message, $code, $body);
		else if($type == 'authentication-error')
			return new App55_AuthenticationException($message, $code, $body);
		else if($type == 'server-error')
			return new App55_ServerException($message, $code, $body);
		else if($type == 'validation-error')
			return new App55_ValidationException($message, $code, $body);
		else if($type == 'resource-error')
			return new App55_ResourceException($message, $code, $body);
		else if($type == 'card-error')
			return new App55_CardException($message, $code, $body);
	} 
}

?>
