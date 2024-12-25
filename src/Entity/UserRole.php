<?php

namespace App\Entity;

use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\Get;
use ApiPlatform\Metadata\GetCollection;
use ApiPlatform\Metadata\Post;
use ApiPlatform\Metadata\Patch;
use ApiPlatform\Metadata\Delete;
use App\Repository\UserRoleRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Serializer\Annotation\Groups;

#[ApiResource(
    operations: [
        new GetCollection(
            normalizationContext: ['groups' => ['user_role:read:collection']]
        ),
        new Post(
            denormalizationContext: ['groups' => ['user_role:write']]
        ),
        new Get(
            normalizationContext: ['groups' => ['user_role:read:item']]
        ),
        new Patch(
            denormalizationContext: ['groups' => ['user_role:write']]
        ),
        new Delete()
    ]
)]
#[ORM\Entity(repositoryClass: UserRoleRepository::class)]
class UserRole
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    #[Groups(['user_role:read:collection', 'user_role:read:item'])]
    private ?int $id = null;

    #[ORM\Column(length: 100, unique: true)]
    #[Assert\NotBlank(message: 'Role name is required')]
    #[Assert\Length(
        max: 100,
        maxMessage: 'Role name cannot exceed {{ limit }} characters'
    )]
    #[Groups(['user_role:read:collection', 'user_role:read:item', 'user_role:write'])]
    private ?string $role_name = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getRoleName(): ?string
    {
        return $this->role_name;
    }

    public function setRoleName(string $role_name): static
    {
        $this->role_name = $role_name;

        return $this;
    }
}
