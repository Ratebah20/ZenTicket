<?php

namespace App\Form;

use App\Entity\Rapport;
use App\Entity\Ticket;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class RapportInterventionType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('titre', TextType::class, [
                'label' => 'Titre du rapport',
                'attr' => ['placeholder' => 'Ex: Intervention sur serveur mail']
            ])
            ->add('service', TextType::class, [
                'label' => 'Service concerné'
            ])
            ->add('ticketPrincipal', EntityType::class, [
                'class' => Ticket::class,
                'choice_label' => 'titre',
                'label' => 'Ticket concerné',
                'required' => true
            ])
            ->add('tempsPasse', IntegerType::class, [
                'label' => 'Temps passé (en minutes)',
                'required' => false
            ])
            ->add('contenu', TextareaType::class, [
                'label' => 'Description de l\'intervention',
                'required' => true,
                'attr' => [
                    'rows' => 5,
                    'placeholder' => 'Décrivez en détail l\'intervention effectuée...'
                ]
            ])
            ->add('recommandations', TextareaType::class, [
                'label' => 'Recommandations',
                'required' => false,
                'attr' => [
                    'rows' => 3,
                    'placeholder' => 'Ajoutez vos recommandations pour éviter ce problème à l\'avenir...'
                ]
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
