<?php

namespace App\Controller;

use App\Repository\UserRepository;
use App\Service\IntraController;
use App\Service\JwtService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Messenger\Exception\ExceptionInterface;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Component\Routing\Attribute\Route;

final class MainController extends AbstractController
{
    /**
     * @throws ExceptionInterface
     */
    #[Route('/', name: 'app_main')]
    public function index(IntraController $intraController,JwtService $jwtService,MessageBusInterface $messageBus,UserRepository $userRepository ,
                          RegistrationController $registrationController,EntityManagerInterface $em): Response
    {
        if($this->getUser() && $this->getUser()->isVerified() && $this->getUser()->isCompleted() && !($this->getUser()->isLogged()))
        {
            $this->addFlash('warning','Check your identity-device');
            //verifiedDevice(UserRepository $userRepository,IntraController $intraController , MessageBusInterface $messageBus, EntityManagerInterface $em

            $registrationController->verifiedDevice($userRepository,$intraController,$messageBus,$em);
            return $this->redirectToRoute('app_verified_user');
        }
        if($this->getUser() && !($this->getUser()->isVerified())){
            $this->addFlash('info','You have to activated your account, please visit your email box');
            $intraController->emailValidate($this->getUser(),$jwtService,$messageBus);
            }

        if($this->getUser() && $this->getUser()->isVerified() && !($this->getUser()->isCompleted()))
        {
            $this->addFlash('info','You should completed your identity account');
            return $this->redirectToRoute('app_identity');
        }


        if($this->getUser() && $this->getUser()->isVerified() && $this->getUser()->isCompleted()) {
            if(!($this->getUser()->isLogged())){
                $this->addFlash('danger','You has been disconnected !');
                return $this->redirectToRoute('app_logout');
            }
        }

        return $this->render('main/index.html.twig', [
            'controller_name' => 'MainController',
        ]);
    }
}
