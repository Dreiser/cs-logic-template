<?php

namespace App\Components;

use Nette\Application\UI\Control;
use Nette\Application\UI\Form;

/**
 * Class ArticleForm
 * @package App\Components
 * @author Jakub Hadamčík <jakub@hadamcik.cz>
 */
abstract class ArticleForm extends Control
{
    /**
     * @return Form
     */
    protected function createComponentArticleForm()
    {
        $form = new Form();

        $form->addText('title', 'Nadpis:')
            ->setRequired('Zadejte prosím nadpis článku.');

        $form->addTextArea('text', 'Text:')
            ->setRequired('Zadejte prosím text článku.');

        $form->addSubmit('save', 'Uložit');

        $form->onSuccess[] = $this->processArticleForm;

        return $form;
    }

    /**
     * @param Form $form
     * @return void
     */
    public abstract function processArticleForm(Form $form);
}
