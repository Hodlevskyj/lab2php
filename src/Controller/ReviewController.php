<?php

namespace App\Controller;

use App\Entity\Review;
use App\Form\ReviewType;
use App\Repository\ReviewRepository;
use App\Service\ReviewService;
use App\Service\ReviewValidatorService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Form\FormError;

#[Route('/review')]
final class ReviewController extends AbstractController
{
    private ReviewService $reviewService;
    private ReviewValidatorService $reviewValidatorService;

    public function __construct(ReviewService $reviewService, ReviewValidatorService $reviewValidatorService){
        $this->reviewService = $reviewService;
        $this->reviewValidatorService = $reviewValidatorService;
    }

    #[Route(name: 'app_review_index', methods: ['GET'])]
    public function index(ReviewRepository $reviewRepository,Request $request): Response
    {
        $itemsPerPage = (int)$request->query->get('itemsPerPage', 2);
        $page = (int)$request->query->get('page', 1);

        $paginationData = $reviewRepository->getPaginatedReviews($itemsPerPage, $page);

        return $this->render('review/index.html.twig', [
            'reviews' => $paginationData['reviews'],
            'totalItems' => $paginationData['totalItems'],
            'totalPages' => $paginationData['totalPages'],
            'currentPage' => $page,
            'itemsPerPage' => $itemsPerPage,
        ]);
    }

    #[Route('/new', name: 'app_review_new', methods: ['GET', 'POST'])]
    public function new(Request $request): Response
    {
        $review = new Review();
        $form = $this->createForm(ReviewType::class, $review);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $validationErrors = $this->reviewValidatorService->validateReview([
                'rating' => $review->getRating(),
                'comment' => $review->getComment(),
            ]);

            foreach ($validationErrors as $field => $error) {
                $form->get($field)?->addError(new FormError($error));
            }

            if ($form->isValid()) {
                $this->reviewService->createReview(
                    $review->getTourist(),
                    $review->getTour(),
                    $review->getRating(),
                    $review->getComment()
                );

                return $this->redirectToRoute('app_review_index', [], Response::HTTP_SEE_OTHER);
            }
        }

        return $this->render('review/new.html.twig', [
            'review' => $review,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_review_show', methods: ['GET'])]
    public function show(Review $review): Response
    {
        return $this->render('review/show.html.twig', [
            'review' => $review,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_review_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Review $review, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(ReviewType::class, $review);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('app_review_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('review/edit.html.twig', [
            'review' => $review,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_review_delete', methods: ['POST'])]
    public function delete(Request $request, Review $review, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$review->getId(), $request->getPayload()->getString('_token'))) {
            $entityManager->remove($review);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_review_index', [], Response::HTTP_SEE_OTHER);
    }
}
