<?php

namespace Acme\SocialBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use SocialBundle\Entity\Tweet;
use Acme\SocialBundle\Form\Type\TweetType;
use Acme\SocialBundle\Form\Type\TweetFilterType;
use Symfony\Component\Form\FormInterface;
use Doctrine\ORM\QueryBuilder;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;

/**
 * Tweet controller.
 *
 * @Route("/admin/tweet")
 */
class TweetController extends Controller
{
    /**
     * Lists all Tweet entities.
     *
     * @Route("/", name="admin_tweet")
     * @Method("GET")
     * @Template()
     */
    public function indexAction()
    {
        $em = $this->getDoctrine()->getManager();
        $form = $this->createForm(new TweetFilterType());
        if (!is_null($response = $this->saveFilter($form, 'tweet', 'admin_tweet'))) {
            return $response;
        }
        $qb = $em->getRepository('SocialBundle:Tweet')->createQueryBuilder('t');
        $paginator = $this->filter($form, $qb, 'tweet');
        
        return array(
            'form'      => $form->createView(),
            'paginator' => $paginator,
        );
    }

    /**
     * Finds and displays a Tweet entity.
     *
     * @Route("/{id}/show", name="admin_tweet_show", requirements={"id"="\d+"})
     * @Method("GET")
     * @Template()
     */
    public function showAction(Tweet $tweet)
    {
        $deleteForm = $this->createDeleteForm($tweet->getId(), 'admin_tweet_delete');

        return array(
            'tweet' => $tweet,
            'delete_form' => $deleteForm->createView(),
        );
    }

    /**
     * Displays a form to create a new Tweet entity.
     *
     * @Route("/new", name="admin_tweet_new")
     * @Method("GET")
     * @Template()
     */
    public function newAction()
    {
        $tweet = new Tweet();
        $form = $this->createForm(new TweetType(), $tweet);

        return array(
            'tweet' => $tweet,
            'form'   => $form->createView(),
        );
    }

    /**
     * Creates a new Tweet entity.
     *
     * @Route("/create", name="admin_tweet_create")
     * @Method("POST")
     * @Security("has_role('ROLE_ADMIN')")
     * @Template("AcmeSocialBundle:Tweet:new.html.twig")
     */
    public function createAction(Request $request)
    {
        $tweet = new Tweet();
        $form = $this->createForm(new TweetType(), $tweet);
        if ($form->handleRequest($request)->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($tweet);
            $em->flush();

            return $this->redirect($this->generateUrl('admin_tweet_show', array('id' => $tweet->getId())));
        }

        return array(
            'tweet' => $tweet,
            'form'   => $form->createView(),
        );
    }

    /**
     * Displays a form to edit an existing Tweet entity.
     *
     * @Route("/{id}/edit", name="admin_tweet_edit", requirements={"id"="\d+"})
     * @Method("GET")
     * @Template()
     */
    public function editAction(Tweet $tweet)
    {
        $editForm = $this->createForm(new TweetType(), $tweet, array(
            'action' => $this->generateUrl('admin_tweet_update', array('id' => $tweet->getId())),
            'method' => 'PUT',
        ));
        $deleteForm = $this->createDeleteForm($tweet->getId(), 'admin_tweet_delete');

        return array(
            'tweet' => $tweet,
            'edit_form'   => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        );
    }

    /**
     * Edits an existing Tweet entity.
     *
     * @Route("/{id}/update", name="admin_tweet_update", requirements={"id"="\d+"})
     * @Method("PUT")
     * @Security("has_role('ROLE_ADMIN')")
     * @Template("AcmeSocialBundle:Tweet:edit.html.twig")
     */
    public function updateAction(Tweet $tweet, Request $request)
    {
        $editForm = $this->createForm(new TweetType(), $tweet, array(
            'action' => $this->generateUrl('admin_tweet_update', array('id' => $tweet->getId())),
            'method' => 'PUT',
        ));
        if ($editForm->handleRequest($request)->isValid()) {
            $this->getDoctrine()->getManager()->flush();

            return $this->redirect($this->generateUrl('admin_tweet_edit', array('id' => $tweet->getId())));
        }
        $deleteForm = $this->createDeleteForm($tweet->getId(), 'admin_tweet_delete');

        return array(
            'tweet' => $tweet,
            'edit_form'   => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        );
    }


