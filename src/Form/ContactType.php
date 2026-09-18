<?php

declare(strict_types=1);

namespace App\Form;

use App\Entity\Contact;
use App\Entity\Event;
use App\Enum\ContactTypeEnum;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\EnumType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\TelType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\UrlType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ContactType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('firstname', TextType::class, [
                'label' => 'Prénom',
                'required' => false,
            ])
            ->add('lastname', TextType::class, [
                'label' => 'Nom',
                'required' => false,
            ])
            ->add('email', EmailType::class, [
                'label' => 'Email',
                'required' => false,
            ])
            ->add('phone', TelType::class, [
                'label' => 'Téléphone',
                'required' => false,
            ])
            ->add('company', TextType::class, [
                'label' => 'Entreprise',
                'required' => false,
            ])
            ->add('website', UrlType::class, [
                'label' => 'Site web',
                'required' => false,
                'default_protocol' => 'https',
            ])
            ->add('address', TextareaType::class, [
                'label' => 'Adresse',
                'required' => false,
                'attr' => ['rows' => 2],
            ])
            ->add('notes', TextareaType::class, [
                'label' => 'Notes',
                'required' => false,
                'attr' => ['rows' => 3],
            ])
            ->add('events', EntityType::class, [
                'class' => Event::class,
                'choice_label' => 'name',
                'multiple' => true,
                'expanded' => false,
                'required' => false,
                'label' => 'Évènements associés',
            ])
            ->add('type', EnumType::class, [
                'class' => ContactTypeEnum::class,
                'choice_label' => fn (ContactTypeEnum $c) => $c->value,
                'placeholder' => false,
            ]);

            ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Contact::class,
        ]);
    }
}
