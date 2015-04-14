<?php
namespace Acme\SocialBundle\Repository;

use SocialBundle\Entity\TwitterRepositoryInterface;

class TwitterRepository implements TwitterRepositoryInterface {

    /**
     * @var \Doctrine\ORM\EntityManager
     */
    private $em;


    function __construct($em)
    {
        $this->em = $em;
    }


    public function persistTweet($tweet)
    {
        $this->em->persist($tweet);
        $this->em->flush();
    }

    public function findTweetByCode($code)
    {
        return $this->em->getRepository("SocialBundle:Tweet")->findOneBy(array('code'=>$code));
    }

    public function findTagByName($name)
    {
        return $this->em->getRepository("SocialBundle:Tag")->findOneBy(array('name'=>$name));
    }

    public function findAuthorByCode($code)
    {
        return $this->em->getRepository("SocialBundle:Author")->findOneBy(array('code'=>$code));
    }
}