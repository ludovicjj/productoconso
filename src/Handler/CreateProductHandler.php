<?php

namespace App\Handler;

use App\Entity\Producer;
use App\Factory\ProductFactory;
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

    /** @var ProductFactory $productFactory */
    private $productFactory;

    public function __construct(
        FormFactoryInterface $formFactory,
        EntityManagerInterface $entityManager,
        Security $security,
        SessionInterface $session,
        ProductFactory $productFactory
    ) {
        $this->formFactory = $formFactory;
        $this->entityManager = $entityManager;
        $this->security = $security;
        $this->session = $session;
        $this->productFactory = $productFactory;
    }

    public function process(FormInterface $form): void
    {
        /** @var Producer $producer */
        $producer = $this->security->getUser();

        $product = $this->productFactory->create($producer, $form->getData());
        $this->entityManager->persist($product);
        $this->entityManager->flush();

        $this->session->getFlashBag()->add("success", "Votre produit a été créé avec succès.");
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
