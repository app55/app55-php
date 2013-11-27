<?php

class TestConfiguration
{
	static $APP55_API_KEY = "enter api key here";
	static $APP55_SECRET_KEY = "enter api secret here";

	public static function getApiKey()
	{
		$config = new TestConfiguration();

		$value = getenv('APP55_API_KEY');
		return $value != null ? $value : TestConfiguration::$APP55_API_KEY;
	}

	public static function getApiSecret() {
		$value = getenv('APP55_API_SECRET');
		return $value != null ? $value : TestConfiguration::$APP55_SECRET_KEY;
	}
}

?>