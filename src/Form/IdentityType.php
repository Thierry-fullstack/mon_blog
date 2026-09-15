<?php

namespace App\Form;

use App\Entity\Identity;
use App\Entity\Portrait;
use App\Entity\Region;
use App\Entity\User;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class IdentityType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('createdAt', null, [
                'widget' => 'single_text',
            ])
            ->add('updatedAt', null, [
                'widget' => 'single_text',
            ])
            ->add('gender')
            ->add('skill')
            ->add('pseudo')
            ->add('portrait', EntityType::class, [
                'class' => Portrait::class,
                'choice_label' => 'id',
            ])
            ->add('region', EntityType::class, [
                'class' => Region::class,
                'choice_label' => 'id',
            ])
            ->add('client', EntityType::class, [
                'class' => User::class,
                'choice_label' => 'id',
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Identity::class,
        ]);
    }
}
