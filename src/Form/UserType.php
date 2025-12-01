<?php

namespace App\Form;

use App\Entity\User;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Validator\Constraints\Image;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;

class UserType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            // Rajouter l'email quand j'aurai fait la vérif
            // ->add('email')
            ->add('firstName', TextType::class, [
                'label' => 'Prénom (facultatif)',
                'required' => false,
            ])
            ->add('lastName', TextType::class, [
                'label' => 'Nom (facultatif)',
                'required' => false,
            ])
            ->add('biography', TextareaType::class, [
                'label' => 'Biographie (facultatif)',
                'required' => false,
                'constraints' => [
                    new Length([
                        'max' => 150,
                        'maxMessage' => 'Votre biographie ne peut pas dépasser {{ limit }} caractères.'
                    ]),
                ],
                'attr' => [
                    'maxlength' => 150,
                    'rows' => 5,
                    'placeholder' => 'Votre bio (150 caractères max)...'
                ],
            ])
            ->addEventListener(FormEvents::PRE_SUBMIT, function (FormEvent $event) {
                $data = $event->getData();
                if (!is_array($data)) {
                    return;
                }

                if (array_key_exists('biography', $data) && null !== $data['biography']) {
                    $data['biography'] = preg_replace('/\R/u', "\n", $data['biography']);
                }

                $event->setData($data);
            })
            ->add('profilePicture', FileType::class, [
                'label' => 'Photo de profil (facultatif)',
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
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => User::class,
        ]);
    }
}
