<?php

namespace App\Presenters;

use App\Components\ArticlePreview\IArticlePreviewFactory;
use App\Components\ArticlesPreview\ArticlesPreview;
use App\Components\ArticlesPreview\IArticlesPreviewFactory;
use App\Components\CommentDetail\CommentDetail;
use App\Components\CommentDetail\ICommentDetailFactory;
use App\Components\EditArticleForm\EditArticleForm;
use App\Components\EditArticleForm\IEditArticleFormFactory;
use App\Components\NewArticleForm\INewArticleFormFactory;
use App\Components\NewArticleForm\NewArticleForm;
use App\Model\Entity\Article;
use App\Model\Entity\User;
use App\Model\Enum\DefaultRoutes;
use App\Model\Enum\FlashMessageType;
use App\Model\Facade\RedactorFacade;
use Kdyby\Doctrine\EntityManager;
use Nette\Application\UI\Presenter;
use Tracy\Debugger;

/**
 * Class BlogPresenter
 * @package App\Presenters
 * @author Jakub Hadamčík <jakub@hadamcik.cz>
 */
class BlogPresenter extends Presenter
{
    use VerifyLoggedIn;

    /** @var EntityManager @inject */
    public $entityManager;

    /** @var RedactorFacade @inject */
    public $redactorFacade;

    /** @var IArticlePreviewFactory @inject */
    public $articlePreviewFactory;

    /** @var IArticlesPreviewFactory @inject */
    public $articlesPreviewFactory;

    /** @var IEditArticleFormFactory @inject */
    public $editArticleFormFactory;

    /** @var ICommentDetailFactory @inject */
    public $commentDetailFactory;

    /** @var INewArticleFormFactory @inject */
    public $newArticleFormFactory;

    /** @var Article */
    private $article;

    /**
     * @param int $articleId
     */
    public function actionApprove($articleId)
    {
        $this->verifyLoggedIn();
        $articleRepository = $this->entityManager->getRepository(Article::class);
        $this->article = $articleRepository->find($articleId);
        if($this->article === null) {
            $this->flashMessage('Článek nenalezen.', FlashMessageType::ERROR);
            $this->redirect(DefaultRoutes::FRONTEND_DEFAULT);
        }
        if($this->article->isPublished()) {
            $this->flashMessage('Článek už byl publikován', FlashMessageType::INFO);
            $this->redirect('Article:detail', [
                'articleId' => $articleId
            ]);
        }
    }

    /**
     * @param int $articleId
     */
    public function renderApprove($articleId)
    {
        $this->template->article = $this->article;
    }

    /**
     * @param int $articleId
     */
    public function actionCommentsForApprove($articleId)
    {
        $this->verifyLoggedIn();
        $articleRepository = $this->entityManager->getRepository(Article::class);
        $this->article = $articleRepository->find($articleId);
        if($this->article === null) {
            $this->flashMessage('Článek neexistuje.', FlashMessageType::INFO);
            $this->redirect(DefaultRoutes::FRONTEND_DEFAULT);
        }
    }

    /**
     * @param int $articleId
     */
    public function renderCommentsForApprove($articleId)
    {
        $this->template->comments = $this->redactorFacade->findCommentsForApprove($this->article);
    }

    /**
     * @return void
     */
    public function actionCreate()
    {
        $this->verifyLoggedIn();
    }

    /**
     * @return void
     */
    public function actionForApprove()
    {
        $this->verifyLoggedIn();
    }

