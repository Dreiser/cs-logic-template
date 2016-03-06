<?php

namespace App\Model\Entity;

use Doctrine\ORM\Mapping as ORM;
use Kdyby\Doctrine\Entities\Attributes\Identifier;
use Kdyby\Doctrine\Entities\MagicAccessors;
use Nette\Utils\DateTime;

/**
 * @ORM\Entity(repositoryClass="\App\Model\Repository\ArticleRepository")
 */
class Article
{
    use Identifier;
    use MagicAccessors;

    /**
     * @ORM\Column(length=128)
     */
    protected $title;

    /**
     * @ORM\Column(type="text")
     */
    protected $text;

    /**
     * @ORM\ManyToOne(targetEntity="User", inversedBy="articles")
     */
    protected $author;

    /**
     * @ORM\Column(type="datetime")
     */
    protected $added;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     */
    protected $published = null;

    /**
     * @ORM\OneToMany(targetEntity="Comment", mappedBy="article")
     */
    protected $comments;

    /**
     * Article constructor.
     * @param User $author
     */
    public function __construct(User $author)
    {
        $this->author = $author;
        $this->added = new DateTime();
    }

    /**
     * @return int
     */
    public function getCommentsCount()
    {
        return count($this->getComments());
    }

    /**
     * @return bool
     */
    public function isPublished()
    {
        return ($this->getPublished() !== null);
    }

    /**
     * @return Article
     */
    public function publish()
    {
        $this->published = new DateTime();
        return $this;
    }

    /**
     * @return Article
     */
    public function unpublish()
    {
        $this->published = null;
        return $this;
    }
}
