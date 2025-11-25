<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class OverOnsController extends AbstractController
{
    #[Route('/over/ons', name: 'app_over_ons')]
    public function index(): Response
    {
        return $this->render('over_ons/index.html.twig', [
            'controller_name' => 'OverOnsController',
        ]);
    }
}
