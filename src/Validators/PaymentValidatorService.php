<?php

namespace App\Service;

use Symfony\Component\Validator\Validator\ValidatorInterface;
use Symfony\Component\Validator\Constraints as Assert;

class PaymentValidatorService
{
    private ValidatorInterface $validator;

    public function __construct(ValidatorInterface $validator)
    {
        $this->validator = $validator;
    }

    public function validatePayment(array $data): array
    {
        $constraints = new Assert\Collection([
            'amount' => [
                new Assert\NotBlank(['message' => 'Amount cannot be blank']),
                new Assert\Type([
                    'type' => 'string',
                    'message' => 'Amount must be a string',
                ]),
                new Assert\Regex([
                    'pattern' => '/^\d+(\.\d{1,2})?$/',
                    'message' => 'Amount must be a valid number',
                ]),
            ],
            'payment_date' => [
                new Assert\NotBlank(['message' => 'Payment date cannot be blank']),
                new Assert\Type([
                    'type' => '\DateTimeInterface',
                    'message' => 'Invalid payment date format',
                ]),
            ],
            'status' => [
                new Assert\NotBlank(['message' => 'Status cannot be blank']),
                new Assert\Choice([
                    'choices' => ['pending', 'completed', 'failed'],
                    'message' => 'Status must be one of "pending", "completed" or "failed"',
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
