<?php

namespace App\Requests;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Validator\Constraints as Assert;
class TemperatureRequest
{
	#[Assert\UUid]
	public string $sensor_uuid;
	#[Assert\Type('float')]
	public float $temperature;

	public function __construct(Request $request)
	{
		$data = $request->toArray();

		$this->sensor_uuid = $data['reading']['sensor_uuid'];
		$this->temperature = $data['reading']['temperature'];
	}

	public function getSensorUuid(): string
	{
		return $this->sensor_uuid;
	}

	public function getTemperature(): float
	{
		return $this->temperature;
	}
}