<?php

namespace App\Controller;

use App\Entity\Destination;
use App\Form\DestinationType;
use App\Repository\DestinationRepository;
use App\Service\DestinationService;
use App\Service\DestinationValidatorService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Form\FormError;

#[Route('/destination')]
final class DestinationController extends AbstractController
{
    private DestinationService $destinationService;
    private DestinationValidatorService $destinationValidatorService;
    public function __construct(DestinationService $destinationService, DestinationValidatorService $destinationValidatorService){
        $this->destinationService = $destinationService;
        $this->destinationValidatorService = $destinationValidatorService;
    }

    #[Route(name: 'app_destination_index', methods: ['GET'])]
    public function index(DestinationRepository $destinationRepository, Request $request): Response
    {
        $itemsPerPage = (int)$request->query->get('itemsPerPage', 2);
        $page = (int)$request->query->get('page', 1);

        $paginationData = $destinationRepository->getPaginatedDestinations($itemsPerPage, $page);

        return $this->render('destination/index.html.twig', [
            'destinations' => $paginationData['destinations'],
            'totalItems' => $paginationData['totalItems'],
            'totalPages' => $paginationData['totalPages'],
            'currentPage' => $page,
            'itemsPerPage' => $itemsPerPage,
        ]);
    }

    #[Route('/new', name: 'app_destination_new', methods: ['GET', 'POST'])]
    public function new(Request $request): Response
    {
        $destination = new Destination();
        $form = $this->createForm(DestinationType::class, $destination);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // Виконуємо валідацію даних
            $validationErrors = $this->destinationValidatorService->validateDestination([
                'name' => $destination->getName(),
                'description' => $destination->getDescription(),
                'country' => $destination->getCountry(),
            ]);

            // Додаємо помилки в форму, якщо вони є
            foreach ($validationErrors as $field => $error) {
                $form->get($field)?->addError(new FormError($error));
            }

            // Якщо форма є валідною, зберігаємо дані
            if ($form->isValid()) {
                $this->destinationService->createDestination(
                    $destination->getName(),
                    $destination->getDescription(),
                    $destination->getCountry()
                );

                return $this->redirectToRoute('app_destination_index', [], Response::HTTP_SEE_OTHER);
            }
        }

        return $this->render('destination/new.html.twig', [
            'destination' => $destination,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_destination_show', methods: ['GET'])]
    public function show(Destination $destination): Response
    {
        return $this->render('destination/show.html.twig', [
            'destination' => $destination,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_destination_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Destination $destination, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(DestinationType::class, $destination);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('app_destination_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('destination/edit.html.twig', [
            'destination' => $destination,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_destination_delete', methods: ['POST'])]
    public function delete(Request $request, Destination $destination, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$destination->getId(), $request->getPayload()->getString('_token'))) {
            $entityManager->remove($destination);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_destination_index', [], Response::HTTP_SEE_OTHER);
    }
}
