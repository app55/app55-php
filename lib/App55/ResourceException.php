<?php

class App55_ResourceException extends App55_ApiException {
	public function __construct($message, $code, $body) {
		parent::__construct('resource-error', $message, $code, $body);
	}
}

?>
