<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Mime\Email;
use Symfony\Component\Mime\Address;
use Symfony\Component\Mailer\MailerInterface;

class ContactController extends AbstractController
{
    // Instellingen
    private string $toAdmin   = 'info@easysolutions.nl';
    private string $fromEmail = 'info@easysolutions.nl';
    private string $fromName  = 'Easy Solutions';
    private string $siteUrl   = 'https://easysolutions.nl';
    // Pas aan naar je echte logo
    private string $logoUrl   = 'https://easysolutions.nl/logo.png';

    #[Route('/contact', name: 'app_contact')]
    public function index(Request $request): Response
    {
        // ‘success’ query param uitlezen (voor de groene alert)
        $success = $request->query->get('success');

        return $this->render('contact/index.html.twig', [
            'success' => $success,
        ]);
    }

    #[Route('/contact/send', name: 'contact_send', methods: ['POST'])]
    public function send(Request $request, MailerInterface $mailer): Response
    {
        // Honeypot
        if ($request->request->get('_honey')) {
            return $this->redirectToRoute('app_contact');
        }

        // Helpers
        $clean = fn (?string $v): string =>
        trim((string) filter_var((string) $v, FILTER_SANITIZE_FULL_SPECIAL_CHARS));

        $isEmail = fn (?string $v): bool =>
        (bool) filter_var((string) $v, FILTER_VALIDATE_EMAIL);

        // Velden (namen komen overeen met je Twig form)
        $naamRaw     = $request->request->get('Naam', '');
        $bedrijfRaw  = $request->request->get('Bedrijf', '');
        $emailRaw    = $request->request->get('email', '');
        $telefoonRaw = $request->request->get('Telefoonnummer', '');
        $berichtRaw  = $request->request->get('Bericht', '');

        $naam     = $clean($naamRaw);
        $bedrijf  = $clean($bedrijfRaw);
        $email    = $clean($emailRaw);
        $telefoon = $clean($telefoonRaw);
        $bericht  = $clean($berichtRaw);

        // Validatie (zelfde logica/teksten als je eerste code)
        $errors = [];

        if ($naam === '') {
            $errors[] = 'Naam ontbreekt';
        }

        if (!$isEmail($email)) {
            $errors[] = 'E-mailadres is ongeldig';
        }

        if ($berichtRaw === '') {
            $errors[] = 'Bericht ontbreekt';
        }

        if (!empty($errors)) {
            return $this->render('contact/index.html.twig', [
                'errors'   => $errors,
                'formData' => $request->request->all(),
            ]);
        }

        // HTML-tabel met velden
        $fieldsHtml = '
<table class="table">
  <tr><th>Naam</th><td>'.nl2br($naam).'</td></tr>
  <tr><th>Bedrijf</th><td>'.nl2br($bedrijf).'</td></tr>
  <tr><th>Email</th><td>'.$email.'</td></tr>
  <tr><th>Telefoon</th><td>'.nl2br($telefoon).'</td></tr>
  <tr><th>Bericht</th><td>'.nl2br($bericht).'</td></tr>
</table>
';

        // Admin-mail
        $subjectAdmin = 'Nieuw contactbericht van ' . $naam;
        $introAdmin   = 'Er is een nieuw contactbericht verstuurd via het formulier op de website. Details staan hieronder.';
        $footerAdmin  = "Je kunt direct reageren via 'Beantwoorden' (Reply-To staat op de inzender).";

        $htmlAdmin = $this->emailTemplate(
            $this->logoUrl,
            $subjectAdmin,
            $introAdmin,
            $fieldsHtml,
            $footerAdmin,
            $this->siteUrl
        );

        $emailAdmin = (new Email())
            ->from(new Address($this->fromEmail, 'Easysolutions.nl Contact'))
            ->to($this->toAdmin)
            ->replyTo($email)
            ->subject($subjectAdmin)
            ->html($htmlAdmin);

        // Bevestiging naar gebruiker
        $subjectUser = 'Bevestiging: we hebben je bericht ontvangen';
        $introUser   = 'Beste ' . $naam . ',<br>Bedankt voor je bericht! We nemen zo snel mogelijk contact met je op. Hieronder vind je een kopie van je bericht.';
        $footerUser  = 'Vragen of aanvulling? Antwoord gerust op deze e-mail.';

        $htmlUser = $this->emailTemplate(
            $this->logoUrl,
            $subjectUser,
            $introUser,
            $fieldsHtml,
            $footerUser,
            $this->siteUrl
        );

        $emailUser = (new Email())
            ->from(new Address($this->fromEmail, $this->fromName))
            ->to($email)
            ->subject($subjectUser)
            ->html($htmlUser);

        try {
            $mailer->send($emailAdmin);
            $mailer->send($emailUser);
        } catch (\Throwable $e) {
            return new Response('Mail verzenden mislukt: ' . $e->getMessage(), 500);
        }

        // Zelfde gedrag als je losse PHP-script: terug naar /contact?success=1
        return $this->redirectToRoute('app_contact', ['success' => 1]);
    }

    private function emailTemplate(
        string $logoUrl,
        string $title,
        string $intro,
        string $fieldsHtml,
        string $footerNote,
        string $siteUrl
    ): string {
        $year = date('Y');

        return <<<HTML
<!doctype html>
<html lang="nl">
<head>
  <meta charset="utf-8"><meta name="viewport" content="width=device-width">
  <title>{$title}</title>
  <style>
    body{margin:0;background:#f6f7fb;font-family:Arial,Helvetica,sans-serif;color:#222;}
    .wrapper{width:100%;padding:24px 12px;}
    .container{max-width:640px;margin:0 auto;background:#fff;border-radius:16px;overflow:hidden;box-shadow:0 6px 20px rgba(0,0,0,.06);}
    .header{padding:24px;text-align:center;background:#f0f5ff;border-bottom:1px solid #e5e7eb;}
    .logo{max-width:200px;height:auto;display:block;margin:0 auto;}
    .brand{font-weight:700;color:#1d4ed8;font-size:14px;margin-top:6px;text-decoration:none;display:inline-block}
    .content{padding:24px;}
    h1{font-size:20px;margin:0 0 8px;color:#1d4ed8;}
    p{margin:0 0 12px;line-height:1.5;}
    .card{border:1px solid #eee;border-radius:12px;padding:16px;background:#fff;}
    .table{width:100%;border-collapse:collapse;}
    .table th,.table td{padding:10px;border-bottom:1px solid #f0f0f0;text-align:left;font-size:14px;}
    .table th{color:#666;width:42%;}
    .footer{padding:18px 24px;color:#666;font-size:12px;text-align:center;border-top:1px solid #f3f4f6;background:#fafafa;}
    a{color:#1d4ed8;text-decoration:none;}
  </style>
</head>
<body>
  <div class="wrapper"><div class="container">
    <div class="header">
      <a href="{$siteUrl}" target="_blank" style="text-decoration:none">
        <img class="logo" src="{$logoUrl}" alt="Easy Solutions logo">
        <div class="brand">Easysolutions.nl</div>
      </a>
    </div>
    <div class="content">
      <h1>{$title}</h1>
      <p>{$intro}</p>
      <div class="card" style="margin:16px 0;">{$fieldsHtml}</div>
      <p style="font-size:12px;color:#6b7280;">{$footerNote}</p>
    </div>
    <div class="footer">© {$year} Easy Solutions · <a href="{$siteUrl}">{$siteUrl}</a></div>
  </div></div>
</body>
</html>
HTML;
    }
}