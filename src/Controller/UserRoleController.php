<?php

namespace App\Controller;

use App\Entity\UserRole;
use App\Form\UserRoleType;
use App\Repository\UserRoleRepository;
use App\Service\UserRoleService;
use App\Service\UserRoleValidatorService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Form\FormError;

#[Route('/user_role')]
final class UserRoleController extends AbstractController
{
    private UserRoleService $userRoleService;
    private UserRoleValidatorService $userRoleValidatorService;
    public function __construct(UserRoleService $userRoleService, UserRoleValidatorService $userRoleValidatorService){
        $this->userRoleService = $userRoleService;
        $this->userRoleValidatorService = $userRoleValidatorService;
    }

    #[Route(name: 'app_user_role_index', methods: ['GET'])]
    public function index(UserRoleRepository $userRoleRepository): Response
    {
        return $this->render('user_role/index.html.twig', [
            'user_roles' => $userRoleRepository->findAll(),
        ]);
    }

    #[Route('/new', name: 'app_user_role_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $userRole = new UserRole();
        $form = $this->createForm(UserRoleType::class, $userRole);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $validationErrors = $this->userRoleValidatorService->validateUserRole([
                'role_name' => $userRole->getRoleName(),
            ]);

            foreach ($validationErrors as $field => $error) {
                $form->get($field)?->addError(new FormError($error));
            }

            if ($form->isValid()) {
                $this->userRoleService->createUserRole($userRole->getRoleName());

                return $this->redirectToRoute('app_user_role_index', [], Response::HTTP_SEE_OTHER);
            }
        }

        return $this->render('user_role/new.html.twig', [
            'user_role' => $userRole,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_user_role_show', methods: ['GET'])]
    public function show(UserRole $userRole): Response
    {
        return $this->render('user_role/show.html.twig', [
            'user_role' => $userRole,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_user_role_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, UserRole $userRole, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(UserRoleType::class, $userRole);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('app_user_role_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('user_role/edit.html.twig', [
            'user_role' => $userRole,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_user_role_delete', methods: ['POST'])]
    public function delete(Request $request, UserRole $userRole, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$userRole->getId(), $request->getPayload()->getString('_token'))) {
            $entityManager->remove($userRole);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_user_role_index', [], Response::HTTP_SEE_OTHER);
    }
}
