<?php


namespace Idea\AdminBundle\Tests\Service;


use AppBundle\Entity\Tweet;
use Doctrine\Common\Collections\ArrayCollection;
use Idea\AdminBundle\Service\TwitterManager;

class TwitterManagerTest extends \PHPUnit_Framework_TestCase {


    /**
     * @var \Idea\AdminBundle\Service\TwitterFactory
     */
    private $factory;

    /**
     * @var \AppBundle\Entity\TwitterRepositoryInterface
     */
    private $repository;

    /**
     * @var \Idea\AdminBundle\Service\TwitterManager
     */
    private $manager;

    private $tweet, $author, $tag, $input;

    public function setUp()
    {
        $this->input=array();

        $this->tweet  = \Phake::mock('AppBundle\Entity\Tweet');
        $this->author = \Phake::mock('AppBundle\Entity\Author');
        $this->tag   = \Phake::mock('AppBundle\Entity\Tag');

        $this->factory =  \Phake::mock('Idea\AdminBundle\Service\TwitterFactory');
        $this->repository=\Phake::mock('AppBundle\Entity\TwitterRepositoryInterface');

        $this->manager = new TwitterManager($this->factory, $this->repository);
    }

    public function testNoData()
    {
        \Phake::when($this->factory)
            ->createPost($this->input)
            ->thenReturn(null);

        $this->assertFalse($this->manager->process($this->input));
        \Phake::verify($this->repository, \Phake::times(0))->persistTweet(\Phake::anyParameters());
    }

    public function testExistingData()
    {
        \Phake::when($this->factory)
            ->createPost($this->input)
            ->thenReturn($this->tweet);

        \Phake::when($this->repository)
            ->findTweetByCode(\Phake::anyParameters())
            ->thenReturn($this->tweet);

        $this->assertFalse($this->manager->process($this->input));
        \Phake::verify($this->repository, \Phake::times(0))->persistTweet(\Phake::anyParameters());
    }

    public function testNewTweet()
    {
        \Phake::when($this->factory)
            ->createPost($this->input)
            ->thenReturn($this->tweet);

        \Phake::when($this->factory)
            ->createAuthor($this->input)
            ->thenReturn($this->author);

        \Phake::when($this->factory)
            ->createTags($this->input)
            ->thenReturn(new ArrayCollection(array($this->tag)));

        \Phake::when($this->repository)
            ->findTweetByCode(\Phake::anyParameters())
            ->thenReturn(null);

        \Phake::when($this->repository)
            ->findTagByName(\Phake::anyParameters())
            ->thenReturn($this->tag);

        \Phake::when($this->repository)
            ->findAuthorByCode(\Phake::anyParameters())
            ->thenReturn($this->author);


        $this->assertTrue($this->manager->process($this->input));
        \Phake::verify($this->repository, \Phake::times(1))->persistTweet(\Phake::anyParameters());
        \Phake::verify($this->tweet, \Phake::times(1))->setStatus(Tweet::PENDING);
    }

    public function testNewTweetWithAutoApproveState()
    {
        \Phake::when($this->factory)
            ->createPost($this->input)
            ->thenReturn($this->tweet);

        \Phake::when($this->factory)
            ->createAuthor($this->input)
            ->thenReturn($this->author);

        \Phake::when($this->factory)
            ->createTags($this->input)
            ->thenReturn(new ArrayCollection(array($this->tag)));

        \Phake::when($this->repository)
            ->findTweetByCode(\Phake::anyParameters())
            ->thenReturn(null);

        \Phake::when($this->repository)
            ->findTagByName(\Phake::anyParameters())
            ->thenReturn($this->tag);

        \Phake::when($this->repository)
            ->findAuthorByCode(\Phake::anyParameters())
            ->thenReturn($this->author);

        $this->manager->autoApprove();

        $this->assertTrue($this->manager->process($this->input));
        \Phake::verify($this->repository, \Phake::times(1))->persistTweet(\Phake::anyParameters());
        \Phake::verify($this->tweet, \Phake::times(1))->setStatus(Tweet::ACCEPTED);
    }

}