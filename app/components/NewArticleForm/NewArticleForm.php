<?php

namespace App\Components\NewArticleForm;

use App\Components\ArticleForm;
use App\Components\OnResultFailedTrait;
use App\Components\OnResultSuccessTrait;
use App\Model\Entity\Article;
use App\Model\Entity\User;
use App\Model\Facade\RedactorFacade;
use Nette\Application\UI\Form;
use Ulozenka\Components\FileTemplateTrait;

/**
 * Class NewArticleForm
 * @package App\Components\NewArticleForm
 * @author Jakub Hadamčík <jakub@hadamcik.cz>
 */
class NewArticleForm extends ArticleForm
{
    use FileTemplateTrait;
    use OnResultFailedTrait;
    use OnResultSuccessTrait;

    /** @var User */
    private $author;

    /** @var RedactorFacade */
    private $redactorFacade;

    /**
     * NewArticleForm constructor.
     * @param User $author
     * @param RedactorFacade $redactorFacade
     */
    public function __construct(User $author, RedactorFacade $redactorFacade)
    {
        parent::__construct();
        $this->author = $author;
        $this->redactorFacade = $redactorFacade;
    }

    /**
     * @param Form $form
     */
    public function processArticleForm(Form $form)
    {
        $values = $form->getValues();
        try {
            $article = new Article($this->author);
            $article->setTitle($values->title);
            $article->setText($values->text);
            $this->redactorFacade->updateArticle($article);
        } catch(\Exception $ex) {
            $form->addError('Při zpracování formuláře došlo k chybě.');
            $this->callOnResultFailed($ex);
            return;
        }
        $this->callOnResultSuccess();
    }
}
