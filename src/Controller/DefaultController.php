<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;

class DefaultController extends AbstractController
{
    #[Route('/', name: 'homepage', methods: ['GET'])]
    public function index()
    {
        if (!$this->getUser())
        {
            return $this->redirectToRoute('login', []);
        }
        return $this->render('default/index.html.twig');
    }
}