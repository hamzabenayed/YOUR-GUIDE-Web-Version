<?php

namespace App\Controller\Mobile;

use App\Entity\ Produit;
use App\Repository\ProduitRepository;
use App\Repository\CategoryRepository;
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
 * @Route("/mobile/produit")
 */
class ProduitMobileController extends AbstractController
{
    /**
     * @Route("/cat={id}", methods={"GET"})
     */
    public function parCat($id, CategoryRepository $categorieRepository, ProduitRepository $produitRepository): Response
    {
        $categorie = $categorieRepository->find((int)$id);

        if (!$categorie) {
            return new JsonResponse([], 204);
        }

        $produits = $produitRepository->findBy(['category' => $categorie]);

        if ($produits) {
            return new JsonResponse($produits, 200);
        } else {
            return new JsonResponse([], 204);
        }
    }

    /**
     * @Route("", methods={"GET"})
     */
    public function index(ProduitRepository $produitRepository): Response
    {
        $produits = $produitRepository->findAll();

        if ($produits) {
            return new JsonResponse($produits, 200);
        } else {
            return new JsonResponse([], 204);
        }
    }

    /**
     * @Route("/show", methods={"POST"})
     */
    public function show(Request $request, ProduitRepository $produitRepository): Response
    {
        $produit = $produitRepository->find((int)$request->get("id"));

        if ($produit) {
            return new JsonResponse($produit, 200);
        } else {
            return new JsonResponse([], 204);
        }
    }

    /**
     * @Route("/add", methods={"POST"})
     */
    public function add(Request $request, CategoryRepository $categoryRepository): JsonResponse
    {
        $produit = new Produit();

        return $this->manage($produit, $categoryRepository, $request);
    }

    /**
     * @Route("/edit", methods={"POST"})
     */
    public function edit(Request $request, ProduitRepository $produitRepository, CategoryRepository $categoryRepository): Response
    {
        $produit = $produitRepository->find((int)$request->get("id"));

        if (!$produit) {
            return new JsonResponse(null, 404);
        }

        return $this->manage($produit, $categoryRepository, $request);
    }

    public function manage($produit, $categoryRepository, $request): JsonResponse
    {
        $category = $categoryRepository->find((int)$request->get("category"));
        if (!$category) {
            return new JsonResponse("category with id " . (int)$request->get("category") . " does not exist", 203);
        }

        $file = $request->files->get("file");
        if ($file) {
            $imageFileName = md5(uniqid()) . '.' . $file->guessExtension();

            try {
                $file->move($this->getParameter('imagesActivite_directory'), $imageFileName);
            } catch (FileException $e) {
                dd($e);
            }
        } else {
            if ($request->get("image")) {
                $imageFileName = $request->get("image");
            } else {
                $imageFileName = "null";
            }
        }

        $produit->setUp(
            $request->get("nom"),
            $request->get("description"),
            (float)$request->get("prix"),
            $imageFileName,
            $category
        );

        $entityManager = $this->getDoctrine()->getManager();
        $entityManager->persist($produit);
        $entityManager->flush();

        return new JsonResponse($produit, 200);
    }

    /**
     * @Route("/delete", methods={"POST"})
     */
    public function delete(Request $request, EntityManagerInterface $entityManager, ProduitRepository $produitRepository): JsonResponse
    {
        $produit = $produitRepository->find((int)$request->get("id"));

        if (!$produit) {
            return new JsonResponse(null, 200);
        }

        $entityManager->remove($produit);
        $entityManager->flush();

        return new JsonResponse([], 200);
    }

    /**
     * @Route("/deleteAll", methods={"POST"})
     */
    public function deleteAll(EntityManagerInterface $entityManager, ProduitRepository $produitRepository): Response
    {
        $produits = $produitRepository->findAll();

        foreach ($produits as $produit) {
            $entityManager->remove($produit);
            $entityManager->flush();
        }

        return new JsonResponse([], 200);
    }

    /**
     * @Route("/image/{image}", methods={"GET"})
     */
    public function getPicture(Request $request): BinaryFileResponse
    {
        return new BinaryFileResponse(
            $this->getParameter('imagesActivite_directory') . "/" . $request->get("image")
        );
    }
}