    /**
     * Save order.
     *
     * @Route("/order/{field}/{type}", name="admin_tweet_sort")
     */
    public function sortAction($field, $type)
    {
        $this->setOrder('tweet', $field, $type);

        return $this->redirect($this->generateUrl('admin_tweet'));
    }

    /**
     * @param string $name  session name
     * @param string $field field name
     * @param string $type  sort type ("ASC"/"DESC")
     */
    protected function setOrder($name, $field, $type = 'ASC')
    {
        $this->getRequest()->getSession()->set('sort.' . $name, array('field' => $field, 'type' => $type));
    }

    /**
     * @param  string $name
     * @return array
     */
    protected function getOrder($name)
    {
        $session = $this->getRequest()->getSession();

        return $session->has('sort.' . $name) ? $session->get('sort.' . $name) : null;
    }

    /**
     * @param QueryBuilder $qb
     * @param string       $name
     */
    protected function addQueryBuilderSort(QueryBuilder $qb, $name)
    {
        $alias = current($qb->getDQLPart('from'))->getAlias();
        if (is_array($order = $this->getOrder($name))) {
            $qb->orderBy($alias . '.' . $order['field'], $order['type']);
        }
    }

    /**
     * Save filters
     *
     * @param  FormInterface $form
     * @param  string        $name   route/entity name
     * @param  string        $route  route name, if different from entity name
     * @param  array         $params possible route parameters
     * @return Response
     */
    protected function saveFilter(FormInterface $form, $name, $route = null, array $params = null)
    {
        $request = $this->getRequest();
        $url = $this->generateUrl($route ?: $name, is_null($params) ? array() : $params);
        if ($request->query->has('submit-filter') && $form->handleRequest($request)->isValid()) {
            $request->getSession()->set('filter.' . $name, $request->query->get($form->getName()));

            return $this->redirect($url);
        } elseif ($request->query->has('reset-filter')) {
            $request->getSession()->set('filter.' . $name, null);

            return $this->redirect($url);
        }
    }

    /**
     * Filter form
     *
     * @param  FormInterface                                       $form
     * @param  QueryBuilder                                        $qb
     * @param  string                                              $name
     * @return \Knp\Component\Pager\Pagination\PaginationInterface
     */
    protected function filter(FormInterface $form, QueryBuilder $qb, $name)
    {
        if (!is_null($values = $this->getFilter($name))) {
            if ($form->submit($values)->isValid()) {
                $this->get('lexik_form_filter.query_builder_updater')->addFilterConditions($form, $qb);
            }
        }

        // possible sorting
        $this->addQueryBuilderSort($qb, $name);
        return $this->get('knp_paginator')->paginate($qb, $this->getRequest()->query->get('page', 1), 20);
    }

    /**
     * Get filters from session
     *
     * @param  string $name
     * @return array
     */
    protected function getFilter($name)
    {
        return $this->getRequest()->getSession()->get('filter.' . $name);
    }

    /**
     * Deletes a Tweet entity.
     *
     * @Route("/{id}/delete", name="admin_tweet_delete", requirements={"id"="\d+"})
     * @Security("has_role('ROLE_ADMIN')")
     * @Method("DELETE")
     */
    public function deleteAction(Tweet $tweet, Request $request)
    {
        $form = $this->createDeleteForm($tweet->getId(), 'admin_tweet_delete');
        if ($form->handleRequest($request)->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->remove($tweet);
            $em->flush();
        }

        return $this->redirect($this->generateUrl('admin_tweet'));
    }

    /**
     * Create Delete form
     *
     * @param integer                       $id
     * @param string                        $route
     * @return \Symfony\Component\Form\Form
     */
    protected function createDeleteForm($id, $route)
    {
        return $this->createFormBuilder(null, array('attr' => array('id' => 'delete')))
            ->setAction($this->generateUrl($route, array('id' => $id)))
            ->setMethod('DELETE')
            ->getForm()
        ;
    }

}
