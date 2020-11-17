<?php

namespace App\Form;

use App\DTO\ResetPasswordDTO;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * Class ResetPasswordType
 * @package App\Form
 */
class ResetPasswordType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add("plainPassword", RepeatedType::class, [
                'type' => PasswordType::class,
                'required' => true,
                'invalid_message' => 'Les mots de passe ne sont pas identiques.',
                'first_options'  => ['label' => 'Nouveau Mot de passe'],
                'second_options' => ['label' => 'Confirmer mot de passe'],
            ])
            ->add("save", SubmitType::class, [
                "label" => "valider"
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => ResetPasswordDTO::class,
            'empty_data' => function (FormInterface $form) {
                return new ResetPasswordDTO(
                    $form->get('plainPassword')->getData()
                );
            }
        ]);
    }
}
