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
    private $title;

    /**
     * @ORM\Column(type="text")
     */
    private $text;

    /**
     * @ORM\ManyToOne(targetEntity="User", inversedBy="articles")
     */
    private $author;

    /**
     * @ORM\Column(type="datetime")
     */
    private $added;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     */
    private $published = null;

    /**
     * @ORM\OneToMany(targetEntity="Comment", mappedBy="article")
     */
    private $comments;

    /**
     * Article constructor.
     * @param User $author
     * @param string $title
     * @param string $text
     */
    public function __construct(User $author, $title, $text)
    {
        $this->setAuthor($author);
        $this->setTitle($title);
        $this->setText($text);
        $this->setAdded(new DateTime());
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
        $this->setPublished(new DateTime());
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

    /**
     * @param User $author
     * @return Article
     */
    private function setAuthor(User $author)
    {
        $this->author = $author;
        return $this;
    }

    /**
     * @return User
     */
    public function getAuthor()
    {
        return $this->author;
    }

    /**
     * @param string $title
     * @return Article
     */
    public function setTitle($title)
    {
        $this->title = $title;
        return $this;
    }

    /**
     * @return string
     */
    public function getTitle()
    {
        return $this->title;
    }

    /**
     * @param string $text
     * @return Article
     */
    public function setText($text)
    {
        $this->text = $text;
        return $text;
    }

    /**
     * @return string
     */
    public function getText()
    {
        return $this->text;
    }

    /**
     * @param DateTime $added
     * @return Article
     */
    private function setAdded(DateTime $added)
    {
        $this->added = $added;
        return $this;
    }

    /**
     * @return DateTime
     */
    public function getAdded()
    {
        return $this->added;
    }

    /**
     * @param DateTime $published
     * @return Article
     */
    private function setPublished(DateTime $published)
    {
        $this->published = $published;
        return $this;
    }

    /**
     * @return DateTime|null
     */
    public function getPublished()
    {
        return $this->published;
    }

    /**
     * @return array
     */
    public function getComments()
    {
        return $this->comments;
    }
}
