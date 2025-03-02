<?php

namespace App\ApiResource;

use App\Entity\Technicien;
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
            normalizationContext: ['groups' => ['technicien:read', 'technicien:item:read']]
        ),
        new GetCollection(
            normalizationContext: ['groups' => ['technicien:read', 'technicien:collection:read']]
        ),
        new Post(
            denormalizationContext: ['groups' => ['technicien:write']]
        ),
        new Put(
            denormalizationContext: ['groups' => ['technicien:write']]
        ),
        new Delete()
    ],
    normalizationContext: ['groups' => ['technicien:read']],
    denormalizationContext: ['groups' => ['technicien:write']],
    paginationEnabled: true,
    paginationItemsPerPage: 10
)]
#[ApiFilter(SearchFilter::class, properties: [
    'nom' => 'partial',
    'email' => 'partial',
    'specialite' => 'partial'
])]
#[ApiFilter(OrderFilter::class, properties: [
    'id', 'nom', 'email', 'specialite'
])]
class TechnicienResource extends Technicien
{
    // Cette classe étend l'entité Technicien et ajoute les métadonnées API Platform
    // sans modifier l'entité originale
}
