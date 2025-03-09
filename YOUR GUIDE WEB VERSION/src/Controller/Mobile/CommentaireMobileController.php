<?php
namespace App\Controller\Mobile;

use App\Entity\Commentaire;
use App\Repository\CommentaireRepository;
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
 * @Route("/mobile/commentaire")
 */
class CommentaireMobileController extends AbstractController
{
    /**
     * @Route("", methods={"GET"})
     */
    public function index(CommentaireRepository $commentaireRepository): Response
    {
        $commentaires = $commentaireRepository->findAll();

        $commentairesArray = [];
        foreach ($commentaires as $commentaire) {
            $commentaireArray = $commentaire->jsonSerialize();

            $likesArray = [];
            foreach ($commentaire->getLikes() as $like) {
                $likesArray[] = $like->jsonSerialize();
            }
            $commentaireArray["likes"] = $likesArray;
            $commentairesArray[] = $commentaireArray;
        }

        if ($commentaires) {
            return new JsonResponse($commentairesArray, 200);
        } else {
            return new JsonResponse([], 204);
        }
    }

    /**
     * @Route("/show", methods={"POST"})
     */
    public function show(Request $request, CommentaireRepository $commentaireRepository): Response
    {
        $commentaire = $commentaireRepository->find((int)$request->get("id"));

        if ($commentaire) {
            return new JsonResponse($commentaire, 200);
        } else {
            return new JsonResponse([], 204);
        }
    }

    /**
     * @Route("/add", methods={"POST"})
     */
    public function add(Request $request): JsonResponse
    {
        $commentaire = new Commentaire();

        return $this->manage($commentaire, $request);
    }

    /**
     * @Route("/edit", methods={"POST"})
     */
    public function edit(Request $request, CommentaireRepository $commentaireRepository): Response
    {
        $commentaire = $commentaireRepository->find((int)$request->get("id"));

        if (!$commentaire) {
            return new JsonResponse(null, 404);
        }

        return $this->manage($commentaire, $request);
    }

    public function manage($commentaire, $request): JsonResponse
    {   
        
        $commentaire->setUp(
            $request->get("description")
        );

        $entityManager = $this->getDoctrine()->getManager();
        $entityManager->persist($commentaire);
        $entityManager->flush();

        return new JsonResponse($commentaire, 200);
    }

    /**
     * @Route("/delete", methods={"POST"})
     */
    public function delete(Request $request, EntityManagerInterface $entityManager, CommentaireRepository $commentaireRepository): JsonResponse
    {
        $commentaire = $commentaireRepository->find((int)$request->get("id"));

        if (!$commentaire) {
            return new JsonResponse(null, 200);
        }

        $entityManager->remove($commentaire);
        $entityManager->flush();

        return new JsonResponse([], 200);
    }

    /**
     * @Route("/deleteAll", methods={"POST"})
     */
    public function deleteAll(EntityManagerInterface $entityManager, CommentaireRepository $commentaireRepository): Response
    {
        $commentaires = $commentaireRepository->findAll();

        foreach ($commentaires as $commentaire) {
            $entityManager->remove($commentaire);
            $entityManager->flush();
        }

        return new JsonResponse([], 200);
    }
    
}
