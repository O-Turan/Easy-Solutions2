<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class AlgemeneVoorwaardenController extends AbstractController
{
    #[Route('/algemene/voorwaarden', name: 'app_algemene_voorwaarden')]
    public function index(): Response
    {
        return $this->render('algemene_voorwaarden/index.html.twig', [
            'controller_name' => 'AlgemeneVoorwaardenController',
        ]);
    }
}
