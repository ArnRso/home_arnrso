<?php

namespace App\Form;

use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class BankOperationModalType extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('affectedBudget', ChoiceType::class, [
                'label' => 'Budget du mois',
                'choices' => $this->getAffectedBudget(24)
            ])
            ->add('bankOperationCategory', EntityType::class, [
                'label' => 'Catégorie',
                'class' => 'App\Entity\BankOperationCategory',
                'choice_label' => 'label',
                'placeholder' => 'Choisir une catégorie',
                'required' => false
            ])
        ;
    }

    private function getAffectedBudget($numberOfMonths = 1)
    {
        $ret = [];

        for ($i = 0; $i <= $numberOfMonths; $i++) {
            $ret[date('m Y', strtotime(sprintf('-%d month', $i)))] = date('m/Y', strtotime(sprintf('-%d month', $i)));
        }
        return $ret;
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
