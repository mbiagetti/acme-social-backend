<?php

namespace Acme\SocialBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use SocialBundle\Entity\Author;
use Acme\SocialBundle\Form\Type\AuthorType;
use Acme\SocialBundle\Form\Type\AuthorFilterType;
use Symfony\Component\Form\FormInterface;
use Doctrine\ORM\QueryBuilder;

/**
 * Author controller.
 *
 * @Route("/admin/author")
 */
class AuthorController extends Controller
{
    /**
     * Lists all Author entities.
     *
     * @Route("/", name="admin_author")
     * @Method("GET")
     * @Template()
     */
    public function indexAction()
    {
        $em = $this->getDoctrine()->getManager();
        $form = $this->createForm(new AuthorFilterType());
        if (!is_null($response = $this->saveFilter($form, 'author', 'admin_author'))) {
            return $response;
        }
        $qb = $em->getRepository('SocialBundle:Author')->createQueryBuilder('a');
        $paginator = $this->filter($form, $qb, 'author');
        
        return array(
            'form'      => $form->createView(),
            'paginator' => $paginator,
        );
    }

    /**
     * Finds and displays a Author entity.
     *
     * @Route("/{id}/show", name="admin_author_show", requirements={"id"="\d+"})
     * @Method("GET")
     * @Template()
     */
    public function showAction(Author $author)
    {
        $deleteForm = $this->createDeleteForm($author->getId(), 'admin_author_delete');

        return array(
            'author' => $author,
            'delete_form' => $deleteForm->createView(),
        );
    }

    /**
     * Displays a form to create a new Author entity.
     *
     * @Route("/new", name="admin_author_new")
     * @Method("GET")
     * @Template()
     */
    public function newAction()
    {
        $author = new Author();
        $form = $this->createForm(new AuthorType(), $author);

        return array(
            'author' => $author,
            'form'   => $form->createView(),
        );
    }

    /**
     * Creates a new Author entity.
     *
     * @Route("/create", name="admin_author_create")
     * @Method("POST")
     * @Security("has_role('ROLE_ADMIN')")
     * @Template("AcmeSocialBundle:Author:new.html.twig")
     */
    public function createAction(Request $request)
    {
        $author = new Author();
        $form = $this->createForm(new AuthorType(), $author);
        if ($form->handleRequest($request)->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($author);
            $em->flush();

            return $this->redirect($this->generateUrl('admin_author_show', array('id' => $author->getId())));
        }

        return array(
            'author' => $author,
            'form'   => $form->createView(),
        );
    }

    /**
     * Displays a form to edit an existing Author entity.
     *
     * @Route("/{id}/edit", name="admin_author_edit", requirements={"id"="\d+"})
     * @Method("GET")
     * @Template()
     */
    public function editAction(Author $author)
    {
        $editForm = $this->createForm(new AuthorType(), $author, array(
            'action' => $this->generateUrl('admin_author_update', array('id' => $author->getId())),
            'method' => 'PUT',
        ));
        $deleteForm = $this->createDeleteForm($author->getId(), 'admin_author_delete');

        return array(
            'author' => $author,
            'edit_form'   => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        );
    }

    /**
     * Edits an existing Author entity.
     *
     * @Route("/{id}/update", name="admin_author_update", requirements={"id"="\d+"})
     * @Method("PUT")
     * @Security("has_role('ROLE_ADMIN')")
     * @Template("AcmeSocialBundle:Author:edit.html.twig")
     */
    public function updateAction(Author $author, Request $request)
    {
        $editForm = $this->createForm(new AuthorType(), $author, array(
            'action' => $this->generateUrl('admin_author_update', array('id' => $author->getId())),
            'method' => 'PUT',
        ));
        if ($editForm->handleRequest($request)->isValid()) {
            $this->getDoctrine()->getManager()->flush();

            return $this->redirect($this->generateUrl('admin_author_edit', array('id' => $author->getId())));
        }
        $deleteForm = $this->createDeleteForm($author->getId(), 'admin_author_delete');

        return array(
            'author' => $author,
            'edit_form'   => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        );
    }


    /**
     * Save order.
     *
     * @Route("/order/{field}/{type}", name="admin_author_sort")
     */
    public function sortAction($field, $type)
    {
        $this->setOrder('author', $field, $type);

        return $this->redirect($this->generateUrl('admin_author'));
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
     * Deletes a Author entity.
     *
     * @Route("/{id}/delete", name="admin_author_delete", requirements={"id"="\d+"})
     * @Security("has_role('ROLE_ADMIN')")
     * @Method("DELETE")
     */
    public function deleteAction(Author $author, Request $request)
    {
        $form = $this->createDeleteForm($author->getId(), 'admin_author_delete');
        if ($form->handleRequest($request)->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->remove($author);
            $em->flush();
        }

        return $this->redirect($this->generateUrl('admin_author'));
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
