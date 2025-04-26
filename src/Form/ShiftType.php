<?php

namespace App\Form;

use App\Entity\Shift;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TimeType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ShiftType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('date', DateType::class, [
                'widget' => 'single_text',
                'input' => 'datetime_immutable',
                'label' => 'Shift Date',
                'required' => true,
            ])
            ->add('startTime', TimeType::class, [
                'widget' => 'choice',
                'input' => 'datetime_immutable',
                'label' => 'Start Time',
                'required' => true,
            ])
            ->add('endTime', TimeType::class, [
                'widget' => 'choice',
                'input' => 'datetime_immutable',
                'label' => 'End Time',
                'required' => true,
            ])
            ->add('notes', TextareaType::class, [
                'required' => false,
                'attr' => [
                    'rows' => 3,
                    'placeholder' => 'Any special instructions or notes for this shift...'
                ],
            ])
            ->add('shiftRoles', CollectionType::class, [
                'entry_type' => ShiftRoleType::class,
                'entry_options' => ['label' => false],
                'allow_add' => true,
                'allow_delete' => true,
                'by_reference' => false,
                'prototype' => true,
                'label' => false,
            ])
            ->add('submit', SubmitType::class)
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Shift::class,
        ]);
    }
}