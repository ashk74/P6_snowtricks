<?php

namespace App\Controller;

use App\Entity\Trick;
use App\Entity\Comment;
use App\Entity\Picture;
use App\Form\TrickType;
use App\Form\CommentType;
use App\Repository\CategoryRepository;
use App\Repository\TrickRepository;
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
    private $entitymanager;

    public function __construct(EntityManagerInterface $entitymanager)
    {
        $this->entitymanager = $entitymanager;
    }

    /**
     * Add : Add a new trick
     *
     * @param \Symfony\Component\String\Slugger\SluggerInterface $slugger
     * @param \Symfony\Component\HttpFoundation\Request $request
     * @param \App\Service\FileUploader $fileUploader
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    #[IsGranted('ROLE_USER')]
    #[Route('/trick/add', name: 'trick_add')]
    public function add(SluggerInterface $slugger, Request $request, FileUploader $fileUploader): Response
    {
        $trick = new Trick();

        $form = $this->createForm(TrickType::class, $trick);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $mainPictureFilename = 'default-picture.png';

            // Check if mainPicture is not empty
            if ($form->get('mainPicture')->getData() != null) {
                $mainPictureFilename = $fileUploader->upload($form->get('mainPicture')->getData(), 'pictures');
            }

            // Set main picture
            $mainPicture = new Picture();
            $mainPicture->setTrick($trick)
                ->setFilename($mainPictureFilename)
                ->setIsMain(true);
            $trick->addPicture($mainPicture);

            // Set other pictures
            foreach ($form->get('pictures')->getData() as $file) {
                $pictureFilename = $fileUploader->upload($file, 'pictures');
                $picture = new Picture();
                $picture->setTrick($trick)
                    ->setFilename($pictureFilename);
                $trick->addPicture($picture);
            }

            // Set videos
            foreach ($trick->getVideos() as $video) {
                $video->setTrick($trick);
            }

            // Set date and slug
            $trick->setCreatedAt(new \DateTime())
                ->setSlug($slugger->slug(strtolower($trick->getName()), '-', 'en'));

            $this->entitymanager->persist($trick);
            $this->entitymanager->flush();

            $this->addFlash('success', "Le trick a bien été ajouté");

            return $this->redirectToRoute('tricks_all');
        }

        return $this->renderForm('trick/add.html.twig', [
            'form' => $form
        ]);
    }

    /**
     * Homepage : List all tricks with pagination
     */
    #[Route('/', name: 'tricks_all')]
    #[Route('/tricks/page/{page}', name: 'tricks_all_paginated', requirements: ['page' => '\d+'])]
    public function all(Pagination $paginator, int $page = 1): Response
    {
        $paginator->setQuery('SELECT trick FROM App\Entity\Trick trick ORDER BY trick.createdAt DESC');

        $tricks = $paginator->paginate($page, 15);

        if (!$tricks->getIterator()->valid() && $paginator->getLastPage() != 0) {
            return $this->redirectToRoute('tricks_all_paginated', ['page' => $paginator->getLastPage()]);
        }

        return $this->render('trick/all.html.twig', [
            'tricks' => $tricks,
            'lastPage' => $paginator->getLastPage(),
            'currentPage' => $page
        ]);
    }

    /**
     * Show : Display trick informations and comments
     */
    #[Route('/trick/{slug}', name: 'trick_show')]
    #[Route('/trick/{slug}/page/{page}', name: 'trick_paginated_comments', requirements: ['page' => '\d+'])]
    public function show(Trick $trick, Request $request): Response
    {
        $comment = new Comment();

        $form = $this->createForm(CommentType::class, $comment);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $comment->setTrick($trick)
                ->setAuthor($this->getUser())
                ->setCreatedAt(new \DateTime());

            $this->entitymanager->persist($comment);
            $this->entitymanager->flush();

            $this->addFlash('success', 'Votre commentaire a bien été publié');

            return $this->redirectToRoute('trick_show', ['slug' => $trick->getSlug()]);
        }

        // Render if success
        return $this->renderForm('trick/show.html.twig', [
            'trick' => $trick,
            'commentForm' => $form
        ]);
    }

    /**
     * Edit : Edit current trick
     */
    #[IsGranted('ROLE_USER')]
    #[Route('/trick/{slug}/edit', name: 'trick_edit')]
    public function edit(Trick $trick, SluggerInterface $slugger, Request $request): Response
    {
        $form = $this->createForm(TrickType::class, $trick, ['validation_groups' => false]);

        $content = $trick->getContent();
        $name = $trick->getName();
        $form->handleRequest($request);
        $trick->setUpdatedAt(new \DateTime());

        if ($trick->getContent() != $content || $trick->getName() != $name) {
            if ($form->isSubmitted() && $form->isValid()) {
                $trick->setUpdatedAt(new \DateTime())
                    ->setSlug($slugger->slug(strtolower($trick->getName()), '-', 'en'));

                $this->entitymanager->flush();

                $this->addFlash('success', "Le trick a bien été modifié");

                return $this->redirectToRoute('trick_show', ['slug' => $trick->getSlug()]);
            }
        }

        return $this->renderForm('trick/edit.html.twig', [
            'form' => $form,
            'trick' => $trick
        ]);
    }

    /**
     * Delete : Delete current trick
     */
    #[Route('/trick/{slug}/delete', name: 'trick_delete')]
    #[IsGranted('ROLE_USER')]
    public function delete(Trick $trick): Response
    {
        $this->entitymanager->remove($trick);
        $this->entitymanager->flush();
        $this->addFlash('success', 'Trick supprimé');

        return $this->redirectToRoute('tricks_all');
    }
}
