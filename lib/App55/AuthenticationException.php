<?php

class App55_AuthenticationException extends App55_ApiException {
	public function __construct($message, $code, $body) {
		parent::__construct('authentication-error', $message, $code, $body);
	}
}

?>
