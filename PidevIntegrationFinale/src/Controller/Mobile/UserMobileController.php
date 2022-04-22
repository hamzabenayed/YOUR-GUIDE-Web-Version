<?php

namespace App\Controller\Mobile;

use App\Entity\ User;
use App\Repository\ UserRepository;
use DateTime;
use Doctrine\ORM\EntityManagerInterface;
use Swift_Mailer;
use Swift_Message;
use Swift_SmtpTransport;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

/**
 * @Route("/mobile/user")
 */
class UserMobileController extends AbstractController
{
    /**
     * @Route("", methods={"GET"})
     */
    public function index(UserRepository $userRepository): Response
    {
        $users = $userRepository->findAll();

        if ($users) {
            return new JsonResponse($users, 200);
        } else {
            return new JsonResponse([], 204);
        }
    }

    /**
     * @Route("/show", methods={"POST"})
     */
    public function show(Request $request, UserRepository $userRepository): Response
    {
        $user = $userRepository->find((int)$request->get("id"));

        if ($user) {
            return new JsonResponse($user, 200);
        } else {
            return new JsonResponse([], 204);
        }
    }

    /**
     * @Route("/add", methods={"POST"})
     */
    public function add(Request $request, UserPasswordEncoderInterface $userPasswordEncoder): JsonResponse
    {
        $user = new User();

        return $this->manage($user, $request, false, $userPasswordEncoder);
    }

    /**
     * @Route("/edit", methods={"POST"})
     */
    public function edit(Request $request, UserRepository $userRepository, UserPasswordEncoderInterface $userPasswordEncoder): Response
    {
        $user = $userRepository->find((int)$request->get("id"));

        if (!$user) {
            return new JsonResponse(null, 404);
        }

        return $this->manage($user, $request, true, $userPasswordEncoder);
    }

    public function manage($user, $request, $isEdit, $userPasswordEncoder): JsonResponse
    {
        $checkLogin = $this->getDoctrine()->getRepository(User::class)
            ->findOneBy(["login" => $request->get("login")]);

        if (!$isEdit) {
            if ($checkLogin) {
                return new JsonResponse("Login already exist", 203);
            }
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

        $user->setUp(
            $request->get("login"),
            $request->get("roles"),
            $request->get("nom"),
            $imageFileName
        );

        $user->setPassword($userPasswordEncoder->encodePassword($user, $request->get("mdp")));

        if (!$isEdit) {
            $login = $user->getLogin();
            if (filter_var($login, FILTER_VALIDATE_EMAIL)) {
                $transport = new Swift_SmtpTransport('smtp.gmail.com', 465, 'ssl');
                $transport->setUsername('pidev.app.esprit@gmail.com')->setPassword('pidev-cred');
                $mailer = new Swift_Mailer($transport);
                $message = new Swift_Message('Khedmetna');
                $message->setFrom(array('pidev.app.esprit@gmail.com' => 'Welcome to Khedmetna'))
                    ->setTo(array($login => $login))
                    ->setBody("<h1>Welcome to Khedmetna</h1>", 'text/html');
                $mailer->send($message);
            }
        }

        $entityManager = $this->getDoctrine()->getManager();
        $entityManager->persist($user);
        $entityManager->flush();

        return new JsonResponse($user, 200);
    }

    /**
     * @Route("/delete", methods={"POST"})
     */
    public function delete(Request $request, EntityManagerInterface $entityManager, UserRepository $userRepository): JsonResponse
    {
        $user = $userRepository->find((int)$request->get("id"));

        if (!$user) {
            return new JsonResponse(null, 200);
        }

        $entityManager->remove($user);
        $entityManager->flush();

        return new JsonResponse([], 200);
    }

    /**
     * @Route("/deleteAll", methods={"POST"})
     */
    public function deleteAll(EntityManagerInterface $entityManager, UserRepository $userRepository): Response
    {
        $users = $userRepository->findAll();

        foreach ($users as $user) {
            $entityManager->remove($user);
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

    /**
     * @Route("/verif", methods={"POST"})
     */
    public function verif(Request $request, UserRepository $userRepository, UserPasswordEncoderInterface $userPasswordEncoder): Response
    {
        $user = $userRepository->findOneBy(["login" => $request->get("login")]);

        if ($user) {
            if ($userPasswordEncoder->isPasswordValid($user, $request->get("mdp"))) {
                return new JsonResponse($user, 200);
            } else {
                return new JsonResponse("user found but pass wrong", 203);
            }
        } else {
            return new JsonResponse([], 204);
        }
    }
}
