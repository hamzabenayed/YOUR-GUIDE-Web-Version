<?php
namespace App\Controller\Mobile;

use App\Entity\ Activite;
use App\Repository\ ActiviteRepository;use App\Repository\ TypeActiviteRepository;
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
 * @Route("/mobile/activite")
 */
class ActiviteMobileController extends AbstractController
{
    /**
     * @Route("", methods={"GET"})
     */
    public function index(ActiviteRepository $activiteRepository): Response
    {
        $activites = $activiteRepository->findAll();

        if ($activites) {
            return new JsonResponse($activites, 200);
        } else {
            return new JsonResponse([], 204);
        }
    }

    /**
     * @Route("/show", methods={"POST"})
     */
    public function show(Request $request, ActiviteRepository $activiteRepository): Response
    {
        $activite = $activiteRepository->find((int)$request->get("id"));

        if ($activite) {
            return new JsonResponse($activite, 200);
        } else {
            return new JsonResponse([], 204);
        }
    }

    /**
     * @Route("/add", methods={"POST"})
     */
    public function add(Request $request, TypeActiviteRepository $typeActiviteRepository): JsonResponse
    {
        $activite = new Activite();

        return $this->manage($activite, $typeActiviteRepository,  $request);
    }

    /**
     * @Route("/edit", methods={"POST"})
     */
    public function edit(Request $request, ActiviteRepository $activiteRepository, TypeActiviteRepository $typeActiviteRepository): Response
    {
        $activite = $activiteRepository->find((int)$request->get("id"));

        if (!$activite) {
            return new JsonResponse(null, 404);
        }

        return $this->manage($activite, $typeActiviteRepository, $request);
    }

    public function manage($activite, $typeActiviteRepository, $request): JsonResponse
    {   
        $typeActivite = $typeActiviteRepository->find((int)$request->get("typeActivite"));
        if (!$typeActivite) {
            return new JsonResponse("typeActivite with id " . (int)$request->get("typeActivite") . " does not exist", 203);
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
        
        $activite->setUp(
            $request->get("nom"),
            $request->get("lieu"),
            $request->get("description"),
            $imageFileName,
            $typeActivite,
            $request->get("longitude"),
            $request->get("lattitude")
        );

        $entityManager = $this->getDoctrine()->getManager();
        $entityManager->persist($activite);
        $entityManager->flush();

        return new JsonResponse($activite, 200);
    }

    /**
     * @Route("/delete", methods={"POST"})
     */
    public function delete(Request $request, EntityManagerInterface $entityManager, ActiviteRepository $activiteRepository): JsonResponse
    {
        $activite = $activiteRepository->find((int)$request->get("id"));

        if (!$activite) {
            return new JsonResponse(null, 200);
        }

        $entityManager->remove($activite);
        $entityManager->flush();

        return new JsonResponse([], 200);
    }

    /**
     * @Route("/deleteAll", methods={"POST"})
     */
    public function deleteAll(EntityManagerInterface $entityManager, ActiviteRepository $activiteRepository): Response
    {
        $activites = $activiteRepository->findAll();

        foreach ($activites as $activite) {
            $entityManager->remove($activite);
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
