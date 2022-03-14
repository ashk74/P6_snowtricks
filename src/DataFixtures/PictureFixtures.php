<?php

namespace App\DataFixtures;

use App\Entity\Picture;
use App\DataFixtures\TrickFixtures;
use App\Repository\TrickRepository;
use App\Service\FileUploader;
use Doctrine\Persistence\ObjectManager;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;

class PictureFixtures extends Fixture implements DependentFixtureInterface
{
    private TrickRepository $trickRepo;
    private FileUploader $fileUploader;
    private string $fixturesDirectory;
    private string $uploadDirectory;

    public function __construct(TrickRepository $trickRepo, FileUploader $fileUploader, string $fixturesDirectory, string $uploadDirectory)
    {
        $this->trickRepo = $trickRepo;
        $this->fileUploader = $fileUploader;
        $this->fixturesDirectory = $fixturesDirectory;
        $this->uploadDirectory = $uploadDirectory;
    }

    public function load(ObjectManager $manager): void
    {
        $tricks = $this->trickRepo->findAll();
        $faker = \Faker\Factory::create('fr_FR');

        $filenames = [
            'default-picture.png',
            'picture-01.jpg',
            'picture-02.jpg',
            'picture-03.jpg',
            'picture-04.jpg',
            'picture-05.jpg',
            'picture-06.jpg',
            'picture-07.jpg',
            'picture-08.jpg',
            'picture-09.jpg',
        ];

        foreach ($filenames as $filename) {
            $originFilePath = $this->fixturesDirectory . '/pictures/' . $filename;
            $targetFilePath = $this->uploadDirectory . '/pictures/' . $filename;
            $this->fileUploader->copy($originFilePath, $targetFilePath);
        }

        $i = 0;
        foreach ($tricks as $trick) {
            $mainPicture = new Picture();
            $mainPicture->setFilename($filenames[$i])
                ->setIsMain(true)
                ->setTrick($trick);
            $manager->persist($mainPicture);
            $i++;
        }

        foreach ($tricks as $trick) {
            for ($j = 0; $j < mt_rand(0, 6); $j++) {
                $otherPictures = new Picture();
                $otherPictures->setFilename($faker->randomElement($filenames))
                    ->setIsMain(false)
                    ->setTrick($trick);
                $manager->persist($otherPictures);
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
