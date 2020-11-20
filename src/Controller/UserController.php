<?php

namespace App\Controller;

use App\Handler\EditUserInfoHandler;
use App\HandlerFactory\HandlerFactory;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\Security\Core\User\UserInterface;
use Twig\Environment;
use Twig\Error\LoaderError;
use Twig\Error\RuntimeError;
use Twig\Error\SyntaxError;

/**
 * Class UserController
 * @package App\Controller
 */
class UserController
{
    /** @var Environment $twig */
    private $twig;

    /** @var HandlerFactory $handlerFactory */
    private $handlerFactory;

    /** @var UrlGeneratorInterface $urlGenerator */
    private $urlGenerator;

    /** @var Security $security */
    private $security;

    /** @var SessionInterface $session */
    private $session;

    /**
     * UserController constructor.
     * @param Environment $twig
     * @param HandlerFactory $handlerFactory
     * @param UrlGeneratorInterface $urlGenerator
     * @param Security $security
     * @param SessionInterface $session
     */
    public function __construct(
        Environment $twig,
        HandlerFactory $handlerFactory,
        UrlGeneratorInterface $urlGenerator,
        Security $security,
        SessionInterface $session
    ) {
        $this->twig = $twig;
        $this->handlerFactory = $handlerFactory;
        $this->urlGenerator = $urlGenerator;
        $this->security = $security;
        $this->session = $session;
    }

    /**
     * @Route("/edit-info", name="user_edit_info")
     * @param Request $request
     * @return Response
     *
     * @throws LoaderError
     * @throws RuntimeError
     * @throws SyntaxError
     */
    public function editUserInfo(Request $request): Response
    {
        /**
         * AccessDeniedException :
         * If the user isn’t logged in yet, they will be asked to log in (e.g. redirected to the login page).
         * If the user is logged in, but does not have the ROLE_ADMIN role, they’ll be shown the 403 access denied page
         */
        if (!$this->security->isGranted('ROLE_USER')) {
            $this->session->getFlashBag()->add("danger", "Vous devez vous connecter pour accéder à cette page");
            throw new AccessDeniedException();
        }

        /** @var UserInterface $user */
        $user = $this->security->getUser();

        /** @var EditUserInfoHandler $handler */
        $handler = $this->handlerFactory->createHandler(EditUserInfoHandler::class);

        if ($handler->handle($request, $user)) {
            return new RedirectResponse(
                $this->urlGenerator->generate("user_edit_info")
            );
        }

        return new Response(
            $this->twig->render("ui/user/edit_info.html.twig", [
                'form' => $handler->createView()
            ])
        );
    }
}
