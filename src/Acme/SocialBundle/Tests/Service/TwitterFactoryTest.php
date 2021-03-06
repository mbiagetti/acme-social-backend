<?php

namespace Acme\SocialBundle\Tests\Service;


use Acme\SocialBundle\Service\TwitterFactory;

class TwitterFactoryTest extends \PHPUnit_Framework_TestCase {


    /**
     * @var TwitterFactory
     */
    private $factory = null;

    const TWITTER_DATE_FORMAT = "D M j H:i:s P Y";

    public function setUp()
    {
        $this->factory = new TwitterFactory();
    }

    /**
     * @dataProvider getCreateEmptyTagsData
     */
    public function testCreateEmptyTags($data)
    {
        $this->assertEmpty($this->factory->createTags($data) );
    }

    public function testCreateTags()
    {
        $tagName = 'mario';
        $tags = $this->factory->createTags(array('entities'=>array('hashtags' => array(array('text'=>$tagName)))));

        $this->assertEquals(1,$tags->count(),'Check correct tags collection of one element');
        $tag = $tags->first();
        $this->assertEquals($tagName, $tag->getName() ,'Check correct tag name');

        $tags = $this->factory->createTags(array('entities'=>array('hashtags' => array(array('text'=>$tagName),array('text'=>$tagName),))));

        $this->assertEquals(2,$tags->count(),'Check correct tags collection of two elements');
    }


    public function testCreateEmptyAuthor()
    {
        $this->assertNull($this->factory->createAuthor(null) );
        $this->assertNull($this->factory->createAuthor(array()) );
        $this->assertNull($this->factory->createAuthor(array('user')) );
    }

    public function testCreateAuthor()
    {
        $simpleAuthor = $this->factory->createAuthor(array('user'=>array('name'=>'mario')));
        $this->assertNotNull( $simpleAuthor );

        $dateAsString ='Fri Apr 06 12:06:32 +0000 2012';

        $fullAuthor = $this->factory->createAuthor(array('user'=>
                                                        array(
                                                            'name'=>'webDEVILopers',
                                                            'id' => 546838854,
                                                            'screen_name' => 'webdevilopers',
                                                            'location' => 'Germany',
                                                            'description' => 'php, @symfony2, @sonataproject enthusiast, #zf2, @doctrine2, @MongoDB',
                                                            'url' => 'http://t.co/CrolYVU4k3',
                                                            'created_at' => $dateAsString,
                                                            'profile_image_url' => 'http://pbs.twimg.com/profile_images/480338296581521409/0VjX7Xy4_normal.jpeg',
                                                        )
                                                    ));
        $this->assertNotNull( $fullAuthor );
        $this->assertEquals('546838854', $fullAuthor->getCode(),'check for correct author twitter id');
        $this->assertEquals(\Datetime::createFromFormat(self::TWITTER_DATE_FORMAT,$dateAsString), $fullAuthor->getCreatedAt(),'check for correct datetime object creation');
    }

    public function testCreateEmptyPost()
    {
        $this->assertNull($this->factory->createPost(null) );
        $this->assertNull($this->factory->createPost(array()) );
        $this->assertNull($this->factory->createPost(array('id')) );
    }

    public function testCreatePost()
    {
        $dateAsString = 'Sun Apr 12 20:07:37 +0000 2015';
        $simplePost = $this->factory->createPost(array(
            'id' => '587346352474083328',
            'text' => 'Yay, @symfony broke the 10.000 stars record on @github! https://t.co/TCN19sEPV8 #Symfony2 #PHP',
            'created_at' => $dateAsString
        ));

        $this->assertNotNull( $simplePost );
        $this->assertEquals('587346352474083328', $simplePost->getCode(),'check for correct author twitter id');
        $this->assertEquals(\Datetime::createFromFormat(self::TWITTER_DATE_FORMAT,$dateAsString), $simplePost->getCreatedAt(),'check for correct datetime object creation');

    }

    public function getCreateEmptyTagsData()
    {
        return array(
            'set #0' => array( null ),
            'set #1' => array( array() ),
            'set #2' => array( array('entities') ),
            'set #3' => array( array('entities'=>array('hashtags')) ),
            'set #4' => array( array('entities'=>array('hashtags' => array()) ),
            'set #5' => array( array('entities'=>array('hashtags' => array('mario')))) )
        );

    }
}