<?php

namespace App\Model\Query;

use App\Model\Entity\Article;
use App\Model\Entity\Comment;
use App\Model\Entity\User;
use Doctrine\ORM\QueryBuilder;
use Kdyby\Doctrine\QueryObject;
use Kdyby\Persistence\Queryable;

class CommentQuery extends QueryObject
{
    use FilterQueryBuilderTrait;
    use SelectQueryBuilderTrait;
    use OrderQueryBuilderTrait;

    /**
     * @param Queryable $repository
     * @return \Kdyby\Doctrine\QueryBuilder
     */
    protected function doCreateQuery(Queryable $repository)
    {
        $qb = $repository->createQueryBuilder();
        $qb->addSelect('c');
        $this->applySelectTrait($qb);
        $qb->from(Comment::class, 'c');
        $this->applyFilterTrait($qb);
        $this->applyOrderTrait($qb);
        return $qb;
    }

    /**
     * @param Article $article
     * @return CommentQuery
     */
    public function byArticle(Article $article)
    {
        $this->filter[] = function (QueryBuilder $qb) use ($article) {
            $qb->andWhere('c.article = :articleId')
                ->setParameter('articleId', $article->getId());
        };
        return $this;
    }

    /**
     * @return CommentQuery
     */
    public function isNotPublished()
    {
        $this->filter[] = function (QueryBuilder $qb) {
            $qb->andWhere('c.published IS NULL');
        };
        return $this;
    }

    /**
     * @return CommentQuery
     */
    public function isPublished()
    {
        $this->filter[] = function (QueryBuilder $qb) {
            $qb->andWhere('c.published IS NOT NULL');
        };
        return $this;
    }

    /**
     * @return CommentQuery
     */
    public function notRemoved()
    {
        $this->filter[] = function (QueryBuilder $qb) {
            $qb->andWhere('c.removed IS NULL');
        };
        return $this;
    }

    /**
     * @param string $order
     * @return CommentQuery
     */
    public function orderByAdded($order = 'ASC')
    {
        $this->order[] = function (QueryBuilder $qb) use ($order) {
            $qb->addOrderBy('c.added', $order);
        };
        return $this;
    }

    /**
     * @return CommentQuery
     */
    public function withAuthors()
    {
       $this->onPostFetch[] = function ($_, Queryable $repository, \Iterator $iterator) {
            $ids = array_keys(iterator_to_array($iterator, true));
           $repository->createQueryBuilder()
               ->select('partial author.{id}', 'comments')
               ->from(User::class, 'author')
               ->leftJoin('author.comments', 'comments')
               ->andWhere('author.id IN (:ids)')->setParameter('ids', $ids)
               ->getQuery()->getResult();
       };
        return $this;
    }
}
