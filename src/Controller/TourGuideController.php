<?php

namespace App\Controller;

use App\Entity\TourGuide;
use App\Form\TourGuideType;
use App\Repository\TourGuideRepository;
use App\Service\TourGuideService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;


#[Route('/tour_guide')]
final class TourGuideController extends AbstractController
{
    private TourGuideService $tourGuideService;

    public function __construct(TourGuideService $tourGuideService){
        $this->tourGuideService = $tourGuideService;
    }

    #[Route(name: 'app_tour_guide_index', methods: ['GET'])]
    public function index(TourGuideRepository $tourGuideRepository,Request $request,): Response
    {
        $itemsPerPage = (int)$request->query->get('itemsPerPage', 2);
        $page = (int)$request->query->get('page', 1);

        $paginationData = $tourGuideRepository->getPaginatedTourGuides($itemsPerPage, $page);

        return $this->render('tour_guide/index.html.twig', [
            'tour_guides' => $paginationData['tourGuides'],
            'totalItems' => $paginationData['totalItems'],
            'totalPages' => $paginationData['totalPages'],
            'currentPage' => $page,
            'itemsPerPage' => $itemsPerPage,
        ]);
    }

    #[Route('/new', name: 'app_tour_guide_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $tourGuide = new TourGuide();
        $form = $this->createForm(TourGuideType::class, $tourGuide);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->tourGuideService->createTourGuide($tourGuide->getTour(),$tourGuide->getGuide());

            return $this->redirectToRoute('app_tour_guide_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('tour_guide/new.html.twig', [
            'tour_guide' => $tourGuide,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_tour_guide_show', methods: ['GET'])]
    public function show(TourGuide $tourGuide): Response
    {
        return $this->render('tour_guide/show.html.twig', [
            'tour_guide' => $tourGuide,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_tour_guide_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, TourGuide $tourGuide, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(TourGuideType::class, $tourGuide);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('app_tour_guide_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('tour_guide/edit.html.twig', [
            'tour_guide' => $tourGuide,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_tour_guide_delete', methods: ['POST'])]
    public function delete(Request $request, TourGuide $tourGuide, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$tourGuide->getId(), $request->getPayload()->getString('_token'))) {
            $entityManager->remove($tourGuide);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_tour_guide_index', [], Response::HTTP_SEE_OTHER);
    }
}
