<?php

namespace App\ApiResource;

use App\Entity\Notification;
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
use ApiPlatform\Doctrine\Orm\Filter\BooleanFilter;
use Symfony\Component\Serializer\Annotation\Groups;

#[ApiResource(
    operations: [
        new Get(
            normalizationContext: ['groups' => ['notification:read', 'notification:item:read']]
        ),
        new GetCollection(
            normalizationContext: ['groups' => ['notification:read', 'notification:collection:read']]
        ),
        new Post(
            denormalizationContext: ['groups' => ['notification:write']]
        ),
        new Put(
            denormalizationContext: ['groups' => ['notification:write']]
        ),
        new Delete()
    ],
    normalizationContext: ['groups' => ['notification:read']],
    denormalizationContext: ['groups' => ['notification:write']],
    paginationEnabled: true,
    paginationItemsPerPage: 10
)]
#[ApiFilter(SearchFilter::class, properties: [
    'titre' => 'partial',
    'message' => 'partial',
    'type' => 'exact',
    'utilisateur.id' => 'exact',
    'ticket.id' => 'exact'
])]
#[ApiFilter(OrderFilter::class, properties: [
    'id', 'dateCreation'
])]
#[ApiFilter(DateFilter::class, properties: ['dateCreation'])]
#[ApiFilter(BooleanFilter::class, properties: ['lu'])]
class NotificationResource extends Notification
{
    // Cette classe étend l'entité Notification et ajoute les métadonnées API Platform
    // sans modifier l'entité originale
}
