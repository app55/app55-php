<?php

class App55_CardCreateRequest extends App55_Request {
	protected function getEndpoint() {
		return $this->gateway->environment->baseUrl . '/card';
	}

	protected function getMethod() {
		return 'POST';
	}
}

?>
