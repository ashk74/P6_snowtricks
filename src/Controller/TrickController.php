<?php

namespace App\Controller;

use App\Entity\Trick;
use App\Entity\Comment;
use App\Entity\Picture;
use App\Form\TrickType;
use App\Form\CommentType;
use App\Repository\TrickRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\String\Slugger\SluggerInterface;

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
    public function create(Request $request, EntityManagerInterface $manager, SluggerInterface $slugger, Trick $trick = null): Response
    {
        if (!$trick) {
            $trick = new Trick();
        }

        $form = $this->createForm(TrickType::class, $trick);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
           if (!$trick->getId()) {
                $trick->setCreatedAt(new \DateTime())
                      ->setSlug($slugger->slug($trick->getName()));
            } else {
                $trick->setUpdatedAt(new \DateTime());
            }

            $uploadsDirectory = $this->getParameter('pictures_directory');
            $files = $form->get('pictures')->getData();

            foreach ($files as $key => $file) {
                $safeFilename = $slugger->slug($trick->getName());
                $newFilename = $safeFilename . '-' . md5(uniqid()) . '.' . $file->guessExtension();
                $file->move(
                    $uploadsDirectory,
                    $newFilename
                );

                $picture = new Picture();
                $picture->setTrick($trick)
                        ->setFilename($newFilename);

                if ($key == 0) {
                    $picture->setIsMain(1);
                } else {
                    $picture->setIsMain(0);
                }

                $trick->addPicture($picture);
            }

            $manager->persist($trick);
            $manager->flush();

            return $this->redirectToRoute('trick_show', ['slug' => $trick->getSlug()]);
        }

        return $this->renderForm('trick/create.html.twig', [
            'form' => $form,
            'editMode' => $trick->getId() !== null
        ]);
    }

    #[Route('/trick/details/{slug}', name: 'trick_show', methods: ['GET', 'POST'])]
    public function show(Request $request, Trick $trick, EntityManagerInterface $manager)
    {
        $comment = new Comment();

        $form = $this->createForm(CommentType::class, $comment);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $comment->setTrick($trick)
                    ->setAuthor($this->getUser())
                    ->setCreatedAt(new \DateTime());

            $manager->persist($comment);
            $manager->flush();
        }

        return $this->renderForm('trick/show.html.twig', [
            'trick' => $trick,
            'commentForm' => $form
        ]);
    }
}
