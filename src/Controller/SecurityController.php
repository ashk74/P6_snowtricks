<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\RegistrationType;
use App\Service\FileUploader;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class SecurityController extends AbstractController
{
    private $entityManager;
    private $passwordHasher;

    public function __construct(EntityManagerInterface $entityManager, UserPasswordHasherInterface $passwordHasher)
    {
        $this->entityManager = $entityManager;
        $this->passwordHasher = $passwordHasher;
    }

    #[Route('/signup', name: 'security_registration')]
    public function registration(Request $request, FileUploader $fileUploader): Response
    {
        $user = new User();

        $form = $this->createForm(RegistrationType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $hashedPassword = $this->passwordHasher->hashPassword($user, $user->getPassword());
            $avatarFile = $form->get('avatar')->getData();

            if ($avatarFile) {
                $fileUploader->upload($avatarFile, 'avatars');
                $user->setAvatar($fileUploader->getFinalFileName());
            } else {
                $user->setAvatar('default-avatar.jpg');
            }

            $user->setPassword($hashedPassword);
            $user->setIsValidate(true);
            $user->setCreatedAt(new \DateTime());

            $this->entityManager->persist($user);
            $this->entityManager->flush();

            return $this->redirectToRoute('security_login');
        }

        return $this->renderForm('security/registration.html.twig', [
            'form' => $form
        ]);
    }

    #[Route('/login', name: 'security_login')]
    public function login(): Response
    {
        return $this->render('security/login.html.twig');
    }

    #[Route('/logout', name: 'security_logout')]
    public function logout()
    {
    }
}
