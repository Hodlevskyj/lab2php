<?php

namespace App\Controller;

use App\Entity\Tour;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\HttpFoundation\Request;



class TourController extends AbstractController
{
    #[Route('/tour/create', name: 'create_tour',methods: ['POST'])]
    public function create(Request $request, EntityManagerInterface $entityManager): Response
    {
        $data=json_decode($request->getContent(),true);
        $tour = new Tour();
        $tour->setName($data['name']);
        $tour->setDescription($data['description']);
        $tour->setDuration($data['duration']);
        $tour->setPrice($data['price']);

        // Повідомляємо EntityManager, що потрібно зберегти цей об'єкт
        $entityManager->persist($tour);

        // Фактично зберігаємо зміни в базу даних
        $entityManager->flush();

        return new Response('Created tour with ID ' . $tour->getId());
    }

    #[Route('/tour', name: 'tour_read', methods: ['GET'])]
    public function read(EntityManagerInterface $entityManager): Response
    {
        $tours = $entityManager->getRepository(Tour::class)->findAll();
        return $this->json($tours);
    }


    #[Route('/tour/delete/{id}', name:'delete_tour',methods: ['DELETE'])]
    public function delete(int $id, EntityManagerInterface $entityManager): Response
    {
        $tour = $entityManager->getRepository(Tour::class)->find($id);

        if (!$tour) {
            return new Response('Tour not found',Response::HTTP_NOT_FOUND);
        }

        $entityManager->remove($tour);
        $entityManager->flush();

        return new Response('Deleted tour with ID ' . $id,Response::HTTP_OK);
    }

    #[Route('/tour/update/{id}', name:'update_tour',methods: ['PUT'])]
    public function update(int $id,Request $request, EntityManagerInterface $entityManager): Response
    {
        $tour = $entityManager->getRepository(Tour::class)->find($id);

        if(!$tour){
            return new Response('Tour not found');
        }

        $data=json_decode($request->getContent(),true);

        $tour->setName($data['name'] ?? $tour->getName());
        $tour->setDescription($data['description'] ?? $tour->getDescription());
        $tour->setDuration($data['duration'] ?? $tour->getDuration());
        $tour->setPrice($data['price'] ?? $tour->getPrice());

        $entityManager->flush();

        return new Response('Updated tour with ID ' . $id);
    }

}
