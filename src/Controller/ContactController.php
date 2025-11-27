<?php
// src/Controller/ContactController.php
namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Mime\Email;
use Symfony\Component\Mailer\MailerInterface;

class ContactController extends AbstractController
{
#[Route('/contact', name: 'app_contact')]
public function index(): Response
{
return $this->render('contact/index.html.twig');
}

#[Route('/contact/send', name: 'contact_send', methods: ['POST'])]
public function send(Request $request, MailerInterface $mailer): Response
{
// Honeypot check
if ($request->request->get('_honey')) {
return $this->redirectToRoute('contact');
}

// Form fields
$naam = trim($request->request->get('Naam', ''));
$bedrijf = trim($request->request->get('Bedrijf', ''));
$email = trim($request->request->get('email', ''));
$telefoon = trim($request->request->get('Telefoonnummer', ''));
$bericht = trim($request->request->get('Bericht', ''));

// Validatie
$errors = [];
if (!$naam) $errors[] = "Naam ontbreekt";
if (!filter_var($email, FILTER_VALIDATE_EMAIL)) $errors[] = "E-mailadres is ongeldig";
if (!$bericht) $errors[] = "Bericht ontbreekt";

if ($errors) {
return $this->render('contact/index.html.twig', [
'errors' => $errors,
'formData' => $request->request->all()
]);
}

// Email naar admin
$emailAdmin = (new Email())
->from('info@easysolutions.nl')
->to('yaser@easysolutions.nl')
->subject("Nieuw contactbericht van $naam")
->html("
<h2>Nieuw contactbericht</h2>
<p><strong>Naam:</strong> $naam</p>
<p><strong>Bedrijf:</strong> $bedrijf</p>
<p><strong>Email:</strong> $email</p>
<p><strong>Telefoon:</strong> $telefoon</p>
<p><strong>Bericht:</strong><br>$bericht</p>
");

// Email naar gebruiker
$emailUser = (new Email())
->from('info@easysolutions.nl')
->to($email)
->subject('Bevestiging: we hebben je bericht ontvangen')
->html("
<p>Beste $naam,</p>
<p>Bedankt voor je bericht! We reageren zo snel mogelijk.</p>
<hr>
<p><strong>Je bericht:</strong><br>$bericht</p>
");

// Versturen
try {
$mailer->send($emailAdmin);
$mailer->send($emailUser);
} catch (\Exception $e) {
return new Response("Versturen mislukt: " . $e->getMessage(), 500);
}

return $this->redirectToRoute('contact_success');
}

#[Route('/contact/bedankt', name: 'contact_success')]
public function success(): Response
{
return $this->render('contact/bedankt.html.twig');
}
}
