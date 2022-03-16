<?php

namespace App\DataFixtures;

use App\Entity\Comment;
use App\DataFixtures\TrickFixtures;
use App\Repository\TrickRepository;
use App\Repository\UserRepository;
use Doctrine\Persistence\ObjectManager;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;

class CommentFixtures extends Fixture implements DependentFixtureInterface
{
    private TrickRepository $trickRepo;
    private UserRepository $userRepo;

    public function __construct(TrickRepository $trickRepo, UserRepository $userRepo)
    {
        $this->trickRepo = $trickRepo;
        $this->userRepo = $userRepo;
    }

    public function load(ObjectManager $manager): void
    {
        $faker = \Faker\Factory::create('fr_FR');
        $tricks = $this->trickRepo->findAll();

        foreach ($tricks as $trick) {
            for ($k = 0; $k < mt_rand(4, 12); $k++) {
                $comment = new Comment();
                $users = $this->userRepo->findAll();

                $content = $faker->paragraphs(mt_rand(2, 4), true);

                if (strlen($content) >= 255) {
                    $length = strlen($content) - 254;
                    $content = substr($content, 0, -$length) . '.';
                }

                $days = (new \DateTime())->diff($trick->getCreatedAt())->days;

                $comment->setAuthor($faker->randomElement($users))
                    ->setContent($content)
                    ->setCreatedAt($faker->dateTimeBetween('-' . $days . ' days'))
                    ->setTrick($trick);

                $manager->persist($comment);
            }
        }
        $manager->flush();
    }

    public function getDependencies()
    {
        return [
            TrickFixtures::class,
            UserFixtures::class
        ];
    }
}
