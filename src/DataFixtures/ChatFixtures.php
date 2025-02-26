<?php

namespace App\DataFixtures;

use App\Entity\Administrateur;
use App\Entity\Categorie;
use App\Entity\Chatbox;
use App\Entity\IA;
use App\Entity\Message;
use App\Entity\Technicien;
use App\Entity\Ticket;
use App\Entity\Utilisateur;
use App\Enum\MessageType;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class ChatFixtures extends Fixture implements DependentFixtureInterface
{
    public function __construct(
        private UserPasswordHasherInterface $passwordHasher
    ) {}

    public function getDependencies(): array
    {
        return [
            AppFixtures::class,
        ];
    }

    public function load(ObjectManager $manager): void
    {
        // Créer l'IA par défaut
        $ia = new IA();
        $ia->setNom('Assistant 3INNOV')
           ->setApiKey('%env(OPENAI_API_KEY)%')
           ->setModel('gpt-3.5-turbo')
           ->setTemperature(0.7)
           ->setDefaultContext('Tu es un assistant technique pour 3INNOV.')
           ->setAdditionalParams([
               'max_tokens' => 500,
               'presence_penalty' => 0.6
           ]);
        $manager->persist($ia);
        $manager->flush();

        // Récupérer les utilisateurs existants
        $utilisateurs = $manager->getRepository(Utilisateur::class)->findAll();
        $techniciens = $manager->getRepository(Technicien::class)->findAll();
        $categories = $manager->getRepository(Categorie::class)->findAll();

        // Créer des tickets avec leurs chatbox pour chaque utilisateur
        foreach ($utilisateurs as $index => $utilisateur) {
            for ($i = 0; $i < 2; $i++) {
                $ticket = new Ticket();
                $ticket->setTitre("Problème #" . ($index + 1) . "-" . ($i + 1))
                       ->setDescription("Description du problème #" . ($index + 1) . "-" . ($i + 1))
                       ->setUtilisateur($utilisateur)
                       ->setCategorie($categories[array_rand($categories)])
                       ->setStatut(Ticket::STATUT_NOUVEAU)
                       ->setPriorite(Ticket::PRIORITE_NORMALE);
                $manager->persist($ticket);

                // Créer une chatbox pour le ticket
                $chatbox = new Chatbox();
                $chatbox->setIa($ia);
                $manager->persist($chatbox);
                
                // Lier le ticket et la chatbox
                $ticket->setChatbox($chatbox);
                $chatbox->setTicket($ticket);
                
                $manager->flush();

                // Ajouter quelques messages
                $messages = [
                    [
                        'content' => "Bonjour, j'ai un problème avec...",
                        'type' => MessageType::USER,
                        'sender' => $utilisateur->getId()
                    ],
                    [
                        'content' => "Je peux vous aider avec ça.",
                        'type' => MessageType::USER,
                        'sender' => $techniciens[0]->getId()
                    ],
                    [
                        'content' => "Voici quelques suggestions...",
                        'type' => MessageType::AI,
                        'sender' => $ia->getId()
                    ]
                ];

                foreach ($messages as $messageData) {
                    $message = new Message();
                    $message->setChatbox($chatbox)
                           ->setMessage($messageData['content'])
                           ->setMessageType($messageData['type'])
                           ->setTimestamp(new \DateTime())
                           ->setSenderID($messageData['sender'])
                           ->setIsRead(false);
                    $manager->persist($message);
                }
                $manager->flush();
            }
        }
    }
}
