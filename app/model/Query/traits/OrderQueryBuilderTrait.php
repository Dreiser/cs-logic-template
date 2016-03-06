<?php

namespace App\Model\Query;
use Kdyby\Doctrine\QueryBuilder;

/**
 * Class OrderQueryBuilderTrait
 * @package App\Model\Query
 * @author Jakub Hadamčík <jakub@hadamcik.cz>
 */
trait OrderQueryBuilderTrait
{
    /** @var array */
    private $order = [];

    /**
     * @param QueryBuilder $qb
     */
    public function applyOrderTrait(QueryBuilder $qb) {
        foreach($this->order as $order) {
            $order($qb);
        }
    }
}
