<?php
namespace Idea\AdminBundle\Service;


use Guzzle\Http\Exception\RequestException;

class Twitter {

    /**
     * @var \Guzzle\Service\Client
     */
    protected $client;

    function __construct($client)
    {
        $this->client = $client;
    }

    public function findBy($query = "php", $limit = 100)
    {
        $url = sprintf("%s?q=%%23%s&count=%d",
            'search/tweets.json',
            $query,
            $limit
        );

        try {
            $response = $this->client->get($url)->send();
            return $response->json();
        } catch (RequestException $e) {
            throw $e;
        }
    }
}