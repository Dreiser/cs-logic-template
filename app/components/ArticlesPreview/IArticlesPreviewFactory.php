<?php

namespace App\Components\ArticlesPreview;
use Kdyby\Doctrine\ResultSet;

/**
 * Interface IArticlesPreviewFactory
 * @package App\Components\ArticlesPreview
 */
interface IArticlesPreviewFactory
{
    /**
     * @param ResultSet $articles
     * @param int $articlesPerPage
     * @return ArticlesPreview
     */
    public function create(ResultSet $articles, $articlesPerPage);
}
