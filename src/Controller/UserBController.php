<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;
use Doctrine\ORM\EntityManagerInterface;
use App\Entity\UserB;
use App\Entity\Invoice;
use App\Form\UserBType;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Doctrine\Persistence\ManagerRegistry;



class UserBController extends AbstractController
{

    private $em;
    /**
     * @param $em
     */
    
     public function __construct(EntityManagerInterface $em){
    
        $this->em=$em;
    }

   
    #[Route('/register', name: 'register')]
    public function register(Request $request, UserPasswordHasherInterface $passwordHasher, ManagerRegistry $doctrine): Response
    {
        $user = new UserB();
        $registration_form = $this->createForm(UserBType::class, $user);
        $registration_form->handleRequest($request);
        if ($registration_form-> isSubmitted() && $registration_form->isValid()) {
           
            $plaintextPassword = $registration_form->get('password')->getData();
            $hashedPassword = $passwordHasher->hashPassword(
                $user,
                $plaintextPassword
            );
            $user->setPassword($hashedPassword);
            
            $this->em->persist($user);
            $this->em->flush();
            return $this->redirectToRoute('app_login');
           }
        return $this->render('user_b/index.html.twig', [
        
            'registration_form' => $registration_form->createView()

        ]);
    }

    #[Route('/profile', name: 'chart')]
    public function chart(Request $request,ManagerRegistry $doctrine): Response
    {
        $invoice = $doctrine->getRepository(Invoice::class)->findBy(['userB' => $this->getUser()]);
        $datos = [];
        
        foreach ($invoice as $invoice) {
            $datos[] = [
                'fecha' => $invoice->getCreationDate()->format('Y-m-d H:i:s'), 
                'precio' => $invoice->getMoney(),
            ];
        }
        
       
        $datosJson = json_encode($datos);
        
        return $this->render('user_b/profile.html.twig', [
            'datosJson' => $datosJson,
        ]);
    }
}

