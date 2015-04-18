<?php

namespace Acme\SocialBundle\Controller;

use SocialBundle\Entity\Author;
use SocialBundle\Entity\Tag;
use SocialBundle\Entity\Tweet;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;

/**
 * @Route("/api")
 */
class ApiController extends Controller
{

    const STANDARD_DATE_FORMAT = 'Y-m-d H:i';

    const PAGE_CODE  = "page";
    const LIMIT_CODE = "limit";

    const PAGE_VALUE  = 1;
    const LIMIT_VALUE = 10;

    /**
     * @Route("", name="acme_social_api", requirements={"_method"="GET"})
     */
    public function indexAction( )
    {
        return
          $this->getJsonResponse(
              array(
                "title" => "Acme Demo Api Service",
                  "Description" => "Only for fun!",
                  "endpoints" => array(
                      "posts" => $this->generateUrl("acme_social_all_posts"),
                      "authors" => $this->generateUrl("acme_social_all_authors"),
                      "tags" => $this->generateUrl("acme_social_all_tags")
                  )
              )
          );
    }

    /**
     * @Route("/posts", name="acme_social_all_posts", requirements={"_method"="GET"})
     */
    public function postsAction(Request $request )
    {
        $data = array();
        $contents = array();

        $page = $request->get(self::PAGE_CODE,self::PAGE_VALUE);
        $limit = $request->get(self::LIMIT_CODE,self::LIMIT_VALUE);

        $qb = $this->getDoctrine()->getRepository("SocialBundle:Tweet")->createQueryBuilder("p");
        $qb->addOrderBy("p.created_at","desc");
        $query = $qb->getQuery();

        $pagination = $this
            ->get('knp_paginator')
            ->paginate($query, $page, $limit);

        foreach($pagination as $elem)
            $contents[] = $this->getTweet($elem);

        $data['pagination'] = $this->getPaginationData($pagination->getPaginationData(), 'acme_social_all_posts');
        $data['posts']=$contents;

        return $this->getJsonResponse($data);
    }

    /**
     *
     * @Route("/posts/{id}", name="acme_social_get_post", requirements={"_method"="GET", "id"="\d+"})
     *
     * @param $tweet \SocialBundle\Entity\Tweet
     * @return JsonResponse
     */
    public function postsDetailAction(Tweet $tweet )
    {
        return $this->getJsonResponse($this->getTweet($tweet));
    }

    /**
     * @Route("/authors", name="acme_social_all_authors", requirements={"_method"="GET"})
     */
    public function authorsAction(Request $request )
    {
        $data = array();
        $contents = array();

        $page = $request->get(self::PAGE_CODE,self::PAGE_VALUE);
        $limit = $request->get(self::LIMIT_CODE,self::LIMIT_VALUE);

        $qb = $this->getDoctrine()->getRepository("SocialBundle:Author")->createQueryBuilder("p");
        $qb->addOrderBy("p.created_at","desc");
        $query = $qb->getQuery();

        $pagination = $this
            ->get('knp_paginator')
            ->paginate($query, $page, $limit);


        foreach($pagination as $elem)
            $contents[] = $this->getAuthor($elem);

        $data['pagination'] = $this->getPaginationData($pagination->getPaginationData(), 'acme_social_all_authors');
        $data['authors']=$contents;

        return $this->getJsonResponse($data);
    }



    /**
     *
     * @Route("/authors/{id}", name="acme_social_get_author", requirements={"_method"="GET", "id"="\d+"})
     *
     * @param $author \SocialBundle\Entity\Author
     * @return JsonResponse
     */
    public function authorDetailAction(Author $author)
    {
        return $this->getJsonResponse($this->getAuthor($author));
    }

    /**
     *
     * @Route("/authors/{id}/posts", name="acme_social_get_author_post", requirements={"_method"="GET", "id"="\d+"})
     *
     * @param $author \SocialBundle\Entity\Author
     * @return JsonResponse
     */
    public function authorPostsAction(Author $author, Request $request)
    {
        $data = array();
        $contents = array();

        $page = $request->get(self::PAGE_CODE,self::PAGE_VALUE);
        $limit = $request->get(self::LIMIT_CODE,self::LIMIT_VALUE);

        $qb = $this->getDoctrine()->getRepository("SocialBundle:Tweet")->createQueryBuilder("p");
        $qb->where('p.author = :author')->setParameter("author",$author->getId());
        $qb->addOrderBy("p.created_at","desc");
        $query = $qb->getQuery();

        $pagination = $this
            ->get('knp_paginator')
            ->paginate($query, $page, $limit);

        foreach($pagination as $elem)
            $contents[] = $this->getTweet($elem);

        $data['pagination'] = $this->getPaginationData($pagination->getPaginationData(), 'acme_social_get_author_post', array('id' => $author->getId()));
        $data['posts']=$contents;

        return $this->getJsonResponse($data);

    }

    /**
     * @Route("/tags", name="acme_social_all_tags", requirements={"_method"="GET"})
     */
    public function tagsAction(Request $request )
    {
        $data = array();
        $contents = array();
        $page = $request->get(self::PAGE_CODE,self::PAGE_VALUE);
        $limit = $request->get(self::LIMIT_CODE,self::LIMIT_VALUE);

        $qb = $this->getDoctrine()->getRepository("SocialBundle:Tag")->createQueryBuilder("p");
        $qb->addOrderBy("p.name","asc");
        $query = $qb->getQuery();

        $pagination = $this
            ->get('knp_paginator')
            ->paginate($query, $page, $limit);

        foreach($pagination as $elem)
            $contents[] = $this->getTag($elem);

        $data['pagination'] = $this->getPaginationData($pagination->getPaginationData(), 'acme_social_all_tags');
        $data['tags']=$contents;

        return $this->getJsonResponse($data);
    }

