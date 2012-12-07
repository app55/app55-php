<?php

abstract class App55_Request extends App55_Message {
	protected $gateway;

	public function __construct($gateway, $args) {
		parent::__construct($args);
		$this->gateway = $gateway;
	}	

	public function __get($name) {
		if($name == 'formData') {
			$args = $this->toArray($this->gateway->apiKey, $this->gateway->apiSecret);
			$args = $this->toDotted($args);
			ksort($args);
			return App55_HttpUrlEncoder::encode($args);
		} else return parent::__get($name);
	}

	public function send() {
		$data = $this->toDotted($this->toArray());
		$auth = base64_encode($this->gateway->apiKey . ':' . $this->gateway->apiSecret);
		$ssl = array(
			'verify_peer' => true,
			'cafile' => dirname(__file__) . '/thawte.pem',
			'verify_depth' => 5,
			'CN_match' => $this->gateway->environment->server
		);

		if(in_array('curl', get_loaded_extensions())) {
			$ch = curl_init();
			curl_setopt($ch, CURLOPT_USERPWD, "{$this->gateway->apiKey}:{$this->gateway->apiSecret}");
			curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
			curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, true);
			curl_setopt($ch, CURLOPT_CAINFO, dirname(__file__) . '/thawte.pem');

			if ($this->getMethod() == 'GET') {
				curl_setopt($ch, CURLOPT_URL, $this->getEndpoint() . '?' . http_build_query($data));
			} else {
				curl_setopt($ch, CURLOPT_URL, $this->getEndpoint());
				if ($this->getMethod() == 'POST') {
					curl_setopt($ch, CURLOPT_POST, true);
				} else {
					curl_setopt($ch, CURLOPT_CUSTOMREQUEST, $this->getMethod());
				}
				curl_setopt($ch, CURLOPT_HTTPHEADER, array(
					"Content-Type: application/x-www-form-urlencoded"
				));
				curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($data));
			}
			$contents = curl_exec($ch);
			curl_close($ch);
		} else {
			if($this->getMethod() == 'GET') {
				$context = stream_context_create(array(
					'http' => array(
						'method' => 'GET',
						'ignore_errors' => true,
						'header' => 'Authorization: Basic ' . $auth
					),
					'ssl' => $ssl
				));
				$url = $this->getEndpoint() . '?' . http_build_query($data);
				$contents = file_get_contents($url, false, $context);
			} else {
				$context = stream_context_create(array(
					'http' => array(
						'method' => $this->getMethod(),
						'header' => "Authorization: Basic " . $auth . "\r\nContent-Type: application/x-www-form-urlencoded",
						'content' => http_build_query($data),
						'ignore_errors' => true
					),
					'ssl' => $ssl
				));
				$url = $this->getEndpoint();
				$contents = file_get_contents($url, false, $context);
			}
		}

		return new App55_Response($this->gateway, null, $contents);
	}

	protected abstract function getEndpoint();
	protected abstract function getMethod(); 
}

?>
