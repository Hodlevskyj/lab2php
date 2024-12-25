<?php

namespace App\Controller;

use App\Entity\Payment;
use App\Form\PaymentType;
use App\Repository\PaymentRepository;
use App\Service\PaymentService;
use App\Service\PaymentValidatorService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Form\FormError;

#[Route('/payment')]
final class PaymentController extends AbstractController
{
    private PaymentService $paymentService;
    private PaymentValidatorService $paymentValidatorService;

    public function __construct(PaymentService $paymentService, PaymentValidatorService $paymentValidatorService)
    {
        $this->paymentService = $paymentService;
        $this->paymentValidatorService = $paymentValidatorService;
    }

    #[Route(name: 'app_payment_index', methods: ['GET'])]
    public function index(PaymentRepository $paymentRepository,Request $request): Response
    {
        $itemsPerPage = (int)$request->query->get('itemsPerPage', 1);
        $page = (int)$request->query->get('page', 1);

        $paginationData = $paymentRepository->getPaginatedPayments($itemsPerPage, $page);

        return $this->render('payment/index.html.twig', [
            'payments' => $paginationData['payments'],
            'totalItems' => $paginationData['totalItems'],
            'totalPages' => $paginationData['totalPages'],
            'currentPage' => $page,
            'itemsPerPage' => $itemsPerPage,
        ]);
    }

    #[Route('/new', name: 'app_payment_new', methods: ['GET', 'POST'])]
    public function new(Request $request): Response
    {
        $payment = new Payment();
        $form = $this->createForm(PaymentType::class, $payment);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // Валідація введених даних
            $validationErrors = $this->paymentValidatorService->validatePayment([
                'amount' => $payment->getAmount(),
                'payment_date' => $payment->getPaymentDate(),
                'status' => $payment->getStatus(),
            ]);

            // Додаємо помилки в форму
            foreach ($validationErrors as $field => $error) {
                $form->get($field)?->addError(new FormError($error));
            }

            if ($form->isValid()) {
                $this->paymentService->createPayment(
                    $payment->getBooking(),
                    $payment->getAmount(),
                    $payment->getPaymentDate(),
                    $payment->getStatus()
                );

                return $this->redirectToRoute('app_payment_index', [], Response::HTTP_SEE_OTHER);
            }
        }

        return $this->render('payment/new.html.twig', [
            'payment' => $payment,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_payment_show', methods: ['GET'])]
    public function show(Payment $payment): Response
    {
        return $this->render('payment/show.html.twig', [
            'payment' => $payment,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_payment_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Payment $payment, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(PaymentType::class, $payment);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('app_payment_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('payment/edit.html.twig', [
            'payment' => $payment,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_payment_delete', methods: ['POST'])]
    public function delete(Request $request, Payment $payment, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$payment->getId(), $request->getPayload()->getString('_token'))) {
            $entityManager->remove($payment);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_payment_index', [], Response::HTTP_SEE_OTHER);
    }
}
