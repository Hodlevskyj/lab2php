<?php

namespace App\Controller;

use App\Entity\Tourist;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Routing\Attribute\Route;

class TouristController extends AbstractController
{
    #[Route('/tourist/create', name: 'create_tourist',methods: ['POST'])]
    public function create(Request $request,EntityManagerInterface $entityManager): Response
    {
        $data=json_decode($request->getContent(),true);

        $tourist = new Tourist();
        $tourist->setFirstName($data['name']);
        $tourist->setLastName($data['surname']);
        $tourist->setEmail($data['email']);
        $tourist->setPhone($data['phone']);
        $tourist->setRegistrationDate(new \DateTime());

        $entityManager->persist($tourist);
        $entityManager->flush();

        return new Response('Create tourist success with id ' . $tourist->getId(), Response::HTTP_CREATED);
    }

    #[Route('/tourist', name: 'get_tourists', methods: ['GET'])]
    public function read(EntityManagerInterface $entityManager): Response
    {
        $tourist = $entityManager->getRepository(Tourist::class)->findAll();
        return $this->json($tourist, Response::HTTP_OK);
    }

    #[Route('/tourist/{id}', name: 'update_tourist', methods: ['PUT'])]
    public function update(int $id,Request $request, EntityManagerInterface $entityManager): Response
    {
        $tourist = $entityManager->getRepository(Tourist::class)->find($id);
        if(!$tourist){
            return new Response(
                'Tourist not found', Response::HTTP_NOT_FOUND);
        }
        $data=json_decode($request->getContent(),true);

        $tourist->setFirstName($data['name'] ?? $tourist->getFirstName());
        $tourist->setLastName($data['surname'] ?? $tourist->getLastName());
        $tourist->setEmail($data['email'] ?? $tourist->getEmail());
        $tourist->setPhone($data['phone'] ?? $tourist->getPhone());

        $entityManager->persist($tourist);
        $entityManager->flush();

        return new Response('Update Tourist success with id ' . $tourist->getId(), Response::HTTP_OK);
    }

    #[Route('/tourist/{id}', name: 'delete_tourist', methods: ['DELETE'])]
    public function delete(int $id, EntityManagerInterface $entityManager): Response
    {
        $tourist = $entityManager->getRepository(Tourist::class)->find($id);
        if(!$tourist){
            return new Response('Tourist not found', Response::HTTP_NOT_FOUND);
        }
        $entityManager->remove($tourist);
        $entityManager->flush();

        return new Response('Delete tourist success with id ' . $tourist->getId(), Response::HTTP_OK);
    }
}
