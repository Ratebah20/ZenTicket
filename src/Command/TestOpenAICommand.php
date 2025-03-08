<?php

namespace App\Command;

use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\HttpClient\HttpClient;

#[AsCommand(
    name: 'app:test-openai',
    description: 'Teste l\'API OpenAI avec une clé spécifique',
)]
class TestOpenAICommand extends Command
{
    protected function configure(): void
    {
        $this
            ->addArgument('message', InputArgument::OPTIONAL, 'Message à envoyer à l\'API', 'Bonjour, ceci est un test de l\'API OpenAI. Réponds simplement "L\'API fonctionne correctement !"')
            ->addArgument('api_key', InputArgument::OPTIONAL, 'Clé API OpenAI à utiliser');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);
        $io->title('Test direct de l\'API OpenAI');
        
        $apiKey = $input->getArgument('api_key') ?: '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';
        $model = 'gpt-3.5-turbo';
        $message = $input->getArgument('message');
        
        $io->info("Clé API utilisée: " . substr($apiKey, 0, 10) . '...' . substr($apiKey, -5));
        $io->info("Modèle: $model");
        $io->info("Message: $message");
        
        try {
            $io->section("Envoi de la requête à l'API OpenAI...");
            
            // Créer un client HTTP
            $client = HttpClient::create();
            
            // Préparer les données pour l'API
            $data = [
                'model' => $model,
                'messages' => [
                    [
                        'role' => 'system',
                        'content' => 'Tu es un assistant helpdesk qui aide les utilisateurs avec leurs problèmes techniques.'
                    ],
                    [
                        'role' => 'user',
                        'content' => $message
                    ]
                ],
                'temperature' => 0.7
            ];
            
            // Envoyer la requête à l'API OpenAI
            $response = $client->request('POST', 'https://api.openai.com/v1/chat/completions', [
                'headers' => [
                    'Authorization' => 'Bearer ' . $apiKey,
                    'Content-Type' => 'application/json'
                ],
                'json' => $data,
                'timeout' => 30
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
                    
                    return Command::SUCCESS;
                } else {
                    $io->error("Format de réponse inattendu");
                    $io->text(json_encode($result, JSON_PRETTY_PRINT));
                    return Command::FAILURE;
                }
            } else {
                $io->error("Erreur lors de l'appel à l'API OpenAI");
                $io->text("Détails de l'erreur: " . $content);
                return Command::FAILURE;
            }
        } catch (\Exception $e) {
            $io->error("Exception lors de l'appel à l'API: " . $e->getMessage());
            return Command::FAILURE;
        }
    }
}
