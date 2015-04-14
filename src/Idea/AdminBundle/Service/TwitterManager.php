<?php


namespace Idea\AdminBundle\Service;


use AppBundle\Entity\Tweet;

class TwitterManager {


    /**
     * @var TwitterFactory
     */
    protected $factory;

    /**
     * @var \AppBundle\Entity\TwitterRepositoryInterface
     */
    protected $repo;

    private $autoApprove = false;

    function __construct($factory, $repo)
    {
        $this->factory = $factory;
        $this->repo = $repo;
    }

    public function process($rawData)
    {
        $post = $this->getPost($rawData);
        if ($post)
        {
            if (!$this->exists($post))
            {
                $tags = $this->getTags($rawData);
                foreach($tags as $tag)
                {
                    $post->addTag($this->manageTag($tag));
                }

                $author = $this->getAuthor($rawData);
                $post->setAuthor($this->manageAuthor($author));
                $this->repo->persistTweet($post);
                return true;
            }
        }
        return false;
    }

    public function autoApprove()
    {
        $this->autoApprove=true;
    }

    private function getPost($rawData)
    {
        $tweet = $this->factory->createPost($rawData );
        if ($tweet)
            $tweet->setStatus($this->getTweetState());

        return $tweet;
    }

    private function getTags($rawData)
    {
        return $this->factory->createTags($rawData );
    }

    private function getAuthor($rawData)
    {
        return $this->factory->createAuthor($rawData);
    }

    private function exists($post)
    {
        return ( $this->repo->findTweetByCode($post->getCode()) != null) ;
    }

    private function manageTag($tag)
    {
        $dbTag =$this->repo->findTagByName($tag->getName());
        if ($dbTag)
            $tag=$dbTag;

    return $tag;

    }

    private function manageAuthor($author)
    {
        $dbAuthor = $this->repo->findAuthorByCode($author->getCode());
        if ($dbAuthor)
            $author= $dbAuthor;

        return $author;
    }

    private function getTweetState()
    {
        return ( $this->autoApprove ? Tweet::ACCEPTED : Tweet::PENDING );
    }


}