<?php

namespace App\Service;

use Symfony\Component\Validator\Validator\ValidatorInterface;
use Symfony\Component\Validator\Constraints as Assert;

class UserRoleValidatorService
{
    private ValidatorInterface $validator;

    public function __construct(ValidatorInterface $validator)
    {
        $this->validator = $validator;
    }

    public function validateUserRole(array $data): array
    {
        $constraints = new Assert\Collection([
            'role_name' => [
                new Assert\NotBlank(['message' => 'Role name cannot be blank']),
                new Assert\Length([
                    'max' => 100,
                    'maxMessage' => 'Role name cannot be longer than {{ limit }} characters',
                ]),
                new Assert\Choice([
                    'choices' => ['ROLE_ADMIN', 'ROLE_USER', 'ROLE_MODERATOR'],
                    'message' => 'Invalid role name "{{ value }}"',
                ]),
            ],
        ]);

        $violations = $this->validator->validate($data, $constraints);
        $errors = [];

        foreach ($violations as $violation) {
            $field = trim($violation->getPropertyPath(), '[]');
            $errors[$field] = $violation->getMessage();
        }

        return $errors;
    }
}

