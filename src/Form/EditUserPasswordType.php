<?php

namespace App\Form;

use App\DTO\EditUserPasswordDTO;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class EditUserPasswordType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add("currentPassword", PasswordType::class, [
                'label' => "Mot de passe"
            ])
            ->add("plainPassword", RepeatedType::class, [
                'type' => PasswordType::class,
                'invalid_message' => 'Les mots de passe ne sont pas identiques.',
                'first_options'  => ['label' => 'Nouveau Mot de passe'],
                'second_options' => ['label' => 'Confirmer nouveau mot de passe'],
            ])
            ->add('save', SubmitType::class, [
                'label' => "valider"
            ]);
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => EditUserPasswordDTO::class,
            'empty_data' => function (FormInterface $form) {
                return new EditUserPasswordDTO(
                    $form->get('currentPassword')->getData(),
                    $form->get('plainPassword')->getData()
                );
            }
        ]);
    }
}
