<?php

namespace App\EventListener;

use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Event\ExceptionEvent;
use Symfony\Component\Validator\ConstraintViolationListInterface;
use Throwable;

class RuntimeConstraintExceptionListener
{
    /**
     * Обробка виключень.
     *
     * @param ExceptionEvent $event
     */
    public function onKernelException(ExceptionEvent $event): void
    {
        $exception = $event->getThrowable(); //отримуємо виключення
        $code = $this->getCode($exception); //визначаємо HTTP-код
        $errors = $this->getErrors($exception); //форматуємо список помилок

        $response = new JsonResponse(
            [
                'data' => [
                    'code' => $code,
                    'errors' => $errors,
                ],
            ],
            $code
        );

        $event->setResponse($response); //установка JSON-відповіді
    }

    /**
     * Отримання статус-коду відповіді (HTTP Status Code).
     *
     * @param Throwable $exception
     * @return int
     */
    private function getCode(Throwable $exception): int
    {
        //якщо в виключення є метод getStatusCode(), намагаємось отримати код
        if (method_exists($exception, 'getStatusCode')) {
            return Response::$statusTexts[$exception->getStatusCode()]
                ? $exception->getStatusCode()
                : Response::HTTP_UNPROCESSABLE_ENTITY; //за замовчуванням 422
        }

        //інакше пробуємо отримати звичайний код виключення
        return Response::$statusTexts[$exception->getCode()]
            ? $exception->getCode()
            : Response::HTTP_UNPROCESSABLE_ENTITY; //за замовчуванням 422
    }

    /**
     * Створення списку помилок.
     *
     * @param Throwable $exception
     * @return array
     */
    private function getErrors(Throwable $exception): array
    {
        //масив помилок, який буде сформовано
        $errors = [];

        //якщо є ConstraintViolationList (наприклад, виключення валідації)
        if (
            method_exists($exception, 'getConstraintViolationList') &&
            $exception->getConstraintViolationList() instanceof ConstraintViolationListInterface
        ) {
            return $this->getAssociativeErrorsForConstraintViolationList(
                $exception->getConstraintViolationList(),
                $errors
            );
        }

        //якщо помилка передана як JSON
        if ($tmpErrors = json_decode($exception->getMessage(), true)) {
            return $this->getAssociativeErrors(
                $tmpErrors['data']['errors'] ?? $tmpErrors,
                $errors
            );
        }

        //якщо це просто текстове повідомлення
        $errors[] = [$exception->getMessage()];

        return $errors;
    }

    /**
     * Форматує список помилок ConstraintViolationList у вигляді асоціативного масиву.
     *
     * @param ConstraintViolationListInterface $list
     * @param array $errors
     * @return array
     */
    private function getAssociativeErrorsForConstraintViolationList(ConstraintViolationListInterface $list, array $errors): array
    {
        foreach ($list as $key => $violation) {
            $errors[$key][$violation->getPropertyPath()] = $violation->getMessage();
        }

        return $errors;
    }

    /**
     * Форматує довільні помилки у вигляді асоціативного масиву.
     *
     * @param array $tmpErrors
     * @param array $errors
     * @return array
     */
    private function getAssociativeErrors(array $tmpErrors, array $errors): array
    {
        foreach ($tmpErrors as $key => $error) {
            if (is_array($error)) {
                $errors[$key] = $this->getAssociativeErrors($error, $errors);
            } else {
                $errors[$key] = $error;
            }
        }

        return $errors;
    }
}