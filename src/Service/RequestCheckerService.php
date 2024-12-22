<?php

namespace App\Service;

use Symfony\Component\Validator\Constraints\Collection;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\HttpKernel\Exception\UnprocessableEntityHttpException;

class RequestCheckerService
{
    private ValidatorInterface $validator;

    public function __construct(ValidatorInterface $validator)
    {
        $this->validator = $validator;
    }

    /**
     * Перевірка на наявність обов'язкових полів.
     *
     * @param mixed $data Дані для перевірки (часто JSON, декодований у масив)
     * @param array $requiredFields Масив обов'язкових полів
     * @return void
     */
    public function validateRequiredFields(mixed $data, array $requiredFields): void
    {
        $missingFields = [];

        foreach ($requiredFields as $field) {
            if (!array_key_exists($field, $data) || $data[$field] === null) {
                $missingFields[] = $field;
            }
        }

        if (!empty($missingFields)) {
            throw new BadRequestHttpException(
                'Fields missing: ' . implode(', ', $missingFields)
            );
        }
    }

    /**
     * Валідація даних за допомогою `Symfony Constraints`.
     *
     * @param array|object $data Дані для перевірки
     * @param array $rules Масив обмежень (constraints)
     * @return void
     */
    public function validateDataWithConstraints(array|object $data, array $rules): void
    {
        $constraints = new Collection($rules);

        $violations = $this->validator->validate($data, $constraints);

        if (count($violations) > 0) {
            $validationErrors = [];

            foreach ($violations as $violation) {
                // Отримання шляху та повідомлення про помилку
                $propertyPath = str_replace(['[', ']'], '', $violation->getPropertyPath());
                $validationErrors[$propertyPath] = $violation->getMessage();
            }

            throw new UnprocessableEntityHttpException(json_encode($validationErrors));
        }
    }

    /**
     * Повна перевірка даних: обов'язкові поля + Constraints.
     *
     * @param mixed $data Дані для перевірки
     * @param array $requiredFields Список обов’язкових полів
     * @param array $constraints Масив обмежень для перевірки
     * @return void
     */
    public function validate(mixed $data, array $requiredFields, array $constraints): void
    {
        // Перевіряємо наявність обов'язкових полів
        $this->validateRequiredFields($data, $requiredFields);

        // Перевіряємо валідність даних за Constraints
        $this->validateDataWithConstraints($data, $constraints);
    }
}