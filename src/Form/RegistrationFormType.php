<?php

namespace App\Form;

use App\Entity\User;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\TelType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\EqualTo;
use Symfony\Component\Validator\Constraints\IsTrue;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints\Regex;

class RegistrationFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder

            // le form inscription avec les différents champs 
        
            ->add('firstName',null,[
                'label'=>'Prénom'
            ])
            ->add('lastName',null,[
                'label'=>'Nom de famille'
            ])
            ->add('tel', TelType:: class,[
                'label'=>'Numéro de téléphone',
                'help'=>'les formats acceptés : 0X XX XX XX XX ou +33 X XX XX XX XX',
                'constraints'=>[
                    
                    new Regex([
                        // les conditions d'un numéro vsoit valide +33 ou 06 
                        'pattern'=>"/^(0|\+33 )[1-9]([-. ]?[0-9]{2} ){3}([-. ]?[0-9]{2})$/", 
                        'message'=>"le numéro de teléphone doit etre sous Ce format : 0X XX XX XX XX ou +33 X XX XX XX XX"
                    ])

                ]
                
                
            ])
            ->add('email' , EmailType::class, [
                'label'=>'Adresse mail'
            ])
            ->add('agreeTerms', CheckboxType::class, [
                'label'=>'Les conditions',
                'mapped' => false,
                'constraints' => [
                    new IsTrue([
                        'message' => 'Vous devez accepté les conditions',
                    ]),
                ],
            ])
            ->add('plainPassword', PasswordType::class, [
                // les conditions pour qu'un mdp soit valide 
                'mapped' => false,
                'help'=>'Au moins 1 caractère majuscule, Au moins 1 caractère minuscule, Au moins 1 chiffre, Au moins 1 caractère spécial',
                'label' => 'Mot de passe',
                'attr' => ['autocomplete' => 'new-password'],
                'constraints' => [
                    new NotBlank([
                        'message' => 'Entrez le mot de passe',
                    ]),
                    new Length([
                        'min' => 8,
                        'minMessage' => 'Le mot de passe doit avoir au moins {{ limit }} caratères',
                        // la longueur max d'un mdp
                        
                    ]),

                    new Regex([
                        'pattern' => '/^(?=.*?[A-Z])(?=.*?[a-z])(?=.*?[0-9])(?=.*?[special characters]).{8,16}$/',
                        
                        'message' => "Au moins 1 caractère majuscule, Au moins 1 caractère minuscule, Au moins 1 chiffre, Au moins 1 caractère spécial"
                    ])
                    
                ],
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => User::class,
        ]);
    }
}
