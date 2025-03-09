<?php
namespace App\Controller\Mobile;

use App\Entity\Codepromo;
use App\Repository\CodepromoRepository;
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
 * @Route("/mobile/codepromo")
 */
class CodepromoMobileController extends AbstractController
{
    /**
     * @Route("", methods={"GET"})
     */
    public function index(CodepromoRepository $codepromoRepository): Response
    {
        $codepromos = $codepromoRepository->findAll();

        if ($codepromos) {
            return new JsonResponse($codepromos, 200);
        } else {
            return new JsonResponse([], 204);
        }
    }

    /**
     * @Route("/show", methods={"POST"})
     */
    public function show(Request $request, CodepromoRepository $codepromoRepository): Response
    {
        $codepromo = $codepromoRepository->find((int)$request->get("id"));

        if ($codepromo) {
            return new JsonResponse($codepromo, 200);
        } else {
            return new JsonResponse([], 204);
        }
    }

    /**
     * @Route("/add", methods={"POST"})
     */
    public function add(Request $request): JsonResponse
    {
        $codepromo = new Codepromo();

        return $this->manage($codepromo, $request);
    }

    /**
     * @Route("/edit", methods={"POST"})
     */
    public function edit(Request $request, CodepromoRepository $codepromoRepository): Response
    {
        $codepromo = $codepromoRepository->find((int)$request->get("id"));

        if (!$codepromo) {
            return new JsonResponse(null, 404);
        }

        return $this->manage($codepromo, $request);
    }

    public function manage($codepromo, $request): JsonResponse
    {   
        
        $codepromo->setUp(
            $request->get("code"),
            DateTime::createFromFormat("d-m-Y", $request->get("dateDebut")),
            DateTime::createFromFormat("d-m-Y", $request->get("dateFin"))
        );

        $entityManager = $this->getDoctrine()->getManager();
        $entityManager->persist($codepromo);
        $entityManager->flush();

        return new JsonResponse($codepromo, 200);
    }

    /**
     * @Route("/delete", methods={"POST"})
     */
    public function delete(Request $request, EntityManagerInterface $entityManager, CodepromoRepository $codepromoRepository): JsonResponse
    {
        $codepromo = $codepromoRepository->find((int)$request->get("id"));

        if (!$codepromo) {
            return new JsonResponse(null, 200);
        }

        $entityManager->remove($codepromo);
        $entityManager->flush();

        return new JsonResponse([], 200);
    }

    /**
     * @Route("/deleteAll", methods={"POST"})
     */
    public function deleteAll(EntityManagerInterface $entityManager, CodepromoRepository $codepromoRepository): Response
    {
        $codepromos = $codepromoRepository->findAll();

        foreach ($codepromos as $codepromo) {
            $entityManager->remove($codepromo);
            $entityManager->flush();
        }

        return new JsonResponse([], 200);
    }
    
}
