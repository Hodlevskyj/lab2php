<?php

namespace App\Controller;

use App\Entity\Guide;
use App\Form\GuideType;
use App\Repository\GuideRepository;
use App\Service\GuideService;
use App\Service\GuideValidatorService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Form\FormError;

#[Route('/guide')]
final class GuideController extends AbstractController
{
    private GuideService $guideService;
    private GuideValidatorService $guideValidator;

    public function __construct(GuideService $guideService, GuideValidatorService $guideValidator){
        $this->guideService = $guideService;
        $this->guideValidator = $guideValidator;
    }

    #[Route(name: 'app_guide_index', methods: ['GET'])]
    public function index(GuideRepository $guideRepository,Request $request): Response
    {
        $itemsPerPage = (int)$request->query->get('itemsPerPage', 1);
        $page = (int)$request->query->get('page', 1);

        $paginationData = $guideRepository->getPaginatedGuides($itemsPerPage, $page);

        return $this->render('guide/index.html.twig', [
            'guides' => $paginationData['guides'],
            'totalItems' => $paginationData['totalItems'],
            'totalPages' => $paginationData['totalPages'],
            'currentPage' => $page,
            'itemsPerPage' => $itemsPerPage,
        ]);
    }

    #[Route('/new', name: 'app_guide_new', methods: ['GET', 'POST'])]
    public function new(Request $request): Response
    {
        $guide = new Guide();
        $form = $this->createForm(GuideType::class, $guide);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $validationErrors = $this->guideValidator->validateGuide([
                'first_name' => $guide->getFirstName(),
                'last_name' => $guide->getLastName(),
                'email' => $guide->getEmail(),
                'phone' => $guide->getPhone(),
                'language' => $guide->getLanguage(),
                'bio' => $guide->getBio(),
            ]);

            foreach ($validationErrors as $field => $error) {
                $form->get($field)?->addError(new FormError($error));
            }

            if ($form->isValid()) {
                $this->guideService->createGuide(
                    $guide->getFirstName(),
                    $guide->getLastName(),
                    $guide->getEmail(),
                    $guide->getPhone(),
                    $guide->getLanguage(),
                    $guide->getBio()
                );

                return $this->redirectToRoute('app_guide_index', [], Response::HTTP_SEE_OTHER);
            }
        }

        return $this->render('guide/new.html.twig', [
            'guide' => $guide,
            'form' => $form,
        ]);
    }


    #[Route('/{id}', name: 'app_guide_show', methods: ['GET'])]
    public function show(Guide $guide): Response
    {
        return $this->render('guide/show.html.twig', [
            'guide' => $guide,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_guide_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Guide $guide, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(GuideType::class, $guide);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('app_guide_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('guide/edit.html.twig', [
            'guide' => $guide,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_guide_delete', methods: ['POST'])]
    public function delete(Request $request, Guide $guide, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$guide->getId(), $request->getPayload()->getString('_token'))) {
            $entityManager->remove($guide);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_guide_index', [], Response::HTTP_SEE_OTHER);
    }
}
