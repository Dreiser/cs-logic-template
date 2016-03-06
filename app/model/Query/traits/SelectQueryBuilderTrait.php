<?php

namespace App\Model\Query;
use Kdyby\Doctrine\QueryBuilder;

/**
 * Class SelectQueryBuilderTrait
 * @package App\Model\Query
 * @author Jakub Hadamčík <jakub@hadamcik.cz>
 */
trait SelectQueryBuilderTrait
{
    /** @var array */
    private $select = [];

    /**
     * @param QueryBuilder $qb
     */
    public function applySelectTrait(QueryBuilder $qb) {
        foreach($this->select as $select) {
            $select($qb);
        }
    }
}
