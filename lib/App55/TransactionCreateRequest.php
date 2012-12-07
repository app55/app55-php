<?php

class App55_TransactionCreateRequest extends App55_Request {
	protected function getEndpoint() {
		return $this->gateway->environment->baseUrl . '/transaction';
	}

	protected function getMethod() {
		return 'POST';
	}
}

?>
