<?php

namespace App\Controller;

use App\Entity\Trick;
use App\Entity\Video;
use App\Entity\Comment;
use App\Entity\Picture;
use App\Form\TrickType;
use App\Form\CommentType;
use App\Service\FileUploader;
use App\Service\Pagination;
use Doctrine\ORM\EntityManagerInterface;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\String\Slugger\SluggerInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class TrickController extends AbstractController
{
    private $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    #[Route('/', name: 'tricks_all')]
    #[Route('/tricks/page/{page}', name: 'tricks_all_paginated', requirements: ['page' => '\d+'])]
    public function index(Request $request, Pagination $paginator)
    {
        $paginator->setQuery('SELECT trick FROM App\Entity\Trick trick ORDER BY trick.createdAt DESC');
        $paginator->setCurrentPage($request);

        $tricks = $paginator->paginate();

        if (!$tricks->getIterator()->valid()) {
            return $this->redirectToRoute('tricks_all_paginated', ['page' => $paginator->getLastPage($tricks)]);
        } else {
            return $this->render('trick/index.html.twig', [
                'tricks' => $tricks,
                'currentPage' => $paginator->getCurrentPage(),
                'lastPage' => $paginator->getLastPage($tricks)
            ]);
        }
    }

    #[Route('/trick/details/{slug}', name: 'trick_show')]
    #[Route('/trick/details/{slug}/page/{page}', name: 'trick_show_paginated', requirements: ['page' => '\d+'])]
    public function show(Trick $trick, Request $request, Pagination $paginator)
    {
        $comment = new Comment();

        $form = $this->createForm(CommentType::class, $comment);
        $form->handleRequest($request);

        $paginator->setQuery('SELECT comment FROM App\Entity\Comment comment WHERE comment.trick = :id ORDER BY comment.createdAt DESC');
        $paginator->setParamsToBind(['id'], [$trick->getId()]);
        $paginator->setCurrentPage($request);
        $paginator->setLimitPerPage(5);

        $comments = $paginator->paginate();

        if ($form->isSubmitted() && $form->isValid()) {
            $comment->setTrick($trick)
                ->setAuthor($this->getUser())
                ->setCreatedAt(new \DateTime());

            $this->entityManager->persist($comment);
            $this->entityManager->flush();

            return $this->redirectToRoute('trick_show', ['slug' => $trick->getSlug()]);
        }

        if ($paginator->getLastPage($comments) != 0 && !$comments->getIterator()->valid()) {
            return $this->redirectToRoute('trick_show_paginated', [
                'slug' => $trick->getSlug(),
                'page' => $paginator->getLastPage($comments)
            ]);
        } else {
            return $this->renderForm('trick/show.html.twig', [
                'trick' => $trick,
                'commentForm' => $form,
                'comments' => $comments,
                'currentPage' => $paginator->getCurrentPage(),
                'lastPage' => $paginator->getLastPage($comments)
            ]);
        }
    }

    #[Route('/trick/new', name: 'trick_create')]
    #[Route('/trick/edit/{slug}', name: 'trick_update')]
    #[IsGranted('IS_AUTHENTICATED_FULLY')]
    public function create(SluggerInterface $slugger, Request $request, FileUploader $fileUploader, Trick $trick = null): Response
    {
        if (!$trick) {
            $trick = new Trick();
        }

        $video1 = new Video();
        $trick->getVideos()->add($video1);

        $video2 = new Video();
        $trick->getVideos()->add($video2);

        $form = $this->createForm(TrickType::class, $trick);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            if (!$trick->getId()) {
                $trick->setCreatedAt(new \DateTime())
                    ->setSlug($slugger->slug(strtolower($trick->getName()), '-', 'en'));
            } else {
                $trick->setUpdatedAt(new \DateTime());
            }

            $files = $form->get('pictures')->getData();

            foreach ($files as $key => $file) {
                $fileUploader->upload($file, 'pictures');

                $picture = new Picture();
                $picture->setTrick($trick)
                    ->setFilename($fileUploader->getFinalFileName());

                if ($key == 0) {
                    $picture->setIsMain(1);
                } else {
                    $picture->setIsMain(0);
                }

                $trick->addPicture($picture);
            }

            $video1->setTrick($trick);
            $video2->setTrick($trick);

            $this->entityManager->persist($trick);
            $this->entityManager->flush();

            $this->addFlash('success', "Le trick a bien été ajouté");

            return $this->redirectToRoute('tricks_all');
        }

        return $this->renderForm('trick/create.html.twig', [
            'form' => $form,
            'editMode' => $trick->getId() !== null
        ]);
    }

    #[Route('/trick/delete/{slug}', name: 'trick_delete', methods: ['GET', 'POST'])]
    public function delete(Trick $trick)
    {
        $this->entityManager->remove($trick);
        $this->entityManager->flush();

        return $this->redirectToRoute('tricks_all');
    }
}
