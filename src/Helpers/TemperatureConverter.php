<?php

namespace App\Helpers;

class TemperatureConverter
{
	public static function toFahrenheit(float $temperature): float
	{
		return ($temperature * 9 / 5) + 32;
	}

	public static function toCelsius(float $temperature): float
	{
		return ($temperature - 32) * 5 / 9;
	}

}