<?php

namespace App\Controller;
use App\Entity\Guide;
use App\Entity\Activite;
use App\Form\ActiviteFormType;
use App\Repository\ActiviteRepository;
use App\Entity\Category;
use App\Entity\Produit;
use App\Form\ProduitType;
use App\Repository\CategoryRepository;
use App\Repository\GuideRepository;
use App\Repository\ProduitRepository;
use Doctrine\ORM\EntityManagerInterface;
use Knp\Component\Pager\PaginatorInterface;
use phpDocumentor\Reflection\Type;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Routing\Annotation\Route;
use function PHPUnit\Framework\isEmpty;

class FrontController extends AbstractController
{

    /**
     * @Route("/home", name="home", methods={"GET"})
     */
    public function index(ProduitRepository $produitRepository): Response
    {
        return $this->render('front/home.html.twig');
    }
    /**
     * @Route("/home/products", name="home_products", methods={"GET"})
     */
    public function inedex(ActiviteRepository $activiteRepository,PaginatorInterface $paginator,Request $request): Response
    {
        $tablerepo=$activiteRepository->findAll();



        return $this->render('front/products.html.twig', [

            'type' =>$tablerepo,
        ]);
//        return $this->render('front/products.html.twig', [
//            'activite' => $produitRepository->findAll(),
//            'guide' => $this->getDoctrine()->getRepository(Type::class)->findAll(),
//        ]);
    }
    /**
     * @Route("/home/guide", name="home_guide", methods={"GET"})
     */
    public function ined(GuideRepository $guideRepository): Response
    {

        return $this->render('front/guide.html.twig', [
            'type' => $guideRepository->findAll(),
        ]);}




    /**
     * @Route("/home/products/{cat}", name="cat_home_prod", methods={"GET"})
     */
    public function catPick($cat, ProduitRepository $produitRepository, CategoryRepository $categoryRepository): Response
    {
        if($cat != "Select categroy...") {
            $catg = $categoryRepository->findBy(array('nom' => $cat))[0];
            //dd($catg);
            $list = $produitRepository->findBy(array("category" => $catg->id));

        }else{
            $list = $produitRepository->findAll();
        }
        return $this->render('front/products.html.twig', [
            'cc' => $cat,
            'products' => $list,
            'catgs' => $this->getDoctrine()->getRepository(Category::class)->findAll(),
        ]);
    }
    /**
     * @Route("/admin", name="front")
     */
    public function index1(): Response
    {
        return $this->render('front/index.html.twig', [
            'controller_name' => 'FrontController',
        ]);
    }
    /**
     * @Route("/admin/produit/add", name="add_prod")
     */
    public function add(Request $request, EntityManagerInterface $entityManager, CategoryRepository $repo): Response
    {

        $produit = new Produit();
        $form = $this->createForm(ProduitType::class, $produit);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            //dd($form->getData());
            $cc = $this->getDoctrine()->getRepository(Category::class)->findBy(['nom' => $produit->getCategory()->getNom()])[0];
            //dd($cc);
            $produit->setCategory($cc);
            $entityManager->persist($produit);
            $entityManager->flush();

            return $this->redirectToRoute('add_prod', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('produit/new.html.twig', [
            'produit' => $produit,
            'form' => $form->createView(),
        ]);

    }
    /**
     * @Route("/admin/produit/show", name="show_prod")
     */
    public function show(ProduitRepository $produitRepository): Response
    {
        return $this->render('produit/show.html.twig', [
            'products' => $produitRepository->findAll(),
        ]);

    }
    /**
     * @Route("/admin/produit/delete/{id}", name="delete_prod")
     */
    public function del(Request $request, Produit $id, EntityManagerInterface $entityManager): Response
    {

        //if ($this->isCsrfTokenValid('delete'.$id->getId(), $request->request->get('_token'))) {
        $entityManager->remove($id);
        $entityManager->flush();
        //}

        return $this->redirectToRoute('show_prod', [], Response::HTTP_SEE_OTHER);
    }
    /**
     * @Route("/admin/produit/mod/{id}", name="mod_prod", methods={"GET", "POST"})
     */
    public function edit(Request $request, Produit $id, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(ProduitType::class, $id);
        $u = $form['category']->getData();
        //dd($request);
        $form->handleRequest($request);
        //dd("krrr");
        if ($form->isSubmitted() && $form->isValid()) {
            //nlawjo "am categ bel esm
            $cc = $this->getDoctrine()->getRepository(Category::class)->find(1);
            $id->setCategory($cc);
            //dd($id);
            $entityManager->persist($id);
            $entityManager->flush();

            return $this->redirectToRoute('show_prod', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('produit/mod.html.twig', [
            'produit' => $id,
            'form' => $form->createView(),
        ]);
    }


    /**
     * @Route("/stat", name="stat", methods={"GET"})
     */
    public function stats(ProduitRepository $prodrepo){
        $produits=$prodrepo->findAll();
        $prodprix=[];
        $prodcount=[];
        foreach ($produits as $produit){
            $prodprix[]=$produit->getPrix();
            //   $produitcount[]=count($produit->getAnnonce);

        }
        return $this->render('produit/stats.html.twig',[
            'prodprix'=> json_encode($prodprix),
            //    'prodcount'=>json_encode($produit)
        ]);

    }

    /**
     * @Route("/front", name="front")
     */
    public function indexx(ProduitRepository  $productsRepository, SessionInterface $session): Response
    {
        $panier = $session->get("panier", []);

        // On "fabrique" les données


        foreach ($panier as $id => $quantite) {
            $product = $productsRepository->find($id);
            $dataPanier[] = [
                "produit" => $product,
                "quantite" => $quantite
            ];
            $quantiteAll = +$quantite;
        }

        return $this->render('base.html.twig', compact("quantiteAll"));








        return $this->render('front/index.html.twig', [
            'controller_name' => 'FrontController',
        ]);
    }
    /**
     * @Route("/back", name="back")
     */
    public function back(): Response
    {
        return $this->render('back/back.html.twig', [
            'controller_name' => 'back_controller',
        ]);
    }


}