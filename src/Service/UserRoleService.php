<?php

namespace App\Service;

use App\Entity\UserRole;
use Doctrine\ORM\EntityManagerInterface;

class UserRoleService
{
    private EntityManagerInterface $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    public function createUserRole(string $role_name): UserRole
    {
        $userRole = new UserRole();

        $userRole->setRoleName($role_name);



        $this->entityManager->persist($userRole);
        $this->entityManager->flush();

        return $userRole;
    }
}