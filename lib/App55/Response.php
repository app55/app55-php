<?php

class App55_Response extends App55_Message {

	private $gateway;
	
	public function __construct($gateway, $qs = null, $json = null) {
		$this->gateway = $gateway;
		if(isset($qs)) {
			$args = new StdClass();
			$pairs = explode("&", file_get_contents("php://input"));
		    $array = array();
		    foreach ($pairs as $pair) {
		        $nv = explode("=", $pair);
		        $name = urldecode($nv[0]);
		        $value = urldecode($nv[1]);
		        $array[$name] = $value;
		    }
			
			foreach($array as $k => $v) {
				$d = $args;
				$k = explode(".", $k);
				/* $k = split('.', $k); */		
				for($i = 0; $i < count($k) - 1; $i++) {
					$key = $k[$i];
					$d->$key = !empty($d->$key) ? $d->$key : new StdClass();
					$d = $d->$key;							
				}
				$key = $k[count($k)-1];
				$d->$key = $v;
			}	
		} else if($json) {
			$args = json_decode($json);
		}

		parent::__construct($args);

		if(isset($this->error)) {
			throw App55_ApiException::create(
				isset($this->error->type) ? $this->error->type : null,
				isset($this->error->message) ? $this->error->message : null,
				isset($this->error->code) ? $this->error->code : null,
				isset($this->error->body) ? $this->error->body : null
			);
		}
		
		if(!isset($this->sig) || !isset($this->ts))
			throw new App55_InvalidSignatureException();
				
		$check = $this->toArray(null, $this->gateway->apiSecret);
		if($this->sig != $check['sig'])
			throw new App55_InvalidSignatureException();
	}

	protected function timestamp() {
		return $this->ts;
	}
	
	public function __get($name) {
		if($name == 'formData') {
			$args = $this->toArray($this->gateway->apiKey, $this->gateway->apiSecret);
			$args = $this->toDotted($args);
			ksort($args);
			return App55_HttpUrlEncoder::encode($args);
		} else return parent::__get($name);
	}
}

?>
