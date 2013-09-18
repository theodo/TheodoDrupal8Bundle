<?php

namespace Theodo\Bundle\Drupal8Bundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\RedirectResponse;

// these import the "@Route" and "@Template" annotations
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;

class DemoController extends Controller
{
    /**
     * @Template()
     */
    public function indexAction()
    {
        $node = $this->get('theodo_drupal8.drupal_wrapper')->getNode(1);

        $currentUser = $this->get('theodo_drupal8.drupal_wrapper')->getCurrentUser();

        return array('content' => $node->body->value, 'user' => $currentUser);
    }

}
