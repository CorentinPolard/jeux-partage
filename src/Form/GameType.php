<?php

namespace App\Form;

use App\Entity\Game;
use App\Entity\Category;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Validator\Constraints\Image;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;

class GameType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add(
                'name',
                TextType::class,
                [
                    'label' => 'Nom du jeu',
                    'required' => true,
                ]
            )
            ->add(
                'description',
                TextareaType::class,
                [
                    'label' => 'Description du jeu',
                    'required' => true,
                ]
            )
            ->add(
                'minimumNumberOfPlayers',
                IntegerType::class,
                [
                    'label' => 'Nombre minimum de joueurs',
                    'required' => true,
                    'attr' => [
                        'min' => 1,
                    ],
                ]
            )
            ->add(
                'maximumNumberOfPlayers',
                IntegerType::class,
                [
                    'label' => 'Nombre maximum de joueurs',
                    'required' => true,
                    'attr' => [
                        'min' => 1,
                    ],
                ]
            )
            ->add(
                'averageGameDuration',
                IntegerType::class,
                [
                    'label' => 'Durée moyenne d\'une partie (en minutes)',
                    'required' => true,
                    'attr' => [
                        'min' => 1,
                    ],
                ]
            )
            ->add(
                'image',
                FileType::class,
                [
                    'label' => 'Image',
                    'mapped' => false,
                    'required' => false,
                    'constraints' => [
                        new Image([
                            'maxSize' => '5M',
                            'mimeTypes' => [
                                'image/jpeg',
                                'image/png',
                                'image/webp',
                            ],
                            'mimeTypesMessage' => 'Merci d\'uploader une image valide (JPG, PNG ou WEBP)',
                            // On peut rajouter des min/max width/height
                        ])
                    ],
                ]
            )
            ->add(
                'category',
                EntityType::class,
                [
                    'class' => Category::class,
                    'label' => 'Catégorie du jeu',
                    'choice_label' => 'label',
                ]
            )
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Game::class,
            'csrf_protection' => true,      // active la protection CSRF
            'csrf_field_name' => '_token',  // nom du champ caché
            'csrf_token_id' => 'game', // identifiant unique pour ce formulaire
        ]);
    }
}
