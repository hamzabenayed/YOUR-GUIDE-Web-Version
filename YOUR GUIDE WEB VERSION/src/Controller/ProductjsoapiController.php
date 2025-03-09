<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class ProductjsoapiController extends AbstractController
{
    /**
     * @Route("/productjsoapi", name="productjsoapi")
     */
    public function index(): Response
    {
        return $this->render('productjsoapi/index.html.twig', [
            'controller_name' => 'ProductjsoapiController',
        ]);
    }
}
