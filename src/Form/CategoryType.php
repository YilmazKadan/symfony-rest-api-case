<?php

namespace App\Form;

use App\Entity\Category;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;

class CategoryType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
        ->add('name', null, [
            'constraints' => [
                new NotBlank([
                    'message' => 'Please enter a name',
                ]),
                new Length([
                    'min' => 2,
                    'max' => 50,
                    'minMessage' => 'Name should have at least {{ limit }} characters',
                    'maxMessage' => 'Name should have at most {{ limit }} characters',
                ]),
            ],
        ])
        ->add('description', null, [
            'constraints' => [
                new NotBlank([
                    'message' => 'Please enter a description',
                ]),
                new Length([
                    'min' => 10,
                    'max' => 200,
                    'minMessage' => 'Description should have at least {{ limit }} characters',
                    'maxMessage' => 'Description should have at most {{ limit }} characters',
                ]),
            ],
        ])->setEmptyData(null);
    ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Category::class,
        ]);
    }
}
