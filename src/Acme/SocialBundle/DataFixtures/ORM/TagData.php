<?php

namespace Acme\SocialBundle\DataFixtures\ORM;

use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use SocialBundle\Entity\Tag;

/**
 * Fixtures Tag
 */
class TagData extends AbstractFixture implements OrderedFixtureInterface
{
    /**
     * {@inheritdoc}
     */
    public function load(ObjectManager $manager)
    {
        $tag1 = new Tag();
        $tag1->setName('Lorem ipsum dolor sit amet');
        $manager->persist($tag1);
        $this->addReference('tag1', $tag1);

        $tag2 = new Tag();
        $tag2->setName('Lorem ipsum dolor sit amet');
        $manager->persist($tag2);
        $this->addReference('tag2', $tag2);

        $tag3 = new Tag();
        $tag3->setName('Lorem ipsum dolor sit amet');
        $manager->persist($tag3);
        $this->addReference('tag3', $tag3);

        
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
