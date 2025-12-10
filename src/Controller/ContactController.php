<?php
//
//namespace App\Controller;
//
//use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
//use Symfony\Component\HttpFoundation\Request;
//use Symfony\Component\HttpFoundation\Response;
//use Symfony\Component\Routing\Annotation\Route;
//use Symfony\Component\Mime\Email;
//use Symfony\Component\Mime\Address;
//use Symfony\Component\Mailer\MailerInterface;
//
//class ContactController extends AbstractController
//{
//    #[Route('/contact', name: 'app_contact')]
//    public function index(): Response
//    {
//        return $this->render('contact/index.html.twig');
//    }
//
//    #[Route('/contact/send', name: 'contact_send', methods: ['POST'])]
//    public function send(Request $request, MailerInterface $mailer): Response
//    {
//        // Honeypot
//        if ($request->request->get('_honey')) {
//            return $this->redirectToRoute('app_contact');
//        }
//
//        // Velden
//        $naam = htmlspecialchars(trim($request->request->get('Naam', '')), ENT_QUOTES, 'UTF-8');
//        $bedrijf = htmlspecialchars(trim($request->request->get('Bedrijf', '')), ENT_QUOTES, 'UTF-8');
//        $email = trim($request->request->get('email', ''));
//        $telefoon = htmlspecialchars(trim($request->request->get('Telefoonnummer', '')), ENT_QUOTES, 'UTF-8');
//        $berichtRaw = trim($request->request->get('Bericht', ''));
//
//        $bericht = nl2br(htmlspecialchars($berichtRaw, ENT_QUOTES, 'UTF-8'));
//
//        // Validatie
//        $errors = [];
//
//        if ($naam === '') {
//            $errors[] = 'Naam ontbreekt';
//        }
//
//        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
//            $errors[] = 'E-mailadres is ongeldig';
//        }
//
//        if ($berichtRaw === '') {
//            $errors[] = 'Bericht ontbreekt';
//        }
//
//        if (!empty($errors)) {
//            return $this->render('contact/index.html.twig', [
//                'errors' => $errors,
//                'formData' => $request->request->all()
//            ]);
//        }
//
//        // Mail naar INFO@
//        $emailAdmin = (new Email())
//            ->from(new Address('info@easysolutions.nl', 'Easysolutions.nl Contact'))
//            ->to('info@easysolutions.nl')
//            ->replyTo($email)
//            ->subject('Nieuw contactbericht van ' . $naam)
//            ->html(
//                '<h2>Nieuw contactbericht</h2>' .
//                '<p><strong>Naam:</strong> ' . $naam . '</p>' .
//                '<p><strong>Bedrijf:</strong> ' . $bedrijf . '</p>' .
//                '<p><strong>Email:</strong> ' . $email . '</p>' .
//                '<p><strong>Telefoon:</strong> ' . $telefoon . '</p>' .
//                '<p><strong>Bericht:</strong><br>' . $bericht . '</p>'
//            );
//
//        // Bevestiging naar gebruiker
//        $emailUser = (new Email())
//            ->from(new Address('info@easysolutions.nl', 'Easy Solutions'))
//            ->to($email)
//            ->subject('Bevestiging: we hebben je bericht ontvangen')
//            ->html(
//                '<p>Beste ' . $naam . ',</p>' .
//                '<p>Bedankt voor je bericht! We nemen zo snel mogelijk contact met je op.</p>' .
//                '<hr>' .
//                '<p><strong>Je bericht:</strong><br>' . $bericht . '</p>'
//            );
//
//        try {
//            $mailer->send($emailAdmin);
//            $mailer->send($emailUser);
//        } catch (\Throwable $e) {
//            return new Response('Mail verzenden mislukt: ' . $e->getMessage(), 500);
//        }
//
//        return $this->redirectToRoute('contact_success');
//    }
//
//    #[Route('/contact/bedankt', name: 'contact_success')]
//    public function success(): Response
//    {
//        return $this->render('contact/bedankt.html.twig');
//    }
//}


/* -------------------------------
   Instellingen
--------------------------------*/
$toAdmin = "yaser@easysolutions.nl";                       // waar jij het bericht wilt ontvangen
$fromEmail = "info@zakelijkopjemobiel.nl";                  // afzender (je Hostinger mailbox)
$fromName = "Zakelijk op je Mobiel";
$siteUrl = "https://zakelijkopjemobiel.nl";
$logoUrl = "https://zakelijkopjemobiel.nl/assets/zakelijkopjemobiel-high-resolution-logo-transparent.png";

/* Helpers */
function clean($v)
{
    return trim(filter_var($v, FILTER_SANITIZE_FULL_SPECIAL_CHARS));
}

function isEmail($v)
{
    return (bool)filter_var($v, FILTER_VALIDATE_EMAIL);
}

/* Alleen POST + honeypot */
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo "Method not allowed";
    exit;
}
if (!empty($_POST['_honey'] ?? '')) {
    header("Location: {$siteUrl}/klantenservice/contact.html?success=1");
    exit;
}

