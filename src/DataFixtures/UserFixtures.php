<?php

namespace App\DataFixtures;

use App\Entity\User;
use App\Service\FileUploader;
use Doctrine\Persistence\ObjectManager;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Symfony\Component\String\Slugger\SluggerInterface;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

// require_once 'vendor/autoload.php';

class UserFixtures extends Fixture
{
    private UserPasswordHasherInterface $passwordHasher;
    private FileUploader $fileUploader;
    private string $fixturesDirectory;
    private string $uploadDirectory;

    public function __construct(UserPasswordHasherInterface $passwordHasher, FileUploader $fileUploader, string $fixturesDirectory, string $uploadDirectory)
    {
        $this->passwordHasher = $passwordHasher;
        $this->fileUploader = $fileUploader;
        $this->fixturesDirectory = $fixturesDirectory;
        $this->uploadDirectory = $uploadDirectory;
    }

    public function load(ObjectManager $manager): void
    {
        $faker = \Faker\Factory::create('fr_FR');
        $user = new User();

        $hashedPassword = $this->passwordHasher->hashPassword($user, $_ENV['ACCOUNT_PASSWORD']);
        $originFilePath = $this->fixturesDirectory . '/avatars/default-avatar.png';
        $targetFilePath = $this->uploadDirectory . '/avatars/default-avatar.png';

        $this->fileUploader->copy($originFilePath, $targetFilePath);

        $user->setEmail($_ENV['ACCOUNT_EMAIL'])
            ->setAvatar('default-avatar.png')
            ->setFullname($_ENV['ACCOUNT_FULLNAME'])
            ->setPassword($hashedPassword)
            ->setIsVerified(true)
            ->setCreatedAt(new \DateTime());

        $manager->persist($user);

        for ($i = 0; $i < 5; $i++) {
            $user = new User();

            $firstname = $faker->firstName();
            $lastname = $faker->name();
            $email = $faker->freeEmail();;
            $hashedPassword = $this->passwordHasher->hashPassword($user, 'password');

            $user->setEmail($email)
                ->setAvatar('default-avatar.png')
                ->setFullname($firstname . ' ' . $lastname)
                ->setPassword($hashedPassword)
                ->setIsVerified(mt_rand(0, 1))
                ->setCreatedAt($faker->dateTimeBetween('-6 months', 'now'));

            $manager->persist($user);
        }
        $manager->flush();
    }
}
