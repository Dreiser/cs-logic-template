<?php

namespace App\Model\Facade;

use App\Model\Entity\Article;
use App\Model\Entity\Comment;
use App\Model\Query\ArticleQuery;
use App\Model\Query\CommentQuery;
use App\Model\Repository\ArticleRepository;
use App\Model\Repository\CommentRepository;
use Kdyby\Doctrine\EntityManager;
use Nette\Object;
use Nette\Utils\DateTime;

/**
 * Class BlogFacade
 * @package App\Model\Facade
 * @author Jakub Hadamčík <jakub@hadamcik.cz>
 */
class BlogFacade extends Object
{
    /** @var EntityManager */
    private $entityManager;

    /** @var ArticleRepository  */
    private $articleRepository;

    /** @var CommentRepository */
    private $commentRepository;

    /**
     * HomepageFacade constructor.
     * @param EntityManager $entityManager
     */
    public function __construct(EntityManager $entityManager)
    {
        $this->entityManager = $entityManager;
        $this->articleRepository = $this->entityManager->getRepository(Article::class);
        $this->commentRepository = $this->entityManager->getRepository(Comment::class);
    }

    /**
     * @param Comment $comment
     */
    public function updateComment(Comment $comment)
    {
        $this->entityManager->persist($comment);
        $this->entityManager->flush($comment);
    }

    /**
     * @param Article $article
     * @return \Kdyby\Doctrine\ResultSet
     */
    public function getArticleComments(Article $article)
    {
        $query = (new CommentQuery())
            ->byArticle($article)
            ->isPublished()
            ->notRemoved()
            ->withAuthors();
        return $this->commentRepository->fetch($query);
    }

    /**
     * @return \Kdyby\Doctrine\ResultSet
     */
    public function findNewArticles()
    {
        $today = new DateTime();
        $query = (new ArticleQuery())
            ->publishedFrom($today->sub(new \DateInterval('P1M'))) // Find published max 1 month ago
            ->orderByPublished('DESC') // Ordered by published DESC
            ->withVisibleComments();
        return $this->articleRepository->fetch($query);
    }
}
