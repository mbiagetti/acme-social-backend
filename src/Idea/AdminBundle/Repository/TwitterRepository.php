<?php
namespace Idea\AdminBundle\Repository;

use AppBundle\Entity\TwitterRepositoryInterface;

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
        return $this->em->getRepository("AppBundle:Tweet")->findOneBy(array('code'=>$code));
    }

    public function findTagByName($name)
    {
        return $this->em->getRepository("AppBundle:Tag")->findOneBy(array('name'=>$name));
    }

    public function findAuthorByCode($code)
    {
        return $this->em->getRepository("AppBundle:Author")->findOneBy(array('code'=>$code));
    }
}