<?php

namespace App\Model\Query;
use Kdyby\Doctrine\QueryBuilder;

/**
 * Class FilterQueryBuilderTrait
 * @package App\Model\Query
 * @author Jakub Hadamčík <jakub@hadamcik.cz>
 */
trait FilterQueryBuilderTrait
{
    /** @var array */
    private $filter = [];

    /**
     * @param QueryBuilder $qb
     */
    public function applyFilterTrait(QueryBuilder $qb) {
        foreach($this->filter as $filter) {
            $filter($qb);
        }
    }
}
