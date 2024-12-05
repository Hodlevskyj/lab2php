<?php

namespace App\Controller;

use App\Entity\Guide;
use App\Entity\Tour;
use App\Entity\TourGuide;
use App\Repository\GuideRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class GuideController extends AbstractController
{
    #[Route('/guide/new', name: 'guide_new',methods: ['POST'])]
    public function create(Request $request, EntityManagerInterface $entityManager): Response
    {
        $data=json_decode($request->getContent(), true);

        if(!isset($data['first_name'],$data['last_name'],$data['email'])){
            return new Response('Invalid data', Response::HTTP_BAD_REQUEST);
        }
        $guide = new Guide();
        $guide->setFirstName($data['first_name']);
        $guide->setLastName($data['last_name']);
        $guide->setEmail($data['email']);
        $guide->setPhone($data['phone'] ?? null);
        $guide->setLanguage($data['language']);
        $guide->setBio($data['bio']);

        // Якщо передані тури, прив'язуємо їх через TourGuide
        if (isset($data['tours'])) {
            foreach ($data['tours'] as $tourId) {
                $tour = $entityManager->getRepository(Tour::class)->find($tourId);

                if ($tour) {
                    $tourGuide = new TourGuide();
                    $tourGuide->setGuide($guide);
                    $tourGuide->setTour($tour);
                    $entityManager->persist($tourGuide);
                }
            }
        }
        $entityManager->persist($guide);
        $entityManager->flush();



        return new Response('Created guide with ID ' .$guide->getId(), Response::HTTP_CREATED);
    }

    #[Route('/guide', name: 'guide_index', methods: ['GET'])]
    public function index(GuideRepository $guideRepository): Response
    {
        $guides = $guideRepository->findAll();

        $result = [];
        foreach ($guides as $guide) {
            $tours = $guide->getTourGuides()->map(function ($tourGuide) {
                return $tourGuide->getTour()->getId(); // Повертаємо ID турів
            })->toArray();

            $result[] = [
                    'id' => $guide->getId(),
                    'first_name' => $guide->getFirstName(),
                    'last_name' => $guide->getLastName(),
                    'email' => $guide->getEmail(),
                    'language' => $guide->getLanguage(),
                    'bio' => $guide->getBio(),
                    'tours' => $tours,
            ];
        }

        return $this->json($result);
    }

    #[Route('/guide/{id}', name: 'guide_show', methods: ['GET'])]
    public function show(int $id, GuideRepository $guideRepository): Response
    {
        $guide = $guideRepository->find($id);

        if (!$guide) {
            return new Response('Guide not found', Response::HTTP_NOT_FOUND);
        }

        $tours = $guide->getTourGuides()->map(function ($tourGuide) {
            return $tourGuide->getTour()->getId(); // Повертаємо ID турів
        })->toArray();

        $result = [
            'id' => $guide->getId(),
            'first_name' => $guide->getFirstName(),
            'last_name' => $guide->getLastName(),
            'email' => $guide->getEmail(),
            'language' => $guide->getLanguage(),
            'bio' => $guide->getBio(),
            'tours' => $tours,
        ];

        return $this->json($result);
    }

    #[Route('/guide/{id}/edit', name: 'guide_edit', methods: ['PUT'])]
    public function update(int $id, Request $request, EntityManagerInterface $entityManager, GuideRepository $guideRepository): Response
    {
        $guide = $guideRepository->find($id);

        if (!$guide) {
            return new Response('Guide not found', Response::HTTP_NOT_FOUND);
        }

        $data = json_decode($request->getContent(), true);

        if (isset($data['first_name'])) {
            $guide->setFirstName($data['first_name']);
        }
        if (isset($data['last_name'])) {
            $guide->setLastName($data['last_name']);
        }
        if (isset($data['email'])) {
            $guide->setEmail($data['email']);
        }
        if (isset($data['phone'])) {
            $guide->setPhone($data['phone']);
        }
        if (isset($data['language'])) {
            $guide->setLanguage($data['language']);
        }
        if (isset($data['bio'])) {
            $guide->setBio($data['bio']);
        }

        // Оновлення турів
        if (isset($data['tours'])) {
            foreach ($guide->getTourGuides() as $tourGuide) {
                $entityManager->remove($tourGuide); // Видаляємо старі записи
            }
            foreach ($data['tours'] as $tourId) {
                $tour = $entityManager->getRepository(Tour::class)->find($tourId);
                if ($tour) {
                    $tourGuide = new TourGuide();
                    $tourGuide->setGuide($guide);
                    $tourGuide->setTour($tour);
                    $entityManager->persist($tourGuide);
                }
            }
        }

        $entityManager->flush();

        return new Response('Updated guide with ID ' . $guide->getId());
    }

    #[Route('/guide/{id}', name: 'guide_delete', methods: ['DELETE'])]
    public function delete(int $id, EntityManagerInterface $entityManager, GuideRepository $guideRepository): Response
    {
        $guide = $guideRepository->find($id);

        if (!$guide) {
            return new Response('Guide not found', Response::HTTP_NOT_FOUND);
        }

        // Видаляємо всі зв'язки з турами
        foreach ($guide->getTourGuides() as $tourGuide) {
            $entityManager->remove($tourGuide);
        }

        $entityManager->remove($guide);
        $entityManager->flush();

        return new Response('Deleted guide with ID ' . $id, Response::HTTP_OK);
    }




}
