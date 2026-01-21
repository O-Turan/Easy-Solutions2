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

    #[Route('/alt/diensten/telefoonnummers', name: 'app_dienst_telefoonnummers')]
    public function telefoonnummers(): Response
    {
        return $this->render('alt_diensten/telefoonnummers.html.twig');
    }

    #[Route('/alt/diensten/zakelijke-voip-telefonie', name: 'app_dienst_zakelijke-voip_telefonie')]
    public function zakelijke(): Response
    {
        return $this->render('alt_diensten/zakelijke-voip-telefonie.html.twig');
    }

    #[Route('/alt/diensten/zakelijktelefoonnummers', name: 'app_alt_diensten_zakelijktelefoonnummers')]
    public function zakelijktelefoonnummers(): Response
    {
        return $this->render('alt_diensten/zakelijktelefoonnummers.html.twig');
    }


}
