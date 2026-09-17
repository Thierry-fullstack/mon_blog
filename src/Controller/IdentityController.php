<?php

namespace App\Controller;


use App\Form\IdentityType;
use App\Repository\IdentityRepository;
use App\Service\FormService;
use Doctrine\ORM\Exception\ORMException;
use Exception;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;


final class IdentityController extends AbstractController
{
    /**
     * @throws Exception|ORMException
     */
    #[Route('/identity/register', name: 'app_identity',methods: ['GET','POST'])]
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
}
