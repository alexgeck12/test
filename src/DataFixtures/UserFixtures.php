<?php

namespace App\DataFixtures;

use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class UserFixtures extends Fixture
{
	private UserPasswordHasherInterface $hasher;

	public function __construct(UserPasswordHasherInterface $hasher)
	{
		$this->hasher = $hasher;
	}

	public function load(ObjectManager $manager)
	{
		$user = new User();
		$user->setUsername('test_user');
		$user->setEmail('test_user@mail.com');
		$user->setEnabled(1);
		$user->setRoles(['ROLE_ADMIN', 'ROLE_SUPER_ADMIN']);
		$user->setCreatedAt(new \DateTimeImmutable('now'));

		$password = $this->hasher->hashPassword($user, 'pass_1234');
		$user->setPassword($password);

		$manager->persist($user);
		$manager->flush();
	}
}
