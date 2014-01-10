<?php

class TestConfiguration
{
	static $APP55_API_KEY = "cHvG680shFTaPWhp8RHhGCSo5QbHkWxP";
	static $APP55_SECRET_KEY = "zMHzGPF3QAAQQzTDoTGtGz8f5WFZFjzM";

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
