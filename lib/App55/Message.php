<?php


abstract class App55_Message {
	private $args;
	private $timestamp;

	public function __construct($args) {
		$this->args = $args;
	}

	public function __get($name) {
		if($this->args instanceof StdClass)
			return $this->args->$name;
		else
			return $this->args[$name];
	}

	public function __isset($name) {
		if($this->args instanceof StdClass)
			return isset($this->args->$name);
		else
			return isset($this->args[$name]);
	}

	protected function timestamp() {
		if(isset($this->timestamp)) return $this->timestamp;
	
		return gmdate('YmdHis');
	}

	public function setTimestamp($timestamp) {
		$this->timestamp = $timestamp;
	}
	
	private function is_assoc($array) {
		return (is_array($array) && count(array_filter(array_keys($array), 'is_string')) == count($array));
	}

	protected function toArray($apiKey = null, $apiSecret = null) {
		$array = array();
	
		foreach($this->args as $k => $v) {
			if($v instanceof App55_DAO)
				$array[$k] = $v->toArray();	
			else
				$array[$k] = $v;
		}

		if($apiKey) {
			$array['api_key'] = $apiKey;
		}

		if($apiSecret) {
			$array['ts'] = $this->timestamp();
			if(array_key_exists('sig', $array))
				unset($array['sig']);
			$dotted = $this->toDotted($array);
			ksort($dotted);
			$qs = App55_HttpUrlEncoder::encode($dotted);
			$array['sig'] = base64_encode(sha1($apiSecret . $qs, true));
			$array['sig'] = str_replace(array('+', '/'), array('-', '_'), $array['sig']);
		}
		return $array;
	}

	protected function toDotted($array, $path = null) {
		$arrout = array();
		foreach($array as $k => $v) {
			$key = $path ? $path . '.' . $k : $k;
			
			if(is_array($v) && !$this->is_assoc($v)) {
				$arrout = array_merge($arrout, $this->toDotted($v, $key));
			} else if(is_array($v)) {
				$arrout = array_merge($arrout, $this->toDotted($v, $key));		
			} else if($v instanceof StdClass) {
				$arrout = array_merge($arrout, $this->toDotted($v, $key));
			} else {
				$arrout[$key] = $v;
			}
		}
		return $arrout;
	} 

	public function __toString() {
		$s = '';
		foreach($this->args as $key => $value) {
			$s .= ', ' . $key . ' => ' . $value;
		}
		$s = get_class($this) . '(' . substr($s, 2) . ')';
		return $s;
	}
}

?>
