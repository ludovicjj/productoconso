<?php

namespace App\Handler;

use App\DTO\EditUserInfoDTO;
use App\Entity\Customer;
use App\Entity\Producer;
use App\Form\EditUserInfoType;
use App\HandlerFactory\AbstractHandler;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Security\Core\User\UserInterface;

/**
 * Class EditUserInfoHandler
 * @package App\Handler
 */
class EditUserInfoHandler extends AbstractHandler
{
    /** @var FormFactoryInterface $formFactory */
    private $formFactory;

    /** @var SessionInterface $session */
    private $session;

    /** @var EntityManagerInterface $entityManager */
    private $entityManager;

    public function __construct(
        FormFactoryInterface $formFactory,
        SessionInterface $session,
        EntityManagerInterface $entityManager
    ) {
        $this->formFactory = $formFactory;
        $this->session = $session;
        $this->entityManager = $entityManager;
    }

    public function process(FormInterface $form): void
    {
        /** @var EditUserInfoDTO $editUserInfoDTO */
        $editUserInfoDTO = $form->getData();

        /** @var Customer|Producer|null $user */
        $user = $this->getEntity();

        if (null != $user && $user instanceof UserInterface) {
            $user->setFirstName($editUserInfoDTO->getFirstName());
            $user->setLastName($editUserInfoDTO->getLastName());
            $user->setEmail($editUserInfoDTO->getEmail());
            $this->entityManager->flush();

            $this->session->getFlashBag()->add("success", "Vous modifications ont été enregistrées avec success.");
        }
    }

    protected function configure(OptionsResolver $resolver): void
    {
        $resolver->setDefault('form_type', EditUserInfoType::class);
        $resolver->setDefault('form_options', []);
    }

    public function getFormFactory(): FormFactoryInterface
    {
        return $this->formFactory;
    }
}
