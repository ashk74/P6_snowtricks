<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\UserType;
use App\Service\FileManager;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class UserController extends AbstractController
{
    private EntityManagerInterface $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    /**
     * Profile : Display own profile
     */
    #[Route('/profile/{id}', name: 'user_show')]
    #[IsGranted('USER_PROFILE', subject: 'user')]
    public function index(User $user): Response
    {
        return $this->render('user/index.html.twig', [
            'user' => $user
        ]);
    }

    /**
     * Edit : Edit profile avatar
     */
    #[Route('/profile/{id}/edit', name: 'user_edit')]
    #[IsGranted('USER_PROFILE', subject: 'user')]
    public function edit(User $user, Request $request, FileManager $fileManager): Response
    {
        $form = $this->createForm(UserType::class, $user);
        $oldAvatar = $user->getAvatar();
        $form->handleRequest($request);
        $file = $form->get('avatar')->getData();

        if ($form->isSubmitted() && $form->isValid()) {
            $fileManager->upload($file, 'avatars');
            $user->setAvatar($fileManager->getFinalFileName());
            $this->entityManager->persist($user);
            $this->entityManager->flush();

            if ($oldAvatar != 'default-avatar.png') {
                $fileManager->remove('avatars', $oldAvatar);
            }

            $this->addFlash('success', 'Votre photo de profil a bien été mise à jour');

            return $this->redirectToRoute('user_show', ['id' => $user->getId()]);
        }

        return $this->renderForm('user/edit.html.twig', [
            'user' => $user,
            'userForm' => $form
        ]);
    }

    /**
     * Delete : Delete own profile
     */
    #[Route('/profile/{id}/delete', name: 'user_delete')]
    #[IsGranted('USER_PROFILE', subject: 'user')]
    public function delete(User $user): Response
    {
        $this->entityManager->remove($user);
        $this->entityManager->flush();

        $this->container->get('security.token_storage')->setToken(null);
        $this->addFlash('success', 'Votre compte a bien été supprimé');

        return $this->redirectToRoute('app_logout');
    }
}
