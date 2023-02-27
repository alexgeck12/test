<?php

namespace App\Controller;

use App\Entity\Sensor;
use App\Entity\Temperature;
use App\Helpers\TemperatureConverter;
use App\Requests\TemperatureRequest;
use App\Repository\SensorRepository;
use App\Repository\TemperatureRepository;
use Doctrine\ORM\NonUniqueResultException;
use Symfony\Component\HttpClient\HttpClient;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class ApiController extends AbstractController
{
    #[Route('/add', name: 'api_temperature_add', methods: ['POST'])]
    public function add(
		TemperatureRequest $request,
		TemperatureRepository $temperatureRepository,
		SensorRepository $sensorRepository,
	): Response
    {

		$sensor = $sensorRepository->find($request->getSensorUuid());

		if ($sensor === null) {
			return $this->json([
				'error_code' => 400,
				'error_description' => 'Invalid request',
				'message' => sprintf("Sensor %s not found.", $request->getSensorUuid())
			], 400);
		}

		$temperature = new Temperature();
		$temperature->setTemperature(TemperatureConverter::toCelsius($request->getTemperature()));
		$temperature->setSensor($sensor);
		$temperature->setTimestamp(new \DateTime());

		$temperatureRepository->save($temperature, true);

		return $this->json([
			'error_code' => 0,
			'error_description' => 'Success',
			'message' => 'Data successfully added'
		]);
    }

	#[Route('/add/{sensor_ip}', name: 'api_temperature_add_by_ip', methods: ['GET'])]
	public function addByIp(
		Request $request,
		TemperatureRepository $temperatureRepository,
		SensorRepository $sensorRepository,
		HttpClient $httpClient,
	): Response
	{
		$sensor = $sensorRepository->findOneBy(['ip' => $request->get('sensor_ip')]);

		if ($sensor === null) {
			return $this->json([
				'error_code' => 400,
				'error_description' => 'Invalid request',
				'message' => sprintf("Sensor %s not found.", $request->get('sensor_ip'))
			], 400);
		}

		$httpClient = $httpClient::create();
		$response = $httpClient->request('GET', 'http://' . $request->get('sensor_ip') . '/data');

		if ($response->getStatusCode() === 200) {
			$cols = explode(",", $response->getContent());

			$temperature = new Temperature();
			$temperature->setTemperature($cols[1]);
			$temperature->setSensor($sensor);
			$temperature->setTimestamp(new \DateTime());

			$temperatureRepository->save($temperature, true);
		}

		return $this->json([
			'error_code' => 0,
			'error_description' => 'Success',
			'message' => 'Data successfully added'
		]);
	}

	/**
	 * @throws NonUniqueResultException
	 */
	#[Route('/get/{start}/{end}', name: 'api_temperature_get_middle', methods: ['GET'])]
	public function getMiddleTemperature(
		Request $request,
		TemperatureRepository $temperatureRepository
	): Response
	{
		$result = $temperatureRepository->getMiddleTemperature($request->get('start'), $request->get('end'));
		return $this->json($result);
	}

	/**
	 * @throws \Exception
	 */
	#[Route('/getForSensor/{id}/{date}', name: 'api_temperature_get_middle_for_sensor', methods: ['GET'])]
	public function getMiddleTemperatureForSensor(
		Request $request,
		TemperatureRepository $temperatureRepository,
		Sensor $sensor = null
	): Response
	{
		if ($sensor === null) {
			return $this->json([
				'error_code' => 400,
				'error_description' => 'Invalid request',
				'message' =>"Sensor not found."
			], 400);
		}

		$result = $temperatureRepository->getMiddleTemperatureForSensor($sensor, $request->get('date'));
		return $this->json($result);
	}
}
