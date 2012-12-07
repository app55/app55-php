<?php

class App55_Environment {
	public static $development;
	public static $sandbox;
	public static $production;

	private $server;
	private $port;
	private $isSsl;
	private $version;

	public function __construct($server, $port, $isSsl, $version) {
		$this->server = $server;
		$this->port = $port;
		$this->isSsl = $isSsl;
		$this->version = $version;
	}

	private function getScheme() {
		return $this->isSsl ? 'https' : 'http';
	}

	private function getHost() {
		if($this->isSsl && $this->port == 443)
			return $this->server;
		if(!$this->isSsl && $this->port == 80)
			return $this->server;
		return $this->server . ':' . $this->port;
	}

	public function __get($name) {
		if($name == 'baseUrl')
			return $this->getScheme() . '://' . $this->getHost() . '/v' . $this->version;
		else if($name == 'server')
			return $this->server;
	}
}
App55_Environment::$development = new App55_Environment('dev.app55.com', 80, false, 1); 
App55_Environment::$sandbox = new App55_Environment('sandbox.app55.com', 443, true, 1); 
App55_Environment::$production = new App55_Environment('api.app55.com', 443, true, 1); 

?>
