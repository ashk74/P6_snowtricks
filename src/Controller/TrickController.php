<?php

namespace App\Controller;

use App\Entity\Trick;
use App\Entity\Comment;
use App\Entity\Picture;
use App\Entity\Video;
use App\Form\TrickType;
use App\Form\CommentType;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\String\Slugger\SluggerInterface;

use App\Repository\TrickRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class TrickController extends AbstractController
{
    private $entityManager;
    protected $requestStack;

    public function __construct(RequestStack $requestStack, EntityManagerInterface $entityManager) {
        $this->requestStack = $requestStack;
        $this->entityManager = $entityManager;
    }

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
    public function create(SluggerInterface $slugger, Trick $trick = null): Response
    {
        if (!$trick) {
            $trick = new Trick();
        }

        $video1 = new Video();
        $trick->getVideos()->add($video1);

        $video2 = new Video();
        $trick->getVideos()->add($video2);

        $form = $this->createForm(TrickType::class, $trick);
        $form->handleRequest($this->requestStack->getCurrentRequest());

        if ($form->isSubmitted() && $form->isValid()) {
           if (!$trick->getId()) {
                $trick->setCreatedAt(new \DateTime())
                      ->setSlug($slugger->slug(strtolower($trick->getName()), '-', 'en'));
            } else {
                $trick->setUpdatedAt(new \DateTime());
            }

            $uploadsDirectory = $this->getParameter('pictures_directory');
            $files = $form->get('pictures')->getData();

            foreach ($files as $key => $file) {
                $safeFilename = $slugger->slug(strtolower($trick->getName()), '-', 'en');
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

            $video1->setTrick($trick);
            $video2->setTrick($trick);

            $this->entityManager->persist($trick);
            $this->entityManager->flush();

            return $this->redirectToRoute('trick_show', ['slug' => $trick->getSlug()]);
        }

        return $this->renderForm('trick/create.html.twig', [
            'form' => $form,
            'editMode' => $trick->getId() !== null
        ]);
    }

    #[Route('/trick/details/{slug}', name: 'trick_show', methods: ['GET', 'POST'])]
    public function show(Trick $trick)
    {
        $comment = new Comment();

        $form = $this->createForm(CommentType::class, $comment);
        $form->handleRequest($this->requestStack->getCurrentRequest());

        if ($form->isSubmitted() && $form->isValid()) {
            $comment->setTrick($trick)
                    ->setAuthor($this->getUser())
                    ->setCreatedAt(new \DateTime());

            $this->entityManager->persist($comment);
            $this->entityManager->flush();
        }

        return $this->renderForm('trick/show.html.twig', [
            'trick' => $trick,
            'commentForm' => $form
        ]);
    }

    #[Route('/trick/delete/{slug}', name: 'trick_delete', methods: ['GET', 'POST'])]
    public function delete(Trick $trick) {
        $this->entityManager->remove($trick);
        $this->entityManager->flush();

        return $this->redirectToRoute('tricks_all');
    }
}
