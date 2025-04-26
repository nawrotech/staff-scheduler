<?php

namespace App\Form;

use App\Entity\ShiftRole;
use App\Enum\StaffPosition;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ButtonType;
use Symfony\Component\Form\Extension\Core\Type\EnumType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Translation\TranslatableMessage;
use Symfony\Component\Validator\Constraints\GreaterThan;
use Symfony\Component\Validator\Constraints\NotBlank;

class ShiftRoleType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('roleName', EnumType::class, [
                'class' => StaffPosition::class,
                'label' => 'Position',
                'required' => true,
                'choice_label' => function ($choice, string $key, mixed $value): TranslatableMessage|string {
                    return $value;
                },
            ])
            ->add('quantity', IntegerType::class, [
                'label' => 'How many needed',
                'constraints' => [
                    new NotBlank(['message' => 'Please enter how many staff are needed']),
                    new GreaterThan([
                        'value' => 0,
                        'message' => 'You need at least 1 staff member',
                    ]),
                ],
                'attr' => [
                    'min' => 1,
                    'class' => 'role-quantity',
                ],
            ])
            ->add('remove', ButtonType::class, [
                'attr' => [
                    'class' => 'btn btn-danger',
                    'data-action' =>  'click->form-collection#removeElement'
                ]
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => ShiftRole::class,
        ]);
    }
}