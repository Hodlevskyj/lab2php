<?php

namespace App\Service;

use Symfony\Component\Validator\Validator\ValidatorInterface;
use Symfony\Component\Validator\Constraints as Assert;

class BookingValidatorService
{
    private ValidatorInterface $validator;

    public function __construct(ValidatorInterface $validator)
    {
        $this->validator = $validator;
    }

    public function validateBooking(array $data): array
    {
        $constraints = new Assert\Collection([
            'touristId' => [
                new Assert\NotBlank(['message' => 'Tourist ID cannot be blank']),
                new Assert\Type([
                    'type' => 'integer',
                    'message' => 'Tourist ID must be an integer',
                ]),
            ],
            'tourId' => [
                new Assert\NotBlank(['message' => 'Tour ID cannot be blank']),
                new Assert\Type([
                    'type' => 'integer',
                    'message' => 'Tour ID must be an integer',
                ]),
            ],
            'number_of_people' => [
                new Assert\NotBlank(['message' => 'Number of people cannot be blank']),
                new Assert\Type([
                    'type' => 'integer',
                    'message' => 'Number of people must be an integer',
                ]),
                new Assert\GreaterThan(0),
            ],
            'totalPrice' => [
                new Assert\NotBlank(['message' => 'Total price cannot be blank']),
                new Assert\Type([
                    'type' => 'string',
                    'message' => 'Total price must be a string',
                ]),
                new Assert\Regex([
                    'pattern' => '/^\d+(\.\d{1,2})?$/',
                    'message' => 'Total price must be a valid number',
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
