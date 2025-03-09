<?php
namespace App\Controller\Mobile;

use App\Entity\Guide;
use App\Repository\GuideRepository;
use App\Repository\ActiviteRepository;
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
 * @Route("/mobile/guide")
 */
class GuideMobileController extends AbstractController
{
    /**
     * @Route("", methods={"GET"})
     */
    public function index(GuideRepository $guideRepository): Response
    {
        $guides = $guideRepository->findAll();

        if ($guides) {
            return new JsonResponse($guides, 200);
        } else {
            return new JsonResponse([], 204);
        }
    }

    /**
     * @Route("/show", methods={"POST"})
     */
    public function show(Request $request, GuideRepository $guideRepository): Response
    {
        $guide = $guideRepository->find((int)$request->get("id"));

        if ($guide) {
            return new JsonResponse($guide, 200);
        } else {
            return new JsonResponse([], 204);
        }
    }

    /**
     * @Route("/add", methods={"POST"})
     */
    public function add(Request $request, ActiviteRepository $activiteRepository): JsonResponse
    {
        $guide = new Guide();

        return $this->manage($guide, $activiteRepository,  $request);
    }

    /**
     * @Route("/edit", methods={"POST"})
     */
    public function edit(Request $request, GuideRepository $guideRepository, ActiviteRepository $activiteRepository): Response
    {
        $guide = $guideRepository->find((int)$request->get("id"));

        if (!$guide) {
            return new JsonResponse(null, 404);
        }

        return $this->manage($guide, $activiteRepository, $request);
    }

    public function manage($guide, $activiteRepository, $request): JsonResponse
    {   
        $activite = $activiteRepository->find((int)$request->get("activite"));
        if (!$activite) {
            return new JsonResponse("activite with id " . (int)$request->get("activite") . " does not exist", 203);
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
        
        $guide->setUp(
            $request->get("nom"),
            (float)$request->get("tel"),
            $activite,
            $imageFileName
        );

        $entityManager = $this->getDoctrine()->getManager();
        $entityManager->persist($guide);
        $entityManager->flush();

        return new JsonResponse($guide, 200);
    }

    /**
     * @Route("/delete", methods={"POST"})
     */
    public function delete(Request $request, EntityManagerInterface $entityManager, GuideRepository $guideRepository): JsonResponse
    {
        $guide = $guideRepository->find((int)$request->get("id"));

        if (!$guide) {
            return new JsonResponse(null, 200);
        }

        $entityManager->remove($guide);
        $entityManager->flush();

        return new JsonResponse([], 200);
    }

    /**
     * @Route("/deleteAll", methods={"POST"})
     */
    public function deleteAll(EntityManagerInterface $entityManager, GuideRepository $guideRepository): Response
    {
        $guides = $guideRepository->findAll();

        foreach ($guides as $guide) {
            $entityManager->remove($guide);
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
