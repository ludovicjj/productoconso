<?php

namespace App\Form;

use App\DTO\PriceDTO;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\MoneyType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * Class PriceType
 * @package App\Form
 */
class PriceType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add("unitPrice", MoneyType::class, [
                "label" => "Prix unitaire (HT)",
                "scale" => 0,
                "help" => "(Vous pouvez utilisez une virgule ou un point comme séparateur)",
                'invalid_message' => "La prix rentré est invalide."
            ])
            ->add("vat", ChoiceType::class, [
                "label" => "TVA",
                "invalid_message" => "La TVA saisi n'est pas valide.",
                "choices" => [
                    "2,1%" => 2.1,
                    "5,5%" => 5.5,
                    "10%" => 10,
                    "20%" => 20
                ]
            ]);
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            "data_class" => PriceDTO::class,
            "empty_data" => function (FormInterface $form) {
                return new PriceDTO(
                    $form->get("unitPrice")->getData(),
                    $form->get("vat")->getData()
                );
            }
        ]);
    }
}
