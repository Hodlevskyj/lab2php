<?php
namespace App\Service;

use Symfony\Component\Validator\Validator\ValidatorInterface;
use Symfony\Component\Validator\Constraints as Assert;

class TourGuideValidatorService
{
    private ValidatorInterface $validator;

    public function __construct(ValidatorInterface $validator)
    {
        $this->validator = $validator;
    }

    public function validateTourGuide(array $data): array
    {
        $constraints = new Assert\Collection([
            'first_name' => [
                new Assert\NotBlank(['message' => 'First name cannot be blank']),
                new Assert\Length([
                    'max' => 100,
                    'maxMessage' => 'First name cannot be longer than {{ limit }} characters',
                ]),
            ],
            'last_name' => [
                new Assert\NotBlank(['message' => 'Last name cannot be blank']),
                new Assert\Length([
                    'max' => 100,
                    'maxMessage' => 'Last name cannot be longer than {{ limit }} characters',
                ]),
            ],
            'email' => [
                new Assert\NotBlank(['message' => 'Email cannot be blank']),
                new Assert\Email(['message' => 'Invalid email format']),
            ],
            'phone' => [
                new Assert\NotBlank(['message' => 'Phone number cannot be blank']),
                new Assert\Regex([
                    'pattern' => '/^\+?\d{10,15}$/',
                    'message' => 'Phone number must be a valid format',
                ]),
            ],
            'language' => [
                new Assert\NotBlank(['message' => 'Language cannot be blank']),
                new Assert\Length([
                    'max' => 50,
                    'maxMessage' => 'Language cannot be longer than {{ limit }} characters',
                ]),
            ],
            'bio' => [
                new Assert\NotBlank(['message' => 'Bio cannot be blank']),
                new Assert\Length([
                    'max' => 1000,
                    'maxMessage' => 'Bio cannot be longer than {{ limit }} characters',
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
