<?php

namespace App\Model\Service;

use App\Model\Entity\User;
use Kdyby\Doctrine\EntityManager;
use Nette\Object;

/**
 * Class RegisterService
 * @package App\Model\Service
 * @author Jakub Hadamčík <jakub@hadamcik.cz>
 */
class RegisterService extends Object
{
    /** @var EntityManager */
    private $entityManager;

    /**
     * RegisterService constructor.
     * @param EntityManager $entityManager
     */
    public function __construct(EntityManager $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    /**
     * @param string $email
     * @param string $password
     * @return User
     */
    public function simple($email, $password)
    {
        $user = new User();
        $user->setEmail($email);
        $user->setPassword($password);
        $this->entityManager->persist($user);
        return $user;
    }
}
