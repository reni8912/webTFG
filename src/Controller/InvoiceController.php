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
}
