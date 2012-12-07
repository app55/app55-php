<?php

class App55_UserAuthenticateRequest extends App55_Request {
	protected function getEndpoint() {
		return $this->gateway->environment->baseUrl . '/user/authenticate';
	}

	protected function getMethod() {
		return 'POST';
	}

}

?>
