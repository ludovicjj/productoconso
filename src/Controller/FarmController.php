<?php

namespace App\Controller;

use App\Core\ParameterBagTransformer;
use App\DTO\FarmDTO;
use App\Entity\Producer;
use App\Handler\UpdateFarmHandler;
use App\HandlerFactory\HandlerFactory;
use App\Repository\FarmRepository;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\Serializer\SerializerInterface;
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

    /** @var FarmRepository $farmRepository */
    private $farmRepository;

    /** @var SerializerInterface $serializer */
    private $serializer;

    /** @var ParameterBagTransformer $parameterBagTransformer */
    private $parameterBagTransformer;

    public function __construct(
        Security $security,
        Environment $twig,
        HandlerFactory $handlerFactory,
        UrlGeneratorInterface $urlGenerator,
        FarmRepository $farmRepository,
        SerializerInterface $serializer,
        ParameterBagTransformer $parameterBagTransformer
    ) {
        $this->security = $security;
        $this->twig = $twig;
        $this->handlerFactory = $handlerFactory;
        $this->urlGenerator = $urlGenerator;
        $this->farmRepository = $farmRepository;
        $this->serializer = $serializer;
        $this->parameterBagTransformer = $parameterBagTransformer;
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

    /**
     * @Route("/{slug}/show", name="farm_show")
     * @param Request $request
     * @return Response
     *
     * @throws LoaderError
     * @throws RuntimeError
     * @throws SyntaxError
     */
    public function show(Request $request): Response
    {
        $slug = $request->attributes->get("slug");

        $farm = $this->farmRepository->findOneBy(['slug' => $slug]);

        if (null === $farm) {
            $message = "Oops, il semblerait qu'aucun résultat n'est été trouvé pour cette requête";
            throw new NotFoundHttpException($message);
        }

        return new Response(
            $this->twig->render("ui/farm/show_farm.html.twig", [
                "farm" => $farm,
            ])
        );
    }

    /**
     * @Route("/all", name="farm_all")
     * @param Request $request
     * @return JsonResponse
     */
    public function all(Request $request): JsonResponse
    {
        $context = $this->parameterBagTransformer->transformQueryToContext($request->query);
        $json = $this->serializer->serialize($this->farmRepository->searchFarm(), "json", $context);

        return new JsonResponse($json, JsonResponse::HTTP_OK, [], true);
    }
}
