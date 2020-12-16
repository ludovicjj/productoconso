<?php

namespace App\Form;

use App\DTO\ProductDTO;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * Class ProductType
 * @package App\Form
 */
class ProductType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add("name", TextType::class, [
                "label" => "Intitulé du produit"
            ])
            ->add("description", TextareaType::class, [
                "label" => "Description du produit"
            ])
            ->add("price", PriceType::class, [
                "label" => false
            ])
            ->add("image", ImageType::class, [
                "label" => false
        ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => ProductDTO::class,
            "empty_data" => function (FormInterface $form) {
                return new ProductDTO(
                    $form->get("name")->getData(),
                    $form->get("description")->getData(),
                    $form->get("price")->getData(),
                    $form->get("image")->getData()
                );
            }
        ]);
    }
}
