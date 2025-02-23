<?php

namespace App\Form;

use App\Entity\Ticket;
use App\Entity\Categorie;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Validator\Constraints\File;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class TicketType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('titre', TextType::class, [
                'label' => 'Titre',
                'attr' => ['class' => 'form-control']
            ])
            ->add('description', TextareaType::class, [
                'label' => 'Description',
                'attr' => ['class' => 'form-control', 'rows' => 3]
            ])
            ->add('categorie', EntityType::class, [
                'class' => Categorie::class,
                'choice_label' => 'nom',
                'label' => 'Catégorie',
                'attr' => ['class' => 'form-control']
            ])
            ->add('priorite', ChoiceType::class, [
                'choices' => [
                    'Basse' => Ticket::PRIORITE_BASSE,
                    'Normale' => Ticket::PRIORITE_NORMALE,
                    'Haute' => Ticket::PRIORITE_HAUTE,
                    'Urgente' => Ticket::PRIORITE_URGENTE
                ],
                'label' => 'Priorité',
                'attr' => ['class' => 'form-control']
            ])
            ->add('commentaireInitial', TextareaType::class, [
                'mapped' => false,
                'required' => false,
                'label' => 'Commentaire initial',
                'attr' => [
                    'class' => 'form-control',
                    'rows' => 3,
                    'placeholder' => 'Ajoutez un commentaire initial (optionnel)'
                ]
            ])
            ->add('pieceJointe', FileType::class, [
                'label' => 'Pièce jointe',
                'mapped' => false,
                'required' => false,
                'constraints' => [
                    new File([
                        'maxSize' => '5M',
                        'mimeTypes' => [
                            'application/pdf',
                            'application/msword',
                            'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
                            'image/jpeg',
                            'image/png',
                        ],
                        'mimeTypesMessage' => 'Veuillez uploader un document valide (PDF, DOC, DOCX, JPG, PNG)',
                    ])
                ],
                'attr' => ['class' => 'form-control']
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Ticket::class,
        ]);
    }
}
