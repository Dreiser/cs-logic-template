<?php

namespace App\Components\EditArticleForm;
use App\Model\Entity\Article;

/**
 * Interface IEditArticleFormFactory
 * @package App\Components\EditArticleForm
 */
interface IEditArticleFormFactory
{
    /**
     * @param Article $article
     * @return EditArticleForm
     */
    public function create(Article $article);
}
