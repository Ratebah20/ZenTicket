<?php

namespace App\Service;

use Lcobucci\JWT\Configuration;
use Lcobucci\JWT\Signer\Hmac\Sha256;
use Lcobucci\JWT\Signer\Key\InMemory;

class MercureJwtProvider
{
    private string $publisherKey;
    private string $subscriberKey;

    public function __construct(string $publisherKey, string $subscriberKey)
    {
        $this->publisherKey = $publisherKey;
        $this->subscriberKey = $subscriberKey;
    }

    public function getPublisherToken(): string
    {
        $config = Configuration::forSymmetricSigner(
            new Sha256(),
            InMemory::plainText($this->publisherKey)
        );

        $now = new \DateTimeImmutable();
        
        return $config->builder()
            ->withClaim('mercure', [
                'publish' => ['*'],
                'subscribe' => ['*']
            ])
            ->issuedAt($now)
            ->expiresAt($now->modify('+1 hour'))
            ->getToken($config->signer(), $config->signingKey())
            ->toString();
    }

    public function getSubscriberToken(): string
    {
        $config = Configuration::forSymmetricSigner(
            new Sha256(),
            InMemory::plainText($this->subscriberKey)
        );

        $now = new \DateTimeImmutable();
        
        // Assurer que le token a des permissions larges pour s'abonner
        return $config->builder()
            ->withClaim('mercure', [
                'subscribe' => ['*']  // Autoriser l'abonnement à tous les topics
            ])
            ->issuedAt($now)
            ->expiresAt($now->modify('+1 hour'))
            ->getToken($config->signer(), $config->signingKey())
            ->toString();
    }
    
    /**
     * Génère un cookie pour l'autorisation Mercure
     */
    public function getCookieValue(): string
    {
        return 'Bearer ' . $this->getSubscriberToken();
    }
}