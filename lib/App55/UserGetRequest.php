<?php

class App55_UserGetRequest extends App55_Request {

	public function __construct($gateway, $args) {
		$this->id = $args['user']->id;
		unset($args['user']->id);
		parent::__construct($gateway, $args);
	}

	protected function getEndpoint() {
		return $this->gateway->environment->baseUrl . '/user/' . $this->id;
	}

	protected function getMethod() {
		return 'GET';
	}
}

?>