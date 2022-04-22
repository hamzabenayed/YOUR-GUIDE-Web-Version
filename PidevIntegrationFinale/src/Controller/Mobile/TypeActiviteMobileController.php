<?php
namespace App\Controller\Mobile;

use App\Entity\ TypeActivite;
use App\Repository\ TypeActiviteRepository;
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
 * @Route("/mobile/typeActivite")
 */
class TypeActiviteMobileController extends AbstractController
{
    /**
     * @Route("", methods={"GET"})
     */
    public function index(TypeActiviteRepository $typeActiviteRepository): Response
    {
        $typeActivites = $typeActiviteRepository->findAll();

        if ($typeActivites) {
            return new JsonResponse($typeActivites, 200);
        } else {
            return new JsonResponse([], 204);
        }
    }

    /**
     * @Route("/show", methods={"POST"})
     */
    public function show(Request $request, TypeActiviteRepository $typeActiviteRepository): Response
    {
        $typeActivite = $typeActiviteRepository->find((int)$request->get("id"));

        if ($typeActivite) {
            return new JsonResponse($typeActivite, 200);
        } else {
            return new JsonResponse([], 204);
        }
    }

    /**
     * @Route("/add", methods={"POST"})
     */
    public function add(Request $request): JsonResponse
    {
        $typeActivite = new TypeActivite();

        return $this->manage($typeActivite, $request);
    }

    /**
     * @Route("/edit", methods={"POST"})
     */
    public function edit(Request $request, TypeActiviteRepository $typeActiviteRepository): Response
    {
        $typeActivite = $typeActiviteRepository->find((int)$request->get("id"));

        if (!$typeActivite) {
            return new JsonResponse(null, 404);
        }

        return $this->manage($typeActivite, $request);
    }

    public function manage($typeActivite, $request): JsonResponse
    {   
        
        $typeActivite->setUp(
            $request->get("nom")
        );

        $entityManager = $this->getDoctrine()->getManager();
        $entityManager->persist($typeActivite);
        $entityManager->flush();

        return new JsonResponse($typeActivite, 200);
    }

    /**
     * @Route("/delete", methods={"POST"})
     */
    public function delete(Request $request, EntityManagerInterface $entityManager, TypeActiviteRepository $typeActiviteRepository): JsonResponse
    {
        $typeActivite = $typeActiviteRepository->find((int)$request->get("id"));

        if (!$typeActivite) {
            return new JsonResponse(null, 200);
        }

        $entityManager->remove($typeActivite);
        $entityManager->flush();

        return new JsonResponse([], 200);
    }

    /**
     * @Route("/deleteAll", methods={"POST"})
     */
    public function deleteAll(EntityManagerInterface $entityManager, TypeActiviteRepository $typeActiviteRepository): Response
    {
        $typeActivites = $typeActiviteRepository->findAll();

        foreach ($typeActivites as $typeActivite) {
            $entityManager->remove($typeActivite);
            $entityManager->flush();
        }

        return new JsonResponse([], 200);
    }
    
}
