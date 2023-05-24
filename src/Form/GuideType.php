<?php

namespace App\Form;

use App\Entity\Guide;
use App\Entity\User;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class GuideType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
        ->add('user',  RegistrationFormType::class
        )
            ->add('description')
            ->add('pictureguides', FileType::class,[
                'label' => 'Importer des images',
                'multiple' => true,
                'mapped' => false,
                'required' => false
            ])
            
        ;
        
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Guide::class, 
        ]);
    }
}
