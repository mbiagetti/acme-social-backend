<?php

namespace Acme\SocialBundle\DataFixtures\ORM;

use Acme\SocialBundle\Entity\Tag;
use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\Persistence\ObjectManager;

/**
 * Fixtures Tag
 */
class TagData extends AbstractFixture
{
    /**
     * {@inheritdoc}
     */
    public function load(ObjectManager $manager)
    {
        // TODO check for possible references...
        $tag1 = new Tag();
        $tag1
        
            ->setName('Lorem ipsum dolor sit amet')
            ->setTweets($this->getReference('tweet1'))
        ;
        $manager->persist($tag1);
        $this->addReference('tag1', $tag1);

        $tag2 = new Tag();
        $tag2
        
            ->setName('Lorem ipsum dolor sit amet')
            ->setTweets($this->getReference('tweet2'))
        ;
        $manager->persist($tag2);
        $this->addReference('tag2', $tag2);

        $tag3 = new Tag();
        $tag3
        
            ->setName('Lorem ipsum dolor sit amet')
            ->setTweets($this->getReference('tweet3'))
        ;
        $manager->persist($tag3);
        $this->addReference('tag3', $tag3);

        
        $manager->flush();
    }
}
