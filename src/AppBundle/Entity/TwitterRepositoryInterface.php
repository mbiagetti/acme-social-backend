<?php


namespace AppBundle\Entity;


interface TwitterRepositoryInterface {

    public function persistTweet($tweet);

    public function findTweetByCode($code);

    public function findTagByName($tagName);

    public function findAuthorByCode($code);

}