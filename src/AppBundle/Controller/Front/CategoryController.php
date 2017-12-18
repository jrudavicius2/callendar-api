<?php

namespace AppBundle\Controller\Front;

use AppBundle\Entity\Category;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

class CategoryController extends Controller
{
    public function createAction()
    {
        return $this->render('@App/category/add.html.twig', [
            'title' => 'Create a new category',
        ]);
    }

    public function storeAction(Request $request)
    {
        $category = new Category();
        $category->setName($request->request->get('name'));

        $validator = $this->get('validator');
        $errors = $validator->validate($category);

        if (count($errors) > 0) {
            return $this->render('@App/category/add.html.twig', [
                'title' => 'Create a new category',
                'errors' => $errors,
                'category' => $category
            ]);
        }

        $em = $this->getDoctrine()->getManager();
        $em->persist($category);
        $em->flush();

        return $this->redirectToRoute('front_category_all');
    }

    public function allAction()
    {
        $categories = $this->getDoctrine()->getRepository(Category::class)->findAll();

        return $this->render('@App/category/all.html.twig', [
            'title' => 'Categories',
            'categories' => $categories,
        ]);
    }

    public function editAction($id)
    {
        $category = $this->getDoctrine()
            ->getRepository(Category::class)
            ->find($id)
        ;

        return $this->render('@App/category/edit.html.twig', [
            'title' => 'Update category',
            'category' => $category,
        ]);
    }

    public function updateAction($id, Request $request)
    {
        $category = $this->getDoctrine()
            ->getRepository(Category::class)
            ->find($id)
        ;

        $category->setName($request->request->get('name'));

        $validator = $this->get('validator');
        $errors = $validator->validate($category);

        if (count($errors) > 0) {
            return $this->render('@App/category/edit.html.twig', [
                'title' => 'Update category',
                'errors' => $errors,
                'category' => $category
            ]);
        }

        $em = $this->getDoctrine()->getManager();
        $em->flush();

        return $this->redirectToRoute('front_category_all');
    }

    public function deleteAction($id)
    {
        $category = $this->getDoctrine()
            ->getRepository(Category::class)
            ->find($id)
        ;

        $em = $this->getDoctrine()->getManager();
        $em->remove($category);
        $em->flush();

        return $this->redirectToRoute('front_category_all');
    }
}
