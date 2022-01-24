<?php

namespace App\DataFixtures;

use App\Entity\Auteur;
use App\Entity\Genre;
use App\Entity\Livre;
use App\Provider\Livre as LivreProvider;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Faker\Factory;

class LivreFixtures extends Fixture
{
    const FIXTURE_COUNT = 50;

    public function load(ObjectManager $manager): void
    {
        $faker = Factory::create('fr_FR');
        $faker->addProvider(new LivreProvider($faker));
        for ($i = 0; $i < self::FIXTURE_COUNT; ++$i) {
            $livre = new Livre();
            $livre->setDateDeParution($faker->dateTimeBetween(
                \DateTime::createFromFormat('Y', 1900),
                \DateTime::createFromFormat('Y', 2021)
            ));
            $livre->setTitre($faker->unique()->words(3, true));
            $livre->setIsbn($faker->unique()->isbn13);
            $livre->setNombrePages($faker->numberBetween(20,500));
            $livre->setNote($faker->numberBetween(0,20));
            $faker->unique(true);
            $auteursCount = $faker->numberBetween(1,3);
            for ($j=0; $j < $auteursCount; ++$j) {
                $livre->addAuteur(
                    $this->getReference(Auteur::class.'_'.$faker->unique()->
                        numberBetween(0, AuteurFixtures::FIXTURE_COUNT-1))
                );
            }
            $faker->unique(true);
            $genresCount = $faker->numberBetween(1,3);
            for ($j=0; $j < $genresCount; ++$j) {
                $livre->addGenre(
                    $this->getReference(Genre::class.'_'.$faker->unique()->
                        numberBetween(0, GenreFixtures::FIXTURE_COUNT-1))
                );
            }
            $manager->persist($livre);
            $this->addReference(Livre::class . '_' . $i, $livre);
        }

        $manager->flush();
    }
}
