<?php

namespace App\Controller;

use App\DTO\FarmDTO;
use App\Entity\Farm;
use App\Entity\Producer;
use App\Handler\UpdateFarmHandler;
use App\HandlerFactory\HandlerFactory;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;
use Symfony\Component\Security\Core\Security;
use Twig\Environment;
use Twig\Error\LoaderError;
use Twig\Error\RuntimeError;
use Twig\Error\SyntaxError;

/**
 * Class FarmController
 * @package App\Controller
 */
class FarmController
{
    /** @var Security $security */
    private $security;

    /** @var Environment $twig */
    private $twig;

    /** @var HandlerFactory $handlerFactory */
    private $handlerFactory;

    /** @var UrlGeneratorInterface $urlGenerator */
    private $urlGenerator;

    public function __construct(
        Security $security,
        Environment $twig,
        HandlerFactory $handlerFactory,
        UrlGeneratorInterface $urlGenerator
    ) {
        $this->security = $security;
        $this->twig = $twig;
        $this->handlerFactory = $handlerFactory;
        $this->urlGenerator = $urlGenerator;
    }

    /**
     * @Route("/update", name="farm_update")
     * @param Request $request
     * @return Response
     *
     * @throws LoaderError
     * @throws RuntimeError
     * @throws SyntaxError
     */
    public function update(Request $request): Response
    {
        if (!$this->security->isGranted('ROLE_PRODUCER')) {
            throw new AccessDeniedException();
        }
        /** @var UpdateFarmHandler $handler */
        $handler = $this->handlerFactory->createHandler(UpdateFarmHandler::class);

        /** @var Producer $user */
        $user = $this->security->getUser();
        $farm = $user->getFarm();
        $farmDTO = new FarmDTO();
        $farmDTO->setUserFarm($farm);

        if ($handler->handle($request, $farm, $farmDTO)) {
            return new RedirectResponse(
                $this->urlGenerator->generate("farm_update")
            );
        }

        return new Response(
            $this->twig->render("ui/farm/update_farm.html.twig", [
                'form' => $handler->createView()
            ])
        );
    }
}
