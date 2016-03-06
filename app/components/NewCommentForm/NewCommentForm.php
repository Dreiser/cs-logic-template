<?php

namespace Ap\Components\NewCommentForm;

use App\Components\CommentForm;
use App\Components\OnResultFailedTrait;
use App\Components\OnResultSuccessTrait;
use App\Model\Entity\Article;
use App\Model\Entity\Comment;
use App\Model\Entity\User;
use App\Model\Facade\BlogFacade;
use Nette\Application\UI\Form;
use Ulozenka\Components\FileTemplateTrait;

/**
 * Class NewCommentForm
 * @package Ap\Components\NewCommentForm
 * @author Jakub Hadamčík <jakub@hadamcik.cz>
 */
class NewCommentForm extends CommentForm
{
    use FileTemplateTrait;
    use OnResultFailedTrait;
    use OnResultSuccessTrait;

    /** @var Article */
    private $article;

    /** @var User */
    private $author;

    /** @var BlogFacade */
    private $blogFacade;

    /**
     * NewCommentForm constructor.
     * @param Article $article
     * @param User $author
     * @param BlogFacade $blogFacade
     */
    public function __construct(Article $article, User $author, BlogFacade $blogFacade)
    {
        parent::__construct();
        $this->article = $article;
        $this->author = $author;
        $this->blogFacade = $blogFacade;
    }

    /**
     * @param Form $form
     */
    public function processCommentForm(Form $form)
    {
        $values = $form->getValues();
        try {
            $comment = new Comment($this->article, $this->author);
            $comment->setText($values->text);
            $this->blogFacade->addComment($comment);
        } catch (\Exception $ex) {
            $form->addError('Došlo k neočekávané chybě.');
            $this->callOnResultFailed($ex);
            return;
        }
        $this->callOnResultSuccess($comment);
    }
}
