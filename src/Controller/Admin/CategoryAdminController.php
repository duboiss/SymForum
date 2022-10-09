<?php

declare(strict_types=1);

namespace App\Controller\Admin;

use App\Controller\AbstractBaseController;
use App\Entity\Category;
use App\Form\CategoryType;
use App\Repository\CategoryRepository;
use Doctrine\ORM\EntityManagerInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\Encoder\DecoderInterface;
use Symfony\Contracts\Translation\TranslatorInterface;

#[IsGranted('ROLE_ADMIN')]
#[Route(path: '/admin/categories', name: 'admin.category.')]
class CategoryAdminController extends AbstractBaseController
{
    public function __construct(private readonly RequestStack $requestStack, private DecoderInterface $decoder, private readonly TranslatorInterface $translator)
    {
        parent::__construct($requestStack, $this->decoder);
    }

    #[Route(path: '/', name: 'index', methods: ['GET'])]
    public function index(CategoryRepository $categoryRepository): Response
    {
        return $this->render('admin/category/index.html.twig', [
            'categories' => $categoryRepository->findAll(),
        ]);
    }

    #[Route(path: '/add', name: 'add', methods: ['GET', 'POST'])]
    public function add(Request $request, EntityManagerInterface $em): Response
    {
        $form = $this->createForm(CategoryType::class);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $category = $form->getData();
            $em->persist($category);
            $em->flush();

            $this->addCustomFlash('success', $this->translator->trans('Category'), $this->translator->trans('The category has been added'));

            return $this->redirectToRoute('admin.category.index');
        }

        return $this->renderForm('admin/category/new.html.twig', [
            'form' => $form,
        ]);
    }

    #[Route(path: '/{uuid}/edit', name: 'edit', methods: ['GET', 'POST'])]
    public function edit(Category $category, Request $request, EntityManagerInterface $em): Response
    {
        $form = $this->createForm(CategoryType::class, $category);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $em->flush();
            $this->addCustomFlash('success', $this->translator->trans('Category'), $this->translator->trans('The category has been edited'));

            return $this->redirectToRoute('admin.category.index');
        }

        return $this->renderForm('admin/category/edit.html.twig', [
            'category' => $category,
            'form' => $form,
        ]);
    }

    #[Route(path: '/{uuid}/delete', name: 'delete', methods: ['DELETE'])]
    public function delete(Category $category, EntityManagerInterface $em): Response
    {
        if (count($category->getForums()) > 0) {
            return $this->json([
                'message' => $this->translator->trans('The category cannot be deleted, it contains forums'),
            ], 403);
        }

        $em->remove($category);
        $em->flush();

        return $this->json(['message' => $this->translator->trans('The category has been deleted')]);
    }
}
