<?php


namespace SocialBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity()
 * @ORM\Table(name="tweet",
 *      options={"collate"="utf8mb4_unicode_ci", "charset"="utf8mb4"})
 */
class Tweet {

    const DECLINED =  0;
    const ACCEPTED =  1;
    const PENDING  = -1;

    /**
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     * @ORM\Column(type="bigint")
     */
    protected $id;

    /**
     * @ORM\Column(type="text", nullable=true)
     */
    protected $text;

    /**
     * @ORM\Column(type="string")
     * @Assert\NotBlank()
     */
    protected $code;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     */
    protected $created_at;

    /**
     * @ORM\Column(type="smallint")
     */
    protected $status = Tweet::PENDING;

    /**
     * @var ArrayCollection
     *
     * @ORM\ManyToMany(targetEntity="Tag", inversedBy="tweets",cascade={"persist"})
     * @ORM\JoinTable(name="tweet_tag",
     *  joinColumns={@ORM\JoinColumn(name="tweet_id", referencedColumnName="id", onDelete="cascade")},
     *  inverseJoinColumns={@ORM\JoinColumn(name="tag_id", referencedColumnName="id", onDelete="cascade")}
     *
     * )
     */
    protected $tags;


    /**
     * @ORM\ManyToOne(targetEntity="Author", inversedBy="tweets",cascade={"persist"})
     * @ORM\JoinColumn(name="author_id", referencedColumnName="id", onDelete="cascade")
     */
    protected $author;

    public function __construct()
    {
        $this->tags = new ArrayCollection();
    }

    /**
     * @return mixed
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @param mixed $id
     */
    public function setId($id)
    {
        $this->id = $id;
    }

    /**
     * @return mixed
     */
    public function getText()
    {
        return $this->text;
    }

    /**
     * @param mixed $text
     */
    public function setText($text)
    {
        $this->text = $text;
    }

    /**
     * @return mixed
     */
    public function getCode()
    {
        return $this->code;
    }

    /**
     * @param mixed $code
     */
    public function setCode($code)
    {
        $this->code = $code;
    }

    /**
     * @return mixed
     */
    public function getCreatedAt()
    {
        return $this->created_at;
    }

    /**
     * @param mixed $created_at
     */
    public function setCreatedAt($created_at)
    {
        $this->created_at = $created_at;
    }


    public function addTag($tag)
    {
        if (!$this->tags->contains($tag)) { // exclude duplicated tags
            $tag->addTweet($this);
            $this->tags->add($tag);
        }

    }

    public function removeTag($tag)
    {
        $this->tags->removeElement($tag);
    }

    /**
     * @return mixed
     */
    public function getStatus()
    {
        return $this->status;
    }

    /**
     * @param mixed $status
     */
    public function setStatus($status)
    {
        $this->status = $status;
    }

    /**
     * @return mixed
     */
    public function getTags()
    {
        return $this->tags;
    }

    /**
     * @param mixed $tags
     */
    public function setTags($tags)
    {
        $this->tags = $tags;
    }

    /**
     * @return mixed
     */
    public function getAuthor()
    {
        return $this->author;
    }

    /**
     * @param mixed $author
     */
    public function setAuthor($author)
    {
        $this->author = $author;
    }

    function __toString()
    {
        return $this->getCode();
    }

}