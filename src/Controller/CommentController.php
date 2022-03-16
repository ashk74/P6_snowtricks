<?php

namespace App\Controller;

use App\Entity\Trick;
use App\Entity\Comment;
use App\Service\Pagination;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class CommentController extends AbstractController
{
    /**
     * Paginated : Display paginated comments
     */
    #[Route('/trick/{slug}/comments/page/{page}', name: 'comments_paginated')]
    public function paginated(Trick $trick, Pagination $paginator, int $page = 1)
    {
        // Init the paginator
        $paginator->setQuery('SELECT comment FROM App\Entity\Comment comment WHERE comment.trick = :id ORDER BY comment.createdAt DESC');
        $paginator->setParamsToBind(['id'], [$trick->getId()]);

        // Retrieve paginated comments with paginator methods
        $comments = $paginator->paginate($page, 10);

        return $this->render('comment/index.html.twig', [
            'comments' => $comments,
            'lastPage' => $paginator->getLastPage(),
            'trick' => $trick
        ]);
    }

    /**
     * Delete : Delete own current comment
     */
    #[Route('/comment/{id}/delete', name: 'comment_delete', requirements: ['id' => '\d+'])]
    #[IsGranted('COMMENT_DELETE', subject: 'comment')]
    public function delete(EntityManagerInterface $entityManager, Comment $comment)
    {
        $entityManager->remove($comment);
        $entityManager->flush();
        $this->addFlash('success', 'Le commentaire à bien été supprimé');

        return $this->redirectToRoute('trick_show', ['slug' => $comment->getTrick()->getSlug()]);
    }
}
