<?php

namespace App\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

// Imports
use App\Entity\rol;
use App\Entity\User;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;

class UserFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('name', TextType::class, [
                'attr' => ['class' => 'input'],
                'label_attr' => ['class' => 'form-control label mb-1']
            ])
            ->add('lastname', TextType::class, [
                'attr' => ['class' => 'input'],
                'label_attr' => ['class' => 'form-control label mb-1']
            ])
            ->add('email', TextType::class, [
                'attr' => ['class' => 'input'],
                'label_attr' => ['class' => 'form-control label mb-1']
            ])
            ->add('password', PasswordType::class, [
                'attr' => ['class' => 'input'],
                'label_attr' => ['class' => 'form-control label mb-1']
            ])            
            ->add('rol', EntityType::class, [
                'class' => rol::class,
                'choice_label' => 'description', // Esto podría ser por también otro atributo de la entidad Rol
                'placeholder' => 'Seleccione el rol',
                'attr' => ['class' => 'input'],
                'label_attr' => ['class' => 'form-control label mb-1']
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => User::class,
            'csrf_protection' => true,
            'csrf_field_name' => 'token'
        ]);
    }
}
