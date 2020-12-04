<?php

namespace App\Handler;

use App\DTO\FarmDTO;
use App\Entity\Adresse;
use App\Entity\Farm;
use App\Form\FarmType;
use App\HandlerFactory\AbstractHandler;
use App\Repository\FarmRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\String\Slugger\SluggerInterface;

/**
 * Class UpdateFarmHandler
 * @package App\Handler
 */
class UpdateFarmHandler extends AbstractHandler
{
    /** @var FormFactoryInterface $formFactory */
    private $formFactory;

    /** @var SessionInterface $session */
    private $session;

    /** @var FarmRepository $farmRepository */
    private $farmRepository;

    /** @var EntityManagerInterface $entityManager */
    private $entityManager;

    /** @var SluggerInterface $slugger */
    private $slugger;

    public function __construct(
        FormFactoryInterface $formFactory,
        SessionInterface $session,
        FarmRepository $farmRepository,
        EntityManagerInterface $entityManager,
        SluggerInterface $slugger
    ) {
        $this->formFactory = $formFactory;
        $this->session = $session;
        $this->farmRepository = $farmRepository;
        $this->entityManager = $entityManager;
        $this->slugger = $slugger;
    }

    public function process(FormInterface $form): void
    {
        /** @var FarmDTO $farmDTO */
        $farmDTO = $form->getData();

        /** @var Farm $farm */
        $farm = $this->getEntity();

        $farm->setName($farmDTO->getName());
        $slug = $this->farmRepository->makeUniqueSlug(
            $this->slugger->slug($farmDTO->getName())->lower()
        );
        $farm->setSlug($slug);
        $farm->setDescription($farmDTO->getDescription());

        $adresse = new Adresse();
        $adresse->setAdresse($farmDTO->getAdresse()->getAdresse());
        $adresse->setRestAdresse($farmDTO->getAdresse()->getRestAdresse());
        $adresse->setZipCode($farmDTO->getAdresse()->getZipCode());
        $adresse->setCity($farmDTO->getAdresse()->getCity());

        $farm->setAdresse($adresse);
        $this->entityManager->flush();
        $this->session->getFlashBag()->add(
            "success",
            "Les informations de votre exploitation ont été modifiée avec succès."
        );
    }

    protected function configure(OptionsResolver $resolver): void
    {
        $resolver->setDefault('form_type', FarmType::class);
        $resolver->setDefault('form_options', [
            "validation_groups" => ["Default", "update"]
        ]);
    }

    public function getFormFactory(): FormFactoryInterface
    {
        return $this->formFactory;
    }
}
