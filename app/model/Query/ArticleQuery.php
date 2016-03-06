<?php

namespace App\Model\Query;

use App\Model\Entity\Article;
use Kdyby\Doctrine\QueryBuilder;
use Kdyby\Doctrine\QueryObject;
use Kdyby\Persistence\Queryable;

/**
 * Class ArticleQuery
 * @package App\Model\Query
 * @author Jakub Hadamčík <jakub@hadamcik.cz>
 */
class ArticleQuery extends QueryObject
{
    use FilterQueryBuilderTrait;
    use OrderQueryBuilderTrait;
    use SelectQueryBuilderTrait;

    /**
     * @param Queryable $repository
     * @return QueryBuilder
     */
    protected function doCreateQuery(Queryable $repository)
    {
        $qb = $repository->createQueryBuilder();
        $qb->addSelect('a');
        $this->applySelectTrait($qb);
        $qb->from(Article::class, 'a');
        $this->applyFilterTrait($qb);
        $this->applyOrderTrait($qb);
        return $qb;
    }

    /**
     * @return ArticleQuery
     */
    public function isNotPublished()
    {
        $this->filter[] = function (QueryBuilder $qb) {
            $qb->andWhere('a.published IS NULL');
        };
        return $this;
    }

    /**
     * @param \DateTime $publishedFrom
     * @return ArticleQuery
     */
    public function publishedFrom(\DateTime $publishedFrom) {
        $this->filter[] = function (QueryBuilder $qb) use ($publishedFrom) {
            $qb->andWhere('a.published >= :publishedFrom')
                ->setParameter('publishedFrom', $publishedFrom->format('Y-m-d 00:00:00'));
        };
        return $this;
    }

    /**
     * @param string $order
     * @return ArticleQuery
     */
    public function orderByPublished($order = 'ASC')
    {
        $this->order[] = function (QueryBuilder $qb) use ($order) {
            $qb->addOrderBy('a.published', $order);
        };
        return $this;
    }

    /**
     * @param string $order
     * @return ArticleQuery
     */
    public function orderByAdded($order = 'ASC')
    {
        $this->order[] = function (QueryBuilder $qb) use ($order) {
            $qb->addOrderBy('a.added', $order);
        };
        return $this;
    }

    /**
     * @return ArticleQuery
     */
    public function withVisibleComments()
    {
        $this->onPostFetch[] = function ($_, Queryable $repository, \Iterator $iterator) {
            $ids = array_keys(iterator_to_array($iterator, true));
            $repository->createQueryBuilder()
                ->select('partial article.{id}', 'comments')
                ->from(Article::class, 'article')
                ->leftJoin('article.comments', 'comments')
                ->andWhere('article.id IN (:ids)')->setParameter('ids', $ids)
                ->andWhere('comments.removed IS NULL')
                ->andWhere('comments.published IS NOT NULL')
                ->getQuery()->getResult();
        };
        return $this;
    }
}
