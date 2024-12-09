<?php

namespace App\Controller;

use App\Entity\PaymentMethod;
use App\Form\PaymentMethodType;
use App\Repository\PaymentMethodRepository;
use App\Service\PaymentMethodService;
use App\Service\PaymentMethodValidatorService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Form\FormError;

#[Route('/payment_method')]
final class PaymentMethodController extends AbstractController
{
    private PaymentMethodService $paymentMethodService;
    private PaymentMethodValidatorService $paymentMethodValidatorService;

    public function __construct(PaymentMethodService $paymentMethodService, PaymentMethodValidatorService $paymentMethodValidatorService){
        $this->paymentMethodService = $paymentMethodService;
        $this->paymentMethodValidatorService = $paymentMethodValidatorService;
    }

    #[Route(name: 'app_payment_method_index', methods: ['GET'])]
    public function index(PaymentMethodRepository $paymentMethodRepository): Response
    {
        return $this->render('payment_method/index.html.twig', [
            'payment_methods' => $paymentMethodRepository->findAll(),
        ]);
    }

    #[Route('/new', name: 'app_payment_method_new', methods: ['GET', 'POST'])]
    public function new(Request $request): Response
    {
        $paymentMethod = new PaymentMethod();
        $form = $this->createForm(PaymentMethodType::class, $paymentMethod);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $validationErrors = $this->paymentMethodValidatorService->validatePaymentMethod([
                'method' => $paymentMethod->getMethod(),
            ]);

            foreach ($validationErrors as $field => $error) {
                $form->get($field)?->addError(new FormError($error));
            }

            if ($form->isValid()) {
                $this->paymentMethodService->createPaymentMethod($paymentMethod->getMethod());

                return $this->redirectToRoute('app_payment_method_index', [], Response::HTTP_SEE_OTHER);
            }
        }

        return $this->render('payment_method/new.html.twig', [
            'paymentMethod' => $paymentMethod,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_payment_method_show', methods: ['GET'])]
    public function show(PaymentMethod $paymentMethod): Response
    {
        return $this->render('payment_method/show.html.twig', [
            'payment_method' => $paymentMethod,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_payment_method_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, PaymentMethod $paymentMethod, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(PaymentMethodType::class, $paymentMethod);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('app_payment_method_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('payment_method/edit.html.twig', [
            'payment_method' => $paymentMethod,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_payment_method_delete', methods: ['POST'])]
    public function delete(Request $request, PaymentMethod $paymentMethod, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$paymentMethod->getId(), $request->getPayload()->getString('_token'))) {
            $entityManager->remove($paymentMethod);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_payment_method_index', [], Response::HTTP_SEE_OTHER);
    }
}
