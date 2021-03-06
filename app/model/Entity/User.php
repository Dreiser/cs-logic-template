<?php

namespace App\Model\Entity;

use Doctrine\ORM\Mapping as ORM;
use Kdyby\Doctrine\Entities\Attributes\Identifier;
use Nette\Security\Passwords;
use Nette\Utils\DateTime;

/**
 * @ORM\Entity(repositoryClass="\App\Model\Repository\UserRepository")
 */
class User
{
    use Identifier;

    /**
     * @ORM\Column(length=128, unique=true)
     */
    private $email;

    /**
     * @ORM\Column(length=123)
     */
    private $password;

    /**
     * @ORM\Column(length=32, nullable=true)
     */
    private $firstname;

    /**
     * @ORM\Column(length=64, nullable=true)
     */
    private $surname;

    /**
     * @ORM\Column(type="datetime")
     */
    private $registered;

    /**
     * @ORM\OneToMany(targetEntity="Article", mappedBy="author")
     */
    private $articles;

    /**
     * @ORM\OneToMany(targetEntity="Comment", mappedBy="author")
     */
    private $comments;

    /**
     * User constructor.
     * @param string $email
     * @param string $password
     */
    public function __construct($email, $password)
    {
        $this->setEmail($email);
        $this->setPassword($password);
        $this->setRegistered(new DateTime());
    }

    /**
     * @return string
     */
    public function getEmail()
    {
        return $this->email;
    }

    /**
     * @return string
     */
    public function getSignature()
    {
        $fullname = $this->getFirstname() . ' ' . $this->getSurname();
        if(trim($fullname) != null) {
            return $this->getEmail() . ' ' . $fullname;
        }
        return $this->getEmail();
    }

    /**
     * @param string $password
     * @return bool
     */
    public function passwordMatch($password)
    {
        return Passwords::verify($password, $this->getPassword());
    }

    /**
     * @param string $email
     * @return User
     */
    private function setEmail($email)
    {
        $this->email = $email;
        return $this;
    }

    /**
     * @param string $password
     * @return User
     */
    private function setPassword($password)
    {
        $this->password = Passwords::hash($password);
        return $this;
    }

    /**
     * @return string
     */
    private function getPassword()
    {
        return $this->password;
    }

    /**
     * @param DateTime $registered
     * @return User
     */
    private function setRegistered(DateTime $registered)
    {
        $this->registered = $registered;
        return $this;
    }

    /**
     * @param string $firstname
     * @return User
     */
    public function setFirstname($firstname)
    {
        $this->firstname = $firstname;
        return $this;
    }

    /**
     * @return string
     */
    public function getFirstname()
    {
        return $this->firstname;
    }

    /**
     * @param string $surname
     * @return User
     */
    public function setSurname($surname)
    {
        $this->surname = $surname;
        return $this;
    }

    /**
     * @return string
     */
    public function getSurname()
    {
        return $this->surname;
    }
}
