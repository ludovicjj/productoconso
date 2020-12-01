<?php

namespace App\Handler;

use App\DTO\EditUserPasswordDTO;
use App\Form\EditUserPasswordType;
use App\HandlerFactory\AbstractHandler;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Symfony\Component\Security\Core\User\UserInterface;

/**
 * Class EditUserPasswordHandler
 * @package App\Handler
 */
class EditUserPasswordHandler extends AbstractHandler
{
    /** @var FormFactoryInterface $formFactory */
    private $formFactory;

    /** @var UserPasswordEncoderInterface $userPasswordEncoder */
    private $userPasswordEncoder;

    /** @var SessionInterface $session */
    private $session;

    private $entityManager;

    public function __construct(
        FormFactoryInterface $formFactory,
        UserPasswordEncoderInterface $userPasswordEncoder,
        SessionInterface $session,
        EntityManagerInterface $entityManager
    ) {
        $this->formFactory = $formFactory;
        $this->userPasswordEncoder = $userPasswordEncoder;
        $this->session = $session;
        $this->entityManager = $entityManager;
    }

    public function process(FormInterface $form): void
    {
        /** @var EditUserPasswordDTO $editUserPasswordDTO */
        $editUserPasswordDTO = $form->getData();

        /** @var UserInterface $user */
        $user = $this->getEntity();
        $user->setPassword(
            $this->userPasswordEncoder->encodePassword($user, $editUserPasswordDTO->getPlainPassword())
        );

        $this->entityManager->flush();

        $this->session->getFlashBag()->add('success', "Votre mot de passe a été modifié avec success.");
    }

    protected function configure(OptionsResolver $resolver): void
    {
        $resolver->setDefault("form_type", EditUserPasswordType::class);
        $resolver->setDefault('form_options', []);
    }

    public function getFormFactory(): FormFactoryInterface
    {
        return $this->formFactory;
    }
}
