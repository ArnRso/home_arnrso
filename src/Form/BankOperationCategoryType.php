<?php

namespace App\Form;

use App\Entity\BankOperationCategory;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class BankOperationCategoryType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('label', TextType::class, [
                'label' => 'bank_operation_category.property.label',
                'required' => true,
            ])
            ->add('slug', TextType::class, [
                'label' => 'bank_operation_category.property.slug',
                'required' => true,
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => BankOperationCategory::class,
        ]);
    }
}
