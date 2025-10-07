<?php

namespace App\Form;

use App\Entity\User;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Validator\Constraints\Image;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\TextType;

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
