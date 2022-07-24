<?php

namespace App\Command;

use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
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

	public function __construct(HttpClient $httpClient, EntityManagerInterface $entityManager, ContainerBagInterface $params)
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
		// get movies
		// The Shawshank Redemption, Schindler's List, The Green Mile

		// get critics
		// Janet Maslin


		$response = $this->httpClient->request('GET', $this->params->get('api.entrypoint').'/svc/movies/v2/reviews/search.json');
		$content = $response->toArray();

		foreach ($content as $item) {

		}

        return Command::SUCCESS;
    }
}