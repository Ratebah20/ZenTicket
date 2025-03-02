<?php

namespace App\ApiResource;

use App\Entity\Categorie;
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
use Symfony\Component\Serializer\Annotation\Groups;

#[ApiResource(
    operations: [
        new Get(
            normalizationContext: ['groups' => ['categorie:read', 'categorie:item:read']]
        ),
        new GetCollection(
            normalizationContext: ['groups' => ['categorie:read', 'categorie:collection:read']]
        ),
        new Post(
            denormalizationContext: ['groups' => ['categorie:write']]
        ),
        new Put(
            denormalizationContext: ['groups' => ['categorie:write']]
        ),
        new Delete()
    ],
    normalizationContext: ['groups' => ['categorie:read']],
    denormalizationContext: ['groups' => ['categorie:write']],
    paginationEnabled: true,
    paginationItemsPerPage: 10
)]
#[ApiFilter(SearchFilter::class, properties: [
    'nom' => 'partial',
    'description' => 'partial'
])]
#[ApiFilter(OrderFilter::class, properties: [
    'id', 'nom'
])]
class CategorieResource extends Categorie
{
    // Cette classe étend l'entité Categorie et ajoute les métadonnées API Platform
    // sans modifier l'entité originale
}
