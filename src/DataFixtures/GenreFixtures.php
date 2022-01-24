<?php

namespace App\DataFixtures;

use App\Entity\Genre;
use App\Provider\Livre as LivreProvider;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Faker\Factory;

class GenreFixtures extends Fixture
{
    const FIXTURE_COUNT = 10;

    public function load(ObjectManager $manager): void
    {
        $faker = Factory::create('fr_FR');
        $faker->addProvider(new LivreProvider($faker));
        for ($i = 0; $i < self::FIXTURE_COUNT; ++$i) {
            $genre = new Genre();
            $genre->setNom($faker->unique()->genre);
            $manager->persist($genre);
            $this->addReference(Genre::class.'_'.$i, $genre);
        }

        $manager->flush();
    }
}
