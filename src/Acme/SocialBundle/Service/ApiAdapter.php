<?php


namespace Acme\SocialBundle\Service;


class ApiAdapter {

    /**
     * @var \Symfony\Component\Routing\Generator\UrlGeneratorInterface
     */
    private $urlGenerator;

    const STANDARD_DATE_FORMAT = 'c';

    function __construct($urlGenerator)
    {
        $this->urlGenerator = $urlGenerator;
    }

    public function postsToArray($posts)
    {
        $contents = array();

        foreach($posts as $elem)
            $contents[] = $this->postToArray($elem);

        return $contents;
    }


    /**
     * @param $elem \SocialBundle\Entity\Tweet
     * @return array
     */
    public function postToArray($elem)
    {
        return array(
            'id'    => $elem->getId(),
            'code'  => $elem->getCode(),
            'text'  => $elem->getText(),
            'author'=> $this->authorToArray($elem->getAuthor()),
            'date'  => $this->formatDate($elem->getCreatedAt()),
            'social_url' => $this->getSocialUrlForTweet($elem),
            'links' =>
                array(
                    array(
                        'rel'   => 'self',
                        'href'  =>   $this->generateUrl('acme_social_get_post', array('id'=>$elem->getId() ) )
                    )
                ),
            'tags' => $this->tagsToArray($elem->getTags())
        );
    }

    /**
     * @param $tags \SocialBundle\Entity\Tag[]
     * @return array
     */
    public function tagsToArray($tags)
    {
        $ret = array();
        foreach($tags as $tag)
            $ret[] = $this->tagToArray($tag);
        return $ret;
    }

    /**
     * @param $tag \SocialBundle\Entity\Tag
     * @return array
     */
    public function tagToArray($tag)
    {
        $ret = array(
            'id' => $tag->getId(),
            'name' => $tag->getName(),
            'links' =>
                array(
                    array(
                        'rel'   => 'self',
                        'href'  =>  $this->generateUrl("acme_social_get_tag",array("id" => $tag->getId()))
                    ),
                    array(
                        'rel'   => 'posts',
                        'href'  =>  $this->generateUrl("acme_social_get_tag_post",array("id" => $tag->getId()))
                    )
                )
        );
        return $ret;
    }

    public function authorsToArray($authors)
    {
        $contents = array();

        foreach($authors as $elem)
            $contents[] = $this->authorToArray($elem);

        return $contents;
    }


    /**
     * @param $author \SocialBundle\Entity\Author
     * @return array
     */
    public function authorToArray($author)
    {
        return array(
            'id'       => $author->getId(),
            'name'      => $author->getName(),
            'code'      => $author->getCode(),
            'screen_name'=> $author->getScreenName(),
            'profile_image_url'=> $author->getProfileImageUrl(),
            'description'=> $author->getDescription(),
            'location' => $author->getLocation(),
            'date'  => $this->formatDate($author->getCreatedAt()),
            'social_url' => $this->getSocialUrlForAuthor($author),
            'links' =>
                array(
                    array(
                        'rel'   => 'self',
                        'href'  => $this->generateUrl('acme_social_get_author',array('id' => $author->getId())),
                    ),
                    array(
                        'rel'   => 'posts',
                        'href'   => $this->generateUrl('acme_social_get_author_post',array('id' => $author->getId())),
                    ),
                )
        );
    }


    public function paginationToArray($paginationData, $routeName, $mandatoryParams = array())
    {
        $page = $paginationData['current'];
        $limit= $paginationData['numItemsPerPage'];
        $pagination = array();

        $pagination['page'] = $page;
        $pagination['limit'] = $limit;
        $pagination['totalPage'] = $paginationData['pageCount'];
        $pagination['totalCount'] = $paginationData['totalCount'];

        $pagination['links'] = array();
        if (isset($paginationData['previous'])){
            $elem = array(
                'rel' => 'prev',
                'href' =>  $this->generateUrl($routeName, ($mandatoryParams + array('limit'=>$limit, 'page'=>$paginationData['previous'])))
            );
            $pagination['links'][] = $elem;
        }
        if (isset($paginationData['next'])){
            $elem = array(
                'rel' => 'next',
                'href'=>  $this->generateUrl($routeName, ($mandatoryParams + array('limit'=>$limit, 'page'=>$paginationData['next'])))
            );
            $pagination['links'][] = $elem;

        }
        return $pagination;
    }


    private function generateUrl($routeName, $params)
    {
        return  $this->urlGenerator->generate($routeName, $params, true);
    }

    /**
     * @param $date \DateTime
     * @return string
     */
    private function formatDate($date)
    {
        return $date->format(self::STANDARD_DATE_FORMAT);
    }

    private function getSocialUrlForAuthor($author)
    {
        return sprintf("https://twitter.com/%s", $author->getScreenName() );
    }

    private function getSocialUrlForTweet($elem)
    {
        return sprintf("https://twitter.com/%s/status/%s", $elem->getAuthor()->getScreenName(), $elem->getCode() );
    }


}