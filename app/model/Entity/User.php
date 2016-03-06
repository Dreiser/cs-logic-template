<?php

namespace App\Model\Entity;

use Doctrine\ORM\Mapping as ORM;
use Kdyby\Doctrine\Entities\Attributes\Identifier;
use Kdyby\Doctrine\Entities\MagicAccessors;
use Nette\Security\Passwords;
use Nette\Utils\DateTime;

/**
 * @ORM\Entity(repositoryClass="\App\Model\Repository\UserRepository")
 */
class User
{
    use Identifier;
    use MagicAccessors;

    /**
     * @ORM\Column(length=128, unique=true)
     */
    protected $email;

    /**
     * @ORM\Column(length=123)
     */
    private $password;

    /**
     * @ORM\Column(length=32, nullable=true)
     */
    protected $firstname;

    /**
     * @ORM\Column(length=64, nullable=true)
     */
    protected $surname;

    /**
     * @ORM\Column(type="datetime")
     */
    protected $registered;

    /**
     * @ORM\OneToMany(targetEntity="Article", mappedBy="author")
     */
    protected $articles;

    /**
     * @ORM\OneToMany(targetEntity="Comment", mappedBy="author")
     */
    protected $comments;

    /**
     * User constructor.
     */
    public function __construct()
    {
        $this->registered = new DateTime();
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
     * @param string $password
     * @return User
     */
    public function setPassword($password)
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
}
