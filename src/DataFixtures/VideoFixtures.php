<?php

namespace App\DataFixtures;

use App\Entity\Video;
use App\DataFixtures\TrickFixtures;
use App\Repository\TrickRepository;
use Doctrine\Persistence\ObjectManager;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;

class VideoFixtures extends Fixture implements DependentFixtureInterface
{
    private TrickRepository $trickRepo;

    public function __construct(TrickRepository $trickRepo)
    {
        $this->trickRepo = $trickRepo;
    }

    public function load(ObjectManager $manager): void
    {
        $tricks = $this->trickRepo->findAll();
        $faker = \Faker\Factory::create('fr_FR');

        $videos = [
            'Les bases du snowboard' => 'https://youtu.be/R2Cp1RumorU',
            'Vos premier slides' => 'https://youtu.be/WOgw5uBSLp0',
            'Comment faire un Backflip ?' => 'https://dai.ly/xyhn43',
            'Goofy ou regular ?' => 'https://dai.ly/x34gh4z',
            'Les grabs : Mute' => 'https://youtu.be/jm19nEvmZgM',
            'Comment faire des grabs' => 'https://youtu.be/L4bIunv8fHM',
            'Les grabs : Indy' => 'https://youtu.be/6yA3XqjTh_w',
            'Les grabs : Tail grab' => 'https://youtu.be/_Qq-YoXwNQY',
            'Comment tomber (sans se blesser)' => 'https://youtu.be/zLFAlw8Nt-w',
            'Comment faire un Frontflip ?' => 'https://dai.ly/x18km1p'
        ];

        for ($i = 0; $i < mt_rand(1, 4); $i++) {
            foreach ($videos as $title => $url) {
                $video = new Video();
                $video->setTitle($title)
                    ->setUrl($url)
                    ->setTrick($faker->randomElement($tricks));
                $manager->persist($video);
            }
        }

        $manager->flush();
    }

    public function getDependencies()
    {
        return [
            TrickFixtures::class,
        ];
    }
}
