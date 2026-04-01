<?php

namespace App\Controller;

use App\Form\ResetPasswordFormType;
use App\Form\ResetPasswordRequestFormType;
use App\Repository\ParticipantRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;
use App\Service\JWTService;
use App\Service\SendEmailService;

class SecurityController extends AbstractController
{
    #[Route(path: '/login', name: 'app_login')]
    public function login(AuthenticationUtils $authenticationUtils): Response
    {
        // get the login error if there is one
        $error = $authenticationUtils->getLastAuthenticationError();

        // last username entered by the user
        $lastUsername = $authenticationUtils->getLastUsername();

        return $this->render('security/login.html.twig', [
            'last_username' => $lastUsername,
            'error' => $error,
        ]);
    }

    #[Route(path: '/login_check', name: 'app_login_check', methods: ['POST'])]
    public function login_check(): void
    {
        throw new \LogicException('Cette méthode ne doit JAMAIS être appelée directement.');
    }

    #[Route(path: '/logout', name: 'app_logout')]
    public function logout(): void
    {
    }

    #[Route('/forgotten_password', name: 'forgotten_password', methods: ['GET', 'POST'])]
    public function forgottenPassword(
        Request               $request,
        ParticipantRepository $participantRepository,
        JWTService            $JWTService,
        SendEmailService      $mail): Response
    {
        // création du formulaire
        $form = $this->createForm(ResetPasswordRequestFormType::class);
        // récupère les données
        $form->handleRequest($request);
        // vérifie si le formulaire est soumis et mail valide(format, non vide)
        if ($form->isSubmitted() && $form->isValid()) {
            // cherche le participant en bdd
            $participant = $participantRepository->findOneByEmail($form->get('email')->getData());
            // si trouvé
            if ($participant) {
                // en-tete du token encodée
                $header = [
                    'typ' => 'JWT',
                    'alg' => 'HS256'
                ];
                // corps du token encodé
                $payload = [
                    'participant_id' => $participant->getId()
                ];
                // génération complete du token
                $token = $JWTService->generate($header, $payload, $JWTService->getSecret());

                // génère le lien complet
                $url = $this->generateUrl('reset_password', ['token' => $token], UrlGeneratorInterface::ABSOLUTE_URL);

                // envoie du mail
                $mail->send('no-reply@openblog.test', $participant->getEmail(), 'Récupération de mot de passe sur le site OpenBlog', 'password_reset', compact('participant', 'url'));

                $this->addFlash('success', 'Email envoyé avec succès');
                return $this->redirectToRoute('app_login');
            }
            $this->addFlash('danger', 'Un problème est survenu');
            return $this->redirectToRoute('app_login');
        }
        return $this->render('security/reset_password_request.html.twig', [
            'requestPassForm' => $form->createView()]);
    }

    #[Route('/forgotten_password/{token}', name: 'reset_password')]
    public function resetPassword(
        $token,
        JWTService $JWTService,
        ParticipantRepository $participantRepository,
        Request $request,
        UserPasswordHasherInterface $passwordHasher,
        EntityManagerInterface $entityManager): Response
    {
        // vérifie si JWT est valide, pas expiré et vérifie la clé secrète sinon renvoie un message flash
        if (!$JWTService->isValid($token) || $JWTService->isExpired($token) || !$JWTService->check($token, $JWTService->getSecret())) {
            $this->addFlash('danger', 'Le token est invalide ou a expiré');
            return $this->redirectToRoute('app_login');
        }
        // extrait les données du token
        $payload = $JWTService->getPayload($token);

        // récupère participant
        $participant = $participantRepository->find($payload['participant_id']);

        // si participant pas récupéré
        if (!$participant) {
            $this->addFlash('danger', 'Utilisateur non trouvé');
            return $this->redirectToRoute('app_login');
        }

        // créer le formulaire
        $form = $this->createForm(ResetPasswordFormType::class);
        $form->handleRequest($request);

        // si formulaire soumis et valid
        if ($form->isSubmitted() && $form->isValid()) {
            // hashage du mot de passe
            $participant->setPassword($passwordHasher->hashPassword($participant, $form->get('password')->getData()));
            // enregistre en bdd
            $entityManager->flush();
            $this->addFlash('success', 'Mot de passe changé avec succès');
            return $this->redirectToRoute('app_login');
        }

        return $this->render('security/reset_password.html.twig', [
            'passform' => $form->createView()
        ]);
    }
}
