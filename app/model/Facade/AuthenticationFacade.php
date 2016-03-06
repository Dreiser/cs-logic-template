<?php

namespace App\Model\Facade;

use App\Model\Entity\User;
use App\Model\Query\UserQuery;
use App\Model\Repository\UserRepository;
use Kdyby\Doctrine\EntityManager;
use Nette\Object;
use Nette\Security\AuthenticationException;
use Nette\Security\Identity;
use Nette\Security\IIdentity;

/**
 * Class AuthenticationFacade
 * @package App\Model\Facade
 * @author Jakub Hadamčík <jakub@hadamcik.cz>
 */
class AuthenticationFacade extends Object
{
    /** @var EntityManager */
    private $entityManager;

    /** @var UserRepository */
    private $userRepository;

    /**
     * AuthenticationFacade constructor.
     * @param EntityManager $entityManager
     */
    public function __construct(EntityManager $entityManager)
    {
        $this->entityManager = $entityManager;
        $this->userRepository = $this->entityManager->getRepository(User::class);
    }

    /**
     * @param string $email
     * @param string $password
     * @return IIdentity
     * @throws AuthenticationException
     */
    public function authenticate($email, $password)
    {
        $query = (new UserQuery())->byEmail($email);
        $userRow = $this->userRepository->fetchOne($query);
        if($userRow === false || $userRow->passwordMatch($password)) {
            return new AuthenticationException();
        }
        return new Identity($userRow->getId());
    }

    /**
     * @param User $user
     * @throws \Exception
     */
    public function addUser(User $user)
    {
        $this->entityManager->persist($user);
        $this->entityManager->flush($user);
    }

    /**
     * @param string $email
     * @return bool
     */
    public function emailExists($email)
    {
        $query = (new UserQuery())->byEmail($email);
        $userRow = $this->userRepository->fetchOne($query);
        return ($userRow !== null);
    }

    /**
     * @param string $email
     * @param string $password
     * @return bool
     */
    public function accountPasswordMatch($email, $password)
    {
        $query = (new UserQuery())->byEmail($email);
        $userRow = $this->userRepository->fetchOne($query);
        if($userRow === null) {
            return false;
        }
        return $userRow->passwordMatch($password);
    }
}
