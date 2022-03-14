<?php

namespace App\Form;

use App\Entity\User;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Validator\Constraints\File;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Validator\Constraints\Image;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints\NotNull;

class UserType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('avatar', FileType::class, [
                'label' => false,
                'required' => false,
                'mapped' => true,
                'data_class' => null,
                'constraints' => [
                    new NotBlank(message: 'Aucune image uploadée'),
                    new Image([
                        'maxSize' => '3M',
                        'uploadFormSizeErrorMessage' => 'Le poid de l\'image doit être de 3Mo maximum',
                        'allowLandscape' => false,
                        'allowLandscapeMessage' => 'L\'image doit être de forme carré',
                        'allowPortrait' => false,
                        'allowPortraitMessage' => 'L\'image doit être de forme carré'
                    ])
                ]
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => User::class,
        ]);
    }
}
