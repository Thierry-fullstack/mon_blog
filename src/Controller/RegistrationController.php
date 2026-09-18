<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\RegistrationFormType;
use App\Form\VerifyNumberType;
use App\Repository\UserRepository;
use App\Security\UserAuthenticator;
use App\Service\IntraController;
use App\Service\JwtService;
use DateTimeImmutable;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\EntityNotFoundException;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Messenger\Exception\ExceptionInterface;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class RegistrationController extends AbstractController
{

    /**
     * @param Request $request
     * @param ValidatorInterface $validator
     * @param UserPasswordHasherInterface $userPasswordHasher
     * @param Security $security
     * @param EntityManagerInterface $entityManager
     * @return Response
     */
    #[Route('/register', name: 'app_register',methods: ['GET','POST'])]
    public function register(Request $request,ValidatorInterface $validator,
                             UserPasswordHasherInterface $userPasswordHasher,
                             Security $security, EntityManagerInterface $entityManager): Response
    {
        $user = new User();
        $form = $this->createForm(RegistrationFormType::class, $user);
        $form->handleRequest($request);
        if($request->isMethod('POST')) {
            $errors = $validator->validate($request);
            if(count($errors)>0){
                return $this->render('registration/register.html.twig', [
                    'registrationForm' => $form->createView(),'errors'=>$errors
                ]);
            }
            if ($form->isSubmitted() && $form->isValid()) {
                /** @var bool $rgpd */
                $rgpd = $form->get('agreeTerms')->getData();
                $user->setRgpd($rgpd);
                /** @var string $plainPassword */
                $plainPassword = $form->get('plainPassword')->getData();
                // encode the plain password
                $user->setPassword($userPasswordHasher->hashPassword($user, $plainPassword))->setRoles(['ROLE_USER']);
                    try {
                        $entityManager->persist($user);
                        $entityManager->flush();
                    }catch (EntityNotFoundException $e){
                        return $this->render('registration/register.html.twig', [
                            'registrationForm' => $form->createView(),'exception'=>$e->getMessage()
                        ]);
                    }
                return $security->login($user, UserAuthenticator::class, 'main');
            }
        }
        return $this->render('registration/register.html.twig', [
            'registrationForm' => $form->createView()
        ]);
    }

    /**
     * @param $token
     * @param JwtService $jwtService
     * @param UserRepository $userRepository
     * @param EntityManagerInterface $entityManager
     * @return Response
     */
    #[Route('/check/{token}',name:'check_user')]
    public function verifyUser($token, JwtService $jwtService, UserRepository $userRepository, EntityManagerInterface $entityManager): Response
    {
        // if token valid, expired & !modified
        if($jwtService->isValid($token) && !$jwtService->isExpired($token) && $jwtService->check($token, $this->getParameter('app.jwtsecret'))){
            $payload = $jwtService->getPayload($token);
            //user token
            try{
                $user = $userRepository->find($payload['user_id']);
                $user->setIsVerified(true);
                $entityManager->persist($user);
                $entityManager->flush();
                return $this->redirectToRoute('app_main');
            }catch(EntityNotFoundException $e){
                return $this->redirectToRoute('app_error',['exception'=>$e]);
            }
        }
        $this->addFlash('warning','Déconnection !');
        return $this->redirectToRoute('app_login');

    }

    /**
     * @param JwtService $jwtService
     * @param IntraController $intraController
     * @param MessageBusInterface $messageBus
     */
    #[Route('/register/verif', name: 'app_registration_verif')]
    public function resendVerif(JWTService $jwtService,IntraController $intraController,MessageBusInterface $messageBus):void
    {
    try{
            $intraController->emailValidate($this->getUser(),$jwtService,$messageBus);
    }catch (ExceptionInterface $e){
            $this->addFlash('warning','Ca n\'a pas fonctionné, essayé plus tard.');
    }
        $this->addFlash('light','Pour confirmer votre adresse, consultez votre boite mail !');
    }



}
