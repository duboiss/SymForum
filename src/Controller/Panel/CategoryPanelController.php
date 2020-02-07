<?php

namespace App\Controller\Panel;

use App\Controller\BaseController;
use App\Entity\Category;
use App\Form\CategoryType;
use App\Repository\CategoryRepository;
use Doctrine\ORM\EntityManagerInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/panel")
 * @IsGranted("ROLE_ADMIN")
 */
class CategoryPanelController extends BaseController
{
    /**
     * @Route("/categories", name="panel.categories")
     * @param CategoryRepository $categoryRepository
     * @return Response
     */
    public function index(CategoryRepository $categoryRepository): Response
    {
        $categories = $categoryRepository->findAll();

        return $this->render('panel/categories/index.html.twig', [
            'categories' => $categories
        ]);
    }

    /**
     * @Route("/categories/add", name="panel.category.add")
     * @param Request $request
     * @param EntityManagerInterface $em
     * @return Response
     */
    public function add(Request $request, EntityManagerInterface $em): Response
    {
        $form = $this->createForm(CategoryType::class);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $category = $form->getData();
            $em->persist($category);
            $em->flush();

            $this->addCustomFlash('success', 'Catégorie', 'La catégorie a été ajoutée !');
            return $this->redirectToRoute('panel.categories');
        }

        return $this->render('panel/categories/new.html.twig', [
            'form' => $form->createView()
        ]);
    }

    /**
     * @Route("/categories/{id}/edit", name="panel.category.edit")
     * @param Category $category
     * @param Request $request
     * @return Response
     */
    public function edit(Category $category, Request $request): Response
    {
        $form = $this->createForm(CategoryType::class, $category);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->getDoctrine()->getManager()->flush();

            $this->addCustomFlash('success', 'Catégorie', 'La catégorie a bien été modifiée !');
            return $this->redirectToRoute('panel.categories');
        }

        return $this->render('panel/categories/edit.html.twig', [
            'category' => $category,
            'form' => $form->createView()
        ]);
    }

    /**
     * @Route("/categories/{id}/delete", name="panel.category.delete", methods={"DELETE"})
     * @param Category $category
     * @param EntityManagerInterface $em
     * @return Response
     */
    public function delete(Category $category, EntityManagerInterface $em): Response
    {
        if (count($category->getForums()) > 0) {
            return $this->json([
                'message' => 'Impossible de supprimer la catégorie, elle contient des forums !'
            ], 403);
        }

        $em->remove($category);
        $em->flush();

        return $this->json([
            'message' => 'La catégorie a bien été supprimée !'
        ], 200);
    }
}
