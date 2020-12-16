<?php

namespace App\Form;

use App\DTO\ImageDTO;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * Class ImageType
 * @package App\Form
 */
class ImageType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add("file", FileType::class, [
                "label" => "image de votre produit",
                "required" => false,
                "help" => "(Image au format jpeg, jpg, png, gif)"
            ]);
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => ImageDTO::class,
            'empty_data' => function (FormInterface $form) {
                return new ImageDTO(
                    $form->get("file")->getData()
                );
            }
        ]);
    }
}
