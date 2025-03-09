<?php

namespace App\Controller;
use App\Entity\Produit;
use App\Repository\ProduitRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Session\SessionInterface;

class PanierController extends AbstractController
{
    /**
     * @Route("/panier", name="panier")
     */
    public function index(SessionInterface $session, ProduitRepository  $ProduitRepository): Response
    {
        $panier = $session->get('panier', []);
        $panierwithData = [];

        $total = 0;
        Foreach ($panier as $id => $quantity) {
            $product = $ProduitRepository->find($id);
            $panierwithData[] = [


                'product' => $ProduitRepository->find($id),
                'quantity' => $quantity
            ];
            $total += $product->getPrix() * $quantity;
        }


        return $this->render('panier/affiche.html.twig', compact("panierwithData", "total"));

    }


    /**
     * @Route("/panier/add/{id}", name="add_cart")
     */
    public function add(Produit $product, SessionInterface $session)
    {
        // On récupère le panier actuel
        $panier = $session->get("panier", []);
        $id = $product->getId();

        if(!empty($panier[$id])){
            $panier[$id]++;
        }else{
            $panier[$id] = 1;
        }

        // On sauvegarde dans la session
        $session->set("panier", $panier);

        return $this->redirectToRoute("panier");
    }
}
