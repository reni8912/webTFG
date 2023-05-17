<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Doctrine\Persistence\ManagerRegistry;
use App\Entity\UserS;
use App\Entity\InvoiceState;
use App\Entity\UserB;
use Symfony\Component\HttpFoundation\Request;
use App\Entity\Invoice;
use Knp\Component\Pager\PaginatorInterface;
use Doctrine\ORM\EntityManagerInterface;
use Stripe\Stripe;
use Stripe\Charge;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Component\Security\Core\Security;
 


class InvoiceController extends AbstractController
{
    #[Route('/facturas', name: 'facturas')]
    public function facturas(Request $request,PaginatorInterface $paginator,ManagerRegistry $doctrine): Response
    {

        $user = $this->getUser();
        $invoices = $doctrine->getRepository(Invoice::class)->findBy(['userB'=>$user],['creationDate'=> 'DESC' ]);
        $pagination = $paginator->paginate(
            $invoices,
            $request->query->getInt('page', 1),
            4
        );
        

        return $this->render('invoice/index.html.twig', [
            'invoices'=>$pagination
        ]);
    }


    #[Route('/createInvoice/{userS}/{money}/{description}', name: 'createInvoice')]
    #[IsGranted("ROLE_USER")]
public function createInvoice( $userS, Security $security, $money, $description, Request $request, EntityManagerInterface $entityManager, PaginatorInterface $paginator, ManagerRegistry $doctrine): Response
{

    $userS = str_replace("7b6X", ".", $userS);
    $money = str_replace("7b6X",".", $money);
    $description = str_replace("7b6X", ".", $description);

    $invoice = new Invoice();

    $b = $security->getUser();

    $s =  $doctrine->getRepository(userS::class)->findOneBy(['email'=>$userS]);
    $p =  $doctrine->getRepository(InvoiceState::class)->findOneBy(['id'=>2]);

    $invoice->setUserB($b);
    $invoice->setUserS($s); 
    $invoice->setMoney($money); 
    $invoice->setDescription($description);
    $invoice->setCreationDate(new \DateTime());
    $invoice->setState($p);

    $entityManager = $doctrine->getManager(); 
    $entityManager->persist($invoice); 
    $entityManager->flush();

    return $this->redirectToRoute('facturas');

}
  

#[Route('/stripe{id}', name: 'stripe')]
public function stripe($id,ManagerRegistry $doctrine): Response
{

    $user = $this->getUser();
    $invoices = $doctrine->getRepository(Invoice::class)->findOneBy(['id'=>$id, ]);

    return $this->render('invoice/pay.html.twig', [
        'stripe_key' => $_ENV["STRIPE_SECRET_KEY"],
        'invoice' => $invoices,
    ]);
}


public function createCharge(Request $request)
{
    Stripe\Stripe::setApiKey($_ENV["STRIPE_SECRET_KEY"]);
    Stripe\Charge::create ([
            "amount" => $invoice->getMoney,
            "currency" => "usd",
            "source" => $request->request->get('stripeToken'),
            "description" => "Binaryboxtuts Payment Test"
    ]);
    $this->addFlash(
        'success',
        'Payment Successful!'
    );
    return $this->redirectToRoute('app_stripe', [], Response::HTTP_SEE_OTHER);
}


}



