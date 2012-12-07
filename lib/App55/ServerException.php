<?php

class App55_ServerException extends App55_ApiException {
	public function __construct($message, $code, $body) {
		parent::__construct('server-error', $message, $code, $body);
	}
}

?>
