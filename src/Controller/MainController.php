<?php

namespace App\Controller;


use App\Repository\UserRepository;
use App\Service\IntraController;
use App\Service\JwtService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;

use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Messenger\Exception\ExceptionInterface;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Component\Routing\Attribute\Route;

final class MainController extends AbstractController
{
    /**
     * @param IntraController $intraController
     * @param MessageBusInterface $messageBus
     * @param JwtService $jwtService
     * @param RegistrationController $registrationController
     * @param UserRepository $userRepository
     * @param EntityManagerInterface $em
     * @return Response
     * @throws ExceptionInterface
     */
    #[Route('/', name: 'app_main')]
    public function index(IntraController $intraController,MessageBusInterface $messageBus,JwtService $jwtService,
                  RegistrationController $registrationController,UserRepository $userRepository,EntityManagerInterface $em ): Response
    {
        if($this->getUser()) {
            if(IntraController::userVerified($this->getUser())){
                $this->ActivedUser($intraController,$jwtService,$messageBus);
            }
           if(IntraController::userCompleted($this->getUser())){
               $this->addFlash('warning','You should completed your identity account');
               return $this->redirectToRoute('app_identity');
           }
           if(IntraController::userLogged($this->getUser())){
               $this->addFlash('warning','Check your identity-device');
               $registrationController->verifiedDevice($userRepository,$intraController,$messageBus,$em);
               return $this->redirectToRoute('app_verified_user');
           }
           if(IntraController::userHacked($this->getUser())){
               return $this->redirectToRoute('app_logout');
           }
        }
        return $this->render('main/index.html.twig');
    }
    /**
     * @param IntraController $intraController
     * @param JwtService $jwtService
     * @param MessageBusInterface $messageBus
     * @return void
     * @throws ExceptionInterface
     */
    private function ActivedUser(IntraController $intraController,JwtService $jwtService,MessageBusInterface $messageBus):void
    {
        $this->addFlash('info','You have to activated your account, please visit your email box');
        $intraController->emailValidate($this->getUser(),$jwtService,$messageBus);
    }




}
