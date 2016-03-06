<?php

namespace App\Model\Facade;

use App\Model\Entity\Article;
use App\Model\Entity\Comment;
use App\Model\Entity\User;
use App\Model\Query\ArticleQuery;
use App\Model\Query\CommentQuery;
use App\Model\Repository\ArticleRepository;
use App\Model\Repository\CommentRepository;
use Kdyby\Doctrine\EntityManager;
use Nette\Object;

/**
 * Class RedactorFacade
 * @package App\Model\Facade
 * @author Jakub Hadamčík <jakub@hadamcik.cz>
 */
class RedactorFacade extends Object
{
    /** @var EntityManager */
    private $entityManager;

    /** @var ArticleRepository */
    private $articleRepository;

    /** @var CommentRepository */
    private $commentRepository;

    /**
     * RedactorFacade constructor.
     * @param EntityManager $entityManager
     */
    public function __construct(EntityManager $entityManager)
    {
        $this->entityManager = $entityManager;
        $this->articleRepository = $this->entityManager->getRepository(Article::class);
        $this->commentRepository = $this->entityManager->getRepository(Comment::class);
    }

    /**
     * @param Article $article
     */
    public function addArticle(Article $article)
    {
        $this->entityManager->persist($article);
        $this->entityManager->flush($article);
    }

    /**
     * @param Article $article
     * @return \Kdyby\Doctrine\ResultSet
     */
    public function getArticleComments(Article $article)
    {
        $query = (new CommentQuery())
            ->byArticle($article)
            ->notRemoved()
            ->withAuthors();
        return $this->commentRepository->fetch($query);
    }

    /**
     * @param Article $article
     * @return \Kdyby\Doctrine\ResultSet
     */
    public function findCommentsForApprove(Article $article)
    {
        $query =( new CommentQuery())
            ->isNotPublished()
            ->isNotRemoved()
            ->byArticle($article)
            ->orderByAdded();
        return $this->commentRepository->fetch($query);
    }

    /**
     * @return \Kdyby\Doctrine\ResultSet
     */
    public function findForApprove()
    {
        $query = (new ArticleQuery())
            ->isNotPublished()
            ->orderByAdded();
        return $this->articleRepository->fetch($query);
    }

    /**
     * @param Article $article
     * @return Article
     * @throws \Exception
     */
    public function publishArticle(Article $article)
    {
        $article->publish();
        $this->entityManager->flush($article);
        return $article;
    }

    /**
     * @param Comment $comment
     * @return Comment
     * @throws \Exception
     */
    public function publishComment(Comment $comment)
    {
        $comment->publish();
        $this->entityManager->flush($comment);
        return $comment;
    }

    /**
     * @param Comment $comment
     * @param User $remover
     * @return Comment
     * @throws \Exception
     */
    public function removeComment(Comment $comment, User $remover)
    {
        $comment->remove($remover);
        $this->entityManager->flush($comment);
        return $comment;
    }

    /**
     * @param Article $article
     * @throws \Exception
     */
    public function unpublishArticle(Article $article)
    {
        $article->unpublish();
        $this->entityManager->flush($article);
    }

    /**
     * @param Article $article
     */
    public function updateArticle(Article $article)
    {
        $this->entityManager->flush($article);
    }
}
