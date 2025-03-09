<?php

namespace App\Controller;

use App\Entity\Activite;
use App\Entity\Guide;
use App\Form\GuideType;
use App\Repository\ActiviteRepository;
use App\Repository\GuideRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class GuideController extends AbstractController
{
    /**
     * @Route("/guide", name="guide")
     */
    public function index(): Response
    {
        return $this->render('guide/index.html.twig', [
            'controller_name' => 'GuideController',
        ]);
    }
    /**
     * @Route("/guide/show", name="show_guide", methods={"GET"})
     */
    public function show(GuideRepository $guideRepository): Response
    {
        return $this->render('guide/show.html.twig', [
            'type' => $guideRepository->findAll(),
        ]);
    }
    /**
     * @Route("/guide/add", name="add_guide")
     */
    public function show1(Request $request, EntityManagerInterface $entityManager, GuideRepository $guideRepository): Response
    {
        $guide = new Guide();
        $form = $this->createForm(GuideType::class, $guide);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($guide);
            $entityManager->flush();

            return $this->redirectToRoute('show_guide', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('guide/add.html.twig', [
            'form' => $form->createView(),
        ]);
    }
    /**
     * @Route("/guide/delete/{id}", name="dele_guide", methods={"GET"})
     */
    public function del($id, Request $request, EntityManagerInterface $entityManager): Response
    {
        $guide = $entityManager->getRepository(Guide::class)->findBy(['id' => $id])[0];
        $entityManager->remove($guide);
        $entityManager->flush();

        return $this->redirectToRoute('show_guide', [], Response::HTTP_SEE_OTHER);
    }
    /**
     * @Route("/guide/modify/{id}", name="mod_guide", methods={"GET", "POST"})
     */
    public function mod($id, Request $request, EntityManagerInterface $entityManager, Guide $guide): Response
    {
        $guide = new Guide();
        $form = $this->createForm(GuideType::class, $guide);

        $form->handleRequest($request);

        if ($form->isSubmitted()  && $form->isValid()) {
            $g = $entityManager->getRepository(Guide::class)->findBy(['id' => $id])[0];
            $entityManager->remove($g);
            $entityManager->persist($guide);
            $entityManager->flush();

            return $this->redirectToRoute('show_guide', [], Response::HTTP_SEE_OTHER);
        }
        $cat = $entityManager->getRepository(Guide::class)->findBy(['id' => $id])[0];
        return $this->render('guide/mod.html.twig', [
            'form' => $form->createView(),
            'cat' => $cat
        ]);
    }
    /**
     * @Route("/guide/modifyy/{id}", name="modr_guide", methods={"post"})
     */
    public function mod1($id, Request $request, EntityManagerInterface $entityManager): Response
    {
        $guide = new Guide();
        $form = $this->createForm(GuideType::class, $guide);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $guide->setId($form['id']->getData());
            $guide->setNom($form['name']->getData());
            $guide->setNom($form['tel']->getData());
            $guide->setNom($form['activite']->getData());
            $entityManager->persist($guide);
            $entityManager->flush();

            return $this->redirectToRoute('show_guide', [], Response::HTTP_SEE_OTHER);
        }
        return new Response();
    }
}
