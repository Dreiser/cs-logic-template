<?php

namespace App\Presenters;

use App\Components\SignInForm\ISignInFormFactory;
use App\Components\SignInForm\SignInForm;
use App\Components\SignUpForm\ISignUpFormFactory;
use App\Components\SignUpForm\SignUpForm;
use App\Model\Enum\DefaultRoutes;
use App\Model\Enum\FlashMessageType;
use Nette\Application\UI\Presenter;
use Tracy\Debugger;

/**
 * Class SignPresenter
 * @package App\Presenters
 * @author Jakub Hadamčík <jakub@hadamcik.cz>
 */
class SignPresenter extends Presenter
{
    /** @var ISignUpFormFactory @inject */
    public $signUpFormFactory;

    /** @var ISignInFormFactory @inject */
    public $signInFormFactory;

    /**
     * @return void
     */
    public function actionUp()
    {
        if($this->getUser()->isLoggedIn()) {
            $this->flashMessage('Nejprve je nutno odhlásit přihlášený účet.', FlashMessageType::INFO);
            $this->redirect(DefaultRoutes::FRONTEND_DEFAULT);
        }
    }

    /**
     * @return void
     */
    public function actionIn()
    {
        if($this->getUser()->isLoggedIn()) {
            $this->flashMessage('Nejprve je nutno odhlásit přihlášený účet.', FlashMessageType::INFO);
            $this->redirect(DefaultRoutes::FRONTEND_DEFAULT);
        }
    }

    /**
     * @return SignUpForm
     */
    protected function createComponentSignUpForm()
    {
        $signUpForm = $this->signUpFormFactory->create();
        $signUpForm->onResultSuccess[] = function () {
            $this->flashMessage('Registrace proběhla úspěšně. Nyní se můžete přihlásit.', FlashMessageType::SUCCESS);
            $this->redirect('Sign:in');
        };
        $signUpForm->onResultFailed[] = function (\Exception $ex) {
            Debugger::log($ex, 'sign_up');
            $this->flashMessage('Došlo k neočekávané chybě.', FlashMessageType::ERROR);
            $this->redirect('this');
        };
        return $signUpForm;
    }

    /**
     * @return SignInForm
     */
    protected function createComponentSignInForm()
    {
        $signInForm = $this->signInFormFactory->create();
        $signInForm->onResultSuccess[] = function () {
            $this->flashMessage('Přihlášení proběhlo úspěšně.', FlashMessageType::SUCCESS);
            $this->redirect(DefaultRoutes::FRONTEND_DEFAULT);
        };
        $signInForm->onResultFailed[] = function ($ex) {
            Debugger::log($ex, 'sign_in');
            $this->flashMessage('Došlo k neočekávané chybě', FlashMessageType::ERROR);
            $this->redirect('this');
        };
        return $signInForm;
    }
}
