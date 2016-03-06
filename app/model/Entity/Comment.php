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
     * @ORM\ManyToOne(targetEntity="User", inversedBy="comments")
     */
    private $author;

    /**
     * @ORM\ManyToOne(targetEntity="Article", inversedBy="comments")
     */
    private $article;

    /**
     * @ORM\Column(type="text")
     */
    private $text;

    /**
     * @ORM\Column(type="datetime")
     */
    private $added;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     */
    private $removed = null;

    /**
     * @ORM\ManyToOne(targetEntity="User")
     */
    private $remover = null;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     */
    private $published = null;

    /**
     * Comment constructor.
     * @param Article $article
     * @param User $author
     * @param string $text
     */
    public function __construct(Article $article, User $author, $text)
    {
        $this->setArticle($article);
        $this->setAuthor($author);
        $this->setText($text);
        $this->setAdded(new DateTime());
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
        $this->setPublished(new DateTime());
        return $this;
    }

    /**
     * @param User $remover
     * @return Comment
     */
    public function remove(User $remover)
    {
        $this->setRemoved(new DateTime());
        $this->setRemover($remover);
        return $this;
    }

    /**
     * @param Article $article
     * @return Comment
     */
    private function setArticle(Article $article)
    {
        $this->article = $article;
        return $this;
    }

    /**
     * @return Article
     */
    public function getArticle()
    {
        return $this->article;
    }

    /**
     * @param User $author
     * @return Comment
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
     * @param string $text
     * @return Comment
     */
    public function setText($text)
    {
        $this->text = $text;
        return $this;
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
     * @return Comment
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
     * @return Comment
     */
    private function setPublished(DateTime $published)
    {
        $this->published = $published;
        return $this;
    }

    /**
     * @return DateTime|null
     */
    private function getPublished()
    {
        return $this->published;
    }

    /**
     * @param DateTime $removed
     * @return Comment
     */
    private function setRemoved(DateTime $removed)
    {
        $this->removed = $removed;
        return $this;
    }

    /**
     * @return DateTime|null
     */
    public function getRemoved()
    {
        return $this->removed;
    }

    /**
     * @param User $remover
     * @return Comment
     */
    private function setRemover(User $remover)
    {
        $this->remover = $remover;
        return $this;
    }

    /**
     * @return User|null
     */
    public function getRemover()
    {
        return $this->remover;
    }
}
