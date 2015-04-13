<?php


namespace Idea\AdminBundle\Service;


use AppBundle\Entity\Author;
use AppBundle\Entity\Tag;
use AppBundle\Entity\Tweet;
use Doctrine\Common\Collections\ArrayCollection;

class TwitterFactory {

    public function createTags($status)
    {
        $ret = array();
        if ($status && isset($status['entities']['hashtags']))
        {
            foreach($status['entities']['hashtags'] as $elem)
            {
                if (isset ($elem['text'])) {
                    $tag = new Tag();
                    $tag->setName(strtolower($elem['text']));
                    $ret[] = $tag;
                }
            }
        }

        return new ArrayCollection($ret);
    }

    public function createAuthor($status)
    {
        $author = null;

        if ($status && isset($status['user']) )
        {
            $data = $status['user'];
            $author = new Author();
            $author->setName($this->safeCheck($data, 'name') );
            $author->setCode($this->safeCheck($data, 'id') );
            $author->setScreenName($this->safeCheck($data,'screen_name') );
            $author->setProfileImageUrl($this->safeCheck($data,'profile_image_url') );
            $author->setLocation($this->safeCheck($data,'location') );
            $author->setDescription($this->safeCheck($data,'description') );
            $author->setCreatedAt(new \DateTime($this->safeCheck($data,'created_at') ));
        }

        return $author;
    }


    public function createPost($status)
    {
        $tweet = null;

        if ($status && isset($status['id']))
        {
            $tweet = new Tweet();
            $tweet->setCode($status['id']);
            $tweet->setText($this->safeCheck($status,'text'));
            $tweet->setCreatedAt(new \DateTime($this->safeCheck($status,'created_at') ));
        }

        return $tweet;
    }

    /**
     * https://dev.twitter.com/overview/api/users
     *
     *  It is generally safe to consider a nulled field, an empty set, and the absence of a field as the same thing.
     *
     */
    private function safeCheck($data, $key)
    {
        $value = null;
        if ( isset($data[$key])  && $data[$key]!='' )
            $value = $data[$key];
        return $value;
    }
}