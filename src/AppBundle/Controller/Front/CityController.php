<?php

namespace AppBundle\Controller\Front;

use AppBundle\Entity\City;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

class CityController extends Controller
{
    public function createAction()
    {
        return $this->render('@App/city/add.html.twig', [
            'title' => 'Create a new city',
        ]);
    }

    public function storeAction(Request $request)
    {
        $city = new City();
        $city->setName($request->request->get('name'));

        $validator = $this->get('validator');
        $errors = $validator->validate($city);

        if (count($errors) > 0) {
            return $this->render('@App/city/add.html.twig', [
                'title' => 'Create a new city',
                'errors' => $errors,
                'city' => $city
            ]);
        }

        $em = $this->getDoctrine()->getManager();
        $em->persist($city);
        $em->flush();

        return $this->redirectToRoute('front_city_all');
    }

    public function allAction()
    {
        $cities = $this->getDoctrine()->getRepository(City::class)->findAll();

        return $this->render('@App/city/all.html.twig', [
            'title' => 'Cities',
            'cities' => $cities,
        ]);
    }

    public function editAction($id)
    {
        $city = $this->getDoctrine()->getRepository(City::class)->find($id);

        return $this->render('@App/city/edit.html.twig', [
            'title' => 'Edit city',
            'city' => $city,
        ]);
    }

    public function updateAction($id, Request $request)
    {
        $city = $this->getDoctrine()
            ->getRepository(City::class)
            ->find($id)
        ;

        $city->setName($request->request->get('name'));

        $validator = $this->get('validator');
        $errors = $validator->validate($city);

        if (count($errors) > 0) {
            return $this->render('@App/city/edit.html.twig', [
                'title' => 'Update city',
                'errors' => $errors,
                'city' => $city
            ]);
        }

        $em = $this->getDoctrine()->getManager();
        $em->flush();

        return $this->redirectToRoute('front_city_all');
    }

    public function deleteAction($id)
    {
        $city = $this->getDoctrine()
            ->getRepository(City::class)
            ->find($id)
        ;

        $em = $this->getDoctrine()->getManager();
        $em->remove($city);
        $em->flush();

        return $this->redirectToRoute('front_city_all');
    }
}
