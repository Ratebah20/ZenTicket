<?php

namespace App\ApiResource;

use App\Entity\Chatbox;
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
            normalizationContext: ['groups' => ['chatbox:read', 'chatbox:item:read']]
        ),
        new GetCollection(
            normalizationContext: ['groups' => ['chatbox:read', 'chatbox:collection:read']]
        ),
        new Post(
            denormalizationContext: ['groups' => ['chatbox:write']]
        ),
        new Put(
            denormalizationContext: ['groups' => ['chatbox:write']]
        ),
        new Delete()
    ],
    normalizationContext: ['groups' => ['chatbox:read']],
    denormalizationContext: ['groups' => ['chatbox:write']],
    paginationEnabled: true,
    paginationItemsPerPage: 10
)]
#[ApiFilter(SearchFilter::class, properties: [
    'ticket.id' => 'exact',
    'ia.id' => 'exact',
    'isTemporary' => 'exact'
])]
#[ApiFilter(OrderFilter::class, properties: [
    'id', 'createdAt'
])]
#[ApiFilter(DateFilter::class, properties: ['createdAt'])]
class ChatboxResource extends Chatbox
{
    // Cette classe étend l'entité Chatbox et ajoute les métadonnées API Platform
    // sans modifier l'entité originale
}
