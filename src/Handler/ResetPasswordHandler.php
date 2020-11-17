<?php

namespace App\Handler;

use App\DTO\ResetPasswordDTO;
use App\Entity\Customer;
use App\Entity\Producer;
use App\Form\ResetPasswordType;
use App\HandlerFactory\AbstractHandler;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Symfony\Component\Security\Core\User\UserInterface;

/**
 * Class ResetPasswordHandler
 * @package App\Handler
 */
class ResetPasswordHandler extends AbstractHandler
{
    /** @var FormFactoryInterface $formFactory */
    private $formFactory;

    /** @var EntityManagerInterface $entityManager */
    private $entityManager;

    /** @var SessionInterface $session */
    private $session;

    /** @var UserPasswordEncoderInterface $userPasswordEncoder */
    private $userPasswordEncoder;

    public function __construct(
        FormFactoryInterface $formFactory,
        EntityManagerInterface $entityManager,
        SessionInterface $session,
        UserPasswordEncoderInterface $userPasswordEncoder
    ) {
        $this->formFactory = $formFactory;
        $this->entityManager = $entityManager;
        $this->session = $session;
        $this->userPasswordEncoder = $userPasswordEncoder;
    }

    protected function configure(OptionsResolver $resolver): void
    {
        $resolver->setDefault('form_type', ResetPasswordType::class);
        $resolver->setDefault('form_options', []);
    }

    public function process(FormInterface $form): void
    {
        /** @var ResetPasswordDTO $resetPasswordDTO */
        $resetPasswordDTO = $form->getData();

        /** @var Customer|Producer|null $user */
        $user = $this->getEntity();
        if ($user !== null && $user instanceof UserInterface) {
            $user->setPassword(
                $this->userPasswordEncoder->encodePassword($user, $resetPasswordDTO->getPlainPassword())
            );

            $this->entityManager->flush();

            $this->session->getFlashBag()->add(
                "success",
                "Votre mot de passe a été réinitialisé avec success."
            );
        }
    }

    public function getFormFactory(): FormFactoryInterface
    {
        return $this->formFactory;
    }
}
