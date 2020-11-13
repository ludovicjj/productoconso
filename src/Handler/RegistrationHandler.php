<?php

namespace App\Handler;

use App\DTO\RegistrationDTO;
use App\Entity\Customer;
use App\Entity\Producer;
use App\Form\RegistrationType;
use App\HandlerFactory\AbstractHandler;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Symfony\Component\Security\Core\User\UserInterface;

class RegistrationHandler extends AbstractHandler
{
    /**
     * @var FormFactoryInterface $formFactory
     */
    private $formFactory;

    /**
     * @var EntityManagerInterface $entityManager
     */
    private $entityManager;

    /**
     * @var UserPasswordEncoderInterface $passwordEncoder
     */
    private $passwordEncoder;

    /**
     * @var SessionInterface $session
     */
    private $session;

    /**
     * RegistrationHandler constructor.
     * @param FormFactoryInterface $formFactory
     * @param EntityManagerInterface $entityManager
     * @param UserPasswordEncoderInterface $passwordEncoder
     * @param SessionInterface $session
     */
    public function __construct(
        FormFactoryInterface $formFactory,
        EntityManagerInterface $entityManager,
        UserPasswordEncoderInterface $passwordEncoder,
        SessionInterface $session
    ) {
        $this->formFactory = $formFactory;
        $this->entityManager = $entityManager;
        $this->passwordEncoder = $passwordEncoder;
        $this->session = $session;
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
            $user->setFirstName($registrationDTO->firstName);
            $user->setLastName($registrationDTO->lastName);
            $user->setEmail($registrationDTO->email);
            $user->setPassword($this->passwordEncoder->encodePassword($user, $registrationDTO->plainPassword));

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
