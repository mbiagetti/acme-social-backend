<?php

namespace Acme\SocialBundle\DataFixtures\ORM;

use Acme\SocialBundle\Entity\Tweet;
use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\Persistence\ObjectManager;

/**
 * Fixtures Tweet
 */
class TweetData extends AbstractFixture
{
    /**
     * {@inheritdoc}
     */
    public function load(ObjectManager $manager)
    {
        // TODO check for possible references...
        $tweet1 = new Tweet();
        $tweet1
        
            ->setText('Lorem ipsum dolor sit amet')
            ->setCode('Lorem ipsum dolor sit amet')
            ->setCreated_at(new \DateTime())
            ->setStatus('Lorem ipsum dolor sit amet')
            ->setTags($this->getReference('tag1'))
            ->setAuthor($this->getReference('author1'))
        ;
        $manager->persist($tweet1);
        $this->addReference('tweet1', $tweet1);

        $tweet2 = new Tweet();
        $tweet2
        
            ->setText('Lorem ipsum dolor sit amet')
            ->setCode('Lorem ipsum dolor sit amet')
            ->setCreated_at(new \DateTime())
            ->setStatus('Lorem ipsum dolor sit amet')
            ->setTags($this->getReference('tag2'))
            ->setAuthor($this->getReference('author2'))
        ;
        $manager->persist($tweet2);
        $this->addReference('tweet2', $tweet2);

        $tweet3 = new Tweet();
        $tweet3
        
            ->setText('Lorem ipsum dolor sit amet')
            ->setCode('Lorem ipsum dolor sit amet')
            ->setCreated_at(new \DateTime())
            ->setStatus('Lorem ipsum dolor sit amet')
            ->setTags($this->getReference('tag3'))
            ->setAuthor($this->getReference('author3'))
        ;
        $manager->persist($tweet3);
        $this->addReference('tweet3', $tweet3);

        
        $manager->flush();
    }
}
