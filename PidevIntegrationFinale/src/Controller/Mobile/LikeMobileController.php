<?php

namespace App\Controller\Mobile;

use App\Entity\ Like;
use App\Repository\ LikeRepository;
use App\Repository\ CommentaireRepository;
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
 * @Route("/mobile/like")
 */
class LikeMobileController extends AbstractController
{
    /**
     * @Route("", methods={"GET"})
     */
    public function index(LikeRepository $likeRepository): Response
    {
        $likes = $likeRepository->findAll();

        if ($likes) {
            return new JsonResponse($likes, 200);
        } else {
            return new JsonResponse([], 204);
        }
    }

    /**
     * @Route("/show", methods={"POST"})
     */
    public function show(Request $request, LikeRepository $likeRepository): Response
    {
        $like = $likeRepository->find((int)$request->get("id"));

        if ($like) {
            return new JsonResponse($like, 200);
        } else {
            return new JsonResponse([], 204);
        }
    }

    /**
     * @Route("/likeDislike", methods={"POST"})
     */
    public function likeDislike(Request $request, LikeRepository $likeRepository, CommentaireRepository $commentaireRepository): JsonResponse
    {
        $entityManager = $this->getDoctrine()->getManager();
        $like = $likeRepository->findOneBy(["rate" => $request->get("rate")]);
        if ($like) {
            $entityManager->remove($like);
            $entityManager->flush();
            return new JsonResponse("Deleted", 201);
        } else {
            $like = new Like();
            return $this->manage($like, $commentaireRepository, $request);
        }
    }

    /**
     * @Route("/add", methods={"POST"})
     */
    public function add(Request $request, CommentaireRepository $commentaireRepository): JsonResponse
    {
        $like = new Like();

        return $this->manage($like, $commentaireRepository, $request);
    }


    /**
     * @Route("/edit", methods={"POST"})
     */
    public function edit(Request $request, LikeRepository $likeRepository, CommentaireRepository $commentaireRepository): Response
    {
        $like = $likeRepository->find((int)$request->get("id"));

        if (!$like) {
            return new JsonResponse(null, 404);
        }

        return $this->manage($like, $commentaireRepository, $request);
    }

    public function manage($like, $commentaireRepository, $request): JsonResponse
    {
        $commentaire = $commentaireRepository->find((int)$request->get("commentaire"));
        if (!$commentaire) {
            return new JsonResponse("commentaire with id " . (int)$request->get("commentaire") . " does not exist", 203);
        }


        $like->setUp(
            $request->get("nom"),
            (float)$request->get("rate"),
            (float)$request->get("note"),
            $commentaire
        );

        $entityManager = $this->getDoctrine()->getManager();
        $entityManager->persist($like);
        $entityManager->flush();

        return new JsonResponse($like, 200);
    }

    /**
     * @Route("/delete", methods={"POST"})
     */
    public function delete(Request $request, EntityManagerInterface $entityManager, LikeRepository $likeRepository): JsonResponse
    {
        $like = $likeRepository->find((int)$request->get("id"));

        if (!$like) {
            return new JsonResponse(null, 200);
        }

        $entityManager->remove($like);
        $entityManager->flush();

        return new JsonResponse([], 200);
    }

    /**
     * @Route("/deleteAll", methods={"POST"})
     */
    public function deleteAll(EntityManagerInterface $entityManager, LikeRepository $likeRepository): Response
    {
        $likes = $likeRepository->findAll();

        foreach ($likes as $like) {
            $entityManager->remove($like);
            $entityManager->flush();
        }

        return new JsonResponse([], 200);
    }

}
