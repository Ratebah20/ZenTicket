<?php

namespace App\Command;

use App\Entity\IA;
use App\Service\ChatAIService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\HttpClient\HttpClient;

#[AsCommand(
    name: 'app:test-ia-api',
    description: 'Teste l\'API IA pour vérifier si les crédits sont disponibles',
)]
class TestIAApiCommand extends Command
{
    private EntityManagerInterface $entityManager;
    private ChatAIService $aiService;

    public function __construct(
        EntityManagerInterface $entityManager,
        ChatAIService $aiService
    ) {
        parent::__construct();
        $this->entityManager = $entityManager;
        $this->aiService = $aiService;
    }

    protected function configure(): void
    {
        $this
            ->addArgument('ia_id', InputArgument::OPTIONAL, 'ID de l\'IA à utiliser', null)
            ->addArgument('message', InputArgument::OPTIONAL, 'Message à envoyer', 'Ceci est un test pour vérifier si l\'API IA répond correctement');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);
        $iaId = $input->getArgument('ia_id');
        $messageContent = $input->getArgument('message');

        $io->title('Test de l\'API IA pour vérifier les crédits');
        
        // Récupérer l'IA
        $ia = null;
        if ($iaId) {
            $ia = $this->entityManager->getRepository(IA::class)->find($iaId);
            if (!$ia) {
                $io->error("IA non trouvée avec l'ID: $iaId");
                return Command::FAILURE;
            }
        } else {
            // Récupérer la première IA disponible
            $ia = $this->entityManager->getRepository(IA::class)->findOneBy([]);
            if (!$ia) {
                $io->error("Aucune IA trouvée dans la base de données");
                return Command::FAILURE;
            }
        }

        $io->info("IA utilisée: " . $ia->getNom() . " (ID: " . $ia->getId() . ")");
        $io->info("Modèle: " . $ia->getModel());
        $io->info("Message à envoyer: " . $messageContent);

        // Tester l'API OpenAI directement
        try {
            $io->section("Test de l'API OpenAI");
            
            // Récupérer la clé API et le modèle depuis l'IA
            $apiKey = $ia->getApiKey();
            $model = $ia->getModel();
            
            if (empty($apiKey)) {
                $io->error("La clé API n'est pas définie pour cette IA");
                return Command::FAILURE;
            }
            
            // Afficher les premiers caractères de la clé API pour vérification
            $maskedKey = substr($apiKey, 0, 5) . '...' . substr($apiKey, -5);
            $io->info("Clé API (masquée): " . $maskedKey);
            $io->info("Longueur de la clé API: " . strlen($apiKey) . " caractères");
            
            $io->info("Envoi de la requête à l'API OpenAI...");
            
            // Créer un client HTTP
            $client = HttpClient::create();
            
            // Préparer les données pour l'API
            $data = [
                'model' => $model,
                'messages' => [
                    [
                        'role' => 'system',
                        'content' => $ia->getDefaultContext() ?? 'Tu es un assistant IA.'
                    ],
                    [
                        'role' => 'user',
                        'content' => $messageContent
                    ]
                ],
                'temperature' => $ia->getTemperature() ?? 0.7
            ];
            
            // Envoyer la requête à l'API OpenAI
            $response = $client->request('POST', 'https://api.openai.com/v1/chat/completions', [
                'headers' => [
                    'Authorization' => 'Bearer ' . $apiKey,
                    'Content-Type' => 'application/json'
                ],
                'json' => $data,
                'timeout' => 60
            ]);
            
            // Récupérer la réponse
            $statusCode = $response->getStatusCode();
            $content = $response->getContent();
            $result = json_decode($content, true);
            
            $io->info("Code de statut HTTP: " . $statusCode);
            
            if ($statusCode === 200) {
                $io->success("L'API a répondu avec succès !");
                
                if (isset($result['choices'][0]['message']['content'])) {
                    $aiResponse = $result['choices'][0]['message']['content'];
                    $io->section("Réponse de l'IA:");
                    $io->text($aiResponse);
                    
                    // Afficher les informations d'utilisation
                    if (isset($result['usage'])) {
                        $io->section("Informations d'utilisation:");
                        $io->table(
                            ['Métrique', 'Valeur'],
                            [
                                ['Tokens prompt', $result['usage']['prompt_tokens'] ?? 'N/A'],
                                ['Tokens complétion', $result['usage']['completion_tokens'] ?? 'N/A'],
                                ['Total tokens', $result['usage']['total_tokens'] ?? 'N/A']
                            ]
                        );
                    }
                    
                    return Command::SUCCESS;
                } else {
                    $io->error("Format de réponse inattendu");
                    $io->text(json_encode($result, JSON_PRETTY_PRINT));
                    return Command::FAILURE;
                }
            } else {
                $io->error("Erreur lors de l'appel à l'API OpenAI");
                $io->text("Détails de l'erreur: " . $content);
                
                // Vérifier si c'est une erreur de crédit
                if (isset($result['error']['type']) && $result['error']['type'] === 'insufficient_quota') {
                    $io->error("ERREUR DE CRÉDIT: Vous n'avez plus de crédits disponibles sur votre compte OpenAI.");
                    $io->text("Message d'erreur: " . ($result['error']['message'] ?? 'Non spécifié'));
                }
                
                return Command::FAILURE;
            }
        } catch (\Exception $e) {
            $io->error("Exception lors de l'appel à l'API: " . $e->getMessage());
            return Command::FAILURE;
        }
    }
}
