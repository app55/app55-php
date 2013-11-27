<?php

class App55_ScheduleListRequest extends App55_Request {

	public function __construct($gateway, $args) {
		$this->id = $args['merchant']->id;
		unset($args['merchant']->id);
		
		parent::__construct($gateway, $args);
	}
	
	protected function getEndpoint() {
		return $this->gateway->environment->baseUrl . '/platform/merchant/' . $this->id . '/schedule';
	}

	protected function getMethod() {
		return 'GET';
	}
}

?>
