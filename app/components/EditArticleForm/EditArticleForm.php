<?php

namespace App\Components\EditArticleForm;

use App\Components\ArticleForm;
use App\Model\Entity\Article;
use App\Model\Facade\RedactorFacade;
use Nette\Application\UI\Form;
use Ulozenka\Components\FileTemplateTrait;

/**
 * Class EditArticleForm
 * @package App\Components\EditArticleForm
 * @author Jakub Hadamčík <jakub@hadamcik.cz>
 */
class EditArticleForm extends ArticleForm
{
    use FileTemplateTrait;

    /** @var array */
    public $onResultFailed = [];

    /** @var array */
    public $onResultSuccess = [];

    /** @var Article */
    private $article;

    /** @var RedactorFacade */
    private $redactorFacade;

    /**
     * EditArticleForm constructor.
     * @param Article $article
     * @param RedactorFacade $redactorFacade
     */
    public function __construct(Article $article, RedactorFacade $redactorFacade)
    {
        parent::__construct();
        $this->article = $article;
        $this->redactorFacade = $redactorFacade;
    }

    /**
     * @param Form $form
     */
    public function processArticleForm(Form $form)
    {
        $values = $form->getValues();
        try {
            $this->article->setTitle($values->title)
                ->setText($values->text);
            $this->redactorFacade->updateArticle($this->article);
        }
        catch(\Exception $ex) {
            $form->addError('Došlo k neočekávané chybě.');
            $this->onResultFailed($ex);
            return;
        }
        $this->onResultSuccess($this->article);
    }

    /**
     * @return Form
     */
    protected function createComponentArticleForm()
    {
        $form = parent::createComponentArticleForm();
        $form->setDefaults($this->getArticleDefault());
        return $form;
    }

    /**
     * @return array
     */
    private function getArticleDefault()
    {
        $default = [
            'title' => $this->article->getTitle(),
            'text' => $this->article->getText()
        ];
        return $default;
    }
}
