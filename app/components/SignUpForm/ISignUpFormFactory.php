<?php

namespace App\Components\SignUpForm;

/**
 * Interface ISignUpFormFactory
 * @package App\Components\SignUpForm
 */
interface ISignUpFormFactory
{
    /**
     * @return SignUpForm
     */
    public function create();
}
