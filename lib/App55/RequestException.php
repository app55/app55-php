<?php

class App55_RequestException extends App55_ApiException {
	public function __construct($message, $code, $body) {
		parent::__construct('request-error', $message, $code, $body);
	}
}

?>
