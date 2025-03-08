<?php

namespace App\Command;

use App\Entity\IA;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

#[AsCommand(
    name: 'app:update-api-key',
    description: 'Met à jour la clé API pour toutes les IAs',
)]
class UpdateApiKeyCommand extends Command
{
    private EntityManagerInterface $entityManager;
    private string $apiKey = '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';

    public function __construct(
        EntityManagerInterface $entityManager
    ) {
        parent::__construct();
        $this->entityManager = $entityManager;
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);
        $io->title('Mise à jour de la clé API pour toutes les IAs');
        
        // Récupérer toutes les IAs
        $ias = $this->entityManager->getRepository(IA::class)->findAll();
        
        if (empty($ias)) {
            $io->error("Aucune IA trouvée dans la base de données");
            return Command::FAILURE;
        }
        
        $count = 0;
        foreach ($ias as $ia) {
            $io->info("Mise à jour de l'IA: " . $ia->getNom() . " (ID: " . $ia->getId() . ")");
            
            // Mettre à jour la clé API
            $ia->setApiKey($this->apiKey);
            $this->entityManager->persist($ia);
            $count++;
        }
        
        // Sauvegarder les changements
        $this->entityManager->flush();
        
        $io->success("$count IA(s) ont été mises à jour avec la nouvelle clé API !");
        
        return Command::SUCCESS;
    }
}
