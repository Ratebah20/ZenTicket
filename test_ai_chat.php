<?php
// Script de test pour la fonctionnalité de chat IA

// Récupérer l'ID de la chatbox depuis les arguments
$chatboxId = isset($argv[1]) ? (int)$argv[1] : 110;
$message = isset($argv[2]) ? $argv[2] : "Ceci est un message de test";

// Exécuter la commande Symfony pour envoyer un message
$command = sprintf(
    'php bin/console app:test-chat %d "%s"',
    $chatboxId,
    addslashes($message)
);

echo "Exécution de la commande: $command\n";
passthru($command);
