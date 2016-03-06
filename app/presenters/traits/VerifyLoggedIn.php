<?php

namespace App\Presenters;

/**
 * Class VerifyLoggedIn
 * @package App\Presenters
 * @author Jakub Hadamčík <jakub@hadamcik.cz>
 */
trait VerifyLoggedIn
{
    /**
     * Verify if user is logged in and if not, redirects
     */
    private function verifyLoggedIn()
    {
        if(!$this->getUser()->isLoggedIn()) {
            $this->flashMessage('Nejprve se musíte přihlásit.', FlashMessageType::INFO);
            $this->redirect('Sign:in');
        }
    }
}
