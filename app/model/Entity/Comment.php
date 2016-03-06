<?php

namespace App\Model\Entity;

use Doctrine\ORM\Mapping as ORM;
use Kdyby\Doctrine\Entities\Attributes\Identifier;
use Kdyby\Doctrine\Entities\MagicAccessors;
use Nette\Utils\DateTime;

/**
 * @ORM\Entity(repositoryClass="\App\Model\Repository\CommentRepository")
 */
class Comment
{
    use Identifier;
    use MagicAccessors;

    /**
     * @ORM\ManyToOne(targetEntity="User")
     */
    protected $author;

    /**
     * @ORM\ManyToOne(targetEntity="Article")
     */
    protected $article;

    /**
     * @ORM\Column(type="text")
     */
    protected $text;

    /**
     * @ORM\Column(type="datetime")
     */
    protected $added;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     */
    protected $removed = null;

    /**
     * @ORM\ManyToOne(targetEntity="User")
     */
    protected $remover;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     */
    protected $published = null;

    /**
     * Comment constructor.
     * @param Article $article
     * @param User $author
     */
    public function __construct(Article $article, User $author)
    {
        $this->article = $article;
        $this->author = $author;
        $this->added = new DateTime();
    }

    /**
     * @return bool
     */
    public function isPublished()
    {
        return ($this->getPublished() !== null);
    }

    /**
     * @return bool
     */
    public function isRemoved()
    {
        return ($this->getRemoved() !== null);
    }

    /**
     * @return Comment
     */
    public function publish()
    {
        $this->published = new DateTime();
        return $this;
    }

    /**
     * @return Comment
     */
    public function remove()
    {
        $this->removed = new DateTime();
        return $this;
    }
}
