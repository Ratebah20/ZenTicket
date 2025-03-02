<?php

namespace App\ApiResource;

use App\Entity\Rapport;
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
            normalizationContext: ['groups' => ['rapport:read', 'rapport:item:read']]
        ),
        new GetCollection(
            normalizationContext: ['groups' => ['rapport:read', 'rapport:collection:read']]
        ),
        new Post(
            denormalizationContext: ['groups' => ['rapport:write']]
        ),
        new Put(
            denormalizationContext: ['groups' => ['rapport:write']]
        ),
        new Delete()
    ],
    normalizationContext: ['groups' => ['rapport:read']],
    denormalizationContext: ['groups' => ['rapport:write']],
    paginationEnabled: true,
    paginationItemsPerPage: 10
)]
#[ApiFilter(SearchFilter::class, properties: [
    'titre' => 'partial',
    'contenu' => 'partial',
    'type' => 'exact',
    'periode' => 'exact',
    'service' => 'exact'
])]
#[ApiFilter(OrderFilter::class, properties: [
    'id', 'dateCreation'
])]
#[ApiFilter(DateFilter::class, properties: ['dateCreation'])]
class RapportResource extends Rapport
{
    // Cette classe étend l'entité Rapport et ajoute les métadonnées API Platform
    // sans modifier l'entité originale
}
