<?php

namespace App\Form;

use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
// Imports
use App\Entity\Category;
use App\Entity\Product;
use Doctrine\ORM\EntityRepository;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;


class ProductFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('name', TextType::class, [
                'attr' => ['class' => 'input'],
                'label_attr' => ['class' => 'form-control label mb-1']
            ])
            ->add('price', NumberType::class, [
                'attr' => ['class' => 'input'],
                'label_attr' => ['class' => 'form-control label mb-1']
            ])
            ->add('description', TextareaType::class, [
                'attr' => ['class' => 'input'],
                'label_attr' => ['class' => 'form-control label mb-1']
            ])
            ->add('stock', NumberType::class, [
                'attr' => ['class' => 'input'],
                'label_attr' => ['class' => 'form-control label mb-1']
            ])            
            ->add('category', EntityType::class, [
                'class' => Category::class,
                'choice_label' => 'name',
                'query_builder' => function (EntityRepository $er) {
                    return $er->createQueryBuilder('c')
                        ->andWhere('c.isDelete = :isDelete')
                        ->setParameter('isDelete', false);
                },
                'attr' => ['class' => 'input'],
                'label_attr' => ['class' => 'form-control label mb-1']
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Product::class,
            'csrf_protection' => true,
            'csrf_field_name' => 'token'
        ]);
    }
}
