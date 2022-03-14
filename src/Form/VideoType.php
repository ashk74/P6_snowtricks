<?php

namespace App\Form;

use App\Entity\Video;
use App\Repository\VideoRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\UrlType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class VideoType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('title', TextType::class, [
                'label' => false,
                'attr' => [
                    'placeholder' => 'Titre de la vidéo'
                ]
            ])->add('url', UrlType::class, [
                'label' => false,
                'attr' => [
                    'placeholder' => 'URL de la vidéo'
                ],
                'help' => "* Rendez vous sur Youtube ou Dailymotion, cliquer sur le bouton 'Partager' et copier le lien au format : https://youtu.be/videoID ou https://dai.ly/videoID"
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Video::class,
        ]);
    }
}
