<?php

namespace Acme\SocialBundle\DataFixtures\ORM;

use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use SocialBundle\Entity\Author;

/**
 * Fixtures Author
 */
class AuthorData extends AbstractFixture implements OrderedFixtureInterface
{
    /**
     * {@inheritdoc}
     */
    public function load(ObjectManager $manager)
    {
        $author1 = new Author();
        $author1->setName('Lorem ipsum dolor sit amet');
        $author1->setCode('Lorem ipsum dolor sit amet');
        $author1->setCreatedAt(new \DateTime());
        $manager->persist($author1);
        $this->addReference('author1', $author1);

        $author2 = new Author();
        $author2->setName('Lorem ipsum dolor sit amet');
        $author2->setCode('Lorem ipsum dolor sit amet');
        $author2->setCreatedAt(new \DateTime());
        $manager->persist($author2);
        $this->addReference('author2', $author2);

        
        $manager->flush();
    }

    /**
     * Get the order of this fixture
     *
     * @return integer
     */
    public function getOrder()
    {
        return 1;
    }
}
