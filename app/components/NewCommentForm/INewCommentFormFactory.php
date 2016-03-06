<?php

namespace App\Components\NewCommentForm;

use Ap\Components\NewCommentForm\NewCommentForm;
use App\Model\Entity\Article;
use App\Model\Entity\User;

/**
 * Interface INewCommentFormFactory
 * @package App\Components\NewCommentForm
 * @author Jakub Hadamčík <jakub@hadamcik.cz>
 */
interface INewCommentFormFactory
{
    /**
     * @param Article $article
     * @param User $author
     * @return NewCommentForm
     */
    public function create(Article $article, User $author);
}