    /**
     *
     * @Route("/tags/{id}", name="acme_social_get_tag", requirements={"_method"="GET", "id"="\d+"})
     *
     * @param $author \SocialBundle\Entity\Tag
     * @return JsonResponse
     */
    public function tagDetailAction(Tag $tag)
    {
        return $this->getJsonResponse($this->getTag($tag));
    }


    /**
     *
     * @Route("/tags/{id}/posts", name="acme_social_get_tag_post", requirements={"_method"="GET", "id"="\d+"})
     *
     * @param $tag \SocialBundle\Entity\Tag
     * @param $request \Symfony\Component\HttpFoundation\Request
     * @return JsonResponse
     */
    public function tagPostsAction(Tag $tag, Request $request)
    {
        $data = array();
        $contents = array();

        $page = $request->get(self::PAGE_CODE,self::PAGE_VALUE);
        $limit = $request->get(self::LIMIT_CODE,self::LIMIT_VALUE);

        $qb = $this->getDoctrine()->getRepository("SocialBundle:Tweet")->createQueryBuilder("p");
        $qb->leftJoin("p.tags","t");
        $qb->where('t = :tag')->setParameter("tag",$tag->getId());
        $qb->addOrderBy("p.created_at","desc");
        $query = $qb->getQuery();

        $pagination = $this
            ->get('knp_paginator')
            ->paginate($query, $page, $limit);

        foreach($pagination as $elem)
            $contents[] = $this->getTweet($elem);

        $data['pagination'] = $this->getPaginationData($pagination->getPaginationData(), 'acme_social_get_tag_post',array('id' => $tag->getId()));
        $data['posts']=$contents;

        return $this->getJsonResponse($data);

    }



    /**
     * @param $elem \SocialBundle\Entity\Tweet
     * @return array
     */
    private function getTweet($elem)
    {
        return array(
            'id'    => $elem->getId(),
            'code'  => $elem->getCode(),
            'text'  => $elem->getText(),
            'author'=> $this->getAuthor($elem->getAuthor()),
            'date'  => $this->formatDate($elem->getCreatedAt()),
            '_links' =>array(
                'self'  => $this->generateUrl('acme_social_get_post', array('id'=>$elem->getId() ), true ),
                'social'=> $this->getSocialUrlForTweet($elem)
            ),
            'tags' => $this->getTags($elem->getTags())
        );
    }

    /**
     * @param $tags \SocialBundle\Entity\Tag[]
     * @return array
     */
    private function getTags($tags)
    {
        $ret = array();
        foreach($tags as $tag)
            $ret[] = $this->getTag($tag);
        return $ret;
    }

    /**
     * @param $tag \SocialBundle\Entity\Tag
     * @return array
     */
    private function getTag($tag)
    {
        $ret = array(
            'id' => $tag->getId(),
            'name' => $tag->getName(),
            '_links' => array(
                'self' => $this->generateUrl("acme_social_get_tag",array("id" => $tag->getId()),true),
                'posts' => $this->generateUrl("acme_social_get_tag_post",array("id" => $tag->getId()),true),
            )
        );
        return $ret;
    }


    /**
     * @param $author \SocialBundle\Entity\Author
     * @return array
     */
    private function getAuthor($author)
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
            '_links' =>array(
                'self' => $this->generateUrl('acme_social_get_author',array('id' => $author->getId()),true),
                'posts' => $this->generateUrl('acme_social_get_author_post',array('id' => $author->getId()),true),
                'social'=> $this->getSocialUrlForAuthor($author)
            )
        );
    }

    private function getSocialUrlForTweet($elem)
    {
        return sprintf("https://twitter.com/%s/status/%s", $elem->getAuthor()->getScreenName(), $elem->getCode() );
    }

    /**
     * @param $date \DateTime
     * @return string
     */
    private function formatDate($date)
    {
        return $date->format(self::STANDARD_DATE_FORMAT);
    }




    private function getPaginationData($paginationData, $routeName, $mandatoryParams = array())
    {
        $page = $paginationData['current'];
        $limit= $paginationData['numItemsPerPage'];
        $pagination = array();

        $pagination['page'] = $page;
        $pagination['limit'] = $limit;
        $pagination['totalPage'] = $paginationData['pageCount'];
        $pagination['totalCount'] = $paginationData['totalCount'];

        if (isset($paginationData['previous']))
            $pagination['prev'] =  $this->generateUrl($routeName, ($mandatoryParams + array('limit'=>$limit, 'page'=>$paginationData['previous'])),true);

        if (isset($paginationData['next']))
            $pagination['next'] =  $this->generateUrl($routeName, ($mandatoryParams + array('limit'=>$limit, 'page'=>$paginationData['next'])),true);

        return $pagination;
    }

    private function getJsonResponse($data)
    {
        return new JsonResponse($data);
    }

    private function getSocialUrlForAuthor($author)
    {
        return sprintf("https://twitter.com/%s", $author->getScreenName() );
    }

}
