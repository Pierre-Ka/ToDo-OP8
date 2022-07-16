<?php

namespace App\Form;

use App\Entity\User;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
use Symfony\Component\Form\CallbackTransformer;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;

class UserType extends AbstractType
{

    public function buildForm(FormBuilderInterface $builder, array $options): void  {
        $builder
            ->add('username', TextType::class, [
                'label' => "Nom d'utilisateur"
            ])
            ->add('password', RepeatedType::class, [
                'type' => PasswordType::class,
                'invalid_message' => 'Les deux mots de passe doivent correspondre.',
                'options' => ['attr' => ['class' => 'password-field']],
                'required' => true,
                'first_options' => [
                    'label' => 'Mot de passe',
                    'constraints' => [
                        new NotBlank([
                            'message' => 'Veuillez saisir un mot de passes',
                        ]),
                        new Length([
                            'min' => 6,
                            'minMessage' => 'Votre mot de passe doit avoir au moins {{ limit }} caractères',
                            'max' => 64,
                        ]),
                    ],
                ],
                'second_options' => ['label' => 'Tapez le mot de passe à nouveau']
            ])
            ->add('email', EmailType::class, ['label' => 'Adresse email'])
            ->add('roles', ChoiceType::class, [
                'label' => 'Statut',
                'choices' => [
                    'Utilisateur' => 'ROLE_USER',
                    'Administrateur' => 'ROLE_ADMIN'
                ],
                'multiple' => false,
                'expanded' => false,
                'empty_data' => ['ROLE_USER'],
                'required' => true
            ]);
        $builder->get('roles')
            ->addModelTransformer(new CallbackTransformer(
                function ($rolesArray) {
                    return count($rolesArray) ? $rolesArray[0] : null;
                },
                function ($rolesString) {
                    return [$rolesString];
                }
            ));
    }

    public function configureOptions(OptionsResolver $resolver): void  {
        $resolver->setDefaults([
            'data_class' => User::class,
        ]);
    }
}
