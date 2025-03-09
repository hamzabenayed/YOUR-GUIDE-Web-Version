<?php
namespace App\Controller\Mobile;

use App\Entity\Reclamation;
use App\Repository\ReclamationRepository;
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
 * @Route("/mobile/reclamation")
 */
class ReclamationMobileController extends AbstractController
{
    /**
     * @Route("", methods={"GET"})
     */
    public function index(ReclamationRepository $reclamationRepository): Response
    {
        $reclamations = $reclamationRepository->findAll();

        if ($reclamations) {
            return new JsonResponse($reclamations, 200);
        } else {
            return new JsonResponse([], 204);
        }
    }

    /**
     * @Route("/show", methods={"POST"})
     */
    public function show(Request $request, ReclamationRepository $reclamationRepository): Response
    {
        $reclamation = $reclamationRepository->find((int)$request->get("id"));

        if ($reclamation) {
            return new JsonResponse($reclamation, 200);
        } else {
            return new JsonResponse([], 204);
        }
    }

    /**
     * @Route("/add", methods={"POST"})
     */
    public function add(Request $request): JsonResponse
    {
        $reclamation = new Reclamation();

        return $this->manage($reclamation, $request);
    }

    /**
     * @Route("/edit", methods={"POST"})
     */
    public function edit(Request $request, ReclamationRepository $reclamationRepository): Response
    {
        $reclamation = $reclamationRepository->find((int)$request->get("id"));

        if (!$reclamation) {
            return new JsonResponse(null, 404);
        }

        return $this->manage($reclamation, $request);
    }

    public function manage($reclamation, $request): JsonResponse
    {   
        
        $reclamation->setUp(
            DateTime::createFromFormat("d-m-Y", $request->get("date")),
            $request->get("type"),
            $request->get("etat"),
            $request->get("description")
        );

        $entityManager = $this->getDoctrine()->getManager();
        $entityManager->persist($reclamation);
        $entityManager->flush();

        return new JsonResponse($reclamation, 200);
    }

    /**
     * @Route("/delete", methods={"POST"})
     */
    public function delete(Request $request, EntityManagerInterface $entityManager, ReclamationRepository $reclamationRepository): JsonResponse
    {
        $reclamation = $reclamationRepository->find((int)$request->get("id"));

        if (!$reclamation) {
            return new JsonResponse(null, 200);
        }

        $entityManager->remove($reclamation);
        $entityManager->flush();

        return new JsonResponse([], 200);
    }

    /**
     * @Route("/deleteAll", methods={"POST"})
     */
    public function deleteAll(EntityManagerInterface $entityManager, ReclamationRepository $reclamationRepository): Response
    {
        $reclamations = $reclamationRepository->findAll();

        foreach ($reclamations as $reclamation) {
            $entityManager->remove($reclamation);
            $entityManager->flush();
        }

        return new JsonResponse([], 200);
    }
    
}