/* Velden uit het formulier */
$naam = clean($_POST['Naam'] ?? '');
$bedrijf = clean($_POST['Bedrijf'] ?? '');
$email = clean($_POST['email'] ?? '');
$telefoon = clean($_POST['Telefoonnummer'] ?? '');
$bericht = clean($_POST['Bericht'] ?? '');

/* Validatie */
$errors = [];
if ($naam === '') $errors[] = "Naam ontbreekt";
if (!isEmail($email)) $errors[] = "E-mailadres is ongeldig";
if ($bericht === '') $errors[] = "Bericht ontbreekt";
if ($errors) {
    http_response_code(422);
    echo "Fouten: " . implode(", ", $errors);
    exit;
}

/* HTML template (met logo + merkregel) */
function emailTemplate($logoUrl, $title, $intro, $fieldsHtml, $footerNote, $siteUrl)
{
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
    .header{padding:24px;text-align:center;background:#f0fff5;border-bottom:1px solid #e8f5ed;}
    .logo{max-width:200px;height:auto;display:block;margin:0 auto;}
    .brand{font-weight:700;color:#15803d;font-size:14px;margin-top:6px;text-decoration:none;display:inline-block}
    .content{padding:24px;}
    h1{font-size:20px;margin:0 0 8px;color:#15803d;}
    p{margin:0 0 12px;line-height:1.5;}
    .card{border:1px solid #eee;border-radius:12px;padding:16px;background:#fff;}
    .table{width:100%;border-collapse:collapse;}
    .table th,.table td{padding:10px;border-bottom:1px solid #f0f0f0;text-align:left;font-size:14px;}
    .table th{color:#666;width:42%;}
    .footer{padding:18px 24px;color:#666;font-size:12px;text-align:center;border-top:1px solid #f3f4f6;background:#fafafa;}
    a{color:#16a34a;text-decoration:none;}
  </style>
</head>
<body>
  <div class="wrapper"><div class="container">
    <div class="header">
      <a href="{$siteUrl}" target="_blank" style="text-decoration:none">
        <div class="brand">Zakelijkopjemobiel.nl</div>
      </a>
    </div>
    <div class="content">
      <h1>{$title}</h1>
      <p>{$intro}</p>
      <div class="card" style="margin:16px 0;">{$fieldsHtml}</div>
      <p style="font-size:12px;color:#6b7280;">{$footerNote}</p>
    </div>
    <div class="footer">© {$year} Zakelijk op je Mobiel · <a href="{$siteUrl}">{$siteUrl}</a></div>
  </div></div>
</body>
</html>
HTML;
}

/* Tabel met velden */
$fieldsHtml = '
<table class="table">
  <tr><th>Naam</th><td>' . nl2br($naam) . '</td></tr>
  <tr><th>Bedrijf</th><td>' . nl2br($bedrijf) . '</td></tr>
  <tr><th>E-mail</th><td>' . $email . '</td></tr>
  <tr><th>Telefoon</th><td>' . nl2br($telefoon) . '</td></tr>
  <tr><th>Bericht</th><td>' . nl2br($bericht) . '</td></tr>
</table>
';

/* E-mails */
$subjectAdmin = "📩 Nieuw contactbericht – {$naam}" . ($bedrijf ? " ({$bedrijf})" : "");
$introAdmin = "Er is een nieuw bericht verstuurd via het contactformulier. Details staan hieronder.";
$footerAdmin = "Reageer gerust via 'Beantwoorden' (Reply-To staat op de inzender).";
$htmlAdmin = emailTemplate($logoUrl, $subjectAdmin, $introAdmin, $fieldsHtml, $footerAdmin, $siteUrl);

$subjectUser = "Bevestiging: we hebben je bericht ontvangen";
$introUser = "Bedankt voor je bericht aan Zakelijk op je Mobiel! We reageren doorgaans binnen 1 werkdag. Hieronder vind je een kopie van je bericht.";
$footerUser = "Vragen of aanvullen? Antwoord gerust op deze e-mail.";
$htmlUser = emailTemplate($logoUrl, $subjectUser, $introUser, $fieldsHtml, $footerUser, $siteUrl);

/* Headers */
$headersCommon = "MIME-Version: 1.0\r\n";
$headersCommon .= "Content-Type: text/html; charset=UTF-8\r\n";
$headersCommon .= "From: {$fromName} <{$fromEmail}>\r\n";
$headersToAdmin = $headersCommon . "Reply-To: {$naam} <{$email}>\r\n";
$headersToUser = $headersCommon . "Reply-To: {$fromName} <{$fromEmail}>\r\n";

/* Versturen + redirect */
$okAdmin = mail($toAdmin, $subjectAdmin, $htmlAdmin, $headersToAdmin);
$okUser = mail($email, $subjectUser, $htmlUser, $headersToUser);

if ($okAdmin) {
    header("Location: {$siteUrl}/klantenservice/contact.html?success=1");
    exit;
} else {
    http_response_code(500);
    echo "Verzenden mislukt. Probeer later opnieuw of neem contact op.";
}

