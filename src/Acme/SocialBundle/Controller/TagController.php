<?php

namespace Acme\SocialBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use SocialBundle\Entity\Tag;
use Acme\SocialBundle\Form\Type\TagType;
use Acme\SocialBundle\Form\Type\TagFilterType;
use Symfony\Component\Form\FormInterface;
use Doctrine\ORM\QueryBuilder;

/**
 * Tag controller.
 *
 * @Route("/admin/tag")
 */
class TagController extends Controller
{
    /**
     * Lists all Tag entities.
     *
     * @Route("/", name="admin_tag")
     * @Method("GET")
     * @Template()
     */
    public function indexAction()
    {
        $em = $this->getDoctrine()->getManager();
        $form = $this->createForm(new TagFilterType());
        if (!is_null($response = $this->saveFilter($form, 'tag', 'admin_tag'))) {
            return $response;
        }
        $qb = $em->getRepository('SocialBundle:Tag')->createQueryBuilder('t');
        $paginator = $this->filter($form, $qb, 'tag');
        
        return array(
            'form'      => $form->createView(),
            'paginator' => $paginator,
        );
    }

    /**
     * Finds and displays a Tag entity.
     *
     * @Route("/{id}/show", name="admin_tag_show", requirements={"id"="\d+"})
     * @Method("GET")
     * @Template()
     */
    public function showAction(Tag $tag)
    {
        $deleteForm = $this->createDeleteForm($tag->getId(), 'admin_tag_delete');

        return array(
            'tag' => $tag,
            'delete_form' => $deleteForm->createView(),
        );
    }

    /**
     * Displays a form to create a new Tag entity.
     *
     * @Route("/new", name="admin_tag_new")
     * @Method("GET")
     * @Template()
     */
    public function newAction()
    {
        $tag = new Tag();
        $form = $this->createForm(new TagType(), $tag);

        return array(
            'tag' => $tag,
            'form'   => $form->createView(),
        );
    }

    /**
     * Creates a new Tag entity.
     *
     * @Route("/create", name="admin_tag_create")
     * @Method("POST")
     * @Security("has_role('ROLE_ADMIN')")
     * @Template("AcmeSocialBundle:Tag:new.html.twig")
     */
    public function createAction(Request $request)
    {
        $tag = new Tag();
        $form = $this->createForm(new TagType(), $tag);
        if ($form->handleRequest($request)->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($tag);
            $em->flush();

            return $this->redirect($this->generateUrl('admin_tag_show', array('id' => $tag->getId())));
        }

        return array(
            'tag' => $tag,
            'form'   => $form->createView(),
        );
    }

    /**
     * Displays a form to edit an existing Tag entity.
     *
     * @Route("/{id}/edit", name="admin_tag_edit", requirements={"id"="\d+"})
     * @Method("GET")
     * @Template()
     */
    public function editAction(Tag $tag)
    {
        $editForm = $this->createForm(new TagType(), $tag, array(
            'action' => $this->generateUrl('admin_tag_update', array('id' => $tag->getId())),
            'method' => 'PUT',
        ));
        $deleteForm = $this->createDeleteForm($tag->getId(), 'admin_tag_delete');

        return array(
            'tag' => $tag,
            'edit_form'   => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        );
    }

    /**
     * Edits an existing Tag entity.
     *
     * @Route("/{id}/update", name="admin_tag_update", requirements={"id"="\d+"})
     * @Method("PUT")
     * @Security("has_role('ROLE_ADMIN')")
     * @Template("AcmeSocialBundle:Tag:edit.html.twig")
     */
    public function updateAction(Tag $tag, Request $request)
    {
        $editForm = $this->createForm(new TagType(), $tag, array(
            'action' => $this->generateUrl('admin_tag_update', array('id' => $tag->getId())),
            'method' => 'PUT',
        ));
        if ($editForm->handleRequest($request)->isValid()) {
            $this->getDoctrine()->getManager()->flush();

            return $this->redirect($this->generateUrl('admin_tag_edit', array('id' => $tag->getId())));
        }
        $deleteForm = $this->createDeleteForm($tag->getId(), 'admin_tag_delete');

        return array(
            'tag' => $tag,
            'edit_form'   => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        );
    }


    /**
     * Save order.
     *
     * @Route("/order/{field}/{type}", name="admin_tag_sort")
     */
    public function sortAction($field, $type)
    {
        $this->setOrder('tag', $field, $type);

        return $this->redirect($this->generateUrl('admin_tag'));
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
     * Deletes a Tag entity.
     *
     * @Route("/{id}/delete", name="admin_tag_delete", requirements={"id"="\d+"})
     * @Method("DELETE")
     * @Security("has_role('ROLE_ADMIN')")
     */
    public function deleteAction(Tag $tag, Request $request)
    {
        $form = $this->createDeleteForm($tag->getId(), 'admin_tag_delete');
        if ($form->handleRequest($request)->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->remove($tag);
            $em->flush();
        }

        return $this->redirect($this->generateUrl('admin_tag'));
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
