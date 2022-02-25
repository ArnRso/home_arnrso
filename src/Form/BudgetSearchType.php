<?php

namespace App\Form;

use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use App\Entity\BankOperationCategory;

class BudgetSearchType extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->setMethod('GET')
            ->add('label', TextType::class, [
                'label' => 'Label',
                'required' => false
            ])
            ->add('sign', ChoiceType::class, [
                'label' => 'Débit / Crédit',
                'choices' => [
                    'Débit' => 'minus',
                    'Crédit' => 'plus'
                ],
                'multiple' => false,
                'expanded' => true,
                'required' => false,
                'placeholder' => 'Tous'
            ])
            ->add('category', EntityType::class, [
                'label' => 'Catégorie',
                'class' => BankOperationCategory::class,
                'choice_label' => 'label',
                'multiple' => false,
                'expanded' => false,
                'required' => false,
                'placeholder' => 'Toutes'
            ])
            ->add('dateFrom', DateType::class, [
                'widget' => 'single_text',
                'label' => 'Du',
                'required' => false
            ])
            ->add('dateTo', DateType::class, [
                'widget' => 'single_text',
                'label' => 'Au',
                'required' => false
            ]);
    }

    /**
     * @param OptionsResolver $resolver
     */
    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            // Configure your form options here
        ]);
    }
}
