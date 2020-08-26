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
     * @Route("/categories", name="panel.categories", methods="GET")
     */
    public function index(CategoryRepository $categoryRepository): Response
    {
        return $this->render('panel/category/index.html.twig', [
            'categories' => $categoryRepository->findAll(),
        ]);
    }

    /**
     * @Route("/categories/add", name="panel.category.add", methods={"GET", "POST"})
     */
    public function new(Request $request, EntityManagerInterface $em): Response
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

        return $this->render('panel/category/new.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/categories/{id}/edit", name="panel.category.edit", methods={"GET", "POST"})
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

        return $this->render('panel/category/edit.html.twig', [
            'category' => $category,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/categories/{id}/delete", name="panel.category.delete", methods="DELETE")
     */
    public function delete(Category $category, EntityManagerInterface $em): Response
    {
        if (count($category->getForums()) > 0) {
            return $this->json([
                'message' => 'Impossible de supprimer la catégorie, elle contient des forums !',
            ], 403);
        }

        $em->remove($category);
        $em->flush();

        return $this->json(['message' => 'La catégorie a bien été supprimée !']);
    }
}
