<?php

namespace App\Controller;

use App\Entity\Booking;
use App\Form\BookingType;
use App\Repository\BookingRepository;
use App\Service\BookingService;
use App\Service\BookingValidatorService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Form\FormError;

#[Route('/booking')]
final class BookingController extends AbstractController
{
    private BookingService $bookingService;
    private BookingValidatorService  $bookingValidatorService;
    public function __construct(BookingService $bookingService, BookingValidatorService $bookingValidatorService){
        $this->bookingService = $bookingService;
        $this->bookingValidatorService = $bookingValidatorService;
    }
    #[Route(name: 'app_booking_index', methods: ['GET'])]
    public function index(BookingRepository $bookingRepository): Response
    {
        return $this->render('booking/index.html.twig', [
            'bookings' => $bookingRepository->findAll(),
        ]);
    }

    #[Route('/new', name: 'app_booking_new', methods: ['GET', 'POST'])]
    public function new(Request $request): Response
    {
        $booking = new Booking();
        $form = $this->createForm(BookingType::class, $booking);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $validationErrors = $this->bookingValidatorService->validateBooking([
                'touristId' => $booking->getTourist()->getId(),
                'tourId' => $booking->getTour()->getId(),
                'number_of_people' => $booking->getNumberOfPeople(),
                'totalPrice' => $booking->getTotalPrice(),
            ]);

            foreach ($validationErrors as $field => $error) {
                $form->get($field)?->addError(new FormError($error));
            }

            if ($form->isValid()) {
                $this->bookingService->createBooking(
                    $booking->getTourist(),
                    $booking->getTour(),
                    $booking->getBookingDate(),
                    $booking->getNumberOfPeople(),
                    $booking->getTotalPrice()
                );

                return $this->redirectToRoute('app_booking_index', [], Response::HTTP_SEE_OTHER);
            }
        }

        return $this->render('booking/new.html.twig', [
            'booking' => $booking,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_booking_show', methods: ['GET'])]
    public function show(Booking $booking): Response
    {
        return $this->render('booking/show.html.twig', [
            'booking' => $booking,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_booking_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Booking $booking, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(BookingType::class, $booking);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('app_booking_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('booking/edit.html.twig', [
            'booking' => $booking,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_booking_delete', methods: ['POST'])]
    public function delete(Request $request, Booking $booking, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$booking->getId(), $request->getPayload()->getString('_token'))) {
            $entityManager->remove($booking);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_booking_index', [], Response::HTTP_SEE_OTHER);
    }
}
