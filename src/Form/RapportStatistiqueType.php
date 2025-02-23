<?php

namespace App\Form;

use App\Entity\Rapport;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class RapportStatistiqueType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('titre', TextType::class, [
                'label' => 'Titre du rapport'
            ])
            ->add('periode', ChoiceType::class, [
                'label' => 'Période',
                'choices' => [
                    'Journalier' => Rapport::PERIODE_JOURNALIER,
                    'Hebdomadaire' => Rapport::PERIODE_HEBDOMADAIRE,
                    'Mensuel' => Rapport::PERIODE_MENSUEL
                ]
            ])
            ->add('service', TextType::class, [
                'label' => 'Service'
            ])
            ->add('contenu', TextareaType::class, [
                'label' => 'Commentaires',
                'required' => true,
                'attr' => [
                    'rows' => 4,
                    'placeholder' => 'Ajoutez vos commentaires ou observations sur ce rapport statistique...'
                ]
            ])
            ->add('dateDebut', DateType::class, [
                'label' => 'Date de début',
                'widget' => 'single_text',
                'mapped' => false
            ])
            ->add('dateFin', DateType::class, [
                'label' => 'Date de fin',
                'widget' => 'single_text',
                'mapped' => false
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Rapport::class,
        ]);
    }
}
