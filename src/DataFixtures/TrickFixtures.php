<?php

namespace App\DataFixtures;

use App\Entity\Trick;
use App\DataFixtures\CategoryFixtures;
use App\Repository\CategoryRepository;
use Doctrine\Persistence\ObjectManager;
use Doctrine\Bundle\FixturesBundle\Fixture;

class TrickFixtures extends Fixture
{
    private CategoryRepository $categoryRepo;

    public function __construct(CategoryRepository $categoryRepo)
    {
        $this->categoryRepo = $categoryRepo;
    }

    public function load(ObjectManager $manager): void
    {
        $faker = \Faker\Factory::create('fr_FR');

        $names = [
            'Mute' => 'Grabs',
            'Sad' => 'Grabs',
            'Indy' => 'Grabs',
            '180' => 'Rotations',
            '360' => 'Rotations',
            '720' => 'Rotations',
            'Frontflip' => 'Flips',
            'Backflip' => 'Flips',
            'Nose slide' => 'Slides',
            'Tail slide' => 'Slides'
        ];

        foreach ($names as $name => $category) {
            $trick = new Trick();

            $trick->setName($name)
                ->setContent($faker->paragraphs(mt_rand(5, 9), true))
                ->setCategory($this->categoryRepo->findOneBy(['name' => $category]))
                ->setCreatedAt($faker->dateTimeBetween('-6 months', 'now'))
                ->setSlug(strtolower(preg_replace('/ /', '-', $trick->getName())));

            $manager->persist($trick);
        }

        $manager->flush();
    }

    public function getDependencies()
    {
        return [
            CategoryFixtures::class
        ];
    }
}
