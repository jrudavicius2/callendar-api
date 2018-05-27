<?php

namespace AppBundle\Controller\Api;

use AppBundle\Entity\Category;
use FOS\RestBundle\Controller\Annotations as Rest;
use FOS\RestBundle\Controller\FOSRestController;
use Nelmio\ApiDocBundle\Annotation\ApiDoc;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class CategoryController extends FOSRestController
{
    /**
     *
     * @ApiDoc(
     *  resource=true,
     *  description="Create a category",
     *  output="AppBundle\Entity\Category",
     *  parameters={
     *      {"name"="name", "dataType"="string", "required"=true, "description"="category name"}
     *  },
     *     statusCodes={
     *         200="Returned when successful",
     *         400="Returned when the input parameters are bad"
     *     }
     * )
     *
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
     * @ApiDoc(
     *  resource=true,
     *  description="Gets list of categories",
     *  output="ArrayCollection<AppBundle\Entity\Category>",
     *     statusCodes={
     *         200="Returned when successful"
     *     }
     * )
     *
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
     *
     * @ApiDoc(
     *  resource=true,
     *  description="Gets category",
     *  output="AppBundle\Entity\Category",
     *     parameters={
     *      {"name"="categoryId", "dataType"="integer", "required"=true, "description"="category id", "requirement"="\d+",}
     *  },
     *     statusCodes={
     *         200="Returned when successful",
     *          404="Returns when category not found"
     *     }
     * )
     *
     *
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
     * @ApiDoc(
     *  resource=true,
     *  description="Create a category",
     *  output="AppBundle\Entity\Category",
     *  parameters={
     *      {"name"="name", "dataType"="string", "required"=true, "description"="category name"}
     *  },
     *     statusCodes={
     *         200="Returned when successful",
     *         400="Returned when the input parameters are bad"
     *     }
     * )
     *
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
