<?php

namespace App\Security;

use App\Entity\User as AppUser;
use Symfony\Component\Mime\Address;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\User\UserCheckerInterface;
use Symfony\Component\Security\Core\Exception\CustomUserMessageAccountStatusException;

/**
 * Add additional checks during user authentication
 */
class UserChecker implements UserCheckerInterface
{
    private $emailVerifier;

    public function __construct(EmailVerifier $emailVerifier)
    {
        $this->emailVerifier = $emailVerifier;
    }

    public function checkPreAuth(UserInterface $user): void
    {
        if (!$user instanceof AppUser) {
            return;
        }
    }

    public function checkPostAuth(UserInterface $user): void
    {
        if (!$user instanceof AppUser) {
            return;
        }

        // If user is not verified resend verification email
        if (!$user->isVerified()) {
            $this->resendVerifEmail($user);
            throw new CustomUserMessageAccountStatusException('Votre email n\'est pas vérifié. Veuillez cliquer sur le lien contenu dans l\'e-mail qui vous a été envoyé');
        }
    }

    public function resendVerifEmail(UserInterface $user)
    {
        return $this->emailVerifier->sendEmailConfirmation(
            'app_verify_email',
            $user,
            (new TemplatedEmail())
                ->from(new Address($_ENV['MAILER_EMAIL'], 'SnowTricks - Confirmation email'))
                ->to($user->getEmail())
                ->subject('Veuillez confirmer votre adresse email')
                ->htmlTemplate('registration/confirmation_email.html.twig')
        );
    }
}
