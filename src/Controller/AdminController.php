<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;
use Doctrine\ORM\EntityManagerInterface;
use App\Entity\UserB;
use App\Entity\UserS;
use App\Form\UserSType;
use App\Entity\Invoice;
use App\Entity\InvoiceState;
use App\Form\UserBType;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Doctrine\Persistence\ManagerRegistry;



class AdminController extends AbstractController
{

    private $em;
    /**
     * @param $em
     */
    
     public function __construct(EntityManagerInterface $em){
    
        $this->em=$em;
    }

    #[Route('/admin', name: 'admin')]
    public function index(): Response
    {
        return $this->render('admin/index.html.twig', [
            'controller_name' => 'AdminController',
        ]);
    }

    #[Route('/admin/invoice', name: 'adminInvoice')]
    public function adminInvoice(ManagerRegistry $doctrine): Response
    {

        $state   = $doctrine->getRepository(InvoiceState::class)->findBy(['state'=> 'Sin Pagar']);
        $invoice = $doctrine->getRepository(Invoice::class)->findBy(['state'=>$state]);


        return $this->render('admin/invoice.html.twig', [
            'invoice' => $invoice,
        ]);
    }

    #[Route('/admin/service', name: 'adminService')]
    public function adminService(ManagerRegistry $doctrine, UserPasswordHasherInterface $passwordHasher, Request $request, EntityManagerInterface $entityManager): Response
    {
        $user = new UserS();
        $registrationForm = $this->createForm(UserSType::class, $user);
        $registrationForm->handleRequest($request);
    
        if ($registrationForm->isSubmitted() && $registrationForm->isValid()) {
            $plaintextPassword = $registrationForm->get('password')->getData();
            $hashedPassword = $passwordHasher->hashPassword($user, $plaintextPassword);
            $user->setPassword($hashedPassword);

            $file = $registrationForm->get('image')->getData();
            if ($file !== null) {
                $user->setImage(file_get_contents($file->getPathname()));
            }
    
            $entityManager->persist($user);
            $entityManager->flush();
    
            return $this->redirectToRoute('adminService');
        }
    
        return $this->render('admin/createUserS.html.twig', [
            'registration_form' => $registrationForm->createView()
        ]);
    }

    #[Route('/admin/reject/{id}', name: 'reject')]
    public function reject($id,ManagerRegistry $doctrine, UserPasswordHasherInterface $passwordHasher, Request $request,EntityManagerInterface $entityManager): Response
    {
        $invoice = $doctrine->getRepository(Invoice::class)->find($id);
    
            if (!$invoice) {
                throw $this->createNotFoundException('No Invoice found for id ' . $id);
            }

            $entityManager->remove($invoice);
            $entityManager->flush();
        
            return $this->redirectToRoute('adminInvoice');
        }
}
