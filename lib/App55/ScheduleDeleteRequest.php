<?php

class App55_ScheduleDeleteRequest extends App55_Request {

	public function __construct($gateway, $args) {
		$this->id = $args['schedule']->id;
		unset($args['schedule']);
		parent::__construct($gateway, $args);
	}

	protected function getEndpoint() {
		return $this->gateway->environment->baseUrl . '/schedule/' . $this->id;
	}

	protected function getMethod() {
		return 'DELETE';
	}
}

?>