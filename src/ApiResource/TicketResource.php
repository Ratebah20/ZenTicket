<?php

namespace App\ApiResource;

use App\Entity\Ticket;
use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\ApiProperty;
use ApiPlatform\Metadata\Get;
use ApiPlatform\Metadata\GetCollection;
use ApiPlatform\Metadata\Post;
use ApiPlatform\Metadata\Put;
use ApiPlatform\Metadata\Delete;
use ApiPlatform\Metadata\ApiFilter;
use ApiPlatform\Doctrine\Orm\Filter\SearchFilter;
use ApiPlatform\Doctrine\Orm\Filter\OrderFilter;
use ApiPlatform\Doctrine\Orm\Filter\DateFilter;
use Symfony\Component\Serializer\Annotation\Groups;

#[ApiResource(
    operations: [
        new Get(
            normalizationContext: ['groups' => ['ticket:read', 'ticket:item:read']]
        ),
        new GetCollection(
            normalizationContext: ['groups' => ['ticket:read', 'ticket:collection:read']]
        ),
        new Post(
            denormalizationContext: ['groups' => ['ticket:write']]
        ),
        new Put(
            denormalizationContext: ['groups' => ['ticket:write']]
        ),
        new Delete()
    ],
    normalizationContext: ['groups' => ['ticket:read']],
    denormalizationContext: ['groups' => ['ticket:write']],
    paginationEnabled: true,
    paginationItemsPerPage: 10
)]
#[ApiFilter(SearchFilter::class, properties: [
    'titre' => 'partial',
    'statut' => 'exact',
    'priorite' => 'exact',
    'utilisateur.id' => 'exact',
    'technicien.id' => 'exact',
    'categorie.id' => 'exact'
])]
#[ApiFilter(OrderFilter::class, properties: [
    'id', 'dateCreation', 'priorite'
])]
#[ApiFilter(DateFilter::class, properties: ['dateCreation'])]
class TicketResource extends Ticket
{
    // Cette classe étend l'entité Ticket et ajoute les métadonnées API Platform
    // sans modifier l'entité originale
}
