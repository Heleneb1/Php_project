<?php
namespace App\Controller;

use App\Entity\User;
use App\Form\RegistrationFormType;
use App\Security\EmailVerifier;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Mime\Address;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Contracts\Translation\TranslatorInterface;
use SymfonyCasts\Bundle\VerifyEmail\Exception\VerifyEmailExceptionInterface;
use Symfony\Component\Security\Http\Authentication\UserAuthenticatorInterface;
use Symfony\Component\Security\Http\Authenticator\FormLoginAuthenticator;
use SymfonyCasts\Bundle\VerifyEmail\VerifyEmailHelperInterface;
use App\Repository\UserRepository;
use App\Security\LoginFormAuthenticator;


class RegistrationController extends AbstractController
{
    private EmailVerifier $emailVerifier;

    public function __construct(EmailVerifier $emailVerifier)
    {
        $this->emailVerifier = $emailVerifier;
    }

    #[Route('/register', name: 'app_register')]
    public function register(Request $request, UserPasswordHasherInterface $userPasswordHasher, EntityManagerInterface $entityManager, UserAuthenticatorInterface $userAuthenticator, FormLoginAuthenticator $formLoginAuthenticator, VerifyEmailHelperInterface $verifyEmailHelper): Response
    {
        $user = new User();
        $form = $this->createForm(RegistrationFormType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // Encode the plain password
            $user->setPassword(
                $userPasswordHasher->hashPassword(
                    $user,
                    $form->get('plainPassword')->getData()
                )
            );

           
            $entityManager->persist($user);
            $entityManager->flush();

            $signatureComponents = $verifyEmailHelper->generateSignature(
                'app_verify_email',
                $user->getId(),
                $user->getEmail(), 
                ['id '=> $user->getId()]//je recupere l'id de l'utilisateur c'est ok
               
            );
 $this->addFlash('success', 'Confirm your email to validate your account');
            // Generate a signed URL and email it to the user
            $this->emailVerifier->sendEmailConfirmation('app_verify_email', $user,
                (new TemplatedEmail())
                    ->from(new Address('heleneb.dev@gmail.com', 'Heleneb'))
                    ->to($user->getEmail())
                    ->subject('Please Confirm your Email')
                    ->htmlTemplate('registration/confirmation_email.html.twig')
                    ->context([
                        'signedUrl' => $signatureComponents->getSignedUrl(),
                        'expiresAtMessageKey' => $signatureComponents->getExpirationMessageKey(),
                        'expiresAtMessageData' => $signatureComponents->getExpirationMessageData(),
                        'user' => $user,  // Pass the user entity to the template
                    ])
            );

            // Authenticate the user
            $userAuthenticator->authenticateUser(
                $user,
                $formLoginAuthenticator,
                $request
            );
            $this->addFlash('success', 'Votre compte est créé.');
            return $this->redirectToRoute('program_index');
        }

        return $this->render('registration/register.html.twig', [
            'registrationForm' => $form->createView(),
        ]);
    }

    // #[Route('/verify/email', name: 'app_verify_email')]
    // public function verifyUserEmail(Request $request, VerifyEmailHelperInterface $verifyEmailHelper, UserRepository $userRepository, EntityManagerInterface $entityManager): Response
    // {   
    //     $user = $userRepository->find($request->query->get('id'));
    //     if (!$user) {
    //         throw $this->createNotFoundException();
    //     }
    //     try {
    //         $verifyEmailHelper->validateEmailConfirmation(
    //             $request->getUri(),
    //             $user->getId(),
    //             $user->getEmail(), 
    //             ['id'=>$user->getId()]
    //         );
    //     } catch (VerifyEmailExceptionInterface $e) {
    //         $this->addFlash('error', $e->getReason());
    //         return $this->redirectToRoute('app_register');
    //     }
    //             $user->setVerified(true);
    //     $entityManager->flush();
    //     $this->addFlash('success', 'Account Verified! You can now log in.');
    
    //     return $this->redirectToRoute('program_index');}
    #[Route('/verify/email', name: 'app_verify_email')]
    public function verifyUserEmail(Request $request, TranslatorInterface $translator): Response
    {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');

        // Validate email confirmation link, sets User::isVerified=true and persists
        try {
            $this->emailVerifier->handleEmailConfirmation($request, $this->getUser());
        } catch (VerifyEmailExceptionInterface $exception) {
            $this->addFlash('verify_email_error', $translator->trans($exception->getReason(), [], 'VerifyEmailBundle'));

            return $this->redirectToRoute('app_register');
            $user->setVerified(true);
        }

        // Change the redirect on success and handle or remove the flash message in your templates
        $this->addFlash('success', 'Your email address has been verified.');

        return $this->redirectToRoute('program_index');
    }
}
