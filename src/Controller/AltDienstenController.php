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

    #[Route('/alt/diensten/voip-telefooncentrale', name: 'app_dienst_voip-telefooncentrale')]
    public function voip(): Response
    {
        return $this->render('alt_diensten/voip-telefooncentrale.html.twig');
    }

    #[Route('/alt/diensten/overstappen-op-voip-telefonie', name: 'app_dienst_overstappen-op-voip-telefonie')]
    public function overstap(): Response
    {
        return $this->render('alt_diensten/overstappen-op-voip-telefonie.html.twig');
    }

    #[Route('/alt/diensten/mogelijkheden-van-voip-telefonie', name: 'app_dienst_mogelijkheden-van-voip-telefonie')]
    public function mogelijkheden(): Response
    {
        return $this->render('alt_diensten/mogelijkheden-van-voip-telefonie.html.twig');
    }

    #[Route('/alt/diensten/zakelijke-telefonie-pakketten', name: 'app_dienst_zakelijke-telefonie-pakketten')]
    public function zakelijk(): Response
    {
        return $this->render('alt_diensten/zakelijke-telefonie-pakketten.html.twig');
    }

}
