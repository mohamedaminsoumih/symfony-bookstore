<?php

namespace App\DataFixtures;

use App\Entity\Auteur;
use App\Provider\Auteur as AuteurProvider;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Faker\Factory;

class AuteurFixtures extends Fixture
{
    const FIXTURE_COUNT = 20;

    public function load(ObjectManager $manager): void
    {
        $faker = Factory::create('fr_FR');
        $faker->addProvider(new AuteurProvider($faker));
        for ($i = 0; $i < self::FIXTURE_COUNT; ++$i) {
            $auteur = new Auteur();
            $auteur->setNomPrenom($faker->unique()->lastName . ' ' . $faker->unique()->firstName);
            $auteur->setDateDeNaissance($faker->dateTimeBetween());
            $auteur->setNationalite($faker->country);
            $auteur->setSexe($faker->sexe);
            $manager->persist($auteur);
            $this->addReference(Auteur::class.'_'.$i, $auteur);
        }

        $manager->flush();
    }
}
