<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class DienstenController extends AbstractController
{
    #[Route('/diensten', name: 'app_diensten')]
    public function index(): Response
    {
        return $this->render('diensten/index.html.twig', [
            'controller_name' => 'DienstenController',
        ]);
    }
}
