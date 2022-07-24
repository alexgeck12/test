<?php

namespace App\Controller;

use App\Entity\Critic;
use App\Repository\CriticRepository;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class CriticController extends AbstractController
{
	private CriticRepository $criticRepository;

	public function __construct(CriticRepository $criticRepository, ValidatorInterface $validator)
	{
		$this->criticRepository = $criticRepository;
		$this->validator = $validator;
	}
	
    #[Route('/critics/', name: 'add_critic', methods: 'POST')]
    public function add(Request $request): JsonResponse
    {
		$data = json_decode($request->getContent(), true);

		$critic = new Critic();
		$critic->setName($data['name']);
		$critic->setStatus($data['status']);
		$critic->setBio($data['bio']);

		$errors = $this->validator->validate($critic);
		if (count($errors) > 0) {
			$errorsString = (string) $errors;
			return new JsonResponse(['status' => $errorsString], Response::HTTP_BAD_REQUEST);
		}

		$this->criticRepository->add($critic, true);

		return new JsonResponse(['status' => 'Critic created!'], Response::HTTP_CREATED);
    }

	#[Route('/critics/{id}/', name: 'get_critic', methods: 'GET')]
	public function get($id): JsonResponse
	{
		$critic = $this->criticRepository->findOneBy(['id' => $id]);

		if (!empty($critic)) {
			$data = [
				'id' => $critic->getId(),
				'name' => $critic->getName(),
				'bio' => $critic->getBio()
			];
			return new JsonResponse($data, Response::HTTP_OK);
		}

		return new JsonResponse(['status' => 'Critic not found'], Response::HTTP_OK);
	}

	#[Route('/critics/{id}/update', name: 'update_critic', methods: 'PUT')]
	public function update(Request $request, $id): JsonResponse
	{
		$critic = $this->criticRepository->findOneBy(['id' => $id]);

		if (empty($critic)) {
			return new JsonResponse(['status' => 'Critic not found'], Response::HTTP_OK);
		}

		$data = json_decode($request->getContent(), true);

		$critic->setName($data['name']);
		$critic->setStatus($data['status']);
		$critic->setBio($data['bio']);

		$errors = $this->validator->validate($critic);
		if (count($errors) > 0) {
			$errorsString = (string) $errors;
			return new JsonResponse(['status' => $errorsString], Response::HTTP_BAD_REQUEST);
		}

		$this->criticRepository->update($critic, true);

		return new JsonResponse(['status' => 'Critic updated!'], Response::HTTP_CREATED);
	}

	#[Route('/critics/{id}/', name: 'delete_critic', methods: 'DELETE')]
	public function delete($id): JsonResponse
	{
		$critic = $this->criticRepository->findOneBy(['id' => $id]);

		if (empty($critic)) {
			return new JsonResponse(['status' => 'Critic not found'], Response::HTTP_OK);
		}

		$this->criticRepository->remove($critic, true);

		return new JsonResponse(['status' => 'Critic deleted!'], Response::HTTP_CREATED);
	}
}
