<?php

namespace App\Form;

use App\Entity\Booking;
use App\Entity\Tour;
use App\Entity\Tourist;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class BookingType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('booking_date', null, [
                'widget' => 'single_text',
            ])
            ->add('number_of_people')
            ->add('total_price')
            ->add('tourist', EntityType::class, [
                'class' => Tourist::class,
                'choice_label' => 'id',
            ])
            ->add('tour', EntityType::class, [
                'class' => Tour::class,
                'choice_label' => 'id',
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Booking::class,
        ]);
    }
}
