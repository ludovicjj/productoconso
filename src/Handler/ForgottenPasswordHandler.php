<?php

namespace App\Handler;

use App\Entity\Customer;
use App\Entity\Producer;
use App\Form\ForgottenPasswordType;
use App\HandlerFactory\AbstractHandler;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ForgottenPasswordHandler extends AbstractHandler
{
    /** @var FormFactoryInterface $formFactory */
    private $formFactory;

    /** @var UserRepository $userRepository */
    private $userRepository;

    /** @var MailerInterface $mailer */
    private $mailer;

    /** @var SessionInterface $session */
    private $session;

    /** @var EntityManagerInterface $entityManager */
    private $entityManager;

    public function __construct(
        FormFactoryInterface $formFactory,
        UserRepository $userRepository,
        MailerInterface $mailer,
        SessionInterface $session,
        EntityManagerInterface $entityManager
    ) {
        $this->formFactory = $formFactory;
        $this->userRepository = $userRepository;
        $this->mailer = $mailer;
        $this->session = $session;
        $this->entityManager = $entityManager;
    }

    protected function configure(OptionsResolver $resolver): void
    {
        $resolver->setDefault("form_type", ForgottenPasswordType::class);
        $resolver->setDefault('form_options', []);
    }

    public function process(FormInterface $form): void
    {
        /** @var Customer|Producer $user */
        $user = $this->userRepository->findOneBy(['email' => $form->get('email')->getData()]);
        $user->hasForgottenPassword();
        $this->entityManager->flush();

        $mailer = (new TemplatedEmail())
            ->from("contact@producteurtoconso.com")
            ->to($user->getEmail())
            ->subject("Mot de pass")
            ->context(['token' => $user->getForgottenPassword()->getToken()])
            ->htmlTemplate('emails/forgotten_password.html.twig');
        $this->mailer->send($mailer);

        $this->session->getFlashBag()->add(
            'success',
            "Vous allez recevoir un email pour réinitialiser votre mot de passe."
        );
    }

    public function getFormFactory(): FormFactoryInterface
    {
        return $this->formFactory;
    }
}
