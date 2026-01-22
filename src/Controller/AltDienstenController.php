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


    #[Route('/alt/diensten/088-nummers', name: 'app_dienst_088_nummers')]
    public function nummer088(): Response
    {
        return $this->render('alt_diensten/088nummer.html.twig');
    }

    #[Route('/alt/diensten/085-nummers', name: 'app_dienst_085_nummers')]
    public function nummer085(): Response
    {
        return $this->render('alt_diensten/085nummer.html.twig');
    }

    #[Route('/alt/diensten/nummer-kiezen', name: 'app_dienst_nummer_kiezen')]
    public function nummerKiezen(): Response
    {
        return $this->render('alt_diensten/nummerkiezen.html.twig');
    }
    #[Route('/alt/diensten/regionale-nummers', name: 'app_dienst_regionale_nummers')]
    public function regionaleNummers(): Response
    {
        return $this->render('alt_diensten/regionale.index.html.twig');
    }
    #[Route('/alt/diensten/zakelijktelefoonnummers', name: 'app_alt_diensten_zakelijktelefoonnummers')]
    public function zakelijktelefoonnummers(): Response
    {
        return $this->render('alt_diensten/zakelijktelefoonnummers.html.twig');
    }

    #[Route('/alt/diensten/vast-op-de-mobiel', name: 'app_dienst_vast-op-de-mobiel')]
    public function vast(): Response
    {
        return $this->render('alt_diensten/vast-op-de-mobiel.html.twig');
    }

}
