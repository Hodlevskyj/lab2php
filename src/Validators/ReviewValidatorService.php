<?php
namespace App\Service;

use Symfony\Component\Validator\Validator\ValidatorInterface;
use Symfony\Component\Validator\Constraints as Assert;

class ReviewValidatorService
{
    private ValidatorInterface $validator;

    public function __construct(ValidatorInterface $validator)
    {
        $this->validator = $validator;
    }

    public function validateReview(array $data): array
    {
        $constraints = new Assert\Collection([
            'rating' => [
                new Assert\NotBlank(['message' => 'Rating cannot be blank']),
                new Assert\Range([
                    'min' => 1,
                    'max' => 10,
                    'notInRangeMessage' => 'Rating must be between {{ min }} and {{ max }}',
                ]),
            ],
            'comment' => [
                new Assert\NotBlank(['message' => 'Comment cannot be blank']),
                new Assert\Length([
                    'max' => 1000,
                    'min' => 2,
                    'maxMessage' => 'Comment cannot be longer than {{ limit }} characters',
                    'minMessage' => 'Comment cannot be shorter than {{ limit }} characters',
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

