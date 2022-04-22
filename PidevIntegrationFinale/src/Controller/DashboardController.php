<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Entity\Commentaire;
use App\Form\CommentaireType;
use App\Repository\CommentaireRepository;


class DashboardController extends AbstractController
{
    /**
     * @Route("/commentairefront", name="commentairefront")
     */
    public function index(CommentaireRepository $commentaireRepository): Response
    {
        return $this->render('dashboard/index.html.twig', [
            'commentaires' => $commentaireRepository->findAll(),
        ]);
    }


}
