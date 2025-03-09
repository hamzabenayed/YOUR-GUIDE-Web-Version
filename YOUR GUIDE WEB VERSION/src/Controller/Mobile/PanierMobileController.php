<?php
namespace App\Controller\Mobile;

use App\Entity\Panier;
use App\Repository\PanierRepository;
use DateTime;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/mobile/panier")
 */
class PanierMobileController extends AbstractController
{
    /**
     * @Route("", methods={"GET"})
     */
    public function index(PanierRepository $panierRepository): Response
    {
        $paniers = $panierRepository->findAll();

        if ($paniers) {
            return new JsonResponse($paniers, 200);
        } else {
            return new JsonResponse([], 204);
        }
    }

    /**
     * @Route("/show", methods={"POST"})
     */
    public function show(Request $request, PanierRepository $panierRepository): Response
    {
        $panier = $panierRepository->find((int)$request->get("id"));

        if ($panier) {
            return new JsonResponse($panier, 200);
        } else {
            return new JsonResponse([], 204);
        }
    }

    /**
     * @Route("/add", methods={"POST"})
     */
    public function add(Request $request): JsonResponse
    {
        $panier = new Panier();

        return $this->manage($panier, $request);
    }

    /**
     * @Route("/edit", methods={"POST"})
     */
    public function edit(Request $request, PanierRepository $panierRepository): Response
    {
        $panier = $panierRepository->find((int)$request->get("id"));

        if (!$panier) {
            return new JsonResponse(null, 404);
        }

        return $this->manage($panier, $request);
    }

    public function manage($panier, $request): JsonResponse
    {   
        
        $panier->setUp(
            $request->get("description")
        );

        $entityManager = $this->getDoctrine()->getManager();
        $entityManager->persist($panier);
        $entityManager->flush();

        return new JsonResponse($panier, 200);
    }

    /**
     * @Route("/delete", methods={"POST"})
     */
    public function delete(Request $request, EntityManagerInterface $entityManager, PanierRepository $panierRepository): JsonResponse
    {
        $panier = $panierRepository->find((int)$request->get("id"));

        if (!$panier) {
            return new JsonResponse(null, 200);
        }

        $entityManager->remove($panier);
        $entityManager->flush();

        return new JsonResponse([], 200);
    }

    /**
     * @Route("/deleteAll", methods={"POST"})
     */
    public function deleteAll(EntityManagerInterface $entityManager, PanierRepository $panierRepository): Response
    {
        $paniers = $panierRepository->findAll();

        foreach ($paniers as $panier) {
            $entityManager->remove($panier);
            $entityManager->flush();
        }

        return new JsonResponse([], 200);
    }
    
}
