<?php

namespace App\Controller;

use App\Entity\Trick;
use App\Form\TrickType;
use App\Repository\TrickRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class TrickController extends AbstractController
{
    #[Route('/', name: 'tricks_all')]
    public function index(TrickRepository $repository): Response
    {
        $tricks = $repository->findAll();

        return $this->render('trick/index.html.twig', [
            'tricks' => $tricks
        ]);
    }

    #[Route('/trick/new', name: 'trick_create')]
    #[Route('/trick/edit/{slug}', name: 'trick_update')]
    public function create(Request $request, EntityManagerInterface $manager, Trick $trick = null): Response
    {
        if (!$trick) {
            $trick = new Trick();
        }

        $form = $this->createForm(TrickType::class, $trick);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            if (!$trick->getId()) {
                $trick->setCreatedAt(new \DateTime());
            } else {
                $trick->setUpdatedAt(new \DateTime());
            }

            $manager->persist($trick);
            $manager->flush();

            return $this->redirectToRoute('trick_show', ['slug' => $trick->getSlug()]);
        }

        $errors = $form->getErrors();

        return $this->renderForm('trick/create.html.twig', [
            'form' => $form,
            'editMode' => $trick->getId() !== null
        ]);
    }

    #[Route('/trick/details/{slug}', name: 'trick_show', methods: ['GET'])]
    public function show(Trick $trick)
    {
        return $this->render('trick/show.html.twig', [
            'trick' => $trick
        ]);
    }
}
