<?php

namespace AppBundle\Controller\Api;

use AppBundle\Entity\City;
use FOS\RestBundle\Controller\Annotations as Rest;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class CityController extends Controller
{
    /**
     * @Rest\View
     *
     * @param Request $request
     * @return City
     * @throws \Symfony\Component\HttpKernel\Exception\HttpException
     * @throws \LogicException
     */
    public function createAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $data = json_decode($request->getContent(), true);

        $city = new City();
        $city->setName($data['name'] ?: '');

        $validator = $this->get('validator');
        $errors = $validator->validate($city);

        if (count($errors) > 0) {
            throw new HttpException(400, $errors[0]->getMessage());
        }

        $em->persist($city);
        $em->flush();

        return $city;
    }

    /**
     * @Rest\View
     *
     * @return City[]
     * @throws \LogicException
     */
    public function allAction()
    {
        return $this->getDoctrine()
            ->getRepository(City::class)
            ->findAll()
        ;
    }

    /**
     * @Rest\View
     *
     * @param $id
     * @return City|null
     * @throws \Symfony\Component\HttpKernel\Exception\NotFoundHttpException
     * @throws \LogicException
     */
    public function getAction($id)
    {
        $city = $this->getDoctrine()
            ->getRepository(City::class)
            ->find($id);

        if (!$city instanceof City) {
            throw new NotFoundHttpException('City not found');
        }

        return $city;
    }

    /**
     * @Rest\View
     *
     * @param $id
     * @param Request $request
     * @return City|null
     * @throws \Symfony\Component\HttpKernel\Exception\NotFoundHttpException
     * @throws \LogicException
     */
    public function editAction($id, Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $city = $em->getRepository(City::class)
            ->find($id)
        ;

        if (!$city instanceof City) {
            throw new NotFoundHttpException('City not found');
        }

        $data = json_decode($request->getContent(), true);
        $city->setName($data['name'] ?: $city->getName());

        $validator = $this->get('validator');
        $errors = $validator->validate($city);

        if (count($errors) > 0) {
            throw new HttpException(400, $errors[0]->getMessage());
        }

        $em->flush();

        return $city;
    }

    /**
     * @Rest\View
     *
     * @param City $city
     * @throws \LogicException
     */
    public function deleteAction(City $city)
    {
        $em = $this->getDoctrine()->getManager();
        $em->remove($city);
        $em->flush();
    }
}
