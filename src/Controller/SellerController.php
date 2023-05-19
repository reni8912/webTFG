<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Doctrine\Persistence\ManagerRegistry;
use App\Entity\UserS;
use Symfony\Component\HttpFoundation\Request;
use GuzzleHttp\Client;
use Knp\Component\Pager\PaginatorInterface;



class SellerController extends AbstractController
{
    public function encodeImage($image)
    {
        $data = stream_get_contents($image);
        $encodedImage = base64_encode($data);
        return $encodedImage;
    }
    

    #[Route('', name: 'index')]
    public function index(PaginatorInterface $paginator,ManagerRegistry $doctrine,Request $request, Client $httpClient): Response
    {

        $response = $httpClient->get('http://ip-api.com/json/');
        $result = json_decode($response->getBody()->getContents(), true);
        $city = $result['regionName'];
  
        $users = $doctrine->getRepository(UserS::class)->findBy(['city' => $city],['id' => 'ASC']);
        
    
        $sellers = [];
    
        foreach ($users as $user) {
            $encodedImage = $this->encodeImage($user->getImage());
            $sellers[] = [
                'user' => $user,
                'encodedImage' => $encodedImage
            ];
        }
        $pagination = $paginator->paginate(
            $sellers,
            $request->query->getInt('page', 1),
            4
        );
    
        return $this->render('seller/index.html.twig', [
            'sellers' => $pagination
        ]);
    }

    #[Route('filtrado/{type}', name: 'filtro')]
    public function filtro(PaginatorInterface $paginator,$type, ManagerRegistry $doctrine,Request $request, Client $httpClient): Response
    {

        $response = $httpClient->get('http://ip-api.com/json/');
        $result = json_decode($response->getBody()->getContents(), true);
        $city = $result['regionName'];
        
        $users = $doctrine->getRepository(UserS::class)->findBy(['city' => $city, 'type' => $type],['id' => 'ASC']);
       
    
        $sellers = [];
    
        foreach ($users as $user) {
            $encodedImage = $this->encodeImage($user->getImage());
            $sellers[] = [
                'user' => $user,
                'encodedImage' => $encodedImage
            ];
        }
        $pagination = $paginator->paginate(
            $sellers,
            $request->query->getInt('page', 1),
            4
        );
    
        return $this->render('seller/index.html.twig', [
            'sellers' => $pagination
        ]);
    }
    
}
