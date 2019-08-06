<?php

namespace PostBundle\Entity;

use BookBundle\Entity\Book;
use Doctrine\ORM\Mapping as ORM;

/**
 * Class Post
 *
 * @ORM\Table(name="posts")
 * @ORM\Entity(repositoryClass="PostBundle\Repository\PostRepository")
 */
class Post
{
    /**
     *
     * @var integer
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     * @ORM\Column(name="id", type="integer", nullable=false)
     *
     */
    protected $id;

    /**
     *
     * @ORM\ManyToOne(targetEntity="UserBundle\Entity\User", inversedBy="users")
     * @ORM\JoinColumn(onDelete="CASCADE")
     *
     */
    protected $author;

    /**
     * @var string
     *
     * @ORM\Column(name="reviewText", type="string", length=255, nullable=true)
     */
    protected $reviewText;

    /**
     *
     * @ORM\ManyToOne(targetEntity="BookBundle\Entity\Book", inversedBy="books")
     * @ORM\JoinColumn(onDelete="CASCADE")
     *
     */
    protected $book;

    /**
     * @var integer
     *
     * @ORM\Column(name="helpful", type="integer", nullable=true)
     */
    protected $helpful;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="postedAt", type="datetime", nullable=false)
     */
    protected $postedAt;

    /**
     * Post constructor.
     *
     * @param int $author
     * @param string $reviewText
     * @param int $helpful
     * @param \DateTime $postedAt
     */
    public function __construct($author, $reviewText, $helpful, \DateTime $postedAt, $book)
    {
        $this->author = $author;
        $this->reviewText = $reviewText;
        $this->helpful = $helpful;
        $this->postedAt = $postedAt;
        $this->book = $book;
    }

    /**
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @param int $id
     */
    public function setId($id)
    {
        $this->id = $id;
    }

    /**
     * @return int
     */
    public function getAuthor()
    {
        return $this->author;
    }

    /**
     * @param int $author
     */
    public function setAuthor($author)
    {
        $this->author = $author;
    }

    /**
     * @return string
     */
    public function getReviewText()
    {
        return $this->reviewText;
    }

    /**
     * @param string $reviewText
     */
    public function setReviewText($reviewText)
    {
        $this->reviewText = $reviewText;
    }

    /**
     * @return int
     */
    public function getHelpful()
    {
        return $this->helpful;
    }

    /**
     * @param int $helpful
     */
    public function setHelpful($helpful)
    {
        $this->helpful = $helpful;
    }

    /**
     * @return \DateTime
     */
    public function getPostedAt()
    {
        return $this->postedAt;
    }

    /**
     * @param \DateTime $postedAt
     */
    public function setPostedAt($postedAt)
    {
        $this->postedAt = $postedAt;
    }

    /**
     * @return Book
     */
    public function getBook()
    {
        return $this->book;
    }

    /**
     * @param Book $book
     */
    public function setBook($book)
    {
        $this->book = $book;
    }

}
