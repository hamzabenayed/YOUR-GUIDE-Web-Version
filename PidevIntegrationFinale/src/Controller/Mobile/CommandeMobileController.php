<?php
namespace App\Controller\Mobile;

use App\Entity\Commande;
use App\Repository\CommandeRepository;
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
 * @Route("/mobile/commande")
 */
class CommandeMobileController extends AbstractController
{
    /**
     * @Route("", methods={"GET"})
     */
    public function index(CommandeRepository $commandeRepository): Response
    {
        $commandes = $commandeRepository->findAll();

        if ($commandes) {
            return new JsonResponse($commandes, 200);
        } else {
            return new JsonResponse([], 204);
        }
    }

    /**
     * @Route("/show", methods={"POST"})
     */
    public function show(Request $request, CommandeRepository $commandeRepository): Response
    {
        $commande = $commandeRepository->find((int)$request->get("id"));

        if ($commande) {
            return new JsonResponse($commande, 200);
        } else {
            return new JsonResponse([], 204);
        }
    }

    /**
     * @Route("/add", methods={"POST"})
     */
    public function add(Request $request): JsonResponse
    {
        $commande = new Commande();

        return $this->manage($commande, $request);
    }

    /**
     * @Route("/edit", methods={"POST"})
     */
    public function edit(Request $request, CommandeRepository $commandeRepository): Response
    {
        $commande = $commandeRepository->find((int)$request->get("id"));

        if (!$commande) {
            return new JsonResponse(null, 404);
        }

        return $this->manage($commande, $request);
    }

    public function manage($commande, $request): JsonResponse
    {   
        
        $commande->setUp(
            DateTime::createFromFormat("d-m-Y", $request->get("date")),
            $request->get("etat"),
            $request->get("commentaire"),
            $request->get("adresse"),
            $request->get("totalcost"),
            $request->get("product")
        );

        $entityManager = $this->getDoctrine()->getManager();
        $entityManager->persist($commande);
        $entityManager->flush();

        return new JsonResponse($commande, 200);
    }

    /**
     * @Route("/delete", methods={"POST"})
     */
    public function delete(Request $request, EntityManagerInterface $entityManager, CommandeRepository $commandeRepository): JsonResponse
    {
        $commande = $commandeRepository->find((int)$request->get("id"));

        if (!$commande) {
            return new JsonResponse(null, 200);
        }

        $entityManager->remove($commande);
        $entityManager->flush();

        return new JsonResponse([], 200);
    }

    /**
     * @Route("/deleteAll", methods={"POST"})
     */
    public function deleteAll(EntityManagerInterface $entityManager, CommandeRepository $commandeRepository): Response
    {
        $commandes = $commandeRepository->findAll();

        foreach ($commandes as $commande) {
            $entityManager->remove($commande);
            $entityManager->flush();
        }

        return new JsonResponse([], 200);
    }
    
}
