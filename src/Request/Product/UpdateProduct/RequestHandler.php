<?php

namespace App\Request\Product\UpdateProduct;

use App\Entity\Product;
use App\Repository\ProductRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\Uid\Uuid;

/**
 * Class RequestHandler
 * @package App\Request\Product\UpdateProduct
 */
class RequestHandler
{
    /** @var Security $security */
    private $security;

    /** @var ProductRepository $productRepository */
    private $productRepository;

    public function __construct(
        Security $security,
        ProductRepository $productRepository
    ) {
        $this->security = $security;
        $this->productRepository = $productRepository;
    }

    /**
     * @param Request $request
     * @return Product
     */
    public function handle(Request $request): Product
    {
        $productId = $request->attributes->get('id');

        // TODO check if uuid is valid
        if (!Uuid::isValid($productId)) {
            throw new NotFoundHttpException("Oops, aucun produit ne correspond à votre requête.");
        }

        /** @var Product|null $product */
        $product = $this->productRepository->findOneBy(['id' => $productId]);

        // TODO check product instance of Product.
        // TODO check current user is instance of Producer.
        // TODO check producer is owner of product.
        if (!$this->security->isGranted('update', $product)) {
            throw new AccessDeniedException(
                "Vous ne disposez pas des autorisations requises pour accéder à cette ressource"
            );
        }

        return $product;
    }
}
