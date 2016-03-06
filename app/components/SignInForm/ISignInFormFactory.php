<?php

namespace App\Components\SignInForm;

/**
 * Interface ISignInFormFactory
 * @package App\Components\SignInForm
 */
interface ISignInFormFactory
{
    /**
     * @return SignInForm
     */
    public function create();
}
