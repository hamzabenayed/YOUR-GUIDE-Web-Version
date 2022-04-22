<?php


namespace App\Controller;

use App\Entity\Category;
use App\Entity\Produit;
use App\Form\CategoryFormType;
use App\Form\ProduitType;
use App\Repository\CategoryRepository;
use App\Repository\ProduitRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class CategoryController extends AbstractController
{
    /**
     * @Route("/category", name="produit_index")
     */
    public function index(ProduitRepository $produitRepository): Response
    {
        return $this->render('produit/show.html.twig', [
            'products' => $produitRepository->findAll(),
        ]);
    }

    /**
     * @Route("/categoryy", name="produit_indexx")
     */
    public function indexx(ProduitRepository $produitRepository): Response
    {
        return $this->render('produit/showw.html.twig', [
            'products' => $produitRepository->findAll(),
        ]);
    }

    /**
     * @Route("/category/show", name="show_cat", methods={"GET"})
     */
    public function show(CategoryRepository $categoryRepository): Response
    {
        return $this->render('category/show.html.twig', [
            'cats' => $categoryRepository->findAll(),
        ]);
    }
    /**
     * @Route("/category/add", name="sho_cat")
     */
    public function show1(Request $request, EntityManagerInterface $entityManager, CategoryRepository $categoryRepository): Response
    {
        $category = new Category();
        $form = $this->createForm(CategoryFormType::class, $category);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($category);
            $entityManager->flush();

            return $this->redirectToRoute('show_cat', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('category/add.html.twig', [
            'form' => $form->createView(),
        ]);
    }
    /**
     * @Route("/category/delete/{id}", name="dele_cat", methods={"GET"})
     */
    public function del($id, Request $request, EntityManagerInterface $entityManager, Category $categoryRepository): Response
    {
        $cat = $entityManager->getRepository(Category::class)->findBy(['id' => $id])[0];
        $entityManager->remove($cat);
        $entityManager->flush();

        return $this->redirectToRoute('show_cat', [], Response::HTTP_SEE_OTHER);
    }
    /**
     * @Route("/category/modify/{id}", name="mod_cat", methods={"GET", "POST"})
     */
    public function mod($id, Request $request, EntityManagerInterface $entityManager, Category $category): Response
    {
        $category = new Category();
        $form = $this->createForm(CategoryFormType::class, $category);

        $form->handleRequest($request);

        if ($form->isSubmitted()  && $form->isValid()) {
            $cc = $entityManager->getRepository(Category::class)->findBy(['id' => $id])[0];
            $entityManager->remove($cc);
            $entityManager->persist($category);
            $entityManager->flush();

            return $this->redirectToRoute('show_cat', [], Response::HTTP_SEE_OTHER);
        }
        $cat = $entityManager->getRepository(Category::class)->findBy(['id' => $id])[0];
        return $this->render('category/mod.html.twig', [
            'form' => $form->createView(),
            'cat' => $cat
        ]);
    }
    /**
     * @Route("/category/modifyy/{id}", name="modr_cat", methods={"post"})
     */
    public function mod1($id, Request $request, EntityManagerInterface $entityManager): Response
    {
        $category = new Category();
        $form = $this->createForm(CategoryFormType::class, $category);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $category->setId($form['id']->getData());
            $category->setNom($form['name']->getData());
            $entityManager->persist($category);
            $entityManager->flush();

            return $this->redirectToRoute('show_cat', [], Response::HTTP_SEE_OTHER);
        }
        return new Response();
    }
}
