<?php

namespace App\Controller;

use App\Entity\Trick;
use App\Entity\Picture;
use App\Form\PictureType;
use App\Service\FileManager;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class PictureController extends AbstractController
{
    private EntityManagerInterface $entitymanager;

    public function __construct(EntityManagerInterface $entitymanager)
    {
        $this->entitymanager = $entitymanager;
    }

    /**
     * Add : Add one or more pictures
     */
    #[IsGranted('ROLE_USER')]
    #[Route('/trick/{slug}/picture/add', name: 'picture_add')]
    public function add(Request $request, FileManager $fileManager, Trick $trick): Response
    {
        $form = $this->createForm(PictureType::class);
        $form->handleRequest($request);
        $files = $form->get('filename')->getData();

        if ($form->isSubmitted() && $form->isValid() && $files != null) {
            foreach ($form->get('filename')->getData() as $file) {
                $fileManager->upload($file, 'pictures');
                $picture = new Picture();
                $picture->setTrick($trick)
                    ->setFilename($fileManager->getFinalFileName());
                $trick->addPicture($picture);
            }

            $this->entitymanager->persist($picture);
            $this->entitymanager->flush();
            $this->addFlash('success', 'Les photos ont bien été ajoutées');

            return $this->redirectToRoute('trick_show', ['slug' => $trick->getSlug()]);
        }

        return $this->renderForm('picture/add.html.twig', [
            'form' => $form
        ]);
    }

    /**
     * Delete : Delete current picture
     */
    #[IsGranted('ROLE_USER')]
    #[Route('/trick/{slug}/picture/{id}/delete', name: 'picture_delete')]
    public function delete(Picture $picture, FileManager $fileManager, string $slug): Response
    {
        $this->entitymanager->remove($picture);
        $this->entitymanager->flush();
        $fileManager->remove('pictures', $picture->getFilename());

        $this->addFlash('success', 'Photo supprimée');

        return $this->redirectToRoute('trick_show', ['slug' => $slug]);
    }
}
