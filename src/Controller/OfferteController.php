<?php
namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Mime\Email;
use Symfony\Component\Mailer\MailerInterface;

class OfferteController extends AbstractController
{
    #[Route('/offerte', name: 'offerte')]
    public function index(): Response
    {
        return $this->render('offerte/index.html.twig');
    }

    #[Route('/offerte/send', name: 'offerte_send', methods: ['POST'])]
    public function send(Request $request, MailerInterface $mailer): Response
    {
        // Honeypot anti-spam
        if ($request->request->get('_honey')) {
            return $this->redirectToRoute('offerte'); // bot detected
        }

        $naam = trim($request->request->get('Naam', ''));
        $bedrijf = trim($request->request->get('Bedrijf', ''));
        $email = trim($request->request->get('email', ''));
        $telefoon = trim($request->request->get('Telefoonnummer', ''));
        $accounts = trim($request->request->get('Aantal_accounts', ''));
        $bericht = trim($request->request->get('Bericht', ''));

        // Basic validation
        $errors = [];
        if (!$naam) $errors[] = 'Naam ontbreekt';
        if (!$bedrijf) $errors[] = 'Bedrijf ontbreekt';
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) $errors[] = 'E-mail ongeldig';
        if (!$telefoon) $errors[] = 'Telefoon ontbreekt';
        if (!$accounts) $errors[] = 'Aantal accounts ontbreekt';

        if ($errors) {
            return $this->render('offerte/index.html.twig', [
                'errors' => $errors,
                'formData' => $request->request->all(),
            ]);
        }

        // Gebruik dezelfde e-mails als contactformulier
        $toAdmin = "yaser@easysolutions.nl";  // PAS AAN
        $fromEmail = "info@easysolutions.nl"; // PAS AAN
        $fromName = "Easy solutions"; // PAS AAN

        $html = "
            <h2>Nieuwe offerteaanvraag</h2>
            <p><strong>Naam:</strong> $naam</p>
            <p><strong>Bedrijf:</strong> $bedrijf</p>
            <p><strong>E-mail:</strong> $email</p>
            <p><strong>Telefoon:</strong> $telefoon</p>
            <p><strong>Aantal accounts:</strong> $accounts</p>
            <p><strong>Bericht:</strong><br>".nl2br(htmlspecialchars($bericht))."</p>
        ";

        $emailAdmin = (new Email())
            ->from("$fromName <$fromEmail>")
            ->to($toAdmin)
            ->subject("Nieuwe offerteaanvraag – $bedrijf ($naam)")
            ->html($html);

        $emailUser = (new Email())
            ->from("$fromName <$fromEmail>")
            ->to($email)
            ->subject("Bevestiging: we hebben je aanvraag ontvangen")
            ->html("<p>Bedankt voor je aanvraag!</p>" . $html);

        try {
            $mailer->send($emailAdmin);
            $mailer->send($emailUser);
        } catch (\Exception $e) {
            return new Response('Verzenden mislukt: ' . $e->getMessage(), 500);
        }

        return $this->redirectToRoute('offerte_success');
    }

    #[Route('/offerte/bedankt', name: 'offerte_success')]
    public function success(): Response
    {
        return $this->render('offerte/bedankt.html.twig');
    }
}
