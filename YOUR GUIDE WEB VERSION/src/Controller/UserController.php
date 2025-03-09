<?php

namespace App\Controller;

use App\Repository\CoursRepository;
use App\Repository\UserRepository;
use Dompdf\Dompdf;
use Dompdf\Options;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Doctrine\Persistence\ManagerRegistry;
use App\Entity\User;
use App\Form\UserUpdateFormType;
class UserController extends AbstractController

{

    /**
     * @Route("/administration/user/show={id}", name="user")
     */
    public function index(ManagerRegistry $doctrine, int $id): Response
    {
        $user = $doctrine->getRepository(User::class)->find($id);

        return $this->render('administration/user/show.html.twig', array('user' =>$user));

    }
    /**
     * @Route("/administration/user/remove={id}", name="user_remove")
     */
    public function remove(ManagerRegistry $doctrine, int $id) : Response{
         $entityManager = $doctrine->getManager();
        $user = $doctrine->getRepository(User::class)->find($id);

        $entityManager->remove($user);
        $entityManager->flush();
        return $this->redirectToRoute('user_list');
    }
    /**
     * @Route("/administration/user/update={id}", name="user_update")
     */
    public function update(ManagerRegistry $doctrine, int $id, Request $request): Response {
        $user = $doctrine->getRepository(User::class)->find($id);
        $form = $this->createForm(UserUpdateFormType::class,$user);
        $form->handleRequest($request);
         if ($form->isSubmitted() && $form->isValid()) {
            /** @var User $user */
            $em = $doctrine->getManager();
            $user = $form->getData();
            $em->persist($user);
            $em->flush();
            $this->addFlash('success', 'User updated!');
            return $this->redirectToRoute('user_list');
        }
        return $this->render('administration/user/update.html.twig', [
            'userUpdate' => $form->createView()
        ]);
    }
    /**
     * @Route("/administration/user/showAll", name="user_list")
     */
    public function list(ManagerRegistry $doctrine): Response {
        $users = $doctrine->getRepository(User::class)->findAll();
        return $this->render('/administration/user/showAll.html.twig', ['users' => $users]);
    }

    /**
     * @Route("/pdf/{id}", name="pdf" ,  methods={"GET"})
     */
    public function pdf($id,UserRepository $repository){

        $user=$repository->find($id);

        $pdfOptions = new Options();
        $pdfOptions->set('defaultFont', 'Arial');
        $dompdf = new Dompdf($pdfOptions);
        $html = $this->renderView('administration/pdf.html.twig', [
            'pdf' => $user
        ]);
        $dompdf->loadHtml($html);

        // (Optional) Setup the paper size and orientation 'portrait' or 'portrait'
        $dompdf->setPaper('A4', 'portrait');

        // Render the HTML as PDF
        $dompdf->render();
        //  $dompdf->stream();
        // Output the generated PDF to Browser (force download)
        $dompdf->stream($user->getUsername(), [
            "Attachment" => false
        ]);




    }



}
