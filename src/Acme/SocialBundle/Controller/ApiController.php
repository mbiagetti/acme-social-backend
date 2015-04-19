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
                      "posts" => $this->generateUrl("acme_social_all_posts", array(), true),
                      "authors" => $this->generateUrl("acme_social_all_authors", array(), true),
                      "tags" => $this->generateUrl("acme_social_all_tags", array(), true)
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

        $page = $request->get(self::PAGE_CODE,self::PAGE_VALUE);
        $limit = $request->get(self::LIMIT_CODE,self::LIMIT_VALUE);

        $qb = $this->get('acme_social.api_repository')->getQueryBuilderForPosts();
        $query = $qb->getQuery();

        $pagination = $this->get('knp_paginator')->paginate($query, $page, $limit);

        $data['pagination'] = $this->get('acme_social.api_adapter')->paginationToArray($pagination->getPaginationData(), 'acme_social_all_posts');
        $data['posts']=$this->get('acme_social.api_adapter')->postsToArray($pagination);

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
        return $this->getJsonResponse($this->get('acme_social.api_adapter')->postToArray(($tweet)));
    }

    /**
     * @Route("/authors", name="acme_social_all_authors", requirements={"_method"="GET"})
     */
    public function authorsAction(Request $request )
    {
        $data = array();

        $page = $request->get(self::PAGE_CODE,self::PAGE_VALUE);
        $limit = $request->get(self::LIMIT_CODE,self::LIMIT_VALUE);

        $qb = $this->get('acme_social.api_repository')->getQueryBuilderForAuthors();
        $query = $qb->getQuery();

        $pagination = $this->get('knp_paginator')->paginate($query, $page, $limit);

        $data['pagination'] = $this->get('acme_social.api_adapter')->paginationToArray($pagination->getPaginationData(), 'acme_social_all_authors');
        $data['authors']=$this->get('acme_social.api_adapter')->authorsToArray($pagination);

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
        return $this->getJsonResponse($this->get('acme_social.api_adapter')->authorToArray($author));
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

        $page = $request->get(self::PAGE_CODE,self::PAGE_VALUE);
        $limit = $request->get(self::LIMIT_CODE,self::LIMIT_VALUE);

        $qb = $this->get('acme_social.api_repository')->getQueryBuilderForPostsByAuthor($author);
        $query = $qb->getQuery();

        $pagination = $this->get('knp_paginator')->paginate($query, $page, $limit);

        $data['pagination'] = $this->get('acme_social.api_adapter')->paginationToArray($pagination->getPaginationData(), 'acme_social_get_author_post', array('id' => $author->getId()));
        $data['posts']=$this->get('acme_social.api_adapter')->postsToArray($pagination);

        return $this->getJsonResponse($data);

    }

    /**
     * @Route("/tags", name="acme_social_all_tags", requirements={"_method"="GET"})
     */
    public function tagsAction(Request $request )
    {
        $data = array();

        $page = $request->get(self::PAGE_CODE,self::PAGE_VALUE);
        $limit = $request->get(self::LIMIT_CODE,self::LIMIT_VALUE);

        $qb = $this->get('acme_social.api_repository')->getQueryBuilderForTags();
        $query = $qb->getQuery();

        $pagination = $this->get('knp_paginator')->paginate($query, $page, $limit);

        $data['pagination'] = $this->get('acme_social.api_adapter')->paginationToArray($pagination->getPaginationData(), 'acme_social_all_tags');
        $data['tags']=$this->get('acme_social.api_adapter')->tagsToArray($pagination);

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
        return $this->getJsonResponse($this->get('acme_social.api_adapter')->tagToArray($tag));
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

        $page = $request->get(self::PAGE_CODE,self::PAGE_VALUE);
        $limit = $request->get(self::LIMIT_CODE,self::LIMIT_VALUE);

        $qb = $this->get('acme_social.api_repository')->getQueryBuilderForPostsByTag($tag);
        $query = $qb->getQuery();

        $pagination = $this->get('knp_paginator')->paginate($query, $page, $limit);

        $data['pagination'] = $this->get('acme_social.api_adapter')->paginationToArray($pagination->getPaginationData(), 'acme_social_get_tag_post',array('id' => $tag->getId()));
        $data['posts']= $this->get('acme_social.api_adapter')->postsToArray($pagination);

        return $this->getJsonResponse($data);
    }

    private function getJsonResponse($data)
    {
        return new JsonResponse($data, 200, array('Access-Control-Allow-Origin'=> '*'));
    }

}
