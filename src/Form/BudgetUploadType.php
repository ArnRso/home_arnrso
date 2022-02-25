<?php

namespace App\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\File;

class BudgetUploadType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('budgetFile', FileType::class, [
                'label' => 'Fichier du compte (.csv)',
                'mapped' => false,
                'required' => true,
                'constraints' => [
                    new File([
                        'disallowEmptyMessage' => 'disallowEmptyMessage',
                        'uploadNoFileErrorMessage' => 'uploadNoFileErrorMessage'
                    ])
                ],
            ])
            ->add('submit', SubmitType::class, [
                'label' => 'Envoyer le fichier'
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            // Configure your form options here
        ]);
    }
}
