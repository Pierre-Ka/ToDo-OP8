<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;

class DefaultController extends AbstractController
{
    /**
     * @Route("/", name="homepage")
     */
    public function indexAction()
    {
        if (!$this->getUser())
        {
            return $this->redirectToRoute('login', []);
        }
        return $this->render('default/index.html.twig');
    }
}
