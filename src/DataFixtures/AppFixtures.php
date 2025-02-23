<?php

namespace App\DataFixtures;

use App\Entity\Administrateur;
use App\Entity\Categorie;
use App\Entity\Technicien;
use App\Entity\Ticket;
use App\Entity\Utilisateur;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class AppFixtures extends Fixture
{
    private $passwordHasher;

    public function __construct(UserPasswordHasherInterface $passwordHasher)
    {
        $this->passwordHasher = $passwordHasher;
    }

    public function load(ObjectManager $manager): void
    {
        // Créer les catégories
        $categories = [];
        $categoriesData = [
            'Réseau' => ['description' => 'Problèmes liés au réseau', 'specialites' => ['Réseau', 'Infrastructure']],
            'Matériel' => ['description' => 'Problèmes matériels', 'specialites' => ['Hardware', 'Support Technique']],
            'Logiciel' => ['description' => 'Problèmes logiciels', 'specialites' => ['Software', 'Applications']],
            'Sécurité' => ['description' => 'Questions de sécurité', 'specialites' => ['Cybersécurité', 'Audit']],
            'Maintenance' => ['description' => 'Maintenance préventive', 'specialites' => ['Maintenance', 'Support Technique']],
            'Cloud' => ['description' => 'Services cloud et hébergement', 'specialites' => ['Cloud', 'Infrastructure']],
            'Base de données' => ['description' => 'Problèmes de bases de données', 'specialites' => ['BDD', 'Infrastructure']]
        ];

        foreach ($categoriesData as $nom => $data) {
            $categorie = new Categorie();
            $categorie->setNom($nom);
            $categorie->setDescription($data['description']);
            $manager->persist($categorie);
            $categories[$nom] = ['entity' => $categorie, 'specialites' => $data['specialites']];
        }

        // Créer des administrateurs
        $admins = [];
        $adminsData = [
            ['nom' => 'Admin Principal', 'email' => 'admin@3innov.fr'],
            ['nom' => 'Admin Système', 'email' => 'sysadmin@3innov.fr'],
            ['nom' => 'Admin Réseau', 'email' => 'netadmin@3innov.fr']
        ];

        foreach ($adminsData as $adminData) {
            $admin = new Administrateur();
            $admin->setNom($adminData['nom']);
            $admin->setEmail($adminData['email']);
            $admin->addRole('ROLE_ADMIN');
            $hashedPassword = $this->passwordHasher->hashPassword($admin, 'admin123');
            $admin->setPassword($hashedPassword);
            $manager->persist($admin);
            $admins[] = $admin;
        }

        // Créer des techniciens
        $techniciens = [];
        $techniciensData = [
            ['nom' => 'Tech Support Réseau', 'email' => 'tech.reseau@3innov.fr', 'specialite' => 'Réseau'],
            ['nom' => 'Tech Hardware', 'email' => 'tech.hardware@3innov.fr', 'specialite' => 'Hardware'],
            ['nom' => 'Tech Software', 'email' => 'tech.software@3innov.fr', 'specialite' => 'Software'],
            ['nom' => 'Tech Sécurité', 'email' => 'tech.security@3innov.fr', 'specialite' => 'Cybersécurité'],
            ['nom' => 'Tech Support BDD', 'email' => 'tech.bdd@3innov.fr', 'specialite' => 'BDD']
        ];

        foreach ($techniciensData as $techData) {
            $technicien = new Technicien();
            $technicien->setNom($techData['nom']);
            $technicien->setEmail($techData['email']);
            $technicien->setSpecialite($techData['specialite']);
            $technicien->addRole('ROLE_TECHNICIEN');
            $hashedPassword = $this->passwordHasher->hashPassword($technicien, 'tech123');
            $technicien->setPassword($hashedPassword);
            $manager->persist($technicien);
            $techniciens[$techData['specialite']] = $technicien;
        }

        // Créer des utilisateurs
        $users = [];
        $utilisateursData = [
            ['nom' => 'Jean Dupont', 'email' => 'jean.dupont@3innov.fr', 'service' => 'Commercial'],
            ['nom' => 'Marie Martin', 'email' => 'marie.martin@3innov.fr', 'service' => 'RH'],
            ['nom' => 'Pierre Durant', 'email' => 'pierre.durant@3innov.fr', 'service' => 'Marketing'],
            ['nom' => 'Sophie Bernard', 'email' => 'sophie.bernard@3innov.fr', 'service' => 'Comptabilité'],
            ['nom' => 'Lucas Petit', 'email' => 'lucas.petit@3innov.fr', 'service' => 'Commercial'],
            ['nom' => 'Emma Richard', 'email' => 'emma.richard@3innov.fr', 'service' => 'Marketing']
        ];

        foreach ($utilisateursData as $userData) {
            $utilisateur = new Utilisateur();
            $utilisateur->setNom($userData['nom']);
            $utilisateur->setEmail($userData['email']);
            $utilisateur->addRole('ROLE_USER');
            $hashedPassword = $this->passwordHasher->hashPassword($utilisateur, 'user123');
            $utilisateur->setPassword($hashedPassword);
            $manager->persist($utilisateur);
            $users[] = $utilisateur;
        }

        // Créer des tickets avec différents statuts
        $statuts = [
            Ticket::STATUT_NOUVEAU => 30,      // 30% nouveaux tickets
            Ticket::STATUT_EN_COURS => 40,     // 40% tickets en cours
            Ticket::STATUT_RESOLU => 20,       // 20% tickets résolus
            Ticket::STATUT_CLOTURE => 10       // 10% tickets clôturés
        ];

        $priorites = [
            Ticket::PRIORITE_BASSE => 20,      // 20% priorité basse
            Ticket::PRIORITE_NORMALE => 50,     // 50% priorité normale
            Ticket::PRIORITE_HAUTE => 20,      // 20% priorité haute
            Ticket::PRIORITE_URGENTE => 10     // 10% priorité urgente
        ];

        // Créer 30 tickets
        for ($i = 1; $i <= 30; $i++) {
            $ticket = new Ticket();
            $ticket->setTitre('Ticket #' . $i . ' - ' . $this->getRandomTicketTitle());
            $ticket->setDescription($this->getRandomTicketDescription());
            $ticket->setDateCreation(new \DateTime('-' . rand(1, 30) . ' days'));
            
            // Sélection de la catégorie et attribution d'un technicien correspondant
            $categorie = array_rand($categories);
            $ticket->setCategorie($categories[$categorie]['entity']);
            
            // Attribution d'une priorité selon la distribution
            $priorite = $this->getRandomWeighted($priorites);
            $ticket->setPriorite($priorite);
            
            // Attribution d'un utilisateur aléatoire
            $ticket->setUtilisateur($users[array_rand($users)]);
            
            // Attribution du statut selon la distribution
            $statut = $this->getRandomWeighted($statuts);
            $ticket->setStatut($statut);

            // Gestion des dates et techniciens selon le statut
            if ($statut !== Ticket::STATUT_NOUVEAU) {
                // Trouver un technicien spécialisé
                $specialites = $categories[$categorie]['specialites'];
                $specialite = $specialites[array_rand($specialites)];
                if (isset($techniciens[$specialite])) {
                    $ticket->setTechnicien($techniciens[$specialite]);
                } else {
                    $ticket->setTechnicien($techniciens[array_rand($techniciens)]);
                }

                if ($statut === Ticket::STATUT_RESOLU || $statut === Ticket::STATUT_CLOTURE) {
                    $ticket->setDateResolution(new \DateTime('-' . rand(1, 5) . ' days'));
                    $ticket->setSolution('Solution appliquée : ' . $this->getRandomSolution());
                    
                    if ($statut === Ticket::STATUT_CLOTURE) {
                        $ticket->setDateCloture(new \DateTime());
                        $ticket->setSolutionValidee(true);
                    }
                }
            }
            
            $manager->persist($ticket);
        }

        $manager->flush();
    }

    private function getRandomWeighted(array $weighted): string
    {
        $rand = rand(1, 100);
        $total = 0;
        foreach ($weighted as $key => $weight) {
            $total += $weight;
            if ($rand <= $total) {
                return $key;
            }
        }
        return array_key_first($weighted);
    }

    private function getRandomTicketTitle(): string
    {
        $titles = [
            'Problème de connexion',
            'Erreur application',
            'Mise à jour requise',
            'Accès refusé',
            'Performance dégradée',
            'Problème d\'impression',
            'Erreur de synchronisation',
            'Problème de messagerie'
        ];
        return $titles[array_rand($titles)];
    }

    private function getRandomTicketDescription(): string
    {
        $descriptions = [
            'L\'utilisateur signale une lenteur importante lors de l\'accès aux ressources réseau.',
            'L\'application se ferme de manière inattendue lors de l\'exportation de données.',
            'Impossible d\'accéder à certains dossiers partagés depuis ce matin.',
            'L\'imprimante affiche une erreur de communication et refuse d\'imprimer.',
            'Le poste de travail ne démarre plus après la dernière mise à jour.',
            'Problèmes de synchronisation avec le serveur de messagerie.',
            'L\'écran affiche des artefacts graphiques par intermittence.',
            'La sauvegarde automatique ne s\'est pas exécutée cette nuit.'
        ];
        return $descriptions[array_rand($descriptions)];
    }

    private function getRandomSolution(): string
    {
        $solutions = [
            'Réinitialisation des paramètres réseau et mise à jour des pilotes.',
            'Installation des dernières mises à jour et nettoyage du cache.',
            'Reconfiguration des permissions d\'accès et validation des droits utilisateur.',
            'Remplacement du matériel défectueux et mise à jour du firmware.',
            'Restauration depuis la dernière sauvegarde fonctionnelle.',
            'Optimisation des paramètres système et nettoyage des fichiers temporaires.',
            'Mise à jour des certificats de sécurité et des clés d\'accès.',
            'Configuration d\'une nouvelle stratégie de sauvegarde.'
        ];
        return $solutions[array_rand($solutions)];
    }
}
