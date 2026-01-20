<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Mime\Email;
use Symfony\Component\Mime\Address;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Contracts\HttpClient\HttpClientInterface;

class OfferteController extends AbstractController
{
    #[Route('/offerte', name: 'offerte')]
    public function index(): Response
    {
        return $this->render('offerte/index.html.twig', [
            'recaptcha_site_key' => $_ENV['RECAPTCHA_SITE_KEY'],
        ]);
    }

    #[Route('/offerte/send', name: 'offerte_send', methods: ['POST'])]
    public function send(Request $request, MailerInterface $mailer, HttpClientInterface $httpClient): Response
    {
        // Honeypot
        if ($request->request->get('_honey')) {
            return $this->redirectToRoute('offerte');
        }

        // reCAPTCHA v3 token ophalen
        $token = $request->request->get('g-recaptcha-response');
        if (!$token) {
            return $this->render('offerte/index.html.twig', [
                'errors' => ['reCAPTCHA verificatie mislukt.'],
                'formData' => $request->request->all(),
                'recaptcha_site_key' => $_ENV['RECAPTCHA_SITE_KEY'],
            ]);
        }

        // Verifieer token via Google API
        try {
            $response = $httpClient->request('POST', 'https://www.google.com/recaptcha/api/siteverify', [
                'body' => [
                    'secret' => $_ENV['RECAPTCHA_SECRET_KEY'],
                    'response' => $token,
                ],
            ]);
            $result = $response->toArray();
        } catch (\Throwable $e) {
            return $this->render('offerte/index.html.twig', [
                'errors' => ['reCAPTCHA server error: ' . $e->getMessage()],
                'formData' => $request->request->all(),
                'recaptcha_site_key' => $_ENV['RECAPTCHA_SITE_KEY'],
            ]);
        }

        if (empty($result['success']) || $result['score'] < 0.5 || ($result['action'] ?? '') !== 'offerte') {
            return $this->render('offerte/index.html.twig', [
                'errors' => ['Verdachte activiteit gedetecteerd.'],
                'formData' => $request->request->all(),
                'recaptcha_site_key' => $_ENV['RECAPTCHA_SITE_KEY'],
            ]);
        }

        // Velden
        $naam = htmlspecialchars(trim($request->request->get('Naam', '')), ENT_QUOTES, 'UTF-8');
        $bedrijf = htmlspecialchars(trim($request->request->get('Bedrijf', '')), ENT_QUOTES, 'UTF-8');
        $email = trim($request->request->get('email', ''));
        $telefoon = htmlspecialchars(trim($request->request->get('Telefoonnummer', '')), ENT_QUOTES, 'UTF-8');
        $accounts = htmlspecialchars(trim($request->request->get('Aantal_accounts', '')), ENT_QUOTES, 'UTF-8');
        $berichtRaw = trim($request->request->get('Bericht', ''));
        $bericht = nl2br(htmlspecialchars($berichtRaw, ENT_QUOTES, 'UTF-8'));

        // Validatie
        $errors = [];
        if ($naam === '') $errors[] = 'Naam ontbreekt';
        if ($bedrijf === '') $errors[] = 'Bedrijf ontbreekt';
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) $errors[] = 'E-mailadres is ongeldig';
        if ($telefoon === '') $errors[] = 'Telefoon ontbreekt';
        if ($accounts === '') $errors[] = 'Aantal accounts ontbreekt';

        if (!empty($errors)) {
            return $this->render('offerte/index.html.twig', [
                'errors' => $errors,
                'formData' => $request->request->all(),
                'recaptcha_site_key' => $_ENV['RECAPTCHA_SITE_KEY'],
            ]);
        }

        // Mail naar info@
        $emailAdmin = (new Email())
            ->from(new Address('info@easysolutions.nl', 'Easysolutions.nl Offerte'))
            ->to('info@easysolutions.nl')
            ->replyTo($email)
            ->subject('Nieuwe offerteaanvraag – ' . $bedrijf)
            ->html(
                '<h2>Nieuwe offerteaanvraag</h2>' .
                '<p><strong>Naam:</strong> ' . $naam . '</p>' .
                '<p><strong>Bedrijf:</strong> ' . $bedrijf . '</p>' .
                '<p><strong>Email:</strong> ' . $email . '</p>' .
                '<p><strong>Telefoon:</strong> ' . $telefoon . '</p>' .
                '<p><strong>Aantal accounts:</strong> ' . $accounts . '</p>' .
                '<p><strong>Bericht:</strong><br>' . $bericht . '</p>'
            );

        // Bevestiging naar gebruiker
        $emailUser = (new Email())
            ->from(new Address('info@easysolutions.nl', 'Easy Solutions'))
            ->to($email)
            ->subject('Bevestiging: we hebben je offerteaanvraag ontvangen')
            ->html(
                '<p>Beste ' . $naam . ',</p>' .
                '<p>Bedankt voor je offerteaanvraag. We nemen zo snel mogelijk contact met je op.</p>' .
                '<hr>' .
                '<p><strong>Je aanvraag:</strong><br>' . $bericht . '</p>'
            );

        try {
            $mailer->send($emailAdmin);
            $mailer->send($emailUser);
        } catch (\Throwable $e) {
            return new Response('Mail verzenden mislukt: ' . $e->getMessage(), 500);
        }

        return $this->redirectToRoute('offerte_success');
    }

    #[Route('/offerte/bedankt', name: 'offerte_success')]
    public function success(): Response
    {
        return $this->render('offerte/bedankt.html.twig');
    }
}
