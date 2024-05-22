<?php

namespace App\Form;

use App\Entity\Item;
use App\Entity\Product;
use App\Entity\Sale;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Doctrine\ORM\EntityRepository;
use Symfony\Component\Form\Extension\Core\Type\NumberType;

class ItemFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder          
            ->add('producto', EntityType::class, [
                'class' => Product::class,
                'choice_label' => 'name',
                'query_builder' => function (EntityRepository $er) {
                    return $er->createQueryBuilder('p')
                        ->andWhere('p.isDelete = :isDelete')
                        ->setParameter('isDelete', false);
                },
                'attr' => ['class' => 'input'],
                'label_attr' => ['class' => 'form-control label mb-1']
            ])
            ->add('quantity', NumberType::class, [
                'attr' => ['class' => 'input'],
                'label_attr' => ['class' => 'form-control label mb-1']
            ])        
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Item::class,
            'csrf_protection' => true,
            'csrf_field_name' => 'token'
        ]);
    }
}
