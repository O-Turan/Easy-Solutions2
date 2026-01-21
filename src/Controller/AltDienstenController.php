<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class AltDienstenController extends AbstractController
{
    #[Route('/alt/diensten', name: 'app_alt_diensten')]
    public function index(): Response
    {
        return $this->render('alt_diensten/index.html.twig', [
            'controller_name' => 'AltDienstenController',
        ]);
    }
}
