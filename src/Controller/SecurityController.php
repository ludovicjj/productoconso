<?php

namespace App\Controller;

use App\Entity\Customer;
use App\Entity\Producer;
use App\Handler\RegistrationHandler;
use App\HandlerFactory\HandlerFactoryInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;
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
    /**
     * @var Environment $twig
     */
    private $twig;

    /**
     * @var HandlerFactoryInterface $handlerFactory
     */
    private $handlerFactory;


    /**
     * SecurityController constructor.
     * @param Environment $twig
     * @param HandlerFactoryInterface $handlerFactory
     */
    public function __construct(
        Environment $twig,
        HandlerFactoryInterface $handlerFactory
    ) {
        $this->twig = $twig;
        $this->handlerFactory = $handlerFactory;
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
            return new RedirectResponse("/");
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
}
