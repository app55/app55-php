<?php

class App55_ValidationException extends App55_ApiException {
	public function __construct($message, $code, $body) {
		parent::__construct('validation-error', $message, $code, $body);
	}
}

?>
