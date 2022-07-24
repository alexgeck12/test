<?php

namespace App\DataFixtures;

use App\Entity\Critic;
use App\Entity\Movie;
use App\Entity\Review;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class AppFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        $movie = new Movie();
		$critic = new Critic();
		$review = new Review();

		$movie->setName('The Green Mile');
		$manager->persist($movie);

		$critic->setName('Janet Maslin');
		$critic->setStatus('part-time');
		$critic->setBio('American journalist, best known as a film and literary critic for The New York Times.');
		$manager->persist($critic);

		$review->setName('The Green Mile');
		$review->setSummary('Gentle-giant healer on death row. Moving performances, durable storytelling.');
		$review->setMpaaRating('R');
		$review->setPublicationDate(new \DateTime('now'));
		$review->setCritic($critic);
		$review->setMovie($movie);
        $manager->persist($review);

        $manager->flush();
    }
}
