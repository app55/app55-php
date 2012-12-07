<?php

class App55_CardDeleteRequest extends App55_Request {

	public function __construct($gateway, $args) {
		$this->token = $args['card']->token;
		unset($args['card']);
		parent::__construct($gateway, $args);
	}

	protected function getEndpoint() {
		return $this->gateway->environment->baseUrl . '/card/' . $this->token;
	}

	protected function getMethod() {
		return 'DELETE';
	}
}

?>
