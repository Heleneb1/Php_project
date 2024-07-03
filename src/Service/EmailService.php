<?php
namespace App\Service;

use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Email;
use Twig\Environment;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;

class EmailService
{
    private $mailer;
    private $twig;
    private $params;

    public function __construct(MailerInterface $mailer, Environment $twig, ParameterBagInterface $params)
    {
        $this->mailer = $mailer;
        $this->twig = $twig;
        $this->params = $params;
    }

    public function send(string $to, string $subject, string $template, array $context): void
    {
        $email = (new Email())
            ->from($this->params->get('mailer_from'))
            ->to($to)
            ->subject($subject)
            ->html($this->twig->render($template, $context));

        $this->mailer->send($email);
    }
}
