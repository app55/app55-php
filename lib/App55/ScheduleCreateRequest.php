<?php

class App55_ScheduleCreateRequest extends App55_Request {
	
	protected function getEndpoint() {
		return $this->gateway->environment->baseUrl . '/schedule';
	}

	protected function getMethod() {
		return 'POST';
	}
}

?>
