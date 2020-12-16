<?php

namespace App\Controller;

use App\Entity\Product;
use App\Handler\CreateProductHandler;
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
 * Class ProductController
 * @package App\Controller
 */
class ProductController
{
    /** @var HandlerFactory $handlerFactory */
    private $handlerFactory;

    /** @var Environment $twig */
    private $twig;

    /** @var Security $security */
    private $security;

    /** @var UrlGeneratorInterface $urlGenerator */
    private $urlGenerator;

    public function __construct(
        HandlerFactory $handlerFactory,
        Environment $twig,
        Security $security,
        UrlGeneratorInterface $urlGenerator
    ) {
        $this->handlerFactory = $handlerFactory;
        $this->twig = $twig;
        $this->security = $security;
        $this->urlGenerator = $urlGenerator;
    }

    /**
     * @Route("/products", name="product_index")
     * @return Response
     *
     * @throws LoaderError
     * @throws RuntimeError
     * @throws SyntaxError
     */
    public function index()
    {
        return new Response(
            $this->twig->render("ui/Product/index.html.twig")
        );
    }

    /**
     * @Route("/product/create", name="product_create")
     * @param Request $request
     * @return Response
     *
     * @throws LoaderError
     * @throws RuntimeError
     * @throws SyntaxError
     */
    public function create(Request $request): Response
    {
        if (!$this->security->isGranted('ROLE_PRODUCER')) {
            throw new AccessDeniedException();
        }

        /** @var CreateProductHandler $handler */
        $handler = $this->handlerFactory->createHandler(CreateProductHandler::class);
        $product = new Product();

        if ($handler->handle($request, $product)) {
            return new RedirectResponse(
                $this->urlGenerator->generate("product_index")
            );
        }

        return new Response(
            $this->twig->render("ui/product/create_product.html.twig", [
                "form" => $handler->createView()
            ])
        );
    }
}
