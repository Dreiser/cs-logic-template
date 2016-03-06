<?php

namespace App\Components\ArticlePreview;

/**
 * Interface IArticlePreviewFactory
 * @package App\Components\ArticlePreview
 */
interface IArticlePreviewFactory
{
    /**
     * @return ArticlePreview
     */
    public function create();
}
