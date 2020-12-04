<?php

namespace App\Form;

use App\DTO\AdresseDTO;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * Class AdresseType
 * @package App\Form
 */
class AdresseType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add("adresse", TextType::class, [
                "label" => "Adresse"
            ])
            ->add("restAdresse", TextType::class, [
                "label" => "Complement d'adresse"
            ])
            ->add("zipCode", TextType::class, [
                "label" => "code postal"
            ])
            ->add("city", TextType::class, [
                "label" => "Ville"
            ]);
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefault("data_class", AdresseDTO::class);
    }
}
