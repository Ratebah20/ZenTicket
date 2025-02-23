<?php

namespace App\Form;

use App\Entity\Chatbox;
use App\Entity\Message;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class MessageType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('message', TextType::class, [
                'label' => 'Message',
                'required' => true,
                'attr' => [
                    'placeholder' => 'Entrez le message',
                ],
            ])
            ->add('timestamp', DateTimeType::class, [
                'widget' => 'single_text',
                'label' => 'Date et heure',
                'required' => true,
            ])
            ->add('statutMessage', CheckboxType::class, [
                'label' => 'Statut du message',
                'required' => false,
            ])
            ->add('senderID', IntegerType::class, [
                'label' => 'ID de l\'expéditeur',
                'required' => true,
            ])
            ->add('chatbox', EntityType::class, [
                'class' => Chatbox::class,
                'choice_label' => 'id',
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Message::class,
        ]);
    }
}
