<?php

namespace App\Service;

use Symfony\Component\Validator\Validator\ValidatorInterface;
use Symfony\Component\Validator\Constraints as Assert;

class DestinationValidatorService
{
    private ValidatorInterface $validator;

    public function __construct(ValidatorInterface $validator)
    {
        $this->validator = $validator;
    }

    public function validateDestination(array $data): array
    {
        $constraints = new Assert\Collection([
            'name' => [
                new Assert\NotBlank(['message' => 'Name cannot be blank']),
                new Assert\Length([
                    'max' => 100,
                    'min'=>3,
                    'maxMessage' => 'Name cannot be longer than {{ limit }} characters',
                ]),
            ],
            'description' => [
                new Assert\NotBlank(['message' => 'Description cannot be blank']),
                new Assert\Length([
                    'max' => 1000,
                    'min'=>10,
                    'maxMessage' => 'Description cannot be longer than {{ limit }} characters',
                ]),
            ],
            'country' => [
                new Assert\NotBlank(['message' => 'Country cannot be blank']),
                new Assert\Length([
                    'max' => 100,
                    'min'=>3,
                    'maxMessage' => 'Country cannot be longer than {{ limit }} characters',
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
