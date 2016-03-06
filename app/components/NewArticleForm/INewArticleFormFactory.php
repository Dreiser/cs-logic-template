<?php

namespace App\Components\NewArticleForm;
use App\Model\Entity\User;

/**
 * Interface INewArticleFormFactory
 * @package App\Components\NewArticleForm
 */
interface INewArticleFormFactory
{
    /**
     * @param User $author
     * @return NewArticleForm
     */
    public function create(User $author);
}
