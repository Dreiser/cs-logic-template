<?php

namespace App\Components\SignInForm;

use App\Model\Facade\AuthenticationFacade;
use Nette\Application\UI\Control;
use Nette\Application\UI\Form;
use Ulozenka\Components\FileTemplateTrait;

/**
 * Class SignInForm
 * @package App\Components\SignInForm
 * @author Jakub Hadamčík <jakub@hadamcik.cz>
 */
class SignInForm extends Control
{
    use FileTemplateTrait;

    /** @var array */
    public $onResultFailed = [];

    /** @var array */
    public $onResultSuccess = [];

    /** @var AuthenticationFacade */
    private $authenticationFacade;

    public function __construct(AuthenticationFacade $authenticationFacade)
    {
        parent::__construct();
        $this->authenticationFacade = $authenticationFacade;
    }

    /**
     * @param Form $form
     */
    public function validateEmail(Form $form)
    {
        $values = $form->getValues();
        if(!$this->authenticationFacade->emailExists($values->email)) {
            $form->addError('Zadaný e-mail není registrován.');
        }
    }

    /**
     * @param Form $form
     */
    public function validateAccountPassword(Form $form)
    {
        $values = $form->getValues();
        if(!$this->authenticationFacade->accountPasswordMatch($values->email, $values->password)) {
            $form->addError('Zadané heslo není správné.');
        }
    }

    /**
     * @param Form $form
     */
    public function processForm(Form $form)
    {
        $values = $form->getValues();
        try {
            $this->getPresenter()->getUser()->login($values->email, $values->password);
        } catch(\Exception $ex) {
            $this->onResultFailed($ex);
            return;
        }
        $this->onResultSuccess();
    }

    /**
     * @return Form
     */
    protected function createComponentForm()
    {
        $form = new Form();

        $form->addText('email', 'E-mail')
            ->setRequired('Zadejte prosím e-mail')
            ->addRule(Form::EMAIL, 'Vámi zadaný e-mail nemá správný tvar.');

        $form->addPassword('password', 'Heslo:')
            ->setRequired('Zadejte prosím heslo');

        $form->addSubmit('signIn', 'Přihlásit se');

        $form->onValidate[] = $this->validateEmail;
        $form->onSuccess[] = $this->processForm;

        return $form;
    }
}
