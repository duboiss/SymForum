<?php

namespace App\Controller\Panel;

use App\Controller\AbstractBaseController;
use App\Entity\Category;
use App\Form\CategoryType;
use App\Repository\CategoryRepository;
use Doctrine\ORM\EntityManagerInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/panel/categories", name="panel.category.")
 * @IsGranted("ROLE_ADMIN")
 */
class CategoryPanelController extends AbstractBaseController
{
    /**
     * @Route("/", name="index", methods="GET")
     */
    public function index(CategoryRepository $categoryRepository): Response
    {
        return $this->render('panel/category/index.html.twig', [
            'categories' => $categoryRepository->findAll(),
        ]);
    }

    /**
     * @Route("/add", name="add", methods={"GET", "POST"})
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

            return $this->redirectToRoute('panel.category.index');
        }

        return $this->render('panel/category/new.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}/edit", name="edit", methods={"GET", "POST"})
     */
    public function edit(Category $category, Request $request): Response
    {
        $form = $this->createForm(CategoryType::class, $category);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->getDoctrine()->getManager()->flush();

            $this->addCustomFlash('success', 'Catégorie', 'La catégorie a bien été modifiée !');

            return $this->redirectToRoute('panel.category.index');
        }

        return $this->render('panel/category/edit.html.twig', [
            'category' => $category,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}/delete", name="delete", methods="DELETE")
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
