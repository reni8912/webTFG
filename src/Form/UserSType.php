<?php

namespace App\Form;

use App\Entity\UserS;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;

class UserSType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('email', EmailType::class)
            ->add('roles', ChoiceType::class, [
                'multiple' => true,
                'choices' => [
                    'User' => 'ROLE_USER',
                    'Admin' => 'ROLE_ADMIN',
                ],
                'data' => ['ROLE_USER'],
            ])
            ->add('password', PasswordType::class)
            ->add('ownerName')
            ->add('premisesName')
            ->add('ownerLastname')
            ->add('field', null, [
                'empty_data' => null,
            ])
            ->add('image', FileType::class, [
                
            ])
            ->add('address')
            ->add('city')
            ->add('type')
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => UserS::class,
        ]);
    }
}
