<?php

namespace App\Controller;

use App\Entity\Customer;
use App\Entity\Producer;
use App\Handler\ForgottenPasswordHandler;
use App\Handler\RegistrationHandler;
use App\Handler\ResetPasswordHandler;
use App\HandlerFactory\HandlerFactoryInterface;
use App\Repository\UserRepository;
use Doctrine\ORM\NonUniqueResultException;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;
use Symfony\Component\Uid\Uuid;
use Twig\Environment;
use Twig\Error\LoaderError;
use Twig\Error\RuntimeError;
use Twig\Error\SyntaxError;

/**
 * Class SecurityController
 * @package App\Controller
 */
class SecurityController
{
    /** @var Environment $twig */
    private $twig;

    /** @var HandlerFactoryInterface $handlerFactory */
    private $handlerFactory;

    /** @var SessionInterface $session */
    private $session;

    /** @var UserRepository $userRepository */
    private $userRepository;

    /** @var UrlGeneratorInterface $urlGenerator */
    private $urlGenerator;

    /**
     * SecurityController constructor.
     * @param Environment $twig
     * @param HandlerFactoryInterface $handlerFactory
     * @param SessionInterface $session
     * @param UserRepository $userRepository
     * @param UrlGeneratorInterface $urlGenerator
     */
    public function __construct(
        Environment $twig,
        HandlerFactoryInterface $handlerFactory,
        SessionInterface $session,
        UserRepository $userRepository,
        UrlGeneratorInterface $urlGenerator
    ) {
        $this->twig = $twig;
        $this->handlerFactory = $handlerFactory;
        $this->session = $session;
        $this->userRepository = $userRepository;
        $this->urlGenerator = $urlGenerator;
    }

    /**
     * @Route("/login", name="security_login")
     *
     * @param AuthenticationUtils $authenticationUtils
     * @return Response
     *
     * @throws LoaderError
     * @throws RuntimeError
     * @throws SyntaxError
     */
    public function login(AuthenticationUtils $authenticationUtils): Response
    {
        // get the login error if there is one
        $error = $authenticationUtils->getLastAuthenticationError();
        // last username entered by the user
        $lastUsername = $authenticationUtils->getLastUsername();

        return new Response(
            $this->twig->render("ui/security/login.html.twig", [
                'error' => $error,
                'last_username' => $lastUsername
            ])
        );
    }

    /**
     * @Route("/registration/{role}", name="security_registration")
     *
     * @param Request $request
     * @return Response
     *
     * @throws LoaderError
     * @throws RuntimeError
     * @throws SyntaxError
     */
    public function registration(Request $request): Response
    {
        $role = $request->attributes->get('role');
        $user = Producer::ROLE === $role ? new Producer() : new Customer();

        /** @var RegistrationHandler $handler */
        $handler = $this->handlerFactory->createHandler(RegistrationHandler::class);

        if ($handler->handle($request, $user)) {
            return new RedirectResponse($this->urlGenerator->generate("home"));
        }

        return new Response(
            $this->twig->render(
                "ui/security/registration.html.twig",
                [
                    'form' => $handler->createView(),
                ]
            )
        );
    }

    /**
     * @Route("/forgotten-password", name="security_forgotten_password")
     * @param Request $request
     * @return Response
     *
     * @throws LoaderError
     * @throws RuntimeError
     * @throws SyntaxError
     */
    public function forgottenPassword(Request $request): Response
    {
        /** @var ForgottenPasswordHandler $handler */
        $handler = $this->handlerFactory->createHandler(ForgottenPasswordHandler::class);

        if ($handler->handle($request)) {
            return new RedirectResponse($this->urlGenerator->generate("security_login"));
        }

        return new Response(
            $this->twig->render(
                'ui/security/forgotten_password.html.twig',
                [
                    'form' => $handler->createView()
                ]
            )
        );
    }

    /**
     * @Route("/reset-password/{token}", name="security_reset_password")
     * @param Request $request
     * @return Response
     *
     * @throws NonUniqueResultException
     * @throws LoaderError
     * @throws RuntimeError
     * @throws SyntaxError
     */
    public function resetPassword(Request $request): Response
    {
        $token = $request->attributes->get('token');

        if (
            !Uuid::isValid($token)
            || null === ($user = $this->userRepository->findUserByForgottenPasswordToken(Uuid::fromString($token)))
        ) {
            $this->session->getFlashBag()->add(
                "danger",
                "Votre demande de réinitialisation de mot de passe est invalide."
            );
            return new RedirectResponse($this->urlGenerator->generate("security_login"));
        }

        /** @var ResetPasswordHandler $handler */
        $handler = $this->handlerFactory->createHandler(ResetPasswordHandler::class);

        if ($handler->handle($request, $user)) {
            return new RedirectResponse($this->urlGenerator->generate("security_login"));
        }

        return new Response(
            $this->twig->render("ui/security/reset_password.html.twig", [
                'form' => $handler->createView()
            ])
        );
    }
}
