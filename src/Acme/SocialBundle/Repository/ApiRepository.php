<?php
namespace Acme\SocialBundle\Repository;


use SocialBundle\Entity\Author;
use SocialBundle\Entity\Tweet;
use SocialBundle\Entity\Tag;

class ApiRepository
{

    /**
     * @var \Doctrine\ORM\EntityManager
     */
    private $em;


    function __construct($em)
    {
        $this->em = $em;
    }

    public function getQueryBuilderForPosts()
    {
        $qb = $this->em->getRepository("SocialBundle:Tweet")->createQueryBuilder("p");
        $qb->where('p.status = :accepted_status')->setParameter('accepted_status', Tweet::ACCEPTED)
            ->addOrderBy("p.created_at", "desc");


        return $qb;
    }

    public function getQueryBuilderForAuthors()
    {
        $qb = $this->em->getRepository("SocialBundle:Author")->createQueryBuilder("p");
        $qb->addOrderBy("p.created_at","desc");

        return $qb;
    }

    public function getQueryBuilderForPostsByAuthor(Author $author)
    {
        $qb = $this->getQueryBuilderForPosts();
        $qb->AndWhere('p.author = :author')->setParameter("author",$author->getId());

        return $qb;
    }

    public function getQueryBuilderForTags()
    {
        $qb = $this->em->getRepository("SocialBundle:Tag")->createQueryBuilder("p");
        $qb->addOrderBy("p.name","asc");

        return $qb;
    }

    public function getQueryBuilderForPostsByTag(Tag $tag)
    {
        $qb = $this->getQueryBuilderForPosts();
        $qb->leftJoin("p.tags","t");
        $qb->andWhere('t = :tag')->setParameter("tag",$tag->getId());

        return $qb;
    }
}