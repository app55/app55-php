<?php

class App55_ScheduleGetRequest extends App55_Request {

	public function __construct($gateway, $args) {
		$this->id = $args['schedule']->id;
		unset($args['schedule']->id);
		parent::__construct($gateway, $args);
	}

	protected function getEndpoint() {
		return $this->gateway->environment->baseUrl . '/schedule/' . $this->id;
	}

	protected function getMethod() {
		return 'GET';
	}
}

?>