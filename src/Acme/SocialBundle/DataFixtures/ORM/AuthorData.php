<?php

namespace Acme\SocialBundle\DataFixtures\ORM;

use Acme\SocialBundle\Entity\Author;
use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\Persistence\ObjectManager;

/**
 * Fixtures Author
 */
class AuthorData extends AbstractFixture
{
    /**
     * {@inheritdoc}
     */
    public function load(ObjectManager $manager)
    {
        // TODO check for possible references...
        $author1 = new Author();
        $author1
        
            ->setName('Lorem ipsum dolor sit amet')
            ->setScreen_name('Lorem ipsum dolor sit amet')
            ->setCode('Lorem ipsum dolor sit amet')
            ->setProfile_image_url('Lorem ipsum dolor sit amet')
            ->setLocation('Lorem ipsum dolor sit amet')
            ->setDescription('Lorem ipsum dolor sit amet')
            ->setCreated_at(new \DateTime())
        ;
        $manager->persist($author1);
        $this->addReference('author1', $author1);

        $author2 = new Author();
        $author2
        
            ->setName('Lorem ipsum dolor sit amet')
            ->setScreen_name('Lorem ipsum dolor sit amet')
            ->setCode('Lorem ipsum dolor sit amet')
            ->setProfile_image_url('Lorem ipsum dolor sit amet')
            ->setLocation('Lorem ipsum dolor sit amet')
            ->setDescription('Lorem ipsum dolor sit amet')
            ->setCreated_at(new \DateTime())
        ;
        $manager->persist($author2);
        $this->addReference('author2', $author2);

        $author3 = new Author();
        $author3
        
            ->setName('Lorem ipsum dolor sit amet')
            ->setScreen_name('Lorem ipsum dolor sit amet')
            ->setCode('Lorem ipsum dolor sit amet')
            ->setProfile_image_url('Lorem ipsum dolor sit amet')
            ->setLocation('Lorem ipsum dolor sit amet')
            ->setDescription('Lorem ipsum dolor sit amet')
            ->setCreated_at(new \DateTime())
        ;
        $manager->persist($author3);
        $this->addReference('author3', $author3);

        
        $manager->flush();
    }
}
