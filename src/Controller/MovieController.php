<?php

namespace App\Controller;

use App\Entity\Movie;
use App\Repository\MovieRepository;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class MovieController extends AbstractController
{
	private MovieRepository $movieRepository;
	private ValidatorInterface $validator;

	public function __construct(MovieRepository $movieRepository, ValidatorInterface $validator)
	{
		$this->movieRepository = $movieRepository;
		$this->validator = $validator;
	}
	
    #[Route('/movies/', name: 'add_movie', methods: 'POST')]
    public function add(Request $request): JsonResponse
    {
		$data = json_decode($request->getContent(), true);

		$movie = new Movie();
		$movie->setName($data['name']);

		$errors = $this->validator->validate($movie);
		if (count($errors) > 0) {
			$errorsString = (string) $errors;
			return new JsonResponse(['status' => $errorsString], Response::HTTP_BAD_REQUEST);
		}

		$this->movieRepository->add($movie, true);

		return new JsonResponse(['status' => 'Movie created!'], Response::HTTP_CREATED);
    }

	#[Route('/movies/{id}/', name: 'get_movie', methods: 'GET')]
	public function get($id): JsonResponse
	{
		$movie = $this->movieRepository->findOneBy(['id' => $id]);

		if (!empty($movie)) {
			$data = [
				'id' => $movie->getId(),
				'name' => $movie->getName(),
			];
			return new JsonResponse($data, Response::HTTP_OK);
		}

		return new JsonResponse(['status' => 'Movie not found'], Response::HTTP_OK);
	}

	#[Route('/movies/{id}/update', name: 'update_movie', methods: 'PUT')]
	public function update(Request $request, $id): JsonResponse
	{
		$movie = $this->movieRepository->findOneBy(['id' => $id]);

		if (empty($movie)) {
			return new JsonResponse(['status' => 'Movie not found'], Response::HTTP_OK);
		}

		$data = json_decode($request->getContent(), true);

		$movie->setName($data['name']);

		$errors = $this->validator->validate($movie);
		if (count($errors) > 0) {
			$errorsString = (string) $errors;
			return new JsonResponse(['status' => $errorsString], Response::HTTP_BAD_REQUEST);
		}

		$this->movieRepository->update($movie, true);

		return new JsonResponse(['status' => 'Movie updated!'], Response::HTTP_CREATED);
	}

	#[Route('/movies/{id}/', name: 'delete_movie', methods: 'DELETE')]
	public function delete($id): JsonResponse
	{
		$movie = $this->movieRepository->findOneBy(['id' => $id]);

		if (empty($movie)) {
			return new JsonResponse(['status' => 'Movie not found'], Response::HTTP_OK);
		}

		$this->movieRepository->remove($movie, true);

		return new JsonResponse(['status' => 'Movie deleted!'], Response::HTTP_CREATED);
	}
}
