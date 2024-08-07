<?php

namespace App\Form;

use App\Entity\Episode;
use App\Entity\Season;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Vich\UploaderBundle\Form\Type\VichImageType;

class EpisodeType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('title')
            ->add('synopsis')
            ->add('number')
            ->add('season', EntityType::class, [
                'class' => Season::class,
                'choice_label' => 'number',
            ])
            ->add('posterFile', VichImageType::class, [
                'required' => false,
                'allow_delete' => true,
                'download_uri' => true,
            ])
            ->add('duration') // Ajoutez cette ligne
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Episode::class,
        ]);
    }
}
