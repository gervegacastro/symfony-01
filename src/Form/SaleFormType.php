<?php

namespace App\Form;

use App\Entity\Sale;
use App\Entity\User;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Doctrine\ORM\EntityRepository;


class SaleFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('client', EntityType::class, [
                'class' => User::class,
                'label' => 'Cliente',
                'choice_label' => 'fullName',
                'query_builder' => function (EntityRepository $er) {
                    return $er->createQueryBuilder('s')
                        ->andWhere('s.isDelete = :isDelete')
                        ->setParameter('isDelete', false);
                },
                'attr' => ['class' => 'input'],
                'label_attr' => ['class' => 'form-control label mb-1']
            ])                        
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Sale::class,
            'csrf_protection' => true,
            'csrf_field_name' => 'token'
        ]);
    }
}
