<?php

namespace App\Presenters;

use Ap\Components\NewCommentForm\NewCommentForm;
use App\Components\CommentDetail\CommentDetail;
use App\Components\CommentDetail\ICommentDetailFactory;
use App\Components\NewCommentForm\INewCommentFormFactory;
use App\Model\Entity\Article;
use App\Model\Entity\User;
use App\Model\Enum\DefaultRoutes;
use App\Model\Enum\FlashMessageType;
use App\Model\Facade\BlogFacade;
use App\Model\Facade\RedactorFacade;
use Kdyby\Doctrine\EntityManager;
use Nette\Application\UI\Presenter;
use Tracy\Debugger;

/**
 * Class ArticlePresenter
 * @package App\Presenters
 * @author Jakub Hadamčík <jakub@hadamcik.cz>
 */
class ArticlePresenter extends Presenter
{
    use VerifyLoggedIn;

    /** @var EntityManager @inject */
    public $entityManager;

    /** @var RedactorFacade @inject */
    public $redactorFacade;

    /** @var BlogFacade @inject */
    public $blogFacade;

    /** @var ICommentDetailFactory @inject */
    public $commentDetailFactory;

    /** @var INewCommentFormFactory @inject */
    public $newCommentFormFactory;

    /** @var Article */
    private $article;

    /**
     * @param int $articleId
     */
    public function actionDetail($articleId)
    {
        $articleRepository = $this->entityManager->getRepository(Article::class);
        $this->article = $articleRepository->find($articleId);
        if($this->article === null) {
            $this->flashMessage('Článek nenalezen.', FlashMessageType::INFO);
            $this->redirect(DefaultRoutes::FRONTEND_DEFAULT);
        }
        if(!$this->article->isPublished()) {
            if($this->getUser()->isLoggedIn()) {
                $this->flashMessage('Článek ještě nebyl publikován. Publikovat jej můžete na této stránce.', FlashMessageType::INFO);
                $this->redirect('Blog:approve', [
                    'articleId' => $articleId
                ]);
            }
            else {
                $this->flashMessage('Článek ještě nebyl publikován', FlashMessageType::INFO);
                $this->redirect(DefaultRoutes::FRONTEND_DEFAULT);
            }
        }
    }

    /**
     * @param int $articleId
     */
    public function renderDetail($articleId)
    {
        $this->template->article = $this->article;
        $this->template->comments = $this->blogFacade->getArticleComments($this->article);
    }

    /**
     * @param int $articleId
     */
    public function handleUnpublish($articleId)
    {
        $this->verifyLoggedIn();
        $articleRepository = $this->entityManager->getRepository(Article::class);
        $article = $articleRepository->find($articleId);
        if($article === null) {
            $this->flashMessage('Článek nenalezen.', FlashMessageType::ERROR);
        }
        if(!$article->isPublished()) {
            $this->flashMessage('Článek není publikován.', FlashMessageType::INFO);
            $this->redirect('Blog:approve', [
                'articleId' => $article->getId()
            ]);
        }
        try {
            $this->redactorFacade->unpublishArticle($article);
            $this->flashMessage('Publikace článku byla zrušena.', FlashMessageType::SUCCESS);
        } catch(\Exception $ex) {
            Debugger::log($ex, 'article_unpublish');
            $this->flashMessage('Došlo k neočekávané chybě.', FlashMessageType::ERROR);
            $this->redirect('this');
        }
        $this->redirect('Blog:approve', [
            'articleId' => $article->getId()
        ]);
    }

    /**
     * @return CommentDetail
     */
    protected function createComponentCommentDetail()
    {
        $commentDetail = $this->commentDetailFactory->create();
        $commentDetail->canRemove($this->getUser()->isLoggedIn());
        $commentDetail->onRemoveFailed = function (\Exception $ex) {
            Debugger::log($ex, 'article_comment_remove');
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
     * @return NewCommentForm
     */
    protected function createComponentNewCommentForm()
    {
        $userRepository = $this->entityManager->getRepository(User::class);
        $user = $userRepository->find($this->getUser()->getId());
        $newCommentForm = $this->newCommentFormFactory->create($this->article, $user);
        $newCommentForm->onResultFailed[] = function (\Exception $ex) {
            Debugger::log($ex, 'article_new_comment');
        };
        $newCommentForm->onResultSuccess[] = function () {
            $this->flashMessage('Komentář byl čekán a čeká na schválení.', FlashMessageType::SUCCESS);
            $this->redirect('Article:detail', [
                'articleId' => $this->article->getId()
            ]);
        };
        return $newCommentForm;
    }
}
