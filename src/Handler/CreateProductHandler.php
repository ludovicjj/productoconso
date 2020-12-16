<?php

namespace App\Handler;

use App\Core\ImageBuilder;
use App\DTO\ProductDTO;
use App\Entity\Image;
use App\Entity\Price;
use App\Entity\Producer;
use App\Entity\Product;
use App\Form\ProductType;
use App\HandlerFactory\AbstractHandler;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Security\Core\Security;

/**
 * Class CreateProductHandler
 * @package App\Handler
 */
class CreateProductHandler extends AbstractHandler
{
    /** @var FormFactoryInterface $formFactory */
    private $formFactory;

    /** @var EntityManagerInterface $entityManager */
    private $entityManager;

    /** @var Security $security */
    private $security;

    /** @var SessionInterface $session */
    private $session;

    /** @var ImageBuilder $imageBuilder */
    private $imageBuilder;

    public function __construct(
        FormFactoryInterface $formFactory,
        EntityManagerInterface $entityManager,
        Security $security,
        SessionInterface $session,
        ImageBuilder $imageBuilder
    ) {
        $this->formFactory = $formFactory;
        $this->entityManager = $entityManager;
        $this->security = $security;
        $this->session = $session;
        $this->imageBuilder = $imageBuilder;
    }

    public function process(FormInterface $form): void
    {
        /** @var Product $product */
        $product = $this->getEntity();

        /** @var ProductDTO $productDTO */
        $productDTO = $form->getData();

        /** @var Producer $producer */
        $producer = $this->security->getUser();

        $price = new Price();
        $price->setUnitPrice($productDTO->getPrice()->getUnitPrice());
        $price->setVat($productDTO->getPrice()->getVat());

        /** @var Image|null $image */
        $image = $this->imageBuilder->build($productDTO->getImage()->getFile());

        $product->setName($productDTO->getName());
        $product->setDescription($productDTO->getDescription());
        $product->setPrice($price);
        $product->setFarm($producer->getFarm());
        $product->setImage($image);

        $this->entityManager->persist($product);
        $this->entityManager->flush();

        $this->session->getFlashBag()->add("success", "Votre produit a bien été créer.");
    }

    protected function configure(OptionsResolver $resolver): void
    {
        $resolver->setDefault("form_type", ProductType::class);
        $resolver->setDefault("form_options", []);
    }

    public function getFormFactory(): FormFactoryInterface
    {
        return $this->formFactory;
    }
}
