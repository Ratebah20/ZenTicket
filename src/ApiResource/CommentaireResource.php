<?php

namespace App\ApiResource;

use App\Entity\Commentaire;
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
            normalizationContext: ['groups' => ['commentaire:read', 'commentaire:item:read']]
        ),
        new GetCollection(
            normalizationContext: ['groups' => ['commentaire:read', 'commentaire:collection:read']]
        ),
        new Post(
            denormalizationContext: ['groups' => ['commentaire:write']]
        ),
        new Put(
            denormalizationContext: ['groups' => ['commentaire:write']]
        ),
        new Delete()
    ],
    normalizationContext: ['groups' => ['commentaire:read']],
    denormalizationContext: ['groups' => ['commentaire:write']],
    paginationEnabled: true,
    paginationItemsPerPage: 10
)]
#[ApiFilter(SearchFilter::class, properties: [
    'contenu' => 'partial',
    'ticket.id' => 'exact',
    'auteur.id' => 'exact'
])]
#[ApiFilter(OrderFilter::class, properties: [
    'id', 'dateCreation'
])]
#[ApiFilter(DateFilter::class, properties: ['dateCreation'])]
class CommentaireResource extends Commentaire
{
    // Cette classe étend l'entité Commentaire et ajoute les métadonnées API Platform
    // sans modifier l'entité originale
}
