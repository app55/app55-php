<?php

class App55_CardException extends App55_ApiException {
	public function __construct($message, $code, $body) {
		parent::__construct('card-error', $message, $code, $body);
	}
}

?>
