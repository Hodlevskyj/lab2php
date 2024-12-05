<?php

namespace App\Controller;

use App\Entity\Destination;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class DestinationController extends AbstractController
{
    #[Route('/destination/create', name: 'create_destination',methods: ['POST'])]
    public function create(Request $request,EntityManagerInterface $entityManager): Response
    {
        $data=json_decode($request->getContent(),true);

        $destination = new Destination();
        $destination->setName($data['name']);
        $destination->setDescription($data['description']);
        $destination->setCountry($data['country']);
        $entityManager->persist($destination);

        $entityManager->persist($destination);
        $entityManager->flush();

        return new Response('Create destination success with id ' . $destination->getId(), Response::HTTP_CREATED);
    }

    #[Route('/destination', name: 'get_destinations', methods: ['GET'])]
    public function read(EntityManagerInterface $entityManager): Response
    {
        $destination = $entityManager->getRepository(Destination::class)->findAll();
        return $this->json($destination);
    }

    #[Route('/destination/{id}', name: 'update_destination', methods: ['PUT'])]
    public function update(int $id,Request $request, EntityManagerInterface $entityManager): Response
    {
        $destination = $entityManager->getRepository(Destination::class)->find($id);
        if(!$destination){
            return new Response(
                'Destination not found', Response::HTTP_NOT_FOUND);
        }
        $data=json_decode($request->getContent(),true);

        $destination->setName($data['name'] ?? $destination->getName());
        $destination->setDescription($data['description'] ?? $destination->getDescription());
        $destination->setCountry($data['country'] ?? $destination->getCountry());

        $entityManager->flush();

        return new Response('Update destination success with id ' . $destination->getId(), Response::HTTP_OK);
    }
    #[Route('/destination/{id}', name: 'delete_destination', methods: ['DELETE'])]
    public function delete(int $id, EntityManagerInterface $entityManager): Response
    {
        $destination = $entityManager->getRepository(Destination::class)->find($id);
        if(!$destination){
            return new Response('Destination not found', Response::HTTP_NOT_FOUND);
        }
        $entityManager->remove($destination);
        $entityManager->flush();

        return new Response('Delete destination success with id ' . $destination->getId(), Response::HTTP_OK);
    }
}
