<?php

namespace App\Components;

use Nette\Application\UI\Control;
use Nette\Application\UI\Form;

/**
 * Class CommentForm
 * @package App\Components
 * @author Jakub Hadamčík <jakub@hadamcik.cz>
 */
abstract class CommentForm extends Control
{
    /**
     * @return Form
     */
    protected function createComponentCommentForm()
    {
        $form = new Form();

        $form->addTextArea('text', 'Text')
            ->setRequired('Zadejte text komentáře');

        $form->addSubmit('save', 'Uložit');

        $form->onSuccess[] = $this->processCommentForm;

        return $form;
    }

    /**
     * @param Form $form
     */
    public abstract function processCommentForm(Form $form);
}
