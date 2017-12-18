<?php

namespace AppBundle\Controller\Front;

use AppBundle\Entity\Category;
use AppBundle\Entity\City;
use AppBundle\Entity\Event;
use Doctrine\ORM\EntityNotFoundException;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

class EventController extends Controller
{
    public function createAction()
    {
        $categories = $this->getDoctrine()->getRepository(Category::class)->findAll();
        $cities = $this->getDoctrine()->getRepository(City::class)->findAll();

        return $this->render('@App/event/add.html.twig', [
            'title' => 'Create a new event',
            'categories' => $categories,
            'cities' => $cities,
        ]);
    }

    /**
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\Response
     * @throws EntityNotFoundException
     */
    public function storeAction(Request $request)
    {
        $categories = $this->getDoctrine()->getRepository(Category::class)->findAll();
        $cities = $this->getDoctrine()->getRepository(City::class)->findAll();

        $em = $this->getDoctrine()->getManager();
        $user = $this->getUser();
        $category = $em->getRepository(Category::class)->find($request->request->get('category'));
        if (!$category) {
            throw new EntityNotFoundException('Category not found');
        }
        $city = $em->getRepository(City::class)->find($request->request->get('city'));
        if (!$city) {
            throw new EntityNotFoundException('City not found');
        }

        $event = new Event();
        $event->setName($request->request->get('name'));
        $event->setType($request->request->get('type'));
        $event->setDescription($request->request->get('description'));
        $event->setDate(new \DateTime($request->request->get('date')));
        $event->setCreator($user);
        $event->setCategory($category);
        $event->setCity($city);

        $validator = $this->get('validator');
        $errors = $validator->validate($event);

        if (count($errors) > 0) {
            return $this->render('@App/event/add.html.twig', [
                'title' => 'Create a new event',
                'errors' => $errors,
                'event' => $event,
                'categories' => $categories,
                'cities' => $cities,
            ]);
        }

        $em->persist($event);
        $em->flush();

        return $this->redirectToRoute('home');
    }

    public function getAction($id)
    {
        $event = $this->getDoctrine()
            ->getRepository(Event::class)
            ->find($id)
        ;

        return $this->render('@App/event/event.html.twig', [
            'event' => $event
        ]);
    }

    public function editAction($id)
    {
        $categories = $this->getDoctrine()->getRepository(Category::class)->findAll();
        $cities = $this->getDoctrine()->getRepository(City::class)->findAll();

        $event = $this->getDoctrine()
            ->getRepository(Event::class)
            ->find($id)
        ;

        return $this->render('@App/event/edit.html.twig', [
            'title' => 'Update event',
            'event' => $event,
            'categories' => $categories,
            'cities' => $cities,
        ]);
    }

    /**
     * @param $id
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\Response
     * @throws EntityNotFoundException
     */
    public function updateAction($id, Request $request)
    {
        $categories = $this->getDoctrine()->getRepository(Category::class)->findAll();
        $cities = $this->getDoctrine()->getRepository(City::class)->findAll();

        $event = $this->getDoctrine()
            ->getRepository(Event::class)
            ->find($id)
        ;

        $category = $this->getDoctrine()->getRepository(Category::class)->find($request->request->get('category'));
        if (!$category) {
            throw new EntityNotFoundException('Category not found');
        }
        $city = $this->getDoctrine()->getRepository(City::class)->find($request->request->get('city'));
        if (!$city) {
            throw new EntityNotFoundException('City not found');
        }

        $event->setName($request->request->get('name'));
        $event->setType($request->request->get('type'));
        $event->setDescription($request->request->get('description'));
        $event->setDate(new \DateTime($request->request->get('date')));
        $event->setCategory($category);
        $event->setCity($city);

        $validator = $this->get('validator');
        $errors = $validator->validate($event);

        if (count($errors) > 0) {
            return $this->render('@App/event/edit.html.twig', [
                'title' => 'Update event',
                'errors' => $errors,
                'event' => $event,
                'categories' => $categories,
                'cities' => $cities,
            ]);
        }

        $em = $this->getDoctrine()->getManager();
        $em->flush();

        return $this->redirectToRoute('front_event_get', ['id' => $event->getId()]);
    }

    public function deleteAction($id)
    {
        $event = $this->getDoctrine()
            ->getRepository(Event::class)
            ->find($id)
        ;

        $em = $this->getDoctrine()->getManager();
        $em->remove($event);
        $em->flush();

        return $this->redirectToRoute('home');
    }
}
