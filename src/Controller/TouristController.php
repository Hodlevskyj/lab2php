<?php

namespace App\Controller;

use App\Entity\Tourist;
use App\Form\TouristType;
use App\Repository\TouristRepository;
use App\Service\TouristService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/tourist')]
final class TouristController extends AbstractController
{
    private TouristService $touristService;

    public function __construct(TouristService $touristService){
        $this->touristService = $touristService;
    }

    #[Route(name: 'app_tourist_index', methods: ['GET'])]
    public function index(TouristRepository $touristRepository): Response
    {
        return $this->render('tourist/index.html.twig', [
            'tourists' => $touristRepository->findAll(),
        ]);
    }

    #[Route('/new', name: 'app_tourist_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $tourist = new Tourist();
        $form = $this->createForm(TouristType::class, $tourist);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->touristService->createTourist($tourist->getFirstName(),$tourist->getLastName(),$tourist->getEmail(),$tourist->getPhone());

            return $this->redirectToRoute('app_tourist_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('tourist/new.html.twig', [
            'tourist' => $tourist,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_tourist_show', methods: ['GET'])]
    public function show(Tourist $tourist): Response
    {
        return $this->render('tourist/show.html.twig', [
            'tourist' => $tourist,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_tourist_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Tourist $tourist, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(TouristType::class, $tourist);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('app_tourist_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('tourist/edit.html.twig', [
            'tourist' => $tourist,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_tourist_delete', methods: ['POST'])]
    public function delete(Request $request, Tourist $tourist, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$tourist->getId(), $request->getPayload()->getString('_token'))) {
            $entityManager->remove($tourist);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_tourist_index', [], Response::HTTP_SEE_OTHER);
    }
}