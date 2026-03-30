<?php
// src/Service/SendEmailService.php
namespace App\Service;

use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Email;
use Twig\Environment;

class SendEmailService
{
    public function __construct(
        private MailerInterface $mailer,
        private Environment     $twig
    )
    {
    }

    public function send(string $from, string $to, string $subject, string $template, array $context = []): void
    {
        try {
            $html = $this->twig->render('emails/' . $template . '.html.twig', $context);
            $text = $this->twig->render('emails/' . $template . '.txt.twig', $context);

            $email = (new Email())
                ->from($from)
                ->to($to)
                ->subject($subject)
                ->html($html)
                ->text($text);

            $this->mailer->send($email);
        } catch (\Exception $e) {
            throw $e;
        }
    }
}
