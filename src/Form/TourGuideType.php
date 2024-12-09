<?php

namespace App\Form;

use App\Entity\Guide;
use App\Entity\Tour;
use App\Entity\TourGuide;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class TourGuideType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('tour', EntityType::class, [
                'class' => Tour::class,
                'choice_label' => 'id',
            ])
            ->add('guide', EntityType::class, [
                'class' => Guide::class,
                'choice_label' => 'id',
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => TourGuide::class,
        ]);
    }
}
