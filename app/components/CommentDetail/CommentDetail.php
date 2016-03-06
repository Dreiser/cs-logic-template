<?php

namespace App\Components\CommentDetail;

use App\Model\Entity\Comment;
use App\Model\Entity\User;
use App\Model\Facade\RedactorFacade;
use Kdyby\Doctrine\EntityManager;
use Nette\Application\UI\Control;
use Ulozenka\Components\FileTemplateTrait;

/**
 * Class CommentDetail
 * @package App\Components\CommentDetail
 * @author Jakub Hadamčík <jakub@hadamcik.cz>
 */
class CommentDetail extends Control
{
    use FileTemplateTrait;

    /** @var callable */
    public $onPublishFailed;

    /** @var callable */
    public $onPublishSuccess;

    /** @var callable */
    public $onRemoveFailed;

    /** @var callable */
    public $onRemoveSuccess;

    /** @var EntityManager */
    private $entityManager;

    /** @var RedactorFacade */
    private $redactorFacade;

    /** @var bool */
    private $publish = false;

    /** @var bool */
    private $remove = false;

    public function __construct(EntityManager $entityManager, RedactorFacade $redactorFacade)
    {
        parent::__construct();
        $this->entityManager = $entityManager;
        $this->redactorFacade = $redactorFacade;
    }

    /**
     * @param Comment $comment
     */
    public function render(Comment $comment)
    {
        $this->template->comment = $comment;
        $this->template->canPublish = $this->publish;
        $this->template->canRemove = $this->remove;
        $this->template->render();
    }

    /**
     * @param bool $canPublish
     * @return CommentDetail
     */
    public function canPublish($canPublish = false)
    {
        $this->publish = $canPublish;
        return $this;
    }

    /**
     * @param bool $canRemove
     * @return CommentDetail
     */
    public function canRemove($canRemove = false)
    {
        $this->remove = $canRemove;
        return $this;
    }

    /**
     * @param int $commentId
     */
    public function handlePublish($commentId)
    {
        try {
            $commentRepository = $this->entityManager->getRepository(Comment::class);
            $comment = $commentRepository->find($commentId);
            if($comment === null) {
                throw new \LogicException('Článek nenalezen');
            }
            $this->redactorFacade->publishComment($comment);
        } catch (\Exception $ex) {
            $this->onPublishFailed($ex);
            return;
        }
        $this->onPublishSuccess();
    }

    public function handleRemove($commentId)
    {
        try {
            $commentRepository = $this->entityManager->getRepository(Comment::class);
            $comment = $commentRepository->find($commentId);
            if($comment === null) {
                throw new \LogicException('Článek nenalezen');
            }
            $userRepository = $this->entityManager->getRepository(User::class);
            $user = $userRepository->find($this->getPresenter()->getUser()->getId());
            $this->redactorFacade->removeComment($comment, $user);
        } catch (\Exception $ex) {
            $this->onRemoveFailed($ex);
            return;
        }
        $this->onRemoveSuccess();
    }

    /**
     * @param $presenter
     * @throws \LogicException
     */
    protected function attached($presenter)
    {
        parent::attached($presenter);
        if($this->publish) {
            if(!is_callable($this->onPublishFailed)) {
                throw new \LogicException('onPublishFailed has to be callable.');
            }
            if(!is_callable($this->onPublishSuccess)) {
                throw new \LogicException('onPublishSuccess has to be callable.');
            }
        }
        if($this->remove) {
            if(!is_callable($this->onRemoveFailed)) {
                throw new \LogicException('onRemoveFailed has to be callable.');
            }
            if(!is_callable($this->onRemoveSuccess)) {
                throw new \LogicException('onRemoveSuccess has to be callable.');
            }
        }
    }
}
