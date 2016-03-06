<?php

namespace App\Presenters;

use App\Components\ArticlesPreview\ArticlesPreview;
use App\Components\ArticlesPreview\IArticlesPreviewFactory;
use App\Model\Entity\Article;
use App\Model\Facade\BlogFacade;
use Nette;

/**
 * Class HomepagePresenter
 * @package App\Presenters
 * @author Jakub Hadamčík <jakub@hadamcik.cz>
 */
class HomepagePresenter extends Nette\Application\UI\Presenter
{
    /** @var BlogFacade @inject */
    public $blogFacade;

    /** @var IArticlesPreviewFactory @inject */
    public $articlesPreviewFactory;

    /**
     * @return ArticlesPreview
     */
    protected function createComponentNewArticles()
    {
        $newArticles = $this->articlesPreviewFactory->create(
            $this->blogFacade->findNewArticles(),
            5
        );
        $newArticles->getDetailLink = function (Article $article) {
            return $this->getPresenter()->link('Article:detail', [
                'articleId' => $article->getId()
            ]);
        };
        return $newArticles;
    }
}
