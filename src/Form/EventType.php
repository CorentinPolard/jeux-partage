<?php

namespace App\Form;

use App\Entity\Game;
use App\Entity\User;
use App\Entity\Event;
use App\Entity\Address;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;

class EventType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add(
                'title',
                TextType::class,
                [
                    'label' => 'Nom de votre événement',
                    'required' => true,
                ]
            )
            ->add(
                'description',
                TextareaType::class,
                [
                    'label' => "Description de l'événement",
                    'required' => true,
                ]
            )
            ->add(
                'eventAt',
                DateTimeType::class,
                [
                    'label' => 'Date et heure de l\'événement',
                    'required' => true
                ]
            )
            ->add(
                'maxNumberOfParticipants',
                IntegerType::class,
                [
                    'label' => "Nombre maximum de participants (vous compris)",
                    'required' => false,
                    'attr' => [
                        'min' => 1,
                    ],
                ]
            )
            ->add(
                'duration',
                IntegerType::class,
                [
                    'label' => 'Durée moyenne de votre événement (en minutes)',
                    'required' => true,
                    'attr' => [
                        'min' => 1,
                    ],
                ]
            )
            ->add(
                'isFree',
                CheckboxType::class,
                [
                    'label' => 'L\'événement est-il totalement gratuit pour les participants ?',
                    'required' => false,
                ]
            )
            ->add('games', EntityType::class, [
                'class' => Game::class,
                'choice_label' => 'name',
                'multiple' => true,
            ])
            ->add(
                'address',
                AddressType::class,
                [
                    'label' => 'Adresse de l\'événement',
                ]
            )
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Event::class,
        ]);
    }
}
