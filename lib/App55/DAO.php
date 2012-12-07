<?php

class App55_DAO {
	private $args;

	public function __construct(array $args) {
		$this->args = $args;
	}

	public function __get($name) {
		return $this->args[$name];
		if(isset($this->args[$name])) return $this->args[$name];
		else return null;
	}

	public function __isset($name) {
		return isset($this->args[$name]);
	}

	public function __set($name, $value) {
		$this->args[$name] = $value;	
	}

	public function __unset($name) {
		unset($this->args[$name]);
	}

	public function __toString() {
		$s = '';
		foreach($this->args as $key => $value) {
			$s .= ', ' . $key . ' => ' . $value;
		}
		$s = get_class($this) . '(' . substr($s, 2) . ')';
		return $s;
	}

	public function toArray() {
		$array = array();
		foreach($this->args as $k => $v) {
			if($v instanceof App55_DAO)
				$array[$k] = $v->toArray();	
			else
				$array[$k] = $v;
		}
		return $array;
	}
}

?>
