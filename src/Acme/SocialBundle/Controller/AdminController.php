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
 * @Route("/admin")
 */
class AdminController extends Controller
{
    /**
     * Lists all Tag entities.
     *
     * @Route("", name="admin_home")
     * @Method("GET")
     */
    public function homeAction()
    {
        return $this->redirect($this->generateUrl('admin_tweet'));
    }

}
