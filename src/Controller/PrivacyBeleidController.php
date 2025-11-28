<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class PrivacyBeleidController extends AbstractController
{
    #[Route('/privacy/beleid', name: 'app_privacy_beleid')]
    public function index(): Response
    {
        return $this->render('privacy_beleid/index.html.twig', [
            'controller_name' => 'PrivacyBeleidController',
        ]);
    }
}
