<?php

namespace App\Service;

use Symfony\Component\Validator\Validator\ValidatorInterface;
use Symfony\Component\Validator\Constraints as Assert;

class PaymentMethodValidatorService
{
    private ValidatorInterface $validator;

    public function __construct(ValidatorInterface $validator)
    {
        $this->validator = $validator;
    }

    public function validatePaymentMethod(array $data): array
    {
        $constraints = new Assert\Collection([
            'method' => [
                new Assert\NotBlank(['message' => 'Payment method cannot be blank']),
                new Assert\Choice([
                    'choices' => ['cart', 'paypal', 'cash'],
                    'message' => 'Payment method must be one of "cart", "paypal", or "cash"',
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

