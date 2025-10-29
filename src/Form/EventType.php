<?php

namespace App\Form;

use App\Entity\Game;
use App\Entity\User;
use Symfony\Component\Validator\Constraints\NotBlank;
use App\Entity\Event;
use App\Entity\Address;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
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
                    'label' => 'Nom de votre évènement',
                    'required' => true,
                    'constraints' => [
                        new NotBlank([
                            'message' => 'Le nom de l’évènement ne peut pas être vide.',
                        ]),
                    ],
                ]
            )
            ->add(
                'description',
                TextareaType::class,
                [
                    'label' => "Description de l'évènement",
                    'required' => true,
                    'constraints' => [
                        new NotBlank([
                            'message' => 'La description de l’évènement ne peut pas être vide.',
                        ]),
                    ],
                ]
            )
            ->add(
                'eventAt',
                DateTimeType::class,
                [
                    'label' => 'Date et heure de l\'évènement',
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
                    'label' => "Nombre maximum de participants, vous compris (facultatif)",
                    'required' => false,
                    'attr' => [
                        'min' => 2,
                    ],
                ]
            )
            ->add(
                'duration',
                IntegerType::class,
                [
                    'label' => 'Durée moyenne de votre évènement (en minutes)',
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
                    'label' => 'L\'évènement est-il totalement gratuit pour les participants ?',
                    'required' => false,
                ]
            )
            ->add('games', EntityType::class, [
                'class' => Game::class,
                'label' => "Jeux associés à l'évènement",
                'choice_label' => 'name',
                'multiple' => true,
                'expanded' => false,      // false => <select multiple>, true => cases à cocher
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
            );

        // Champ conditionnel, affiché seulement si 'show_admin_fields' = true
        if ($options['show_admin_fields']) {
            $builder
                ->add('participants', EntityType::class, [
                    'class' => User::class,
                    'choice_label' => 'getFullName',
                    'multiple' => true,
                    'by_reference' => false,
                    'required' => false,
                    'attr' => [
                        "class" => 'participants-selector display-none',
                    ]
                ])
                ->add('organizer', EntityType::class, [
                    'class' => User::class,
                    'choice_label' => 'getFullName',
                    'multiple' => false,
                    'attr' => [
                        "class" => 'organizer-selector display-none',
                    ]
                ]);
        }
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Event::class,
            'show_admin_fields' => false, // Champs admin cachés par défaut
            'csrf_protection' => true,      // active la protection CSRF
            'csrf_field_name' => '_token',  // nom du champ caché
            'csrf_token_id' => 'event', // identifiant unique pour ce formulaire
        ]);
    }
}
