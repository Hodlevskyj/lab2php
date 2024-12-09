<?php

namespace App\Controller;

use App\Entity\Tour;
use App\Form\TourType;
use App\Service\TourService;
use App\Repository\TourRepository;
use App\Service\TourValidatorService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Form\FormError;


#[Route('/tour')]
final class TourController extends AbstractController
{

    private TourService $tourService;
    private TourValidatorService $tourValidatorService;

    public function __construct(TourService $tourService, TourValidatorService $tourValidatorService){
        $this->tourService = $tourService;
        $this->tourValidatorService = $tourValidatorService;
    }

    #[Route(name: 'app_tour_index', methods: ['GET'])]
    public function index(TourRepository $tourRepository): Response
    {
        return $this->render('tour/index.html.twig', [
            'tours' => $tourRepository->findAll(),
        ]);
    }


    #[Route('/new', name: 'app_tour_new', methods: ['GET', 'POST'])]
    public function new(Request $request): Response
    {
        $tour = new Tour();
        $form = $this->createForm(TourType::class, $tour);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $validationErrors = $this->tourValidatorService->validateTour([
                'name' => $tour->getName(),
                'description' => $tour->getDescription(),
                'duration' => $tour->getDuration(),
                'price' => $tour->getPrice(),
            ]);

            foreach ($validationErrors as $field => $error) {
                $form->get($field)?->addError(new FormError($error));
            }

            if ($form->isValid()) {
                $this->tourService->createTour(
                    $tour->getName(),
                    $tour->getDescription(),
                    $tour->getDuration(),
                    $tour->getPrice(),

                );

                return $this->redirectToRoute('app_tour_index', [], Response::HTTP_SEE_OTHER);
            }
        }

        return $this->render('tour/new.html.twig', [
            'tour' => $tour,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_tour_show', methods: ['GET'])]
    public function show(Tour $tour): Response
    {
        return $this->render('tour/show.html.twig', [
            'tour' => $tour,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_tour_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Tour $tour, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(TourType::class, $tour);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('app_tour_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('tour/edit.html.twig', [
            'tour' => $tour,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_tour_delete', methods: ['POST'])]
    public function delete(Request $request, Tour $tour, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$tour->getId(), $request->getPayload()->getString('_token'))) {
            $entityManager->remove($tour);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_tour_index', [], Response::HTTP_SEE_OTHER);
    }
}
