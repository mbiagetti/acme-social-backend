<?php

namespace SocialBundle\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class TweetControllerTest extends WebTestCase
{
    protected function getClient()
    {
        // Create a new client to browse the application
        return static::createClient(array(), array(
            "PHP_AUTH_USER" => "admin",
            "PHP_AUTH_PW"   => "test"
        ));
    }

    public function testCreate()
    {
        $client = $this->getClient();
        $crawler = $client->request('GET', '/admin/tweet/');
        $this->assertCount(0, $crawler->filter('table.records_list tbody tr'));
        $crawler = $client->click($crawler->filter('.new_entry a')->link());
        $form = $crawler->filter('form button[type="submit"]')->form(array(
//            'tweet[text]' => 'Lorem ipsum dolor sit amet',
            'tweet[code]' => 'data',
  //          'tweet[created_at]' => new \DateTime(),
    //        'tweet[status]' => 42,
                    ));
        $client->submit($form);
        $crawler = $client->followRedirect();
        $crawler = $client->click($crawler->filter('.record_actions a')->link());
        $this->assertCount(1, $crawler->filter('table.records_list tbody tr'));
    }

    public function testCreateError()
    {
        $client = $this->getClient();
        $crawler = $client->request('GET', '/admin/tweet/new');
        $form = $crawler->filter('form button[type="submit"]')->form();
        $crawler = $client->submit($form);
        $this->assertGreaterThan(0, $crawler->filter('form div.has-error')->count());
        $this->assertTrue($client->getResponse()->isSuccessful());
    }

    /**
     * @depends testCreate
     */
    public function testEditError()
    {
        $client = $this->getClient();
        $crawler = $client->request('GET', '/admin/tweet/');
        $crawler = $client->click($crawler->filter('table.records_list tbody tr td .btn-group a')->eq(1)->link());
        $form = $crawler->filter('form button[type="submit"]')->form(array(
            'tweet[code]' => '',
            // ... use a required field here ...
        ));
        $crawler = $client->submit($form);
        $this->assertGreaterThan(0, $crawler->filter('form div.has-error')->count());
    }

    /**
     * @depends testCreate
     */
    public function testDelete()
    {
        $client = $this->getClient();
        $crawler = $client->request('GET', '/admin/tweet/');
        $this->assertTrue($client->getResponse()->isSuccessful());
        $this->assertCount(1, $crawler->filter('table.records_list tbody tr'));
        $crawler = $client->click($crawler->filter('table.records_list tbody tr td .btn-group a')->eq(0)->link());
        $client->submit($crawler->filter('form#delete button[type="submit"]')->form());
        $crawler = $client->followRedirect();
        $this->assertCount(0, $crawler->filter('table.records_list tbody tr'));
    }

    /**
     * @depends testCreate
     */
    public function testFilter()
    {
        $client = $this->getClient();
        $crawler = $client->request('GET', '/admin/tweet/');
        $form = $crawler->filter('div#filter form button[type="submit"]')->form(array(
            'tweet_filter[text]' => 'First%',
        ));
        $client->submit($form);
        $crawler = $client->followRedirect();
        $this->assertTrue($client->getResponse()->isSuccessful());
        $crawler = $client->click($crawler->filter('div#filter a')->link());
        $crawler = $client->followRedirect();
        $this->assertTrue($client->getResponse()->isSuccessful());
    }    /**
     * @depends testCreate
     */
    public function testSort()
    {
        $client = $this->getClient();
        $crawler = $client->request('GET', '/admin/tweet/');
        $this->assertCount(1, $crawler->filter('table.records_list th')->eq(0)->filter('a i.fa-sort'));
        $crawler = $client->click($crawler->filter('table.records_list th a')->link());
        $crawler = $client->followRedirect();
        $this->assertTrue($client->getResponse()->isSuccessful());
        $this->assertCount(1, $crawler->filter('table.records_list th a i.fa-sort-up'));
    }
}
