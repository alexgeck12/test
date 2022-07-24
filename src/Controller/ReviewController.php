<?php

namespace App\Controller;

use App\Entity\Review;
use App\Repository\ReviewRepository;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class ReviewController extends AbstractController
{
	private ReviewRepository $reviewRepository;
	private ValidatorInterface $validator;

	public function __construct(ReviewRepository $reviewRepository, ValidatorInterface $validator)
	{
		$this->reviewRepository = $reviewRepository;
		$this->validator = $validator;
	}
	
    #[Route('/reviews/', name: 'add_review', methods: 'POST')]
    public function add(Request $request): JsonResponse
    {
		$data = json_decode($request->getContent(), true);

		$review = new Review();
		$review->setName($data['name']);
		$review->setSummary($data['summery']);
		$review->setMpaaRating($data['mpaa_rating']);
		$review->setPublicationDate(new \DateTime($data['publication_date']));

		$errors = $this->validator->validate($review);
		if (count($errors) > 0) {
			$errorsString = (string) $errors;
			return new JsonResponse(['status' => $errorsString], Response::HTTP_BAD_REQUEST);
		}

		$this->reviewRepository->add($review, true);

		return new JsonResponse(['status' => 'Review created!'], Response::HTTP_CREATED);
    }

	#[Route('/reviews/{id}/', name: 'get_review', methods: 'GET')]
	public function get($id): JsonResponse
	{
		$review = $this->reviewRepository->findOneBy(['id' => $id]);

		if (!empty($review)) {
			$data = [
				'id' => $review->getId(),
				'name' => $review->getName(),
				'summery' => $review->getSummary(),
				'mpaa_rating' => $review->getMpaaRating(),
				'publication_date' => $review->getPublicationDate(),
			];
			return new JsonResponse($data, Response::HTTP_OK);
		}

		return new JsonResponse(['status' => 'Review not found'], Response::HTTP_OK);
	}

	#[Route('/reviews/{id}/update', name: 'update_review', methods: 'PUT')]
	public function update(Request $request, $id): JsonResponse
	{
		$review = $this->reviewRepository->findOneBy(['id' => $id]);

		if (empty($review)) {
			return new JsonResponse(['status' => 'Review not found'], Response::HTTP_OK);
		}

		$data = json_decode($request->getContent(), true);

		$review->setName($data['name']);
		$review->setSummary($data['summery']);
		$review->setMpaaRating($data['mpaa_rating']);
		$review->setPublicationDate(new \DateTime($data['publication_date']));

		$errors = $this->validator->validate($review);
		if (count($errors) > 0) {
			$errorsString = (string) $errors;
			return new JsonResponse(['status' => $errorsString], Response::HTTP_BAD_REQUEST);
		}

		$this->reviewRepository->update($review, true);

		return new JsonResponse(['status' => 'Review updated!'], Response::HTTP_CREATED);
	}

	#[Route('/reviews/{id}/', name: 'delete_review', methods: 'DELETE')]
	public function delete($id): JsonResponse
	{
		$review = $this->reviewRepository->findOneBy(['id' => $id]);

		if (empty($review)) {
			return new JsonResponse(['status' => 'Review not found'], Response::HTTP_OK);
		}

		$this->reviewRepository->remove($review, true);

		return new JsonResponse(['status' => 'Review deleted!'], Response::HTTP_CREATED);
	}
}
