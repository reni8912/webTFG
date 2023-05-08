<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Doctrine\Persistence\ManagerRegistry;
use App\Entity\UserS;
use Symfony\Component\HttpFoundation\Request;
use App\Entity\Invoice;
use Knp\Component\Pager\PaginatorInterface;
use Doctrine\ORM\EntityManagerInterface;

class InvoiceController extends AbstractController
{
    #[Route('/facturas', name: 'facturas')]
    public function facturas(Request $request,PaginatorInterface $paginator,ManagerRegistry $doctrine): Response
    {

        $user = $this->getUser();
        $invoices = $doctrine->getRepository(Invoice::class)->findBy([],['creationDate'=> 'DESC' ]);
        $pagination = $paginator->paginate(
            $invoices,
            $request->query->getInt('page', 1),
            4
        );
        

        return $this->render('invoice/index.html.twig', [
            'invoices'=>$pagination
        ]);
    }

    #[Route('/createInvoice/{userB}/{userS}/{money}/{description}', name: 'createInvoice')]
public function createInvoice($userB, $userS, $money, $description, Request $request, EntityManagerInterface $entityManager, PaginatorInterface $paginator, ManagerRegistry $doctrine): Response
{

    $invoice = new Invoice();

    $invoice->setUserB($userB);
    $invoice->setUserS($userS); // Corregido: setUserS en lugar de setUserB
    $invoice->setMoney($money); // Corregido: setMoney en lugar de setUserB
    $invoice->setDescription($description); // Corregido: setDescription en lugar de setUserB
    $invoice->setCreationDate(new DateTime());

    $entityManager = $doctrine->getManager(); // Corregido: se eliminó "$this->"
    $entityManager->persist($invoice); // Corregido: "$product" cambiado a "$invoice"
    $entityManager->flush();

    return $this->redirectToRoute('facturas');

}


}
