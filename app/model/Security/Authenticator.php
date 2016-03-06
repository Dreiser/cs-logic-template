<?php

namespace App\Model\Security;

use App\Model\Facade\AuthenticationFacade;
use Nette\Security\AuthenticationException;
use Nette\Security\IAuthenticator;
use Nette\Security\IIdentity;

/**
 * Class Authenticator
 * @package App\Model\Security
 * @author Jakub Hadamčík <jakub@hadamcik.cz>
 */
class Authenticator implements IAuthenticator
{
    /** @var AuthenticationFacade */
    private $authenticationFacade;

    /**
     * Authenticator constructor.
     * @param AuthenticationFacade $authenticationFacade
     */
    public function __construct(AuthenticationFacade $authenticationFacade)
    {
        $this->authenticationFacade = $authenticationFacade;
    }

    /**
     * @param array $credentials
     * @return IIdentity
     * @throws AuthenticationException
     */
    public function authenticate(array $credentials)
    {
        list($email, $password) = $credentials;
        return $this->authenticationFacade->authenticate($email, $password);
    }
}
