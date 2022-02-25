<?php

namespace App\Controller;

use App\Entity\BankOperationCategory;
use App\Form\BankOperationCategoryType;
use App\Repository\BankOperationCategoryRepository;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\NonUniqueResultException;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/budget-category')]
class BankOperationCategoryController extends AbstractController
{
    private BankOperationCategoryRepository $bankOperationCategoryRepository;

    public function __construct(BankOperationCategoryRepository $repository)
    {
        $this->bankOperationCategoryRepository = $repository;
    }

    #[Route('/', name: 'bank_operation_category_index', methods: ['GET'])]
    public function index(BankOperationCategoryRepository $bankOperationCategoryRepository): Response
    {
        return $this->render('bank_operation_category/index.html.twig', [
            'bank_operation_categories' => $bankOperationCategoryRepository->findAll(),
        ]);
    }

    #[Route('/new', name: 'bank_operation_category_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $bankOperationCategory = new BankOperationCategory();
        $form = $this->createForm(BankOperationCategoryType::class, $bankOperationCategory);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($bankOperationCategory);
            $entityManager->flush();

            return $this->redirectToRoute('bank_operation_category_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('bank_operation_category/new.html.twig', [
            'bank_operation_category' => $bankOperationCategory,
            'form' => $form,
        ]);
    }

    #[Route('/{slug}', name: 'bank_operation_category_show', methods: ['GET'])]
    public function show(string $slug): Response
    {
        try {
            $bankOperationCategory = $this->bankOperationCategoryRepository->getOneWithBankOperationsBySlug($slug);
        } catch (\Exception $e) {
            throw $this->createNotFoundException($e->getMessage());
        }

        if (!$bankOperationCategory) {
            throw $this->createNotFoundException(sprintf(
                'The bank operation category with slug "%s" does not exist',
                $slug
            ));
        }

        return $this->render('bank_operation_category/show.html.twig', [
            'bank_operation_category' => $bankOperationCategory,
        ]);
    }

    #[Route('/{slug}/edit', name: 'bank_operation_category_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, BankOperationCategory $bankOperationCategory, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(BankOperationCategoryType::class, $bankOperationCategory);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('bank_operation_category_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('bank_operation_category/edit.html.twig', [
            'bank_operation_category' => $bankOperationCategory,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'bank_operation_category_delete', methods: ['POST'])]
    public function delete(Request $request, BankOperationCategory $bankOperationCategory, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$bankOperationCategory->getId(), $request->request->get('_token'))) {
            $entityManager->remove($bankOperationCategory);
            $entityManager->flush();
        }

        return $this->redirectToRoute('bank_operation_category_index', [], Response::HTTP_SEE_OTHER);
    }
}
