<?php

namespace App\Handler;

use App\DTO\RegistrationDTO;
use App\Entity\Customer;
use App\Entity\Farm;
use App\Entity\Producer;
use App\Form\RegistrationType;
use App\HandlerFactory\AbstractHandler;
use App\Repository\FarmRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\String\Slugger\SluggerInterface;

class RegistrationHandler extends AbstractHandler
{
    /** @var FormFactoryInterface $formFactory */
    private $formFactory;

    /** @var EntityManagerInterface $entityManager */
    private $entityManager;

    /** @var UserPasswordEncoderInterface $passwordEncoder */
    private $passwordEncoder;

    /** @var SessionInterface $session */
    private $session;

    /** @var FarmRepository $farmRepository */
    private $farmRepository;

    /** @var SluggerInterface $slugger */
    private $slugger;

    /**
     * RegistrationHandler constructor.
     * @param FormFactoryInterface $formFactory
     * @param EntityManagerInterface $entityManager
     * @param UserPasswordEncoderInterface $passwordEncoder
     * @param SessionInterface $session
     * @param FarmRepository $farmRepository
     * @param SluggerInterface $slugger
     */
    public function __construct(
        FormFactoryInterface $formFactory,
        EntityManagerInterface $entityManager,
        UserPasswordEncoderInterface $passwordEncoder,
        SessionInterface $session,
        FarmRepository $farmRepository,
        SluggerInterface $slugger
    ) {
        $this->formFactory = $formFactory;
        $this->entityManager = $entityManager;
        $this->passwordEncoder = $passwordEncoder;
        $this->session = $session;
        $this->farmRepository = $farmRepository;
        $this->slugger = $slugger;
    }

    protected function configure(OptionsResolver $resolver): void
    {
        $resolver->setDefault("form_type", RegistrationType::class);
        $resolver->setDefault('form_options', [
            "validation_groups" => ["Default", "registration"]
        ]);
    }

    public function process(FormInterface $form): void
    {
        /** @var RegistrationDTO $registrationDTO */
        $registrationDTO = $form->getData();

        $entity = $this->getEntity();

        if ($entity !== null && $entity instanceof UserInterface) {
            /** @var Producer|Customer $user */
            $user = $this->getEntity();
            $user->setFirstName($registrationDTO->getFirstName());
            $user->setLastName($registrationDTO->getLastName());
            $user->setEmail($registrationDTO->getEmail());
            $user->setPassword($this->passwordEncoder->encodePassword($user, $registrationDTO->getPlainPassword()));

            if ($user instanceof Producer) {
                /** @var Farm $farm */
                $farm = $user->getFarm();
                $farm->setName($registrationDTO->getFarm()->getName());
                $slug = $this->farmRepository->makeUniqueSlug(
                    $this->slugger->slug($farm->getName())->lower()
                );
                $farm->setSlug($slug);
            }

            $this->entityManager->persist($user);
            $this->entityManager->flush();
            $this->session->getFlashBag()->add('success', "Votre inscription a été effectuée avec succès.");
        }
    }

    public function getFormFactory(): FormFactoryInterface
    {
        return $this->formFactory;
    }
}
