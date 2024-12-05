<?php

namespace App\Controller;

use App\Entity\Booking;
use App\Entity\Tour;
use App\Entity\Tourist;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;


class BookingController extends AbstractController
{
    #[Route('/booking/create', name: 'create_booking', methods: ['POST'])]
    public function create(Request $request, EntityManagerInterface $entityManager): Response
    {
        $data = json_decode($request->getContent(), true);

        // Перевірка, чи всі необхідні поля надані
        if (!isset($data['tourist_id'], $data['tour_id'], $data['booking_date'], $data['number_of_people'], $data['total_price'])) {
            return new Response('Missing required fields', Response::HTTP_BAD_REQUEST);
        }

        // Пошук туриста та туру за їх ID
        $tourist = $entityManager->getRepository(Tourist::class)->find($data['tourist_id']);
        $tour = $entityManager->getRepository(Tour::class)->find($data['tour_id']);

        if (!$tourist || !$tour) {
            return new Response('Tourist or tour not found', Response::HTTP_NOT_FOUND);
        }

        // Створення нового бронювання
        $booking = new Booking();
        $booking->setTourist($tourist);
        $booking->setTour($tour);
        $booking->setBookingDate(new \DateTime($data['booking_date']));
        $booking->setNumberOfPeople($data['number_of_people']);
        $booking->setTotalPrice($data['total_price']);

        // Збереження нового бронювання в базу
        $entityManager->persist($booking);
        $entityManager->flush();

        return new Response('Booking created with ID: ' . $booking->getId(), Response::HTTP_CREATED);
    }
    #[Route('/booking', name: 'get_bookings', methods: ['GET'])]
    public function getAllBookings(EntityManagerInterface $entityManager): Response
    {
        // Отримання всіх бронювань
        $bookings = $entityManager->getRepository(Booking::class)->findAll();

        // Підготовка результату для відповіді
        $result = [];
        foreach ($bookings as $booking) {
            $result[] = [
                'id' => $booking->getId(),
                'tourist' => $booking->getTourist()->getId(),
                'tour' => $booking->getTour()->getId(),
                'booking_date' => $booking->getBookingDate()->format('Y-m-d H:i:s'),
                'number_of_people' => $booking->getNumberOfPeople(),
                'total_price' => $booking->getTotalPrice(),
            ];
        }

        return $this->json($result, Response::HTTP_OK);
    }

    #[Route('/booking/{id}', name: 'get_booking', methods: ['GET'])]
    public function getOneBooking(int $id, EntityManagerInterface $entityManager): Response
    {
        // Отримання конкретного бронювання за ID
        $booking = $entityManager->getRepository(Booking::class)->find($id);

        if (!$booking) {
            return new Response('Booking not found', Response::HTTP_NOT_FOUND);
        }

        return $this->json([
            'id' => $booking->getId(),
            'tourist' => $booking->getTourist()->getId(),
            'tour' => $booking->getTour()->getId(),
            'booking_date' => $booking->getBookingDate()->format('Y-m-d H:i:s'),
            'number_of_people' => $booking->getNumberOfPeople(),
            'total_price' => $booking->getTotalPrice(),
        ]);
    }
    #[Route('/booking/{id}/update', name: 'update_booking', methods: ['PUT'])]
    public function update(int $id, Request $request, EntityManagerInterface $entityManager): Response
    {
        // Пошук бронювання за ID
        $booking = $entityManager->getRepository(Booking::class)->find($id);

        if (!$booking) {
            return new Response('Booking not found', Response::HTTP_NOT_FOUND);
        }

        // Отримуємо дані для оновлення
        $data = json_decode($request->getContent(), true);

        $booking->setBookingDate(new \DateTime($data['booking_date'] ?? $booking->getBookingDate()->format('Y-m-d H:i:s')));
        $booking->setNumberOfPeople($data['number_of_people'] ?? $booking->getNumberOfPeople());
        $booking->setTotalPrice($data['total_price'] ?? $booking->getTotalPrice());

        // Збереження змін
        $entityManager->flush();

        return new Response('Booking updated successfully', Response::HTTP_OK);
    }

    #[Route('/booking/{id}/delete', name: 'delete_booking', methods: ['DELETE'])]
    public function delete(int $id, EntityManagerInterface $entityManager): Response
    {
        // Пошук бронювання за ID
        $booking = $entityManager->getRepository(Booking::class)->find($id);

        if (!$booking) {
            return new Response('Booking not found', Response::HTTP_NOT_FOUND);
        }

        // Видалення бронювання
        $entityManager->remove($booking);
        $entityManager->flush();

        return new Response('Booking deleted successfully', Response::HTTP_OK);
    }



}
