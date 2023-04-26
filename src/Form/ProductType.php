<?php

namespace App\Form;

use App\Entity\Category;
use App\Entity\Product;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\Choice;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints\NotNull;
use Symfony\Component\Validator\Constraints\PositiveOrZero;
use Symfony\Component\Validator\Context\ExecutionContextInterface;

class ProductType extends AbstractType
{

    private $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
        ->add('name', null, [
            'constraints' => [
                new NotBlank([
                    'message' => 'Lütfen bir isim giriniz.'
                ])
            ]
        ])
        ->add('description', null, [
            'constraints' => [
                new NotBlank([
                    'message' => 'Lütfen bir açıklama giriniz.'
                ])
            ]
        ])
        ->add('price', null, [
            'constraints' => [
                new NotNull([
                    'message' => 'Lütfen bir fiyat giriniz.'
                ]),
                new PositiveOrZero([
                    'message' => 'Lütfen geçerli bir fiyat giriniz.'
                ])
            ]
        ])
        ->add('image_url', null, [
            'constraints' => [
                new NotBlank([
                    'message' => 'Lütfen bir resim URL giriniz.'
                ])
            ]
        ])
        ->add('color', null, [
            'constraints' => [
                new NotBlank([
                    'message' => 'Lütfen bir renk giriniz.'
                ])
            ]
        ])
        ->add('size', null, [
            'constraints' => [
                new NotBlank([
                    'message' => 'Lütfen bir beden giriniz.'
                ])
            ]
        ])
        ->add('weight', null, [
            'constraints' => [
                new NotNull([
                    'message' => 'Lütfen bir ağırlık giriniz.'
                ]),
                new PositiveOrZero([
                    'message' => 'Lütfen geçerli bir ağırlık giriniz.'
                ])
            ]
        ])
    ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        //  Kategori ile alakalı bir bug olduğu için , class dönüşümü devre dışı bırakıldı. Daha iyi bir hata mesajı verebilmek adına
        
        $resolver->setDefaults([
            'data_class' => Product::class,
            'allow_extra_fields' => true,
        ]);
    }
}
