<?php

namespace Acme\SocialBundle\DataFixtures\ORM;

use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use SocialBundle\Entity\Tweet;

/**
 * Fixtures Tweet
 */
class TweetData extends AbstractFixture implements OrderedFixtureInterface
{
    /**
     * {@inheritdoc}
     */
    public function load(ObjectManager $manager)
    {
        $tweet1 = new Tweet();
        $tweet1->setText('Lorem ipsum dolor sit amet');
        $tweet1->setCode('Lorem ipsum dolor sit amet');
        $tweet1->setCreatedAt(new \DateTime());
        $tweet1->setStatus(Tweet::ACCEPTED);
        $tweet1->setAuthor($this->getReference('author1'));

        $tweet1->addTag($this->getReference('tag1'));
        $tweet1->addTag($this->getReference('tag2'));
        $tweet1->addTag($this->getReference('tag3'));

        $manager->persist($tweet1);

        $tweet2 = new Tweet();
        $tweet2->setText('Lorem ipsum dolor sit amet');
        $tweet2->setCode('Lorem ipsum dolor sit amet');
        $tweet2->setCreatedAt(new \DateTime());
        $tweet2->setStatus(Tweet::ACCEPTED);
        $tweet2->setAuthor($this->getReference('author2'));

        $tweet2->addTag($this->getReference('tag2'));
        $tweet2->addTag($this->getReference('tag3'));

        $manager->persist($tweet2);

        $tweet3 = new Tweet();
        $tweet3->setText('Lorem ipsum dolor sit amet');
        $tweet3->setCode('Lorem ipsum dolor sit amet');
        $tweet3->setCreatedAt(new \DateTime());
        $tweet3->setStatus(Tweet::ACCEPTED);
        $tweet3->setAuthor($this->getReference('author1'));

        $tweet3->addTag($this->getReference('tag2'));
        $tweet3->addTag($this->getReference('tag3'));

        $manager->persist($tweet3);

        $manager->flush();
    }

    /**
     * Get the order of this fixture
     *
     * @return integer
     */
    public function getOrder()
    {
        return 2;
    }
}
