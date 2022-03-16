<?php

namespace App\Controller;

use App\Entity\Trick;
use App\Entity\Video;
use App\Form\VideoType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class VideoController extends AbstractController
{
    private $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    /**
     * Add : Add a new video to the current trick
     */
    #[Route('/trick/{slug}/video/add', name: 'video_add')]
    #[IsGranted('ROLE_USER')]
    public function add(Request $request, Trick $trick): Response
    {
        $video = new Video();
        $form = $this->createForm(VideoType::class, $video);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $video->setTrick($trick);

            $this->entityManager->persist($video);
            $this->entityManager->flush();
            $this->addFlash('success', 'La vidéo a bien été ajoutée');

            return $this->redirectToRoute('trick_show', ['slug' => $trick->getSlug()]);
        }

        return $this->renderForm('video/add.html.twig', [
            'form' => $form
        ]);
    }

    /**
     * Delete : Delete a video to the current trick
     */
    #[Route('/trick/{slug}/video/{id}/delete', name: 'video_delete')]
    #[IsGranted('ROLE_USER')]
    public function delete(Video $video, string $slug): Response
    {
        $this->entityManager->remove($video);
        $this->entityManager->flush();
        $this->addFlash('success', 'Vidéo supprimée');

        return $this->redirectToRoute('trick_show', ['slug' => $slug]);
    }
}
