<?php

namespace App\Controller\Profil;


use App\Form\IdentityType;
use App\Form\VerifyNumberType;
use App\Repository\IdentityRepository;
use App\Repository\UserRepository;
use App\Service\FormService;
use App\Service\IntraController;
use DateTimeImmutable;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Exception\ORMException;
use Exception;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Messenger\Exception\ExceptionInterface;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/profile/',name: 'profile_')]
final class IdentityController extends AbstractController
{
    /**
     * @throws Exception|ORMException
     */
    #[Route('identity/register', name: 'profile_app_identity',methods: ['GET','POST'])]
    public function index(RequestStack $requestStack,IdentityRepository $identityRepository,FormService $formService): Response
    {
        if(!$this->getUser()){
            return $this->redirectToRoute('app_login');
        }
        if($this->getUser()->isCompleted()){
            return $this->redirectToRoute('app_main');
        }
        $request =$requestStack->getMainRequest();
        $form = $this->createForm(IdentityType::class,$identityRepository->findByGender());
        $form->handleRequest($request);
        if($request->isMethod('POST')){
            if($form->isSubmitted()){
                $user = $this->getUser();
                return $formService->handleFormData($form,$user);
            }
        }
        return $this->render('identity/index.html.twig', [
            'form'=>$form->createView()
        ]);
    }

    /**
     * @param UserRepository $userRepository
     * @param IntraController $intraController
     * @param MessageBusInterface $messageBus
     * @param EntityManagerInterface $em
     * @return Response
     * @throws ExceptionInterface
     */
    #[Route('register/verified',name: 'app_register_verified')]
    public function verifiedDevice(UserRepository $userRepository,IntraController $intraController , MessageBusInterface $messageBus, EntityManagerInterface $em
    ):Response
    {
        $user = $userRepository->find($this->getUser());
        $user?->setResetDateTime(new  DateTimeImmutable());
        $number = mt_rand(100001,999999);
        $intraController->emailSimple($user,$messageBus,['user'=>$user,'number'=>$number]);
        $user->setResetDateTime(new DateTimeImmutable())->setResetNumber($number)->setIsLogged(false);
        $em->flush();
        return $this->redirectToRoute('profile_app_verified_user');
    }

    /**
     * @param EntityManagerInterface $em
     * @param Request $request
     * @return Response
     */
    #[Route('register/user',name: 'app_verified_user',methods: ['GET','POST'])]
    public function verifiedUser(EntityManagerInterface $em,Request $request):Response
    {
        $user = $this->getUser();
        $form = $this->createForm(VerifyNumberType::class,$user);
        $form->handleRequest($request);
        if($request->isMethod('POST')) {
            if ($form->isSubmitted() && $form->isValid()) {
                $number = $form->get('number')->getData();
                $now = new DateTimeImmutable();
                $today = $now->getTimestamp();
                $validity = 900;
                $limitTime = $user->getResetDateTime()->getTimestamp() + $validity;
                if (strcmp($number, $user->getResetNumber() && $limitTime <= $today)) {
                    $user->setResetNumber(null)->setResetDateTime(null)->setIsLogged(true);
                    $em->flush();
                    return $this->redirectToRoute('app_main');
                } else {
                    $this->addFlash('warning', 'Erreur !');
                    return $this->redirectToRoute('app_login');
                }
            }
        }
        return $this->render('identity/verified-device.html.twig',['form'=>$form->createView()]);
    }
}
