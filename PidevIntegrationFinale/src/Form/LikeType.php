<?php

namespace App\Form;

use App\Entity\Commentaire;
use App\Entity\Like;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;

class LikeType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('nom_like',TextType::class,[
                'attr'=>['placeholder'=>'entrer la description'],
            ])

            ->add('note',NumberType::class,[
                'attr'=>['placeholder'=>'entrer la note'],
            ])
            ->add('commentaire', EntityType::class,[
                    'class'=>Commentaire::class,
                    'choice_label'=>'descriptionCommentaire'
                ]
            )
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Like::class,
        ]);
    }
}
