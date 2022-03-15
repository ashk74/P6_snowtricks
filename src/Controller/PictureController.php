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
    private EntityManagerInterface $entityManager;
    private FileManager $fileManager;

    public function __construct(EntityManagerInterface $entityManager, FileManager $fileManager)
    {
        $this->entityManager = $entityManager;
        $this->fileManager = $fileManager;
    }

    /**
     * Add : Add one or more pictures
     */
    #[IsGranted('ROLE_USER')]
    #[Route('/trick/{slug}/picture/add', name: 'picture_add')]
    public function add(Request $request, Trick $trick): Response
    {
        $form = $this->createForm(PictureType::class);
        $form->handleRequest($request);
        $files = $form->get('filename')->getData();

        if ($form->isSubmitted() && $form->isValid() && $files != null) {
            foreach ($form->get('filename')->getData() as $file) {
                $this->fileManager->upload($file, 'pictures');
                $picture = new Picture();
                $picture->setTrick($trick)
                    ->setFilename($this->fileManager->getFinalFileName());
                $trick->addPicture($picture);
            }

            $this->entityManager->persist($picture);
            $this->entityManager->flush();
            $this->addFlash('success', 'Les images ont bien été ajoutées');

            return $this->redirectToRoute('trick_show', ['slug' => $trick->getSlug()]);
        }

        return $this->renderForm('picture/add.html.twig', [
            'form' => $form
        ]);
    }

    /**
     * Set main picture
     */
    #[IsGranted('ROLE_USER')]
    #[Route('/trick/{slug}/picture/{id}/set-main', name: 'picture_set_main')]
    public function setMainPicture(Picture $selectedPicture, string $slug): Response
    {
        foreach ($selectedPicture->getTrick()->getPictures() as $picture) {
            if ($picture->getIsMain() === true) {
                if ($picture->getFilename() === 'default-picture.png') {
                    $this->entityManager->remove($picture);
                }

                if ($picture != null) {
                    $picture->setIsMain(false);
                }

                $selectedPicture->setIsMain(true);
                $this->entityManager->flush();
                $this->addFlash('success', 'Photo principale modifiée');

                return $this->redirectToRoute('trick_show', ['slug' => $slug]);
            }
        }
    }

    /**
     * Delete : Delete current picture
     */
    #[IsGranted('ROLE_USER')]
    #[Route('/trick/{slug}/picture/{id}/delete', name: 'picture_delete')]
    public function delete(Picture $picture, string $slug): Response
    {
        $oldPicture = $picture->getFilename();
        $this->entityManager->remove($picture);
        $this->entityManager->flush();

        if ($oldPicture != 'default-picture.png') {
            $this->fileManager->remove('pictures', $picture->getFilename());
        }

        $this->addFlash('success', 'Photo supprimée');

        return $this->redirectToRoute('trick_show', ['slug' => $slug]);
    }
}
