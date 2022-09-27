<?php

namespace App\Form;

use App\Entity\Tag;
use App\Entity\Bulletin;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;

class BulletinType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('title', TextType::class,[
                'label' => 'Titre du Bulletin'
            ])
            ->add('category', ChoiceType::class, ['label'=> 'Catégories', 'choices'=>['Général' => 'General',
            'Divers' => 'Divers',
            'Urgent'=> 'Urgent', 
        ], 
        'expanded' => false, // Menu déroulant ou Case a cocher 
        'multiple'=>false, // Multiple choix, ici False sous peine d'erreur
            ]) // Val Affichée => Val retenue
            ->add('content',TextareaType::class,['label'=> 'Contenu', 
            ])
            ->add('submit',SubmitType::class, ['label'=> 'Créer', 'attr' =>[ 'style' => 'margin-top:5px', 'class' => 'btn btn-info', ]])
            ->add( 'tags' , EntityType::class, [
                'label' => 'Tags', 
                'class' => Tag::class, 
                'choice_label' => 'name', 
                'expanded' => true,
                'multiple' => true, 
                ])
                ->add('submit', SubmitType::class, ['label'=> 'Créer',
                'attr' => [
                    'style' => 'margin-top:5px',
                    'class' => 'btn btn-success', 
                    ]
                ]) ; }
                 public function configureOptions(OptionsResolver $resolver): void
                    {
                        $resolver->setDefaults([
                            'data_class' => Bulletin::class,
                        ])
                        ;
                    }
}
