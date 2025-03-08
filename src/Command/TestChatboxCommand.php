<?php

namespace App\Command;

use App\Entity\Chatbox;
use App\Entity\Message;
use App\Enum\MessageType;
use App\Service\ChatWebSocketService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

#[AsCommand(
    name: 'app:test-chatbox',
    description: 'Teste l\'envoi de messages sur une chatbox spécifique',
)]
class TestChatboxCommand extends Command
{
    private EntityManagerInterface $entityManager;
    private ChatWebSocketService $webSocketService;

    public function __construct(
        EntityManagerInterface $entityManager,
        ChatWebSocketService $webSocketService
    ) {
        parent::__construct();
        $this->entityManager = $entityManager;
        $this->webSocketService = $webSocketService;
    }

    protected function configure(): void
    {
        $this
            ->addArgument('chatbox_id', InputArgument::REQUIRED, 'ID de la chatbox')
            ->addArgument('message', InputArgument::OPTIONAL, 'Message à envoyer', 'Test de la chatbox depuis la commande');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);
        $chatboxId = $input->getArgument('chatbox_id');
        $messageContent = $input->getArgument('message');

        $io->title('Test de la chatbox ' . $chatboxId);
        
        // Récupérer la chatbox
        $chatbox = $this->entityManager->getRepository(Chatbox::class)->find($chatboxId);
        if (!$chatbox) {
            $io->error("Chatbox non trouvée avec l'ID: $chatboxId");
            return Command::FAILURE;
        }

        $io->info("Chatbox trouvée: ID=" . $chatbox->getId());
        $io->info("Utilisateur: " . ($chatbox->getUser() ? $chatbox->getUser()->getId() : 'Aucun'));
        $io->info("IA: " . ($chatbox->getIa() ? $chatbox->getIa()->getNom() . " (ID: " . $chatbox->getIa()->getId() . ")" : 'Aucune'));
        $io->info("Temporaire: " . ($chatbox->isTemporary() ? 'Oui' : 'Non'));

        // Créer un message utilisateur
        $userMessage = new Message();
        $userMessage->setChatbox($chatbox)
                ->setMessage($messageContent)
                ->setMessageType(MessageType::USER)
                ->setTimestamp(new \DateTime())
                ->setSenderId($chatbox->getUser()->getId());

        // Persister le message
        $this->entityManager->persist($userMessage);
        $this->entityManager->flush();

        $io->success("Message utilisateur créé avec l'ID: " . $userMessage->getId());

        // Créer un message IA
        $aiMessage = new Message();
        $aiMessage->setChatbox($chatbox)
                ->setMessage("Réponse de test pour la chatbox $chatboxId: " . $userMessage->getMessage())
                ->setMessageType(MessageType::AI)
                ->setTimestamp(new \DateTime())
                ->setSenderId($chatbox->getIa()->getId())
                ->setUserMessageId($userMessage->getId());

        // Persister le message IA
        $this->entityManager->persist($aiMessage);
        $this->entityManager->flush();

        $io->success("Message IA créé avec l'ID: " . $aiMessage->getId());

        // Publier le message utilisateur via Mercure
        $userMessageData = [
            'id' => $userMessage->getId(),
            'content' => $userMessage->getMessage(),
            'messageType' => $userMessage->getMessageType()->value,
            'senderId' => $userMessage->getSenderId(),
            'timestamp' => $userMessage->getTimestamp()->format('c'),
            'reactions' => [],
            'isRead' => false
        ];

        $userMercureData = [
            'type' => 'message',
            'conversationId' => $chatbox->getId(),
            'message' => $userMessageData
        ];

        $userTopic = "/chat/{$chatbox->getId()}";
        $userResult = $this->webSocketService->publishMessage($userMercureData, $userTopic);

        if ($userResult) {
            $io->success("Message utilisateur publié avec succès sur le topic: $userTopic");
        } else {
            $io->error("Erreur lors de la publication du message utilisateur");
        }

        // Publier le message IA via Mercure
        $aiMessageData = [
            'id' => $aiMessage->getId(),
            'content' => $aiMessage->getMessage(),
            'messageType' => $aiMessage->getMessageType()->value,
            'senderId' => $aiMessage->getSenderId(),
            'timestamp' => $aiMessage->getTimestamp()->format('c'),
            'userMessageId' => $aiMessage->getUserMessageId(),
            'reactions' => [],
            'isRead' => false
        ];

        $aiMercureData = [
            'type' => 'message',
            'conversationId' => $chatbox->getId(),
            'message' => $aiMessageData
        ];

        $aiTopic = "/chat/{$chatbox->getId()}";
        $aiResult = $this->webSocketService->publishMessage($aiMercureData, $aiTopic);

        if ($aiResult) {
            $io->success("Message IA publié avec succès sur le topic: $aiTopic");
        } else {
            $io->error("Erreur lors de la publication du message IA");
        }

        return Command::SUCCESS;
    }
}
