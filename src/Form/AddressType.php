<?php

namespace App\Form;

use App\Entity\Address;
use App\Entity\Event;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class AddressType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add(
                'street',
                TextType::class,
                [
                    'label' => 'Adresse de l\'évènement',
                    'required' => true,
                ]
            )
            ->add(
                'postcode',
                TextType::class,
                [
                    'label' => 'Code postal',
                    'required' => true,
                ]
            )
            ->add(
                'city',
                TextType::class,
                [
                    'label' => 'Ville',
                    'required' => true,
                ]
            )
            ->add(
                'longitude',
                HiddenType::class,
                [
                    'required' => true,
                    'attr' => ['aria-hidden' => true],
                ]
            )
            ->add(
                'latitude',
                HiddenType::class,
                [
                    'required' => true,
                    'attr' => ['aria-hidden' => true],
                ]
            )
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Address::class,
        ]);
    }
}
