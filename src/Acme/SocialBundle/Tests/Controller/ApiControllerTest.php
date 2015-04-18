<?php


namespace SocialBundle\Tests\Controller;


use Acme\SocialBundle\DataFixtures\ORM\AuthorData;
use Acme\SocialBundle\DataFixtures\ORM\TagData;
use Acme\SocialBundle\DataFixtures\ORM\TweetData;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Doctrine\Common\DataFixtures\Loader;
use Doctrine\Common\DataFixtures\Executor\ORMExecutor;
use Doctrine\Common\DataFixtures\Purger\ORMPurger;

class ApiControllerTest extends WebTestCase {

    private $client;

    protected function setUp()
    {
        $this->client = static::createClient();
        $em = $this->client->getContainer()->get('doctrine')->getManager();

        $loader = new Loader();
        $loader->addFixture(new AuthorData());
        $loader->addFixture(new TweetData());
        $loader->addFixture(new TagData());
        $purger = new ORMPurger();
        $executor = new ORMExecutor($em, $purger);
        $executor->execute($loader->getFixtures());
    }

    public function testHomeApi()
    {
        $mainEndPoint = '/api';
        $this->client->request('GET', $mainEndPoint);
        $this->checkJsonResponse();
        $response = $this->client->getResponse();
        $data = json_decode($response->getContent(), true);
        $postEndPoint = $data['endpoints']['posts'];
        $tagEndPoint = $data['endpoints']['tags'];
        $authorsEndPoint = $data['endpoints']['authors'];
        $this->client->request('GET', $postEndPoint);
        $this->checkJsonResponse();
        $this->client->request('GET', $tagEndPoint);
        $this->checkJsonResponse();
        $this->client->request('GET', $authorsEndPoint);
        $this->checkJsonResponse();
    }

    public function testPostsApi()
    {
        $this->client->request('GET', "/api/posts");
        $this->checkJsonResponse();
        $response = $this->client->getResponse();
        $data = json_decode($response->getContent(), true);
        $postDetailEndPoint = $data['posts'][0]['_links']['self'];

        $this->client->request('GET', $postDetailEndPoint);
        $this->checkJsonResponse();
    }

    public function testAuthorsApi()
    {
        $this->client->request('GET', "/api/authors");
        $this->checkJsonResponse();
        $response = $this->client->getResponse();
        $data = json_decode($response->getContent(), true);
        $authorDetailEndPoint = $data['authors'][0]['_links']['self'];

        $this->client->request('GET', $authorDetailEndPoint);
        $this->checkJsonResponse();
    }

    public function tesTagsApi()
    {
        $this->client->request('GET', "/api/tags");
        $this->checkJsonResponse();
        $response = $this->client->getResponse();
        $data = json_decode($response->getContent(), true);
        $tagDetailEndPoint = $data['tags'][0]['_links']['self'];

        $this->client->request('GET', $tagDetailEndPoint);
        $this->checkJsonResponse();
    }

    protected function checkJsonResponse()
    {
        $this->assertEquals(
            200, $this->client->getResponse()->getStatusCode(),
            $this->client->getResponse()->getContent()
        );
        $this->assertTrue(
            $this->client->getResponse()->headers->contains('Content-Type', 'application/json'),
            $this->client->getResponse()->headers
        );
    }

}
