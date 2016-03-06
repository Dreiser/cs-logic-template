<?php

namespace App\Components\SignUpForm;

use App\Model\Facade\AuthenticationFacade;
use Nette\Application\UI\Control;
use Nette\Application\UI\Form;
use Ulozenka\Components\FileTemplateTrait;

/**
 * Class SignUpForm
 * @package App\Components\SignUpForm
 * @author Jakub Hadamčík <jakub@hadamcik.cz>
 */
class SignUpForm extends Control
{
    use FileTemplateTrait;

    /** @var array */
    public $onResultFailed = [];

    /** @var array */
    public $onResultSuccess = [];

    /** @var AuthenticationFacade */
    private $authenticationFacade;

    /**
     * SignUpForm constructor.
     * @param AuthenticationFacade $authenticationFacade
     */
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
        if($this->authenticationFacade->emailExists($values->email)) {
            $form->addError('E-mail už je použit u jiného účtu.');
        }
    }

    /**
     * @param Form $form
     */
    public function processForm(Form $form)
    {
        $values = $form->getValues();
        try {
            $this->authenticationFacade->createUser($values->email, $values->password);
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

        $form->addText('email', 'E-mail:')
            ->addRule(Form::EMAIL, 'Zadejte prosím platnou e-mailovou adresu')
            ->setRequired('Vyplňte prosím e-mail.');

        $form->addPassword('password', 'Heslo')
            ->setRequired('Vyplňte prosím heslo.');

        $form->addPassword('password2', 'Heslo znovu')
            ->addRule(Form::EQUAL, 'Hesla se neshodují', $form['password'])
            ->setRequired();

        $form->addSubmit('signUp', 'Zaregistrovat se');

        $form->onValidate[] = $this->validateEmail;
        $form->onSuccess[] = $this->processForm;

        return $form;
    }
}
