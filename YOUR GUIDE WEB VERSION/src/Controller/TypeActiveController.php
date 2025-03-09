<?php

namespace App\Controller;

use App\Entity\TypeActivite;
use App\Form\TypeActiviteFormType;
use App\Repository\TypeActiviteRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class TypeActiveController extends AbstractController
{
    /**
     * @Route("/type", name="type")
     */
    public function index(): Response
    {
        return $this->render('type_active/index.html.twig', [
            'controller_name' => 'TypeActiveController',
        ]);
    }
    /**
     * @Route("/type/show", name="show_type", methods={"GET"})
     */
    public function show(TypeActiviteRepository $typeActiviteRepository): Response
    {
        return $this->render('type_active/show.html.twig', [
            'type' => $typeActiviteRepository->findAll(),
        ]);
    }
    /**
     * @Route("/type/add", name="add_type")
     */
    public function show1(Request $request, EntityManagerInterface $entityManager, TypeActiviteRepository $typeActiviteRepository): Response
    {
        $type = new TypeActivite();
        $form = $this->createForm(TypeActiviteFormType::class, $type);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($type);
            $entityManager->flush();

            return $this->redirectToRoute('show_type', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('type_active/add.html.twig', [
            'form' => $form->createView(),
        ]);
    }
    /**
     * @Route("/type/delete/{id}", name="dele_type", methods={"GET"})
     */
    public function del($id, Request $request, EntityManagerInterface $entityManager): Response
    {
        $type = $entityManager->getRepository(TypeActivite::class)->findBy(['id' => $id])[0];
        $entityManager->remove($type);
        $entityManager->flush();

        return $this->redirectToRoute('show_type', [], Response::HTTP_SEE_OTHER);
    }
    /**
     * @Route("/type/modify/{id}", name="mod_type", methods={"GET", "POST"})
     */
    public function mod($id, Request $request, EntityManagerInterface $entityManager, TypeActivite $typeActivite): Response
    {
        $typeActivite = new TypeActivite();
        $form = $this->createForm(TypeActiviteFormType::class, $typeActivite);

        $form->handleRequest($request);

        if ($form->isSubmitted()  && $form->isValid()) {
            $type = $entityManager->getRepository(TypeActivite::class)->findBy(['id' => $id])[0];
            $entityManager->remove($type);
            $entityManager->persist($typeActivite);
            $entityManager->flush();

            return $this->redirectToRoute('show_type', [], Response::HTTP_SEE_OTHER);
        }
        $cat = $entityManager->getRepository(TypeActivite::class)->findBy(['id' => $id])[0];
        return $this->render('type_active/mod.html.twig', [
            'form' => $form->createView(),
            'cat' => $cat
        ]);
    }
    /**
     * @Route("/type/modifyy/{id}", name="modr_type", methods={"post"})
     */
    public function mod1($id, Request $request, EntityManagerInterface $entityManager): Response
    {
        $type = new TypeActivite();
        $form = $this->createForm(TypeActiviteFormType::class, $type);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $type->setId($form['id']->getData());
            $type->setNom($form['name']->getData());
            $entityManager->persist($type);
            $entityManager->flush();

            return $this->redirectToRoute('show_type', [], Response::HTTP_SEE_OTHER);
        }
        return new Response();
    }
}
