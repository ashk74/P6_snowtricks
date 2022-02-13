<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\RegistrationType;

use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class SecurityController extends AbstractController
{
    #[Route('/signup', name: 'security_registration')]
    public function registration(Request $request, EntityManagerInterface $manager, UserPasswordHasherInterface $passwordHasher): Response
    {
        $user = new User();
        $form = $this->createForm(RegistrationType::class, $user);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $hashedPassword = $passwordHasher->hashPassword($user, $user->getPassword());

            $user->setPassword($hashedPassword);

            $user->setAvatar('avatar.jpg');
            $user->setIsValidate(true);
            $user->setCreatedAt(new \DateTime());
            $manager->persist($user);
            $manager->flush();

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
    public function logout() {}
}
