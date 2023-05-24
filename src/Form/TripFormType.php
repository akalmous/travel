<?php

namespace App\Form;

use App\Entity\Trip;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\Image;

class TripFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('name', null,
            [
                'label'=>"Nom du voyage", 
            ],
            )
            ->add('departureDate', DateType :: class, [
                'widget' => 'single_text',
                'label'=> "Date de départ"
                
                ]
            )
            ->add('returnDate', DateType :: class, [
                'widget' => 'single_text',
                'label'=> "Date de retour"
                
                ]
            )
            ->add('departurePlace', null,
            [
                'label'=>"Place de départ", 
            ],
            )

            ->add('numberPlaces', null,
            [
                'label'=>"Nombre de places", 
            ],
            )

            //->add('reservedPlaces')
            ->add('price', null,
            [
                'label'=>"Le prix", 
            ],
            )
            
            
            //->add('duration')
            

            ->add('pictures', FileType::class,[
                'label' => 'Importer des images',
                'multiple' => true,
                'mapped' => false,
                'required' => false,
                 
            ])
        ;
    }
    

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Trip::class,
        ]);
    }
}

