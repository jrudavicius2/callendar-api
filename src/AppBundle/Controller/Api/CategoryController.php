<?php

namespace AppBundle\Controller\Api;

use AppBundle\Entity\Category;
use FOS\RestBundle\Controller\Annotations as Rest;
use FOS\RestBundle\Controller\FOSRestController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class CategoryController extends FOSRestController
{
    /**
     * @Rest\View
     *
     * @param Request $request
     * @return Category
     * @throws \LogicException
     * @throws \Symfony\Component\HttpKernel\Exception\HttpException
     */
    public function createAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $data = json_decode($request->getContent(), true);

        $category = new Category();
        $category->setName($data['name'] ?: '');

        $validator = $this->get('validator');
        $errors = $validator->validate($category);

        if (count($errors) > 0) {
            throw new HttpException(400, $errors[0]->getMessage());
        }

        $em->persist($category);
        $em->flush();

        return $category;
    }

    /**
     * @Rest\View
     *
     * @return Category[]
     * @throws \LogicException
     */
    public function allAction()
    {
        return $this->getDoctrine()
            ->getRepository(Category::class)
            ->findAll()
        ;
    }

    /**
     * @Rest\View
     *
     * @param $id
     * @return Category|null
     * @throws \Symfony\Component\HttpKernel\Exception\NotFoundHttpException
     * @throws \LogicException
     */
    public function getAction($id)
    {
        $category = $this->getDoctrine()
            ->getRepository(Category::class)
            ->find($id);

        if (!$category instanceof Category) {
            throw new NotFoundHttpException('Category not found');
        }

        return $category;
    }

    /**
     * @Rest\View
     *
     * @param $id
     * @param Request $request
     * @return Category|null
     * @throws \Symfony\Component\HttpKernel\Exception\NotFoundHttpException
     * @throws \LogicException
     */
    public function editAction($id, Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $category = $em->getRepository(Category::class)
            ->find($id)
        ;

        if (!$category instanceof Category) {
            throw new NotFoundHttpException('Category not found');
        }

        $data = json_decode($request->getContent(), true);
        $category->setName($data['name'] ?: $category->getName());

        $validator = $this->get('validator');
        $errors = $validator->validate($category);

        if (count($errors) > 0) {
            throw new HttpException(400, $errors[0]->getMessage());
        }

        $em->flush();

        return $category;
    }

    /**
     * @Rest\View
     *
     * @param Category $category
     * @throws \LogicException
     */
    public function deleteAction(Category $category)
    {
        $em = $this->getDoctrine()->getManager();
        $em->remove($category);
        $em->flush();
    }
}
