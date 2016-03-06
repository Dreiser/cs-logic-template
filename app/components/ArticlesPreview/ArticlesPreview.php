<?php

namespace App\Components\ArticlesPreview;

use App\Components\ArticlePreview\ArticlePreview;
use App\Components\ArticlePreview\IArticlePreviewFactory;
use App\Components\Paginator\IPaginatorFactory;
use App\Model\Entity\Article;
use Kdyby\Doctrine\ResultSet;
use Nette\Application\UI\Control;
use Nette\Utils\Paginator;
use Ulozenka\Components\FileTemplateTrait;

/**
 * Class ArticlesPreview
 * @package App\Components\ArticlesPreview
 * @author Jakub Hadamčík <jakub@hadamcik.cz>
 */
class ArticlesPreview extends Control
{
    use FileTemplateTrait;

    /** @var ResultSet */
    private $articles;

    /** @var callable */
    public $getDetailLink;

    /** @var IArticlePreviewFactory */
    private $articlePreviewFactory;

    /** @var Paginator */
    private $paginator;

    /** @var IPaginatorFactory */
    private $paginatorFactory;

    /**
     * NewArticles constructor.
     * @param ResultSet $articles
     * @param int $articlesPerPage
     * @param IArticlePreviewFactory $articlePreviewFactory
     * @param IPaginatorFactory $paginatorFactory
     */
    public function __construct(ResultSet $articles, $articlesPerPage, IArticlePreviewFactory $articlePreviewFactory, IPaginatorFactory $paginatorFactory)
    {
        parent::__construct();
        $this->articles = $articles;
        $this->articlePreviewFactory = $articlePreviewFactory;
        $this->paginator = new Paginator();
        $this->paginator->setItemsPerPage($articlesPerPage);
        $this->paginatorFactory = $paginatorFactory;
    }

    /**
     * @param int $page
     * @return string
     */
    public function getPageLink($page)
    {
        return $this->link('changePage!', [
            'page' => $page
        ]);
    }

    /**
     * @param int $page
     */
    public function handleChangePage($page = 1)
    {
        $this->paginator->setPage($page);
        $this->template->articles = $this->articles->applyPaginator($this->paginator)->toArray();
        $this->redrawControl('articles');
    }

    /**
     * @return void
     */
    public function render()
    {
        if(!isset($this->template->articles)) {
            $this->paginator->setPage(1);
            $this->template->articles = $this->articles->applyPaginator($this->paginator)->toArray();
        }
        $this->template->paginator = $this->paginator;
        $this->template->getPageLink = $this->getPageLink;
        $hasLink = !empty($this->getDetailLink);
        $this->template->hasLink = $hasLink;
        if($hasLink) {
            $this->template->addFilter('getLink', function (Article $article) {
                return $this->getDetailLink($article);
            });
        }
        $this->template->render();
    }

    /**
     * @param $presenter
     * @throws \LogicException
     */
    protected function attached($presenter)
    {
        parent::attached($presenter);
        if(empty($this->getDetailLink)) {
            throw new \LogicException('getDetailLink callback has to be set up.');
        }
    }


    /**
     * @return ArticlePreview
     */
    protected function createComponentArticlePreview()
    {
        $articlePreview = $this->articlePreviewFactory->create();
        return $articlePreview;
    }

    /**
     * @return \App\Components\Paginator\Paginator
     */
    protected function createComponentPaginator()
    {
        $paginator = $this->paginatorFactory->create();
        return $paginator;
    }
}
