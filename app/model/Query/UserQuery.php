<?php

namespace App\Model\Query;

use App\Model\Entity\User;
use Kdyby\Doctrine\QueryBuilder;
use Kdyby\Doctrine\QueryObject;
use Kdyby\Persistence\Queryable;

/**
 * Class UserQuery
 * @package App\Model\Query
 * @author Jakub Hadamčík <jakub@hadamcik.cz>
 */
class UserQuery extends QueryObject
{
    use FilterQueryBuilderTrait;

    /**
     * @param Queryable $repository
     * @return QueryBuilder
     */
    protected function doCreateQuery(Queryable $repository)
    {
        $qb = $repository->createQueryBuilder();
        $qb->select('u')
            ->from(User::class, 'u');
        $this->applyFilterTrait($qb);
        return $qb;
    }

    /**
     * @param string $email
     * @return UserQuery
     */
    public function byEmail($email)
    {
        $this->filter[] = function(QueryBuilder $qb) use ($email) {
            $qb->andWhere('u.email = :e')
                ->setParameter('e', $email);
        };
        return $this;
    }
}
