<?php

namespace App\DataFixtures;

use App\Entity\Trick;
use Doctrine\Persistence\ObjectManager;
use Doctrine\Bundle\FixturesBundle\Fixture;

class TrickFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        $faker = \Faker\Factory::create('fr_FR');

        $trickName = ['Mute', 'Sad', 'Indy', '180', '360', '720', 'Front flip', 'Back flip', 'Nose slide', 'Tail slide'];
        $trickContent = $faker->paragraphs(10);

        for ($i = 0; $i < 10; $i++) {
            $trick = new Trick();
            $trick->setName($trickName[$i])
                  ->setContent($trickContent[$i])
                  ->setSlug(strtolower(preg_replace('/ /', '-', $trick->getName())))
                  ->setCreatedAt($faker->dateTimeBetween($startDate = '-6 months', $endDate = 'now'));
            $manager->persist($trick);
        }

        $manager->flush();
    }
}
