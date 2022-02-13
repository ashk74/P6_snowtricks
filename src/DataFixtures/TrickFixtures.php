<?php

namespace App\DataFixtures;

use App\Entity\Category;
use App\Entity\Comment;
use App\Entity\Trick;
use Doctrine\Persistence\ObjectManager;
use Doctrine\Bundle\FixturesBundle\Fixture;

class TrickFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        $faker = \Faker\Factory::create('fr_FR');

        $categories =  ['Grabs', 'Rotations', 'Flips', 'Slides'];
        $trickName = ['Mute', 'Sad', 'Indy', '180', '360', '720', 'Front flip', 'Back flip', 'Nose slide', 'Tail slide'];

        for ($i = 0; $i < 4; $i++) {
            $category = new Category();
            $category->setName($categories[$i]);

            $manager->persist($category);
        }

        for ($j = 0; $j < 10; $j++) {
            $trick = new Trick();

            $content = $faker->paragraphs(mt_rand(5, 9), true);

            $trick->setName($trickName[$j])
                ->setContent($content)
                ->setSlug(strtolower(preg_replace('/ /', '-', $trick->getName())))
                ->setCreatedAt($faker->dateTimeBetween($startDate = '-6 months', $endDate = 'now'))
                ->setCategory($category);

            $manager->persist($trick);

            for ($k = 0; $k < mt_rand(4, 8); $k++) {
                $comment = new Comment();

                $content = $faker->paragraphs(mt_rand(2, 4), true);
                $days = (new \DateTime())->diff($trick->getCreatedAt())->days;

                $comment->setAuthor($faker->name)
                        ->setContent($content)
                        ->setCreatedAt($faker->dateTimeBetween('-' . $days . ' days'))
                        ->setTrick($trick);

                $manager->persist($comment);
            }
        }
        $manager->flush();
    }
}
