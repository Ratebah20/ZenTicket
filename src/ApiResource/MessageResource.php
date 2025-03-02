<?php

namespace App\ApiResource;

use App\Entity\Message;
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
            normalizationContext: ['groups' => ['message:read', 'message:item:read']]
        ),
        new GetCollection(
            normalizationContext: ['groups' => ['message:read', 'message:collection:read']]
        ),
        new Post(
            denormalizationContext: ['groups' => ['message:write']]
        ),
        new Put(
            denormalizationContext: ['groups' => ['message:write']]
        ),
        new Delete()
    ],
    normalizationContext: ['groups' => ['message:read']],
    denormalizationContext: ['groups' => ['message:write']],
    paginationEnabled: true,
    paginationItemsPerPage: 10
)]
#[ApiFilter(SearchFilter::class, properties: [
    'message' => 'partial',
    'chatbox.id' => 'exact',
    'messageType' => 'exact',
    'isRead' => 'exact'
])]
#[ApiFilter(OrderFilter::class, properties: [
    'id', 'timestamp'
])]
#[ApiFilter(DateFilter::class, properties: ['timestamp'])]
class MessageResource extends Message
{
    // Cette classe étend l'entité Message et ajoute les métadonnées API Platform
    // sans modifier l'entité originale
}
