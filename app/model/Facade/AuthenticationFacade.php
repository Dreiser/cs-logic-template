<?php

namespace App\Model\Facade;

use App\Model\Entity\User;
use App\Model\Query\UserQuery;
use App\Model\Repository\UserRepository;
use App\Model\Service\RegisterService;
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

    /** @var RegisterService */
    private $registerService;

    /** @var UserRepository */
    private $userRepository;

    /**
     * AuthenticationFacade constructor.
     * @param EntityManager $entityManager
     * @param RegisterService $registerService
     */
    public function __construct(EntityManager $entityManager, RegisterService $registerService)
    {
        $this->entityManager = $entityManager;
        $this->registerService = $registerService;
        $this->userRepository = $entityManager->getRepository(User::class);
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
     * @param string $user
     * @param string $password
     * @throws \Exception
     */
    public function createUser($user, $password)
    {
        $entity = $this->registerService->simple($user, $password);
        $this->entityManager->flush($entity);
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
