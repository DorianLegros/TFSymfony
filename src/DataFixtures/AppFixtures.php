<?php

namespace App\DataFixtures;

use App\Entity\Media;
use App\Entity\Town;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Faker\Factory;

class AppFixtures extends Fixture
{
    public function load(ObjectManager $manager)
    {
        $faker = Factory::create("Fr-fr");

        for ($i = 0; $i < 20; $i++) {
            $commune = new Town();
            $commune->setNom($faker->city);
            $commune->setCode($faker->numberBetween(10000, 100000));
            $commune->setCodeDepartement($faker->numberBetween(0,100));
            $commune->setCodeRegion($faker->numberBetween(0,100));
            $commune->setCodesPostaux(["76000"]);
            $commune->setPopulation($faker->numberBetween(10000, 1000000));
            $manager->persist($commune);

            $media = new Media();
            $media->setUrl($faker->imageUrl(640,480,'city'))
                ->setCommune($commune);
            $manager->persist($media);
        }

        $manager->flush();
    }
}
