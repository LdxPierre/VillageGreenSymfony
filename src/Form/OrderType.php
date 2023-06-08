<?php

namespace App\Form;

use App\Entity\Address;
use App\Entity\Order;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\NotBlank;

class OrderType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('shipping', ChoiceType::class, [
                'mapped' => false,
                'choices' => $options['address'],
                'placeholder' => false,
                'constraints' => [
                    new NotBlank([
                        'message' => 'Veuillez choisir une adresse de livraison',
                    ])
                ]
            ])
            ->add('billingCheck', CheckboxType::class, [
                'mapped' => false,
                'label' => 'Identique à l\'adresse de facturation',
                'data' => true,
            ])
            ->add('billing', ChoiceType::class, [
                'mapped' => false,
                'choices' => $options['address'],
                'placeholder' => false,
            ])
            ->add('submit', SubmitType::class, []);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Order::class,
            'address' => Address::class,
        ]);
    }
}
