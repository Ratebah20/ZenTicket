<?php

namespace App\DataFixtures;

use App\Entity\Utilisateur;
use App\Entity\Ticket;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class AppFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        // Créer un utilisateur
        $utilisateur = new Utilisateur();
        $utilisateur->setNom("Jean Dupont");
        $utilisateur->setEmail("jean.dupont@example.com");
        $manager->persist($utilisateur);

        // Créer un ticket lié à cet utilisateur
        $ticket = new Ticket();
        $ticket->setDescription("Problème avec le réseau.");
        $ticket->setStatut("En attente");
        $ticket->setDateCreation(new \DateTime());
        $ticket->setSolution("La solution au problème de réseau est en cours.");
        $ticket->setUtilisateur($utilisateur); // Relier le ticket à l'utilisateur
        $manager->persist($ticket);

        // Sauvegarder dans la base
        $manager->flush();
    }
}
