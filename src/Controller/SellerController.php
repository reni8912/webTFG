<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Doctrine\Persistence\ManagerRegistry;
use App\Entity\UserS;
use GeoIp2\Database\Reader;


class SellerController extends AbstractController
{
    public function encodeImage($image)
    {
        $data = stream_get_contents($image);
        $encodedImage = base64_encode($data);
        return $encodedImage;
    }

    #[Route('', name: 'tiendas')]
    public function tiendas(ManagerRegistry $doctrine): Response
    {
        $users = $doctrine->getRepository(UserS::class)->findAll();
    
        $sellers = [];
    
        foreach ($users as $user) {
            $encodedImage = $this->encodeImage($user->getImage());
            $sellers[] = [
                'user' => $user,
                'encodedImage' => $encodedImage
            ];
        }
    
        return $this->render('seller/index.html.twig', [
            'sellers' => $sellers
        ]);
    }

    
    
}
