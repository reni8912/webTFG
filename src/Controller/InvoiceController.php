<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;


class InvoiceController extends AbstractController
{
    #[Route('/facturas', name: 'facturas')]
    public function facturas(): Response
    {

         


        return $this->render('invoice/index.html.twig', [
             
        ]);
    }
}
