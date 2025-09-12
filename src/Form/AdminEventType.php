<?php

namespace App\Form;

use App\Entity\Game;
use App\Entity\User;
use App\Entity\Event;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;

class AdminEventType extends AbstractType
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
                    'required' => true,
                    'attr' => [
                        'min' => (new \DateTime())->format('Y-m-d\TH:i'), // aujourd'hui
                    ],
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
                        'min' => 5,
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
            ->add('organizer', EntityType::class, [
                'class' => User::class,
                'choice_label' => 'getFullName',
                'multiple' => false,
                'attr' => [
                    "class" => 'organizer-selector display-none',
                ]
            ])
            ->add('participants', EntityType::class, [
                'class' => User::class,
                'choice_label' => 'getFullName',
                'multiple' => true,
                'attr' => [
                    "class" => 'participants-selector display-none',
                ]
            ])
            ->add('games', EntityType::class, [
                'class' => Game::class,
                'label' => "Jeux associés à l'événement",
                'choice_label' => 'name',
                'multiple' => true,
                'expanded' => false,
                'attr' => [
                    "class" => 'games-selector display-none',
                ]
            ])
            ->add(
                'address',
                AddressType::class,
                [
                    'label' => false,
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
