<?php

namespace App\Form;

use App\Entity\Review;
use App\Entity\Tour;
use App\Entity\Tourist;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ReviewType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('rating')
            ->add('comment')
            ->add('review_date', null, [
                'widget' => 'single_text',
            ])
            ->add('tour', EntityType::class, [
                'class' => Tour::class,
                'choice_label' => 'id',
            ])
            ->add('tourist', EntityType::class, [
                'class' => Tourist::class,
                'choice_label' => 'id',
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Review::class,
        ]);
    }
}
