<?php

namespace App\Command;

use App\Entity\IA;
use App\Entity\Chatbox;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\Console\Attribute\AsCommand;

#[AsCommand(
    name: 'app:create-ia',
    description: 'Crée une nouvelle IA et l\'associe à une ChatBox'
)]
class CreateIACommand extends Command
{
    public function __construct(
        private EntityManagerInterface $entityManager
    ) {
        parent::__construct();
    }

    protected function configure(): void
    {
        $this
            ->addArgument('chatbox-id', InputArgument::REQUIRED, 'ID de la ChatBox')
            ->addArgument('api-key', InputArgument::REQUIRED, 'Clé API OpenAI')
            ->addArgument('name', InputArgument::REQUIRED, 'Nom de l\'IA');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);
        
        // Récupérer la ChatBox
        $chatboxId = $input->getArgument('chatbox-id');
        $chatbox = $this->entityManager->getRepository(Chatbox::class)->find($chatboxId);
        
        if (!$chatbox) {
            $io->error('ChatBox non trouvée');
            return Command::FAILURE;
        }

        // Créer l'IA
        $ia = new IA();
        $ia->setNom($input->getArgument('name'))
           ->setApiKey($input->getArgument('api-key'))
           ->setModel('gpt-3.5-turbo')
           ->setTemperature(0.7)
           ->setDefaultContext('Tu es un assistant helpdesk professionnel qui aide les utilisateurs avec leurs problèmes techniques.');

        $this->entityManager->persist($ia);
        
        // Associer l'IA à la ChatBox
        $chatbox->setIa($ia);
        
        $this->entityManager->flush();

        $io->success('IA créée et associée à la ChatBox avec succès');

        return Command::SUCCESS;
    }
}
