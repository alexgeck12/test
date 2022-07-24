<?php

namespace App\Command;

use App\Entity\Critic;
use App\Entity\Movie;
use App\Entity\Review;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\DependencyInjection\ParameterBag\ContainerBagInterface;
use Symfony\Component\HttpClient\HttpClient;
use Symfony\Contracts\HttpClient\HttpClientInterface;

#[AsCommand(
    name: 'app:api-bootstrap',
    description: 'Command for receiving data from api',
)]
class ApiBootstrapCommand extends Command
{
	protected HttpClientInterface $httpClient;
	protected EntityManagerInterface $entityManager;
	protected ContainerBagInterface $params;

	public function __construct(
		HttpClient $httpClient,
		EntityManagerInterface $entityManager,
		ContainerBagInterface $params
	)
	{
		$this->httpClient = $httpClient::create();
		$this->entityManager = $entityManager;
		$this->params = $params;
		parent::__construct();
	}

	protected function configure(): void
	{

	}

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
		$io = new SymfonyStyle($input, $output);
		$critics = $this->entityManager->getRepository(Critic::class)->findAll();

		if (!empty($critics)) {
			foreach ($critics as $critic) {
				// get critic's reviews
				$response = $this->httpClient->request('GET', $this->params->get('api.entrypoint').'/svc/movies/v2/reviews/search.json', [
					'query' => [
						'api-key' => $this->params->get('api.key'),
						'reviewer' => $critic->getName(),
					],
				]);
				// check response status
				$content = $response->toArray();
				if ($content['status'] !== 'OK') {
					$io->error('Failed to get information');
					return Command::FAILURE;
				}
				// add movies and reviews
				foreach ($content['results'] as $item) {
					$movie = $this->entityManager->getRepository(Movie::class)->findOneBy(['name' => $item['display_title']]);
					if (empty($movie)) {
						$movie = new Movie();
						$movie->setName($item['display_title']);
						$this->entityManager->persist($movie);
					}

					$review = $this->entityManager->getRepository(Review::class)->findBy([
						'name' => $item['headline'],
						'publication_date' => new \DateTime($item['publication_date'])
					]);
					if (empty($review)) {
						$review = new Review();
						$review->setName($item['headline']);
						$review->setSummary($item['summary_short']);
						$review->setMpaaRating($item['mpaa_rating']);
						$review->setPublicationDate(new \DateTime($item['publication_date']));
						$review->setCritic($critic);
						$review->setMovie($movie);
						$this->entityManager->persist($review);
					}

					$this->entityManager->flush();
				}
			}
			return Command::SUCCESS;
		}

		$io->error('Critics is empty. First run the "bin/console doctrine:fixtures:load" command');
		return Command::FAILURE;
    }
}