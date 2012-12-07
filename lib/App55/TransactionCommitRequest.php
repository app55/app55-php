<?php

class App55_TransactionCommitRequest extends App55_Request {

	public function __construct($gateway, $args) {
		$this->id = $args['transaction']->id;
		unset($args['transaction']->id);
		parent::__construct($gateway, $args);
	}

	protected function getEndpoint() {
		return $this->gateway->environment->baseUrl . '/transaction/' . $this->id;
	}

	protected function getMethod() {
		return 'POST';
	}
}

?>
