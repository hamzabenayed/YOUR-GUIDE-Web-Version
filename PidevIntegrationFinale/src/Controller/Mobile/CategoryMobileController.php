<?php
namespace App\Controller\Mobile;

use App\Entity\ Category;
use App\Repository\ CategoryRepository;
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
 * @Route("/mobile/category")
 */
class CategoryMobileController extends AbstractController
{
    /**
     * @Route("", methods={"GET"})
     */
    public function index(CategoryRepository $categoryRepository): Response
    {
        $categorys = $categoryRepository->findAll();

        if ($categorys) {
            return new JsonResponse($categorys, 200);
        } else {
            return new JsonResponse([], 204);
        }
    }

    /**
     * @Route("/show", methods={"POST"})
     */
    public function show(Request $request, CategoryRepository $categoryRepository): Response
    {
        $category = $categoryRepository->find((int)$request->get("id"));

        if ($category) {
            return new JsonResponse($category, 200);
        } else {
            return new JsonResponse([], 204);
        }
    }

    /**
     * @Route("/add", methods={"POST"})
     */
    public function add(Request $request): JsonResponse
    {
        $category = new Category();

        return $this->manage($category, $request);
    }

    /**
     * @Route("/edit", methods={"POST"})
     */
    public function edit(Request $request, CategoryRepository $categoryRepository): Response
    {
        $category = $categoryRepository->find((int)$request->get("id"));

        if (!$category) {
            return new JsonResponse(null, 404);
        }

        return $this->manage($category, $request);
    }

    public function manage($category, $request): JsonResponse
    {   
        
        $category->setUp(
            $request->get("name")
        );

        $entityManager = $this->getDoctrine()->getManager();
        $entityManager->persist($category);
        $entityManager->flush();

        return new JsonResponse($category, 200);
    }

    /**
     * @Route("/delete", methods={"POST"})
     */
    public function delete(Request $request, EntityManagerInterface $entityManager, CategoryRepository $categoryRepository): JsonResponse
    {
        $category = $categoryRepository->find((int)$request->get("id"));

        if (!$category) {
            return new JsonResponse(null, 200);
        }

        $entityManager->remove($category);
        $entityManager->flush();

        return new JsonResponse([], 200);
    }

    /**
     * @Route("/deleteAll", methods={"POST"})
     */
    public function deleteAll(EntityManagerInterface $entityManager, CategoryRepository $categoryRepository): Response
    {
        $categorys = $categoryRepository->findAll();

        foreach ($categorys as $category) {
            $entityManager->remove($category);
            $entityManager->flush();
        }

        return new JsonResponse([], 200);
    }
    
}
