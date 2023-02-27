<?php

namespace App\Controller;

use App\Entity\Sensor;
use App\Form\SensorType;
use App\Repository\SensorRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class SensorController extends AbstractController
{
	#[Route('/', name: 'app_sensors')]
	public function index(SensorRepository $sensorRepository): Response
	{
		return $this->render('sensor/index.html.twig', [
			'sensors' => $sensorRepository->findAll(),
		]);
	}

	#[Route('/sensor/create', name: 'app_sensor_create')]
    #[Route('/sensor/{id}/edit', name: 'app_sensor_edit')]
    public function edit(Request $request, EntityManagerInterface $entityManager, Sensor $sensor = null): Response
    {
		if ($sensor === null) {
			$sensor = new Sensor();
		}
		$form = $this->createForm(SensorType::class, $sensor);
		$form->handleRequest($request);

		if ($form->isSubmitted() && $form->isValid()) {
			$entityManager->persist($sensor);
			$entityManager->flush();

			return $this->redirectToRoute('app_sensors');
		}

        return $this->render('sensor/edit.html.twig', [
			'sensor' => $sensor,
			'form'   => $form->createView()
        ]);
    }
}
