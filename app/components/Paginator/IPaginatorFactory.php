<?php

namespace App\Components\Paginator;

/**
 * Interface IPaginatorFactory
 * @package App\Components\Paginator
 */
interface IPaginatorFactory
{
    /**
     * @return Paginator
     */
    public function create();
}
