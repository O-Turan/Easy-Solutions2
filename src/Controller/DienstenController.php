<?php

// src/Controller/DienstenController.php
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

#[Route('/diensten/hosted-telefonie', name: 'app_dienst_hosted_telefonie')]
public function hostedTelefonie(): Response
{
return $this->render('diensten/hosted_telefonie.html.twig');
}

#[Route('/diensten/white-label', name: 'app_dienst_white_label')]
public function whiteLabel(): Response
{
return $this->render('diensten/white_label.html.twig');
}

#[Route('/diensten/sip-trunk', name: 'app_dienst_sip_trunk')]
public function sipTrunk(): Response
{
return $this->render('diensten/sip_trunk.html.twig');
}

#[Route('/diensten/nummerportering', name: 'app_dienst_nummerportering')]
public function nummerportering(): Response
{
return $this->render('diensten/nummerportering.html.twig');
}

#[Route('/diensten/pbx', name: 'app_dienst_pbx')]
public function pbx(): Response
{
return $this->render('diensten/pbx.html.twig');
}

#[Route('/diensten/hosted-pbx', name: 'app_dienst_hosted_pbx')]
public function hostedPbx(): Response
{
return $this->render('diensten/hosted_pbx.html.twig');
}

#[Route('/diensten/kpn-business-partner', name: 'app_dienst_kpn')]
public function kpn(): Response
{
return $this->render('diensten/kpn.html.twig');
}

#[Route('/diensten/mobiele-app', name: 'app_dienst_mobiele_app')]
public function mobieleApp(): Response
{
return $this->render('diensten/mobiele_app.html.twig');
}
}
