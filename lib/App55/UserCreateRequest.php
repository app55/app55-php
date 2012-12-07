<?php

class App55_UserCreateRequest extends App55_Request {
	protected function getEndpoint() {
		return $this->gateway->environment->baseUrl . '/user';
	}

	protected function getMethod() {
		return 'POST';
	}
	
}


?>