    /**
     * @param int $articleId
     */
    public function handlePublish($articleId)
    {
        $this->verifyLoggedIn();
        $articleRepository = $this->entityManager->getRepository(Article::class);
        $article = $articleRepository->find($articleId);
        if($article === null) {
            $this->flashMessage('Článek nenalazen.', FlashMessageType::ERROR);
            $this->redirect('Blog:forApprove');
        }
        if($article->isPublished()) {
            $this->flashMessage('Článek už by publikován ' . $article->getPublished()->format('j.n.Y'), FlashMessageType::INFO);
            $this->redirect('Article:detail', [
                'articleId' => $article->getId()
            ]);
        }
        try {
            $this->redactorFacade->publishArticle($article);
            $this->flashMessage('Článek byl publikován.', FlashMessageType::SUCCESS);
        } catch(\Exception $ex) {
            Debugger::log($ex, 'blog_publish_article');
            $this->flashMessage('Došlo k neočekávané chybě.', FlashMessageType::ERROR);
            $this->redirect('this');
        }
        $this->redirect('Article:detail', [
            'articleId' => $article->getId()
        ]);
    }

    /**
     * @return \App\Components\ArticlePreview\ArticlePreview
     */
    protected function createComponentArticlePreview()
    {
        $articlePreview = $this->articlePreviewFactory->create();
        return $articlePreview;
    }

    /**
     * @return ArticlesPreview
     */
    protected function createComponentArticlesForApprove()
    {
        $articlesForApprove = $this->articlesPreviewFactory->create(
            $this->redactorFacade->findForApprove(),
            10
        );
        $articlesForApprove->getDetailLink = function (Article $article) {
            return $this->getPresenter()->link('Blog:approve', [
                'articleId' => $article->getId()
            ]);
        };
        return $articlesForApprove;
    }

    /**
     * @return CommentDetail
     */
    protected function createComponentCommentDetail()
    {
        $commentDetail = $this->commentDetailFactory->create();
        $commentDetail->canPublish($this->getUser()->isLoggedIn());
        $commentDetail->onPublishFailed = function (\Exception $ex) {
            Debugger::log($ex, 'blog_comment_publish');
            $this->flashMessage('Došlo k neočekávané chybě.', FlashMessageType::ERROR);
            $this->redirect('this');
        };
        $commentDetail->onPublishSuccess = function () {
            $this->flashMessage('Komentář byl publikován.', FlashMessageType::SUCCESS);
            $this->redirect('this');
        };
        $commentDetail->canRemove($this->getUser()->isLoggedIn());
        $commentDetail->onRemoveFailed = function (\Exception $ex) {
            Debugger::log($ex, 'blog_comment_remove');
            $this->flashMessage('Došlo k neočekávané chybě.', FlashMessageType::ERROR);
            $this->redirect('this');
        };
        $commentDetail->onRemoveSuccess = function () {
            $this->flashMessage('Komentář byl odstraněn.', FlashMessageType::SUCCESS);
            $this->redirect('this');
        };
        return $commentDetail;
    }

    /**
     * @return EditArticleForm
     */
    protected function createComponentEditArticleForm()
    {
        $editArticleForm = $this->editArticleFormFactory->create($this->article);
        $editArticleForm->onResultFailed[] = function (\Exception $ex) {
            Debugger::log($ex, 'blog_edi_article');
        };
        $editArticleForm->onResultSuccess[] = function (Article $article) {
            $this->flashMessage('Článek byl upraven', FlashMessageType::SUCCESS);
            $this->redirect('Blog:approve', [
                'articleId' => $article->getId()
            ]);
        };
        return $editArticleForm;
    }

    /**
     * @return NewArticleForm
     */
    protected function createComponentNewArticleForm()
    {
        $userRepository = $this->entityManager->getRepository(User::class);
        $user = $userRepository->find($this->getUser()->getId());
        $newArticle = $this->newArticleFormFactory->create($user);
        $newArticle->onResultFailed[] = function (\Exception $ex) {
            Debugger::log($ex, 'blog_new_article');
        };
        $newArticle->onResultSuccess[] = function () {
            $this->flashMessage('Článek byl přidán a čeká na schválení.', FlashMessageType::SUCCESS);
            $this->redirect(DefaultRoutes::FRONTEND_DEFAULT);
        };
        return $newArticle;
    }
}
