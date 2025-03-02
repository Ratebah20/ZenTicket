<?php

namespace App\ApiResource;

use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\ApiProperty;
use ApiPlatform\Metadata\ApiFilter;
use ApiPlatform\Metadata\Get;
use ApiPlatform\Metadata\GetCollection;
use ApiPlatform\Metadata\Post;
use ApiPlatform\Metadata\Put;
use ApiPlatform\Metadata\Delete;
use ApiPlatform\Doctrine\Orm\Filter\SearchFilter;
use ApiPlatform\Doctrine\Orm\Filter\OrderFilter;
use ApiPlatform\Doctrine\Orm\Filter\BooleanFilter;
use App\Entity\Utilisateur;
use Symfony\Component\Serializer\Annotation\Groups;

#[ApiResource(
    operations: [
        new Get(
            normalizationContext: ['groups' => ['utilisateur:read']]
        ),
        new GetCollection(
            normalizationContext: ['groups' => ['utilisateur:read']]
        ),
        new Post(
            normalizationContext: ['groups' => ['utilisateur:read']],
            denormalizationContext: ['groups' => ['utilisateur:write']]
        ),
        new Put(
            normalizationContext: ['groups' => ['utilisateur:read']],
            denormalizationContext: ['groups' => ['utilisateur:write']]
        ),
        new Delete()
    ],
    normalizationContext: ['groups' => ['utilisateur:read']],
    denormalizationContext: ['groups' => ['utilisateur:write']],
    paginationItemsPerPage: 10
)]
#[ApiFilter(SearchFilter::class, properties: [
    'email' => 'partial',
    'nom' => 'partial',
    'prenom' => 'partial',
    'service' => 'partial'
])]
#[ApiFilter(OrderFilter::class, properties: ['id', 'nom', 'prenom', 'email'])]
#[ApiFilter(BooleanFilter::class, properties: ['isActive'])]
class UtilisateurResource extends Utilisateur
{
    #[ApiProperty(identifier: true)]
    #[Groups(['utilisateur:read'])]
    protected ?int $id = null;

    #[Groups(['utilisateur:read', 'utilisateur:write'])]
    protected ?string $email = null;

    #[Groups(['utilisateur:read', 'utilisateur:write'])]
    protected ?string $nom = null;

    #[Groups(['utilisateur:read', 'utilisateur:write'])]
    protected ?string $prenom = null;

    #[Groups(['utilisateur:read', 'utilisateur:write'])]
    protected ?string $telephone = null;

    #[Groups(['utilisateur:read', 'utilisateur:write'])]
    protected ?string $service = null;

    #[Groups(['utilisateur:write'])]
    protected ?string $password = null;

    #[Groups(['utilisateur:read', 'utilisateur:write'])]
    protected array $roles = [];

    #[Groups(['utilisateur:read'])]
    protected ?\DateTimeImmutable $lastLogin = null;

    #[Groups(['utilisateur:read', 'utilisateur:write'])]
    protected ?bool $isActive = true;

    public function getId(): ?int
    {
        return $this->id;
    }
}
