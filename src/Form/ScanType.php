<?php

declare(strict_types=1);

namespace App\Form;

use App\Entity\Event;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\File;
use Symfony\Component\Validator\Constraints\NotBlank;

class ScanType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('image', FileType::class, [
                'label' => 'Photo de la carte de visite',
                'required' => true,
                'constraints' => [
                    new NotBlank(message: 'Veuillez sélectionner ou prendre une photo de carte de visite.'),
                    new File(
                        maxSize: '10M',
                        mimeTypes: [
                            'image/jpeg',
                            'image/png',
                            'image/webp',
                            'image/heic',
                        ],
                        mimeTypesMessage: 'Veuillez uploader une image valide (JPG, PNG, WEBP, HEIC).'
                    ),
                ],
            ])
            ->add('event', EntityType::class, [
                'class' => Event::class,
                'choice_label' => 'name',
                'required' => false,
                'placeholder' => '-- Aucun ou sélectionner un évènement --',
                'label' => 'Associer immédiatement à un évènement',
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([]);
    }
}
