<?php

namespace App\Command;

use App\Entity\Chatbox;
use App\Entity\Message;
use App\Enum\MessageType;
use App\Service\ChatAIService;
use App\Service\ChatWebSocketService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

#[AsCommand(
    name: 'app:test-chat',
    description: 'Teste la fonctionnalité de chat IA',
)]
class TestChatCommand extends Command
{
    private EntityManagerInterface $entityManager;
    private ChatWebSocketService $webSocketService;
    private ChatAIService $aiService;

    public function __construct(
        EntityManagerInterface $entityManager,
        ChatWebSocketService $webSocketService,
        ChatAIService $aiService
    ) {
        parent::__construct();
        $this->entityManager = $entityManager;
        $this->webSocketService = $webSocketService;
        $this->aiService = $aiService;
    }

    protected function configure(): void
    {
        $this
            ->addArgument('chatbox_id', InputArgument::REQUIRED, 'ID de la chatbox')
            ->addArgument('message', InputArgument::OPTIONAL, 'Message à envoyer', 'Ceci est un message de test');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);
        $chatboxId = $input->getArgument('chatbox_id');
        $messageContent = $input->getArgument('message');

        $io->title('Test de la fonctionnalité de chat IA');
        $io->section("Chatbox ID: $chatboxId");
        $io->section("Message: $messageContent");

        // Récupérer la chatbox
        $chatbox = $this->entityManager->getRepository(Chatbox::class)->find($chatboxId);

        if (!$chatbox) {
            $io->error("Chatbox non trouvée avec l'ID: $chatboxId");
            return Command::FAILURE;
        }

        $io->info("Chatbox trouvée: " . $chatbox->getId());
        $io->info("IA associée: " . ($chatbox->getIa() ? $chatbox->getIa()->getNom() : 'Aucune'));

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

        // Créer un message IA de test
        $aiMessage = new Message();
        $aiMessage->setChatbox($chatbox)
                ->setMessage("Voici une réponse de test pour le message: " . $userMessage->getMessage())
                ->setMessageType(MessageType::AI)
                ->setTimestamp(new \DateTime())
                ->setSenderId($chatbox->getIa()->getId())
                ->setUserMessageId($userMessage->getId());

        // Persister le message IA
        $this->entityManager->persist($aiMessage);
        $this->entityManager->flush();

        $io->success("Message IA créé avec l'ID: " . $aiMessage->getId());

        // Préparer les données pour Mercure
        $messageData = [
            'id' => $aiMessage->getId(),
            'content' => $aiMessage->getMessage(),
            'messageType' => $aiMessage->getMessageType()->value,
            'senderId' => $aiMessage->getSenderId(),
            'timestamp' => $aiMessage->getTimestamp()->format('c'),
            'userMessageId' => $aiMessage->getUserMessageId(),
            'reactions' => [],
            'isRead' => false
        ];

        // Structure complète du message pour Mercure
        $mercureData = [
            'type' => 'message',
            'conversationId' => $chatbox->getId(),
            'message' => $messageData
        ];

        // Publication du message sur le topic /chat/{id}
        $topic = "/chat/{$chatbox->getId()}";
        $result = $this->webSocketService->publishMessage($mercureData, $topic);

        if ($result) {
            $io->success("Message IA publié avec succès sur le topic: $topic");
        } else {
            $io->error("Erreur lors de la publication du message IA");
        }

        return Command::SUCCESS;
    }
}
