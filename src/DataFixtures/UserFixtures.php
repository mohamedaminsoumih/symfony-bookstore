<?php

namespace App\DataFixtures;

use App\Entity\User;
use App\Provider\Livre as LivreProvider;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Faker\Factory;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class UserFixtures extends Fixture
{
    const FIXTURE_COUNT = 1;
    /**
     * @var UserPasswordHasherInterface
     */
    private $userPasswordHasher;

    public function __construct(UserPasswordHasherInterface $userPasswordHasher)
    {
        $this->userPasswordHasher = $userPasswordHasher;
    }

    public function load(ObjectManager $manager): void
    {
        $user = new User();
        $user->setEmail('admin@php.net');
        $user->setRoles(['ROLE_ADMIN']);
        $user->setPassword($this->userPasswordHasher->hashPassword($user, 'p@ssword'));
        $manager->persist($user);
        $this->addReference(User::class.'_0', $user);

        $manager->flush();
    }
}
