<?php
namespace App\Controller\Mobile;

use App\Entity\ Promo;
use App\Repository\ PromoRepository;use App\Repository\ CodepromoRepository;
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
 * @Route("/mobile/promo")
 */
class PromoMobileController extends AbstractController
{
    /**
     * @Route("", methods={"GET"})
     */
    public function index(PromoRepository $promoRepository): Response
    {
        $promos = $promoRepository->findAll();

        if ($promos) {
            return new JsonResponse($promos, 200);
        } else {
            return new JsonResponse([], 204);
        }
    }

    /**
     * @Route("/show", methods={"POST"})
     */
    public function show(Request $request, PromoRepository $promoRepository): Response
    {
        $promo = $promoRepository->find((int)$request->get("id"));

        if ($promo) {
            return new JsonResponse($promo, 200);
        } else {
            return new JsonResponse([], 204);
        }
    }

    /**
     * @Route("/add", methods={"POST"})
     */
    public function add(Request $request, CodepromoRepository $codepromoRepository): JsonResponse
    {
        $promo = new Promo();

        return $this->manage($promo, $codepromoRepository,  $request);
    }

    /**
     * @Route("/edit", methods={"POST"})
     */
    public function edit(Request $request, PromoRepository $promoRepository, CodepromoRepository $codepromoRepository): Response
    {
        $promo = $promoRepository->find((int)$request->get("id"));

        if (!$promo) {
            return new JsonResponse(null, 404);
        }

        return $this->manage($promo, $codepromoRepository, $request);
    }

    public function manage($promo, $codepromoRepository, $request): JsonResponse
    {   
        $codepromo = $codepromoRepository->find((int)$request->get("codepromo"));
        if (!$codepromo) {
            return new JsonResponse("codepromo with id " . (int)$request->get("codepromo") . " does not exist", 203);
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
        
        $promo->setUp(
            (int)$request->get("pourcentage"),
            $codepromo,
            $imageFileName
        );

        $entityManager = $this->getDoctrine()->getManager();
        $entityManager->persist($promo);
        $entityManager->flush();

        return new JsonResponse($promo, 200);
    }

    /**
     * @Route("/delete", methods={"POST"})
     */
    public function delete(Request $request, EntityManagerInterface $entityManager, PromoRepository $promoRepository): JsonResponse
    {
        $promo = $promoRepository->find((int)$request->get("id"));

        if (!$promo) {
            return new JsonResponse(null, 200);
        }

        $entityManager->remove($promo);
        $entityManager->flush();

        return new JsonResponse([], 200);
    }

    /**
     * @Route("/deleteAll", methods={"POST"})
     */
    public function deleteAll(EntityManagerInterface $entityManager, PromoRepository $promoRepository): Response
    {
        $promos = $promoRepository->findAll();

        foreach ($promos as $promo) {
            $entityManager->remove($promo);
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
