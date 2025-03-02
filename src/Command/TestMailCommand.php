<?php

namespace App\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\Mailer\Exception\TransportExceptionInterface;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Email;
use Symfony\Component\Mime\Address;

class TestMailCommand extends Command
{
    protected static $defaultName = 'app:test-mail';
    protected static $defaultDescription = 'Envoie un email de test pour vérifier la configuration';

    private MailerInterface $mailer;

    public function __construct(MailerInterface $mailer)
    {
        parent::__construct();
        $this->mailer = $mailer;
    }

    protected function configure(): void
    {
        $this
             ->setName(self::$defaultName)
             ->setDescription(self::$defaultDescription)
             ->setHelp('Cette commande envoie un email de test pour vérifier que la configuration du mailer fonctionne correctement.');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);
        
        try {
            $io->note('Préparation de l\'envoi de l\'email de test...');
            
            $email = (new Email())
                ->from(new Address('ame.sanglant2004@gmail.com', 'ZenTicket Support'))
                ->to(new Address('tutii9er@gmail.com', 'Utilisateur Test'))
                ->subject('Test de configuration - ZenTicket')
                ->text('Ceci est un email de test envoyé depuis l\'application ZenTicket.
                
Si vous recevez cet email, cela signifie que la configuration de messagerie fonctionne correctement.

Cordialement,
L\'équipe ZenTicket')
                ->html('<div>
                    <h2>Test de configuration ZenTicket</h2>
                    <p>Ceci est un email de test envoyé depuis l\'application ZenTicket.</p>
                    <p>Si vous recevez cet email, cela signifie que la configuration de messagerie fonctionne correctement.</p>
                    <p>Cordialement,<br>L\'équipe ZenTicket</p>
                </div>');

            $io->info('Envoi de l\'email en cours...');
            $this->mailer->send($email);
            
            $io->success('L\'email de test a été envoyé avec succès !');
            return Command::SUCCESS;
            
        } catch (TransportExceptionInterface $e) {
            $io->error([
                'Une erreur est survenue lors de l\'envoi de l\'email',
                $e->getMessage()
            ]);
            
            if ($output->isVerbose()) {
                $io->error($e->getTraceAsString());
            }
            
            return Command::FAILURE;
        }
    }
}
