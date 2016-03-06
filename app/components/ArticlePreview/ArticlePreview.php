<?php

namespace App\Components\ArticlePreview;

use App\Model\Entity\Article;
use Nette\Application\UI\Control;
use Ulozenka\Components\FileTemplateTrait;

/**
 * Class ArticlePreview
 * @package App\Components\ArticlePreview
 * @author Jakub Hadamčík <jakub@hadamcik.cz>
 */
class ArticlePreview extends Control
{
    use FileTemplateTrait;

    /**
     * @param Article $article
     */
    public function render(Article $article)
    {
        $this->template->article = $article;
        $this->template->render();
    }
}
