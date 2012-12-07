<?php


/**
 * RFC 2396 URL encoder
 *
 * unreserved  = alphanum | mark
 * mark        = "-" | "_" | "." | "!" | "~" | "*" | "'" | "(" | ")"
 *
 */
abstract class App55_HttpUrlEncoder {
	public static function encode($array) {
		$arrout = array();
		foreach($array as $k => $v) {
			$arrout[] = $k . '=' . str_replace(array('%21', '%2A', '%27', '%28', '%29' ), array('!', '*', "'", '(', ')'), rawurlencode($v));
		}
		return join('&', $arrout);
	}
}

?>
