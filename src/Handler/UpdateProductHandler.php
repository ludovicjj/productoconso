<?php

namespace App\Handler;

use App\Entity\Product;
use App\Factory\ProductFactory;
use App\Form\ProductType;
use App\HandlerFactory\AbstractHandler;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * Class UpdateProductHandler
 * @package App\Handler
 */
class UpdateProductHandler extends AbstractHandler
{
    /** @var FormFactoryInterface $formFactory */
    private $formFactory;

    /** @var SessionInterface $session */
    private $session;

    /** @var EntityManagerInterface $entityManager */
    private $entityManager;

    /** @var ProductFactory $productFactory */
    private $productFactory;

    public function __construct(
        FormFactoryInterface $formFactory,
        SessionInterface $session,
        EntityManagerInterface $entityManager,
        ProductFactory $productFactory
    ) {
        $this->formFactory = $formFactory;
        $this->session = $session;
        $this->entityManager = $entityManager;
        $this->productFactory = $productFactory;
    }

    public function process(FormInterface $form): void
    {
        /** @var Product $product */
        $product = $this->getEntity();
        $this->productFactory->update($product, $form->getData());
        $this->entityManager->flush();
        $this->session->getFlashBag()->add("success", "Votre produit a été modifié avec succès.");
    }

    public function getFormFactory(): FormFactoryInterface
    {
        return $this->formFactory;
    }

    protected function configure(OptionsResolver $resolver): void
    {
        $resolver->setDefault("form_type", ProductType::class);
        $resolver->setDefault("form_options", []);
    }
}
