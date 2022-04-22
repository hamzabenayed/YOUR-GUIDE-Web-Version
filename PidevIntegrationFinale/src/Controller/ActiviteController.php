<?php

namespace App\Controller;

use App\Entity\Activite;
use App\Entity\Search;
use App\Form\ActiviteFormType;
use App\Form\SearchType;
use App\Repository\ActiviteRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\String\Slugger\SluggerInterface;
use Knp\Component\Pager\PaginatorInterface;

class ActiviteController extends AbstractController
{
    /**
     * @Route("/activite", name="activite")
     */
    public function index(): Response
    {
        return $this->render('activite/index.html.twig', [
            'controller_name' => 'ActiviteController',
        ]);
    }
    /**
     * @Route("/activite/show", name="show_activite", methods={"GET"})
     */
    public function show(Request  $request ,ActiviteRepository $activiteRepository,PaginatorInterface $paginator): Response
    {
$tableactivite= $activiteRepository->findAll();
        $tableactivite = $paginator->paginate(
            $tableactivite,
            $request->query->getInt('page', 1),
            4
        );


        return $this->render('activite/show.html.twig', [
            'type' => $tableactivite,
        ]);
    }

    /**
     * @Route("/activite/showw", name="show_activitew", methods={"GET"})
     */
    public function showw(ActiviteRepository $activiteRepository): Response
    {
        return $this->render('activite/showfront.html.twig', [
            'type' => $activiteRepository->findAll(),
        ]);
    }

    /**
     * @Route("/activite/add", name="add_activite")
     */
    public function show1(Request $request, EntityManagerInterface $entityManager, ActiviteRepository $activiteRepository): Response
    {
        $act = new Activite();
        $form = $this->createForm(ActiviteFormType::class, $act);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            /** @var UploadedFile $imageFile */
            $imageFile = $form->get('image')->getData();
            // this condition is needed because the 'image' field is not required

            if ($imageFile) {
                // generate new name to the file image with the function generateUniqueFileName
                $fileName = $this->generateUniqueFileName().'.'.$imageFile->guessExtension();

                // moves the file to the directory where products are stored
                $imageFile->move(
                    $this->getParameter('imagesActivite_directory'),
                    $fileName
                );

                // updates the 'product' property to store the image file name
                // instead of its contents
                $act->setImage($fileName);
            }



            $entityManager->persist($act);
            $entityManager->flush();

            return $this->redirectToRoute('show_activite', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('activite/add.html.twig', [
            'form' => $form->createView(),
        ]);
    }
    /**
     * @Route("/activite/delete/{id}", name="dele_activite", methods={"GET"})
     */
    public function del($id, Request $request, EntityManagerInterface $entityManager): Response
    {
        $act = $entityManager->getRepository(Activite::class)->findBy(['id' => $id])[0];
        $entityManager->remove($act);
        $entityManager->flush();

        return $this->redirectToRoute('show_activite', [], Response::HTTP_SEE_OTHER);
    }
    /**
     * @Route("/activite/modify/{id}", name="mod_activite", methods={"GET", "POST"})
     */
    public function mod($id, Request $request, EntityManagerInterface $entityManager, Activite $activite): Response
    {
        $activite = new Activite();
        $form = $this->createForm(ActiviteFormType::class, $activite);

        $form->handleRequest($request);

        if ($form->isSubmitted()  && $form->isValid()) {
            /** @var UploadedFile $imageFile */
            $imageFile = $form->get('image')->getData();
            // this condition is needed because the 'image' field is not required

            if ($imageFile) {
                // generate new name to the file image with the function generateUniqueFileName
                $fileName = $this->generateUniqueFileName().'.'.$imageFile->guessExtension();

                // moves the file to the directory where products are stored
                $imageFile->move(
                    $this->getParameter('imagesActivite_directory'),
                    $fileName
                );

                // updates the 'product' property to store the image file name
                // instead of its contents
                $activite->setImage($fileName);
            }
            $act = $entityManager->getRepository(Activite::class)->findBy(['id' => $id])[0];
            $entityManager->remove($act);
            $entityManager->persist($activite);
            $entityManager->flush();

            return $this->redirectToRoute('show_activite', [], Response::HTTP_SEE_OTHER);
        }
        $cat = $entityManager->getRepository(Activite::class)->findBy(['id' => $id])[0];
        return $this->render('activite/mod.html.twig', [
            'form' => $form->createView(),
            'cat' => $cat
        ]);
    }
    /**
     * @Route("/activite/modifyy/{id}", name="modr_activite", methods={"post"})
     */
    public function mod1($id, Request $request, EntityManagerInterface $entityManager): Response
    {
        $activite = new Activite();
        $form = $this->createForm(ActiviteFormType::class, $activite);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $activite->setId($form['id']->getData());
            $activite->setNom($form['name']->getData());
            $activite->setNom($form['lieu']->getData());
            $activite->setNom($form['description']->getData());
            $activite->setNom($form['type']->getData());
            $entityManager->persist($activite);
            $entityManager->flush();

            return $this->redirectToRoute('show_type', [], Response::HTTP_SEE_OTHER);
        }
        return new Response();
    }
    // fonction qui generer un identifiant unique pour chaque image
    /**
     * @return string
     */
    private function generateUniqueFileName()
    {
        // md5() reduces the similarity of the file names generated by
        // uniqid(), which is based on timestamps
        return md5(uniqid());
    }
    /**
     * @Route("/back/a/tri_nom", name="tri_username")
     */
    public function tri_username(EntityManagerInterface $entityManager, Request $request, PaginatorInterface $paginator)
    {

        $query = $entityManager->createQuery(
            'SELECT a FROM App\Entity\Activite a 
            ORDER BY a.nom ASC'
        );
        $activite = $query->getResult();
        $activite=$paginator->paginate(
            $activite, //on passe les données
            $request->query->getInt('page', 1), //num de la page en cours, 1 par défaut
            4 //nbre d'articles par page
        );
        return $this->render('activite/show.html.twig', array("type" => $activite));
    }



}